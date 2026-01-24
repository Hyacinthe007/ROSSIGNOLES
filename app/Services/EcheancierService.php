<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\EcheancierEcolage;
use App\Models\TarifInscription;
use App\Models\Inscription;
use App\Models\Classe;
use App\Models\Niveau;
use Exception;

/**
 * Service EcheancierService
 * Gère la logique métier de l'échéancier d'écolage
 */

class EcheancierService {
    
    public $echeancierModel;
    public $tarifModel;
    public $inscriptionModel;
    public $classeModel;
    public $niveauModel;
    
    public function __construct() {
        $this->echeancierModel = new EcheancierEcolage();
        $this->tarifModel = new TarifInscription();
        $this->inscriptionModel = new Inscription();
        $this->classeModel = new Classe();
        $this->niveauModel = new Niveau();
    }
    
    /**
     * Génère automatiquement l'échéancier après validation d'une inscription
     * 
     * @param int $inscriptionId ID de l'inscription
     * @param int $userId ID de l'utilisateur créateur
     * @return array Résultat de la génération
     */
    public function genererEcheancierInscription($inscriptionId, $userId = null) {
        try {
            // Récupérer les détails de l'inscription
            $inscription = $this->inscriptionModel->getDetails($inscriptionId);
            
            if (!$inscription) {
                throw new Exception("Inscription introuvable");
            }
            
            // Vérifier que l'inscription est validée
            if ($inscription['statut'] !== 'validee') {
                throw new Exception("L'inscription doit être validée avant de générer l'échéancier");
            }
            
            // Récupérer la classe pour obtenir le niveau
            $classe = $this->classeModel->findById($inscription['classe_id']);
            if (!$classe) {
                throw new Exception("Classe introuvable");
            }
            
            // Récupérer le tarif applicable
            $tarif = $this->tarifModel->getByAnneeAndNiveau(
                $inscription['annee_scolaire_id'],
                $classe['niveau_id']
            );
            
            if (!$tarif) {
                throw new Exception("Aucun tarif défini pour ce niveau et cette année scolaire");
            }
            
            // Montant mensuel de l'écolage
            $montantMensuel = $tarif['ecolage_mensuel'];
            
            // Mois de début (par défaut octobre, mais peut être défini dans le tarif)
            $moisDebut = $tarif['mois_debut_annee'] ?? 10;
            
            // Générer l'échéancier
            $echeances = $this->echeancierModel->genererEcheancier(
                $inscription['eleve_id'],
                $inscription['annee_scolaire_id'],
                $montantMensuel,
                $moisDebut,
                $userId
            );
            
            return [
                'success' => true,
                'message' => 'Échéancier généré avec succès',
                'nb_echeances' => count($echeances),
                'montant_total' => $montantMensuel * 9,
                'echeances' => $echeances
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Génère l'échéancier avec des paramètres personnalisés
     * 
     * @param array $params Paramètres de génération
     * @return array Résultat de la génération
     */
    public function genererEcheancierPersonnalise($params) {
        try {
            $eleveId = $params['eleve_id'] ?? null;
            $anneeScolaireId = $params['annee_scolaire_id'] ?? null;
            $montantMensuel = $params['montant_mensuel'] ?? null;
            $moisDebut = $params['mois_debut'] ?? 10;
            $userId = $params['user_id'] ?? null;
            
            if (!$eleveId || !$anneeScolaireId || !$montantMensuel) {
                throw new Exception("Paramètres manquants pour la génération de l'échéancier");
            }
            
            $echeances = $this->echeancierModel->genererEcheancier(
                $eleveId,
                $anneeScolaireId,
                $montantMensuel,
                $moisDebut,
                $userId
            );
            
            return [
                'success' => true,
                'message' => 'Échéancier généré avec succès',
                'nb_echeances' => count($echeances),
                'montant_total' => $montantMensuel * 9,
                'echeances' => $echeances
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupère l'échéancier complet d'un élève avec statistiques
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Échéancier avec statistiques
     */
    public function getEcheancierAvecStatistiques($eleveId, $anneeScolaireId) {
        $echeances = $this->echeancierModel->getEcheancierEleve($eleveId, $anneeScolaireId);
        
        // Calculer les statistiques
        $stats = [
            'total_du' => 0,
            'total_paye' => 0,
            'total_restant' => 0,
            'nb_echeances_payees' => 0,
            'nb_echeances_en_retard' => 0,
            'nb_echeances_total' => count($echeances)
        ];
        
        foreach ($echeances as $echeance) {
            $stats['total_du'] += $echeance['montant_du'];
            $stats['total_paye'] += $echeance['montant_paye'];
            $stats['total_restant'] += $echeance['montant_restant'];
            
            if ($echeance['statut'] === 'paye') {
                $stats['nb_echeances_payees']++;
            }
            
            if (in_array($echeance['statut'], ['retard', 'retard_grave', 'exclusion'])) {
                $stats['nb_echeances_en_retard']++;
            }
        }
        
        $stats['taux_paiement'] = $stats['total_du'] > 0 
            ? round(($stats['total_paye'] / $stats['total_du']) * 100, 2) 
            : 0;
        
        return [
            'echeances' => $echeances,
            'statistiques' => $stats
        ];
    }
    
    /**
     * Met à jour les statuts de toutes les échéances d'un élève
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return int Nombre d'échéances mises à jour
     */
    public function updateStatutsEcheancier($eleveId, $anneeScolaireId) {
        $echeances = $this->echeancierModel->getEcheancierEleve($eleveId, $anneeScolaireId);
        $nbUpdates = 0;
        
        foreach ($echeances as $echeance) {
            if ($this->echeancierModel->updateStatut($echeance['id'])) {
                $nbUpdates++;
            }
        }
        
        return $nbUpdates;
    }
    
    /**
     * Récupère tous les élèves en retard de paiement
     * 
     * @param int $anneeScolaireId ID de l'année scolaire (optionnel)
     * @return array Liste des élèves en retard
     */
    public function getElevesEnRetard($anneeScolaireId = null) {
        $sql = "SELECT DISTINCT 
                    ee.eleve_id,
                    e.matricule,
                    e.nom,
                    e.prenom,
                    c.nom as classe_nom,
                    COUNT(ee.id) as nb_echeances_retard,
                    SUM(ee.montant_restant) as total_retard
                FROM echeanciers_ecolages ee
                INNER JOIN eleves e ON ee.eleve_id = e.id
                INNER JOIN inscriptions i ON (ee.eleve_id = i.eleve_id AND ee.annee_scolaire_id = i.annee_scolaire_id)
                INNER JOIN classes c ON i.classe_id = c.id
                WHERE ee.statut IN ('retard', 'retard_grave', 'exclusion')";
        
        $params = [];
        
        if ($anneeScolaireId) {
            $sql .= " AND ee.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " GROUP BY ee.eleve_id, e.matricule, e.nom, e.prenom, c.nom
                  ORDER BY total_retard DESC";
        
        return $this->echeancierModel->query($sql, $params);
    }
    
    /**
     * Supprime l'échéancier d'un élève (en cas d'annulation d'inscription)
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return bool Succès de la suppression
     */
    public function supprimerEcheancier($eleveId, $anneeScolaireId) {
        try {
            $echeances = $this->echeancierModel->getEcheancierEleve($eleveId, $anneeScolaireId);
            
            foreach ($echeances as $echeance) {
                // Vérifier qu'aucun paiement n'a été effectué
                if ($echeance['montant_paye'] > 0) {
                    throw new Exception("Impossible de supprimer l'échéancier : des paiements ont déjà été effectués");
                }
                
                $this->echeancierModel->delete($echeance['id']);
            }
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression de l'échéancier : " . $e->getMessage());
        }
    }
    
    /**
     * Enregistre un paiement sur une ou plusieurs échéances
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @param float $montantPaiement Montant du paiement
     * @param int $factureId ID de la facture associée
     * @return array Résultat de l'enregistrement
     */
    public function enregistrerPaiement($eleveId, $anneeScolaireId, $montantPaiement, $factureId = null) {
        try {
            // Récupérer les échéances non payées, triées par date
            $echeances = $this->echeancierModel->query(
                "SELECT * FROM echeanciers_ecolages 
                 WHERE eleve_id = ? AND annee_scolaire_id = ? AND montant_restant > 0
                 ORDER BY annee ASC, mois ASC",
                [$eleveId, $anneeScolaireId]
            );
            
            if (empty($echeances)) {
                throw new Exception("Aucune échéance impayée trouvée");
            }
            
            $montantRestant = $montantPaiement;
            $echeancesPayees = [];
            
            // Répartir le paiement sur les échéances
            foreach ($echeances as $echeance) {
                if ($montantRestant <= 0) {
                    break;
                }
                
                $montantAAppliquer = min($montantRestant, $echeance['montant_restant']);
                
                $nouveauMontantPaye = $echeance['montant_paye'] + $montantAAppliquer;
                $nouveauMontantRestant = $echeance['montant_du'] - $nouveauMontantPaye;
                
                $updateData = [
                    'montant_paye' => $nouveauMontantPaye,
                    'montant_restant' => $nouveauMontantRestant,
                    'nombre_paiements' => $echeance['nombre_paiements'] + 1
                ];
                
                // Si totalement payé
                if ($nouveauMontantRestant <= 0) {
                    $updateData['statut'] = 'paye';
                    $updateData['date_paiement_complet'] = date('Y-m-d');
                    $updateData['jours_retard'] = 0;
                }
                
                // Associer la facture si fournie
                if ($factureId) {
                    $updateData['derniere_facture_id'] = $factureId;
                }
                
                $this->echeancierModel->update($echeance['id'], $updateData);
                
                $echeancesPayees[] = [
                    'echeance_id' => $echeance['id'],
                    'mois_libelle' => $echeance['mois_libelle'],
                    'montant_applique' => $montantAAppliquer
                ];
                
                $montantRestant -= $montantAAppliquer;
            }
            
            return [
                'success' => true,
                'message' => 'Paiement enregistré avec succès',
                'montant_paye' => $montantPaiement,
                'montant_non_affecte' => $montantRestant,
                'echeances_payees' => $echeancesPayees
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
