<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BaseModel;
use Exception;

/**
 * Service EligibiliteService
 * Gère la vérification de l'éligibilité des élèves aux évaluations
 */

class EligibiliteService {
    
    private $db;
    
    public function __construct() {
        $this->db = new BaseModel();
    }
    
    /**
     * Vérifie si un élève peut passer une évaluation
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @param int $mois Mois de vérification (1-12)
     * @return array ['peut_passer' => bool, 'message' => string]
     */
    public function verifierEligibilite($eleveId, $anneeScolaireId, $mois = null) {
        // Si le mois n'est pas spécifié, utiliser le mois actuel
        if ($mois === null) {
            $mois = (int)date('n');
        }
        
        try {
            // Appeler la procédure stockée
            $sql = "CALL verifier_ecolage_eleve(?, ?, ?, @peut_passer, @message)";
            $this->db->query($sql, [$eleveId, $anneeScolaireId, $mois]);
            
            // Récupérer les résultats
            $result = $this->db->query("SELECT @peut_passer as peut_passer, @message as message");
            
            if ($result && count($result) > 0) {
                return [
                    'peut_passer' => (bool)$result[0]['peut_passer'],
                    'message' => $result[0]['message']
                ];
            }
            
            return [
                'peut_passer' => false,
                'message' => 'Erreur lors de la vérification de l\'éligibilité'
            ];
            
        } catch (Exception $e) {
            error_log("Erreur vérification éligibilité: " . $e->getMessage());
            return [
                'peut_passer' => false,
                'message' => 'Erreur système: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Vérifie l'éligibilité de plusieurs élèves
     * 
     * @param array $elevesIds Liste des IDs d'élèves
     * @param int $anneeScolaireId ID de l'année scolaire
     * @param int $mois Mois de vérification
     * @return array Tableau associatif [eleve_id => ['peut_passer' => bool, 'message' => string]]
     */
    public function verifierEligibiliteMultiple($elevesIds, $anneeScolaireId, $mois = null) {
        $resultats = [];
        
        foreach ($elevesIds as $eleveId) {
            $resultats[$eleveId] = $this->verifierEligibilite($eleveId, $anneeScolaireId, $mois);
        }
        
        return $resultats;
    }
    
    /**
     * Récupère la liste des élèves éligibles pour une classe
     * 
     * @param int $classeId ID de la classe
     * @param int $anneeScolaireId ID de l'année scolaire
     * @param int $mois Mois de vérification
     * @return array ['eligibles' => array, 'bloques' => array]
     */
    public function getElevesEligiblesClasse($classeId, $anneeScolaireId, $mois = null) {
        if ($mois === null) {
            $mois = (int)date('n');
        }
        
        // Récupérer tous les élèves de la classe
        $sql = "SELECT DISTINCT 
                    e.id,
                    e.matricule,
                    e.nom,
                    e.prenom
                FROM eleves e
                INNER JOIN inscriptions i ON e.id = i.eleve_id
                WHERE i.classe_id = ?
                  AND i.annee_scolaire_id = ?
                  AND i.statut = 'validee'
                ORDER BY e.nom, e.prenom";
        
        $eleves = $this->db->query($sql, [$classeId, $anneeScolaireId]);
        
        $eligibles = [];
        $bloques = [];
        
        foreach ($eleves as $eleve) {
            $verification = $this->verifierEligibilite($eleve['id'], $anneeScolaireId, $mois);
            
            $eleveData = array_merge($eleve, $verification);
            
            if ($verification['peut_passer']) {
                $eligibles[] = $eleveData;
            } else {
                $bloques[] = $eleveData;
            }
        }
        
        return [
            'eligibles' => $eligibles,
            'bloques' => $bloques,
            'total' => count($eleves),
            'nb_eligibles' => count($eligibles),
            'nb_bloques' => count($bloques)
        ];
    }
    
    /**
     * Récupère les alertes d'éligibilité depuis la vue SQL
     * 
     * @param int $anneeScolaireId ID de l'année scolaire
     * @param bool $seulementBloques Si true, retourne uniquement les élèves bloqués
     * @return array Liste des alertes
     */
    public function getAlertesEligibilite($anneeScolaireId, $seulementBloques = false) {
        $sql = "SELECT *
                FROM vue_alertes_eligibilite_evaluations
                WHERE annee_scolaire_id = ?";
        
        if ($seulementBloques) {
            $sql .= " AND peut_passer_evaluations = 0";
        }
        
        $sql .= " ORDER BY priorite_alerte ASC, classe, eleve";
        
        return $this->db->query($sql, [$anneeScolaireId]);
    }
    
    /**
     * Génère un rapport d'éligibilité pour une classe
     * 
     * @param int $classeId ID de la classe
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Rapport détaillé
     */
    public function genererRapportClasse($classeId, $anneeScolaireId) {
        $eligibilite = $this->getElevesEligiblesClasse($classeId, $anneeScolaireId);
        
        // Récupérer les informations de la classe
        $sql = "SELECT 
                    c.nom as classe,
                    c.code,
                    n.libelle as niveau,
                    a.libelle as annee_scolaire
                FROM classes c
                INNER JOIN niveaux n ON c.niveau_id = n.id
                INNER JOIN annees_scolaires a ON a.id = ?
                WHERE c.id = ?";
        
        $classe = $this->db->query($sql, [$anneeScolaireId, $classeId]);
        
        return [
            'classe' => $classe ? $classe[0] : null,
            'statistiques' => [
                'total_eleves' => $eligibilite['total'],
                'nb_eligibles' => $eligibilite['nb_eligibles'],
                'nb_bloques' => $eligibilite['nb_bloques'],
                'taux_eligibilite' => $eligibilite['total'] > 0 
                    ? round(($eligibilite['nb_eligibles'] / $eligibilite['total']) * 100, 2) 
                    : 0
            ],
            'eleves_eligibles' => $eligibilite['eligibles'],
            'eleves_bloques' => $eligibilite['bloques'],
            'date_generation' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Calcule la moyenne de bulletin selon la formule
     * 
     * @param float $moyenneInterro Moyenne des interrogations
     * @param float $noteExamen Note de l'examen
     * @return float Note du bulletin
     */
    public function calculerMoyenneBulletin($moyenneInterro, $noteExamen) {
        try {
            $sql = "SELECT calculer_moyenne_bulletin(?, ?) as note_bulletin";
            $result = $this->db->query($sql, [$moyenneInterro, $noteExamen]);
            
            if ($result && count($result) > 0) {
                return (float)$result[0]['note_bulletin'];
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Erreur calcul moyenne bulletin: " . $e->getMessage());
            return null;
        }
    }
}
