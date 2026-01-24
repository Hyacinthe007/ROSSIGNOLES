<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Loggable;
use Exception;
use DateTime;
use App\Models\AnneeScolaire;

/**
 * Modèle EcheancierEcolage
 * Gestion des échéanciers d'écolage avec journalisation automatique
 */

class EcheancierEcolage extends BaseModel {
    use Loggable;
    
    protected $table = 'echeanciers_ecolages';
    protected $fillable = [
        'eleve_id', 'annee_scolaire_id', 'mois', 'annee', 'mois_libelle',
        'date_limite', 'date_limite_normale', 'date_limite_grace', 'date_exclusion',
        'montant_du', 'montant_paye', 'montant_restant',
        'statut', 'date_paiement_complet', 'nombre_paiements',
        'derniere_facture_id', 'jours_retard', 'created_by', 'updated_by'
    ];
    
    /**
     * Met à jour une échéance avec journalisation des changements de statut
     * @param int $id ID de l'échéance
     * @param array $newData Nouvelles données
     * @return bool Succès de l'opération
     */
    public function update($id, $newData) {
        // Récupérer l'ancienne échéance
        $oldData = $this->find($id);
        
        if (!$oldData) {
            return false;
        }
        
        $success = parent::update($id, $newData);
        
        if ($success && isset($newData['statut']) && $oldData['statut'] != $newData['statut']) {
            // Logger les changements de statut critiques
            $description = "Changement de statut d'échéance pour l'élève #{$oldData['eleve_id']} - {$oldData['mois_libelle']}: {$oldData['statut']} → {$newData['statut']}";
            
            // Alerte spéciale pour passage en exclusion
            if ($newData['statut'] == 'exclusion' || $newData['statut'] == 'impaye_exclu') {
                $this->logExclusionImpaye(
                    $oldData['eleve_id'],
                    $id,
                    $oldData['montant_restant']
                );
            } else {
                $this->logActivity(
                    'update_statut',
                    'finance',
                    $description,
                    'echeancier_ecolage',
                    $id
                );
            }
        }
        
        return $success;
    }
    
    /**
     * Obtient les échéances en retard pour un élève
     */
    public function getRetards($eleveId) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE eleve_id = ? AND statut = 'retard'
             ORDER BY date_limite ASC",
            [$eleveId]
        );
    }
    
    /**
     * Génère l'échéancier de 9 mois (Octobre - Juin) pour un élève inscrit
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @param float $montantMensuel Montant de l'écolage mensuel
     * @param int $moisDebut Mois de début (1-12), par défaut octobre (10)
     * @param int $userId ID de l'utilisateur créateur
     * @return array Tableau des échéances créées
     */
    public function genererEcheancier($eleveId, $anneeScolaireId, $montantMensuel, $moisDebut = 10, $userId = null) {
        try {
            // Vérifier si un échéancier existe déjà
            $existant = $this->queryOne(
                "SELECT COUNT(*) as count FROM {$this->table} 
                 WHERE eleve_id = ? AND annee_scolaire_id = ?",
                [$eleveId, $anneeScolaireId]
            );
            
            if ($existant && $existant['count'] > 0) {
                throw new Exception("Un échéancier existe déjà pour cet élève et cette année scolaire");
            }
            
            // Récupérer l'année scolaire pour obtenir l'année de début
            $anneeScolaireModel = new AnneeScolaire();
            $anneeScolaire = $anneeScolaireModel->findById($anneeScolaireId);
            
            if (!$anneeScolaire) {
                throw new Exception("Année scolaire introuvable");
            }
            
            // Déterminer l'année de départ (année de la date de début)
            $anneeDebut = (int)date('Y', strtotime($anneeScolaire['date_debut']));
            
            $echeances = [];
            $moisActuel = $moisDebut;
            $anneeActuelle = $anneeDebut;
            
            // Générer 9 échéances mensuelles (Octobre à Juin)
            for ($i = 0; $i < 9; $i++) {
                // Calculer les dates limites
                $dates = $this->calculerDatesLimites($moisActuel, $anneeActuelle);
                
                // Libellé du mois
                $moisLibelle = $this->getMoisLibelle($moisActuel);
                
                // Créer l'échéance
                $echeanceData = [
                    'eleve_id' => $eleveId,
                    'annee_scolaire_id' => $anneeScolaireId,
                    'mois' => $moisActuel,
                    'annee' => $anneeActuelle,
                    'mois_libelle' => $moisLibelle . ' ' . $anneeActuelle,
                    'date_limite' => $dates['date_limite_normale'],
                    'date_limite_normale' => $dates['date_limite_normale'],
                    'date_limite_grace' => $dates['date_limite_grace'],
                    'date_exclusion' => $dates['date_exclusion'],
                    'montant_du' => $montantMensuel,
                    'montant_paye' => 0.00,
                    'montant_restant' => $montantMensuel,
                    'statut' => 'en_attente',
                    'nombre_paiements' => 0,
                    'jours_retard' => 0,
                    'created_by' => $userId
                ];
                
                $echeanceId = $this->create($echeanceData);
                $echeanceData['id'] = $echeanceId;
                $echeances[] = $echeanceData;
                
                // Passer au mois suivant
                $moisActuel++;
                if ($moisActuel > 12) {
                    $moisActuel = 1;
                    $anneeActuelle++;
                }
            }
            
            return $echeances;
            
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la génération de l'échéancier : " . $e->getMessage());
        }
    }
    
    /**
     * Calcule les dates limites pour un mois donné
     * Règles de paiement :
     * - Date limite normale : 10 du mois M (paiement attendu)
     * - Période de recouvrement : du 1er au 10 du mois M+1 (retard léger)
     * - Date d'exclusion : à partir du 11 du mois M+1 (suspension automatique)
     * 
     * Exemple pour le mois d'Octobre 2025 :
     * - Paiement attendu : avant le 10 octobre 2025
     * - Recouvrement : du 1er au 10 novembre 2025
     * - Exclusion : à partir du 11 novembre 2025 (inclus)
     * 
     * @param int $mois Mois (1-12)
     * @param int $annee Année
     * @return array Tableau avec les dates calculées
     */
    private function calculerDatesLimites($mois, $annee) {
        // Date limite normale : 10 du mois M (paiement attendu avant cette date)
        $dateLimiteNormale = sprintf('%04d-%02d-10', $annee, $mois);
        
        // Date de début de recouvrement : 1er du mois M+1
        // Entre le 1er et le 10 du mois M+1, l'élève est en "recouvrement"
        $d = new DateTime(sprintf('%04d-%02d-01', $annee, $mois));
        $d->modify('+1 month');
        $dateRecouvrement = $d->format('Y-m-d');
        
        // Date d'exclusion/suspension : 11 du mois M+1
        // À partir de cette date (inclus), l'élève est automatiquement suspendu
        $d = new DateTime(sprintf('%04d-%02d-11', $annee, $mois));
        $d->modify('+1 month');
        $dateExclusion = $d->format('Y-m-d');
        
        return [
            'date_limite_normale' => $dateLimiteNormale,
            'date_limite_grace' => $dateRecouvrement, // Utilisé pour le statut 'retard'
            'date_exclusion' => $dateExclusion        // Utilisé pour le statut 'exclusion'
        ];
    }
    
    /**
     * Retourne le libellé français d'un mois
     * 
     * @param int $mois Numéro du mois (1-12)
     * @return string Libellé du mois
     */
    private function getMoisLibelle($mois) {
        $moisLibelles = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        
        return $moisLibelles[$mois] ?? 'Inconnu';
    }
    
    /**
     * Obtient toutes les échéances d'un élève pour une année scolaire
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Liste des échéances
     */
    public function getEcheancierEleve($eleveId, $anneeScolaireId) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE eleve_id = ? AND annee_scolaire_id = ?
             ORDER BY annee ASC, mois ASC",
            [$eleveId, $anneeScolaireId]
        );
    }
    
    /**
     * Met à jour le statut d'une échéance en fonction de la date actuelle
     * 
     * @param int $echeanceId ID de l'échéance
     * @return bool Succès de la mise à jour
     */
    public function updateStatut($echeanceId) {
        $echeance = $this->findById($echeanceId);
        
        if (!$echeance) {
            return false;
        }
        
        $dateActuelle = date('Y-m-d');
        $montantRestant = $echeance['montant_restant'];
        
        // Si totalement payé
        if ($montantRestant <= 0) {
            $this->update($echeanceId, ['statut' => 'paye', 'jours_retard' => 0]);
            
            // Si l'échéance était bloquante, on vérifie si on peut réactiver l'élève
            $this->syncEleveStatut($echeance['eleve_id']);
            return true;
        }
        
        // Calculer les statuts selon les nouvelles règles
        if ($dateActuelle >= $echeance['date_exclusion']) {
            $joursRetard = (strtotime($dateActuelle) - strtotime($echeance['date_exclusion'])) / 86400;
            $this->update($echeanceId, [
                'statut' => 'exclusion',
                'jours_retard' => (int)$joursRetard
            ]);
            // Désactiver l'élève
            $this->db->prepare("UPDATE eleves SET statut = 'inactif' WHERE id = ?")->execute([$echeance['eleve_id']]);
        } elseif ($dateActuelle >= $echeance['date_limite_grace']) {
            $joursRetard = (strtotime($dateActuelle) - strtotime($echeance['date_limite_grace'])) / 86400;
            $this->update($echeanceId, [
                'statut' => 'retard', // Devient visible dans l'échéancier (Recouvrement)
                'jours_retard' => (int)$joursRetard
            ]);
        } elseif ($dateActuelle > $echeance['date_limite_normale']) {
            $this->update($echeanceId, ['statut' => 'impaye']); // Retard léger (après le 10 du mois M)
        } else {
            $this->update($echeanceId, ['statut' => 'en_attente', 'jours_retard' => 0]);
        }
        
        return true;
    }

    /**
     * Synchronise le statut de l'élève en fonction de ses retards critiques
     */
    private function syncEleveStatut($eleveId) {
        // Vérifier s'il reste des échéances au statut 'exclusion'
        $res = $this->queryOne(
            "SELECT COUNT(*) as nb FROM {$this->table} WHERE eleve_id = ? AND statut = 'exclusion' AND montant_restant > 0",
            [$eleveId]
        );
        
        if (!$res || $res['nb'] == 0) {
            // Plus de retard critique -> on réactive
            $this->db->prepare("UPDATE eleves SET statut = 'actif' WHERE id = ?")->execute([$eleveId]);
        }
    }
}
