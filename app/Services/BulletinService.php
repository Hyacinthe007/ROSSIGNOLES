<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\AnneeScolaire;
use App\Models\Periode;
use Exception;

/**
 * Service BulletinService
 * Gère la génération automatique des bulletins scolaires
 */

class BulletinService {
    
    private $noteModel;
    private $eleveModel;
    private $classeModel;
    private $matiereModel;
    private $anneeScolaireModel;
    /**
     * Connexion PDO brute (utilisée pour les transactions INSERT/DELETE directes)
     * @var PDO
     */
    private $db;
    
    public function __construct() {
        $this->noteModel = new Note();
        $this->eleveModel = new Eleve();
        $this->classeModel = new Classe();
        $this->matiereModel = new Matiere();
        $this->anneeScolaireModel = new AnneeScolaire();

        // Utiliser la connexion PDO publique exposée par BaseModel
        // (évite d'appeler la méthode protégée getConnection())
        $this->db = BaseModel::getDBConnection();
    }
    
    /**
     * Génère les bulletins pour une classe et une période
     * 
     * @param int $classeId ID de la classe
     * @param int $periodeId ID de la période (trimestre/semestre)
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Résultat de la génération
     */
    public function genererBulletins($classeId, $periodeId, $anneeScolaireId) {
        try {
            // Récupérer les élèves de la classe
            $eleves = $this->getElevesClasse($classeId, $anneeScolaireId);
            
            if (empty($eleves)) {
                return [
                    'success' => false,
                    'message' => 'Aucun élève trouvé dans cette classe'
                ];
            }
            
            // Récupérer les matières de la classe avec coefficients
            $matieres = $this->getMatieresClasse($classeId);
            
            if (empty($matieres)) {
                return [
                    'success' => false,
                    'message' => 'Aucune matière configurée pour cette classe'
                ];
            }
            
            $bulletins = [];
            
            // Calculer les moyennes pour chaque élève
            foreach ($eleves as $eleve) {
                $bulletin = $this->calculerBulletin($eleve['id'], $classeId, $periodeId, $anneeScolaireId, $matieres);
                $bulletins[] = $bulletin;
            }
            
            // Attribuer les rangs
            $bulletins = $this->attribuerRangs($bulletins);
            
            // Sauvegarder les bulletins
            $this->sauvegarderBulletins($bulletins, $classeId, $periodeId, $anneeScolaireId);
            
            return [
                'success' => true,
                'message' => count($bulletins) . ' bulletin(s) généré(s) avec succès',
                'bulletins' => $bulletins
            ];
            
        } catch (Exception $e) {
            error_log("Erreur génération bulletins : " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la génération : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Calcule le bulletin d'un élève
     */
    private function calculerBulletin($eleveId, $classeId, $periodeId, $anneeScolaireId, $matieres) {
        $bulletin = [
            'eleve_id' => $eleveId,
            'classe_id' => $classeId,
            'periode_id' => $periodeId,
            'annee_scolaire_id' => $anneeScolaireId,
            'notes_matieres' => [],
            'total_points' => 0,
            'total_coefficients' => 0,
            'moyenne_generale' => 0,
            'rang' => 0,
            'appreciation' => '',
            'decision_conseil' => ''
        ];
        
        foreach ($matieres as $matiere) {
            $notesMatiere = $this->getNotesMatiere($eleveId, $matiere['id'], $periodeId, $anneeScolaireId);
            
            if (!empty($notesMatiere)) {
                // Calculer la moyenne de la matière
                $moyenneMatiere = $this->calculerMoyenneMatiere($notesMatiere);
                $coefficient = $matiere['coefficient'];
                
                $bulletin['notes_matieres'][] = [
                    'matiere_id' => $matiere['id'],
                    'matiere_nom' => $matiere['nom'],
                    'coefficient' => $coefficient,
                    'moyenne' => $moyenneMatiere,
                    'points' => $moyenneMatiere * $coefficient,
                    'appreciation' => $this->getAppreciationMatiere($moyenneMatiere)
                ];
                
                $bulletin['total_points'] += $moyenneMatiere * $coefficient;
                $bulletin['total_coefficients'] += $coefficient;
            }
        }
        
        // Calculer la moyenne générale
        if ($bulletin['total_coefficients'] > 0) {
            $bulletin['moyenne_generale'] = round($bulletin['total_points'] / $bulletin['total_coefficients'], 2);
        }
        
        // Appréciation générale
        $bulletin['appreciation'] = $this->getAppreciationGenerale($bulletin['moyenne_generale']);
        
        // Décision du conseil
        $bulletin['decision_conseil'] = $this->getDecisionConseil($bulletin['moyenne_generale'], $periodeId);
        
        return $bulletin;
    }
    
    /**
     * Récupère les notes d'une matière pour un élève
     */
    private function getNotesMatiere($eleveId, $matiereId, $periodeId, $anneeScolaireId) {
        $sql = "SELECT n.note, n.type, e.coefficient as coef_eval
                FROM (
                    SELECT note, evaluation_id, 'examen' as type FROM notes_examens WHERE eleve_id = ?
                    UNION ALL
                    SELECT note, evaluation_id, 'interrogation' as type FROM notes_interrogations WHERE eleve_id = ?
                ) n
                INNER JOIN evaluations e ON n.evaluation_id = e.id
                WHERE e.matiere_id = ? 
                AND e.periode_id = ?
                AND e.annee_scolaire_id = ?
                AND n.note IS NOT NULL";
        
        return $this->noteModel->query($sql, [$eleveId, $eleveId, $matiereId, $periodeId, $anneeScolaireId]);
    }
    
    /**
     * Calcule la moyenne d'une matière selon la nouvelle formule :
     * Moyenne = (Moyenne Interrogations + Note Examen × 2) / 3
     * 
     * Pondération : 
     * - Interrogations : coefficient 1
     * - Examen : coefficient 2
     */
    private function calculerMoyenneMatiere($notes) {
        if (empty($notes)) return 0;
        
        $examNotes = [];
        $interroNotes = [];
        
        foreach ($notes as $note) {
            if ($note['type'] === 'examen') {
                $examNotes[] = $note['note'];
            } else {
                $interroNotes[] = $note['note'];
            }
        }
        
        // Moyenne des interrogations
        $moyenneInterros = null;
        if (!empty($interroNotes)) {
            $moyenneInterros = array_sum($interroNotes) / count($interroNotes);
        }
        
        // Note d'examen (on prend la moyenne s'il y en a plusieurs par erreur, sinon la seule)
        $noteExamen = null;
        if (!empty($examNotes)) {
            $noteExamen = array_sum($examNotes) / count($examNotes);
        }
        
        // Si pas d'examen, on prend juste la moyenne des interros
        if ($noteExamen === null && $moyenneInterros !== null) {
            return round($moyenneInterros, 2);
        }
        
        // Si pas d'interros, on prend juste l'examen
        if ($moyenneInterros === null && $noteExamen !== null) {
            return round($noteExamen, 2);
        }
        
        // Si aucune note, retourner 0
        if ($moyenneInterros === null && $noteExamen === null) {
            return 0;
        }
        
        // Formule finale : (Moyenne Interros × 1 + Note Examen × 2) / 3
        return round(($moyenneInterros + ($noteExamen * 2)) / 3, 2);
    }
    
    /**
     * Attribue les rangs aux élèves
     */
    private function attribuerRangs($bulletins) {
        // Trier par moyenne décroissante
        usort($bulletins, function($a, $b) {
            return $b['moyenne_generale'] <=> $a['moyenne_generale'];
        });
        
        // Attribuer les rangs
        $rang = 1;
        $derniereMoyenne = null;
        $compteurRang = 1;
        
        foreach ($bulletins as &$bulletin) {
            if ($derniereMoyenne !== null && $bulletin['moyenne_generale'] < $derniereMoyenne) {
                $rang = $compteurRang;
            }
            
            $bulletin['rang'] = $rang;
            $bulletin['effectif'] = count($bulletins);
            $derniereMoyenne = $bulletin['moyenne_generale'];
            $compteurRang++;
        }
        
        return $bulletins;
    }
    
    /**
     * Sauvegarde les bulletins en base de données
     */
    private function sauvegarderBulletins($bulletins, $classeId, $periodeId, $anneeScolaireId) {
        try {
            $this->db->beginTransaction();
            
            // Supprimer les anciens bulletins de cette période
            $sql = "DELETE FROM bulletins WHERE classe_id = ? AND periode_id = ? AND annee_scolaire_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$classeId, $periodeId, $anneeScolaireId]);
            
            foreach ($bulletins as $bulletin) {
                // Insérer le bulletin
                $sql = "INSERT INTO bulletins (
                    eleve_id, classe_id, periode_id, annee_scolaire_id,
                    moyenne_generale, total_points, total_coefficients,
                    rang, effectif, appreciation_generale, decision_conseil,
                    date_generation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $bulletin['eleve_id'],
                    $bulletin['classe_id'],
                    $bulletin['periode_id'],
                    $bulletin['annee_scolaire_id'],
                    $bulletin['moyenne_generale'],
                    $bulletin['total_points'],
                    $bulletin['total_coefficients'],
                    $bulletin['rang'],
                    $bulletin['effectif'],
                    $bulletin['appreciation'],
                    $bulletin['decision_conseil']
                ]);
                
                $bulletinId = $this->db->lastInsertId();
                
                // Insérer les notes par matière
                foreach ($bulletin['notes_matieres'] as $noteMatiere) {
                    $sql = "INSERT INTO bulletins_notes (
                        bulletin_id, matiere_id, moyenne, coefficient,
                        points, appreciation
                    ) VALUES (?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $bulletinId,
                        $noteMatiere['matiere_id'],
                        $noteMatiere['moyenne'],
                        $noteMatiere['coefficient'],
                        $noteMatiere['points'],
                        $noteMatiere['appreciation']
                    ]);
                }
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Récupère les élèves d'une classe
     * 
     * Vérifie que l'élève :
     * - A une inscription validée (i.statut = 'validee')
     * - N'est pas bloqué pour paiement initial (i.bloquee = 0)
     * - Peut suivre les cours (pas exclu pour impayé mensuel)
     * 
     * @param int $classeId ID de la classe
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Liste des élèves éligibles
     */
    public function getElevesClasse($classeId, $anneeScolaireId) {
        $sql = "SELECT DISTINCT 
                    e.id, 
                    e.matricule, 
                    e.nom, 
                    e.prenom,
                    i.bloquee,
                    COALESCE(see.peut_suivre_cours, 1) as peut_suivre_cours,
                    see.statut as statut_ecolage_mois
                FROM eleves e
                INNER JOIN inscriptions i ON e.id = i.eleve_id
                LEFT JOIN statuts_eleves_ecolage see ON (
                    see.eleve_id = e.id 
                    AND see.annee_scolaire_id = i.annee_scolaire_id
                    AND see.mois = MONTH(CURDATE())
                    AND see.annee = YEAR(CURDATE())
                )
                WHERE i.classe_id = ? 
                  AND i.annee_scolaire_id = ? 
                  AND i.statut = 'validee'
                  AND i.bloquee = 0
                  AND COALESCE(see.peut_suivre_cours, 1) = 1
                ORDER BY e.nom, e.prenom";
        
        return $this->eleveModel->query($sql, [$classeId, $anneeScolaireId]);
    }
    
    /**
     * Récupère les matières d'une classe avec coefficients
     */
    public function getMatieresClasse($classeId) {
        $sql = "SELECT m.id, m.nom, m.code, 
                COALESCE(mc.coefficient, ms.coefficient, mn.coefficient, 1.00) as coefficient
                FROM matieres m
                INNER JOIN classes c ON c.id = ?
                LEFT JOIN matieres_classes mc ON (m.id = mc.matiere_id AND c.id = mc.classe_id AND c.annee_scolaire_id = mc.annee_scolaire_id)
                LEFT JOIN matieres_series ms ON (m.id = ms.matiere_id AND c.serie_id = ms.serie_id AND ms.actif = 1)
                LEFT JOIN matieres_niveaux mn ON (m.id = mn.matiere_id AND c.niveau_id = mn.niveau_id AND mn.actif = 1)
                WHERE (mc.id IS NOT NULL OR ms.id IS NOT NULL OR mn.id IS NOT NULL)
                ORDER BY m.nom";
        
        return $this->matiereModel->query($sql, [$classeId]);
    }
    
    /**
     * Génère une appréciation pour une matière
     */
    private function getAppreciationMatiere($moyenne) {
        if ($moyenne >= 18) return "Excellent";
        if ($moyenne >= 16) return "Très bien";
        if ($moyenne >= 14) return "Bien";
        if ($moyenne >= 12) return "Assez bien";
        if ($moyenne >= 10) return "Passable";
        if ($moyenne >= 8) return "Insuffisant";
        return "Très insuffisant";
    }
    
    /**
     * Génère une appréciation générale
     */
    private function getAppreciationGenerale($moyenne) {
        if ($moyenne >= 18) return "Excellent travail. Félicitations !";
        if ($moyenne >= 16) return "Très bon travail. Continuez ainsi.";
        if ($moyenne >= 14) return "Bon travail. Peut mieux faire.";
        if ($moyenne >= 12) return "Travail satisfaisant. Encouragements.";
        if ($moyenne >= 10) return "Travail acceptable. Doit faire plus d'efforts.";
        if ($moyenne >= 8) return "Travail insuffisant. Doit redoubler d'efforts.";
        return "Travail très insuffisant. Nécessite un soutien urgent.";
    }
    
    /**
     * Détermine la décision du conseil
     */
    private function getDecisionConseil($moyenne, $periodeId) {
        // Pour le dernier trimestre/semestre
        $sql = "SELECT COUNT(*) as count FROM periodes WHERE id > ? AND actif = 1";

        // Utiliser le modèle Periode (hérite de BaseModel) pour profiter de queryOne()
        $periodeModel = new Periode();
        $result = $periodeModel->queryOne($sql, [$periodeId]);
        $estDernierePeriode = ($result['count'] == 0);
        
        if ($estDernierePeriode) {
            if ($moyenne >= 10) {
                return "ADMIS(E) en classe supérieure";
            } else if ($moyenne >= 8) {
                return "PASSAGE CONDITIONNEL";
            } else {
                return "REDOUBLEMENT";
            }
        } else {
            if ($moyenne >= 16) {
                return "FÉLICITATIONS";
            } else if ($moyenne >= 14) {
                return "ENCOURAGEMENTS";
            } else if ($moyenne >= 10) {
                return "PASSAGE";
            } else {
                return "AVERTISSEMENT TRAVAIL";
            }
        }
    }
    
    /**
     * Récupère un bulletin
     */
    public function getBulletin($eleveId, $periodeId, $anneeScolaireId) {
        $bulletin = $this->db->queryOne(
            "SELECT b.*, e.nom, e.prenom, e.matricule, c.nom as classe_nom, p.libelle as periode_libelle
             FROM bulletins b
             INNER JOIN eleves e ON b.eleve_id = e.id
             INNER JOIN classes c ON b.classe_id = c.id
             INNER JOIN periodes p ON b.periode_id = p.id
             WHERE b.eleve_id = ? AND b.periode_id = ? AND b.annee_scolaire_id = ?",
            [$eleveId, $periodeId, $anneeScolaireId]
        );
        
        if ($bulletin) {
            $bulletin['notes_matieres'] = $this->db->query(
                "SELECT bn.*, m.nom as matiere_nom, m.code as matiere_code
                 FROM bulletins_notes bn
                 INNER JOIN matieres m ON bn.matiere_id = m.id
                 WHERE bn.bulletin_id = ?
                 ORDER BY m.nom",
                [$bulletin['id']]
            );
        }
        
        return $bulletin;
    }
    
    /**
     * Génère le PDF du bulletin (ancienne méthode conservée pour compatibilité)
     */
    public function generatePdf($bulletin) {
        require_once APP_PATH . '/Services/PdfService.php';
        $pdfService = new PdfService();
        $pdfService->generateBulletin($bulletin);
    }
}
