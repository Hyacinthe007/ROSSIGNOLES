<?php
/**
 * Service EcolageService
 * Gère les exclusions automatiques pour non-paiement
 */

require_once __DIR__ . '/../Models/EcheancierEcolage.php';
require_once __DIR__ . '/../Models/Eleve.php';
require_once __DIR__ . '/../Models/Inscription.php';
require_once __DIR__ . '/../Services/NotificationService.php';

class EcolageService {
    
    private $echeancierModel;
    private $eleveModel;
    private $inscriptionModel;
    private $notificationService;
    
    public function __construct() {
        $this->echeancierModel = new EcheancierEcolage();
        $this->eleveModel = new Eleve();
        $this->inscriptionModel = new Inscription();
        $this->notificationService = new NotificationService();
    }
    
    /**
     * Vérifie et applique les exclusions automatiques
     * À exécuter quotidiennement via CRON
     * 
     * @param int $anneeScolaireId ID de l'année scolaire (optionnel)
     * @return array Résultat de la vérification
     */
    public function verifierExclusions($anneeScolaireId = null) {
        try {
            $dateActuelle = date('Y-m-d');
            $elevesExclus = [];
            $elevesAvertis = [];
            $elevesRelances = [];
            
            // Récupérer toutes les échéances dépassant la date d'exclusion
            $sql = "SELECT DISTINCT 
                        ee.eleve_id,
                        e.matricule,
                        e.nom,
                        e.prenom,
                        i.id as inscription_id,
                        i.classe_id,
                        c.nom as classe_nom,
                        COUNT(ee.id) as nb_echeances_retard,
                        SUM(ee.montant_restant) as total_impaye,
                        MIN(ee.date_exclusion) as premiere_date_exclusion,
                        MAX(ee.date_exclusion) as derniere_date_exclusion
                    FROM echeanciers_ecolages ee
                    INNER JOIN eleves e ON ee.eleve_id = e.id
                    INNER JOIN inscriptions i ON (ee.eleve_id = i.eleve_id AND ee.annee_scolaire_id = i.annee_scolaire_id)
                    INNER JOIN classes c ON i.classe_id = c.id
                    WHERE ee.date_exclusion < ? 
                    AND ee.montant_restant > 0
                    AND ee.statut != 'paye'
                    AND i.statut = 'validee'
                    AND i.bloquee = 0";
            
            $params = [$dateActuelle];
            
            if ($anneeScolaireId) {
                $sql .= " AND ee.annee_scolaire_id = ?";
                $params[] = $anneeScolaireId;
            }
            
            $sql .= " GROUP BY ee.eleve_id, e.matricule, e.nom, e.prenom, i.id, i.classe_id, c.nom
                      HAVING total_impaye > 0
                      ORDER BY nb_echeances_retard DESC, total_impaye DESC";
            
            $elevesEnRetard = $this->echeancierModel->query($sql, $params);
            
            foreach ($elevesEnRetard as $eleve) {
                // Calculer le nombre de jours de retard depuis la première date d'exclusion
                $joursRetard = (strtotime($dateActuelle) - strtotime($eleve['premiere_date_exclusion'])) / 86400;
                
                // Politique d'exclusion progressive
                if ($joursRetard >= 15) {
                    // Exclusion définitive après 15 jours
                    $this->appliquerExclusion($eleve, 'definitive');
                    $elevesExclus[] = $eleve;
                    
                } elseif ($joursRetard >= 7) {
                    // Exclusion temporaire après 7 jours
                    $this->appliquerExclusion($eleve, 'temporaire');
                    $elevesExclus[] = $eleve;
                    
                } elseif ($joursRetard >= 3) {
                    // Avertissement après 3 jours
                    $this->envoyerAvertissement($eleve);
                    $elevesAvertis[] = $eleve;
                    
                } else {
                    // Simple relance
                    $this->envoyerRelance($eleve);
                    $elevesRelances[] = $eleve;
                }
                
                // Mettre à jour le statut des échéances
                $this->mettreAJourStatutEcheances($eleve['eleve_id'], $eleve['inscription_id']);
            }
            
            // Générer le rapport
            $rapport = $this->genererRapport($elevesExclus, $elevesAvertis, $elevesRelances);
            
            // Envoyer notification aux administrateurs
            $this->notifierAdministrateurs($rapport);
            
            return [
                'success' => true,
                'date_verification' => $dateActuelle,
                'nb_exclusions' => count($elevesExclus),
                'nb_avertissements' => count($elevesAvertis),
                'nb_relances' => count($elevesRelances),
                'eleves_exclus' => $elevesExclus,
                'eleves_avertis' => $elevesAvertis,
                'eleves_relances' => $elevesRelances,
                'rapport' => $rapport
            ];
            
        } catch (Exception $e) {
            error_log("Erreur vérification exclusions : " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Applique l'exclusion à un élève
     * 
     * @param array $eleve Données de l'élève
     * @param string $type Type d'exclusion (temporaire|definitive)
     */
    private function appliquerExclusion($eleve, $type = 'temporaire') {
        try {
            // Bloquer l'inscription
            $this->inscriptionModel->update($eleve['inscription_id'], [
                'bloquee' => 1,
                'motif_blocage' => $type === 'definitive' 
                    ? 'Exclusion définitive pour non-paiement' 
                    : 'Exclusion temporaire pour non-paiement',
                'date_blocage' => date('Y-m-d H:i:s')
            ]);
            
            // Mettre à jour le statut de l'élève
            $this->eleveModel->update($eleve['eleve_id'], [
                'statut' => $type === 'definitive' ? 'exclu_definitif' : 'exclu_temporaire'
            ]);
            
            // Enregistrer dans les logs
            $this->enregistrerLog([
                'type' => 'exclusion_' . $type,
                'eleve_id' => $eleve['eleve_id'],
                'inscription_id' => $eleve['inscription_id'],
                'montant_impaye' => $eleve['total_impaye'],
                'nb_echeances' => $eleve['nb_echeances_retard'],
                'date_action' => date('Y-m-d H:i:s')
            ]);
            
            // Notifier les parents
            $this->notifierParents($eleve, $type);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur application exclusion : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un avertissement à l'élève/parents
     */
    private function envoyerAvertissement($eleve) {
        $message = "AVERTISSEMENT - Risque d'exclusion\n\n";
        $message .= "Élève : {$eleve['nom']} {$eleve['prenom']} ({$eleve['matricule']})\n";
        $message .= "Classe : {$eleve['classe_nom']}\n\n";
        $message .= "Montant impayé : " . number_format($eleve['total_impaye'], 0, ',', ' ') . " FCFA\n";
        $message .= "Nombre d'échéances en retard : {$eleve['nb_echeances_retard']}\n\n";
        $message .= "⚠️ ATTENTION : Sans régularisation sous 4 jours, l'élève sera exclu temporairement.\n\n";
        $message .= "Veuillez contacter le service financier de l'établissement.";
        
        // TODO: Implémenter envoyerNotification dans NotificationService
        // $this->notificationService->envoyerNotification([
        //     'destinataire_type' => 'parents',
        //     'eleve_id' => $eleve['eleve_id'],
        //     'titre' => '⚠️ Avertissement - Risque d\'exclusion',
        //     'message' => $message,
        //     'priorite' => 'haute',
        //     'type' => 'avertissement_exclusion'
        // ]);
        
        $this->enregistrerLog([
            'type' => 'avertissement_envoye',
            'eleve_id' => $eleve['eleve_id'],
            'date_action' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Envoie une relance de paiement
     */
    private function envoyerRelance($eleve) {
        $message = "Rappel de paiement\n\n";
        $message .= "Élève : {$eleve['nom']} {$eleve['prenom']} ({$eleve['matricule']})\n";
        $message .= "Classe : {$eleve['classe_nom']}\n\n";
        $message .= "Montant impayé : " . number_format($eleve['total_impaye'], 0, ',', ' ') . " FCFA\n";
        $message .= "Nombre d'échéances en retard : {$eleve['nb_echeances_retard']}\n\n";
        $message .= "Merci de régulariser votre situation dans les plus brefs délais.";
        
        // TODO: Implémenter envoyerNotification dans NotificationService
        // $this->notificationService->envoyerNotification([
        //     'destinataire_type' => 'parents',
        //     'eleve_id' => $eleve['eleve_id'],
        //     'titre' => 'Rappel de paiement',
        //     'message' => $message,
        //     'priorite' => 'normale',
        //     'type' => 'relance_paiement'
        // ]);
    }
    
    /**
     * Notifie les parents d'une exclusion
     */
    private function notifierParents($eleve, $type) {
        $titre = $type === 'definitive' 
            ? '🔴 EXCLUSION DÉFINITIVE' 
            : '⚠️ EXCLUSION TEMPORAIRE';
        
        $message = "Nous vous informons que l'élève {$eleve['nom']} {$eleve['prenom']} ";
        $message .= "a été exclu" . ($type === 'temporaire' ? ' temporairement' : ' définitivement') . " ";
        $message .= "de l'établissement pour non-paiement des frais de scolarité.\n\n";
        $message .= "Détails :\n";
        $message .= "- Montant impayé : " . number_format($eleve['total_impaye'], 0, ',', ' ') . " FCFA\n";
        $message .= "- Échéances en retard : {$eleve['nb_echeances_retard']}\n\n";
        
        if ($type === 'temporaire') {
            $message .= "L'élève pourra réintégrer l'établissement dès régularisation de sa situation.\n\n";
        }
        
        $message .= "Veuillez contacter le service financier pour plus d'informations.";
        
        // TODO: Implémenter envoyerNotification dans NotificationService
        // $this->notificationService->envoyerNotification([
        //     'destinataire_type' => 'parents',
        //     'eleve_id' => $eleve['eleve_id'],
        //     'titre' => $titre,
        //     'message' => $message,
        //     'priorite' => 'urgente',
        //     'type' => 'notification_exclusion'
        // ]);
    }
    
    /**
     * Met à jour le statut des échéances d'un élève
     */
    private function mettreAJourStatutEcheances($eleveId, $inscriptionId) {
        $dateActuelle = date('Y-m-d');
        
        // Mettre à jour toutes les échéances impayées
        $sql = "UPDATE echeanciers_ecolages 
                SET statut = CASE 
                    WHEN date_exclusion < ? AND montant_restant > 0 THEN 'exclusion'
                    WHEN date_limite_grace < ? AND montant_restant > 0 THEN 'retard_grave'
                    WHEN date_limite_normale < ? AND montant_restant > 0 THEN 'retard'
                    ELSE statut
                END,
                jours_retard = CASE 
                    WHEN date_exclusion < ? THEN DATEDIFF(?, date_exclusion)
                    WHEN date_limite_grace < ? THEN DATEDIFF(?, date_limite_grace)
                    WHEN date_limite_normale < ? THEN DATEDIFF(?, date_limite_normale)
                    ELSE 0
                END
                WHERE eleve_id = ? AND montant_restant > 0";
        
        $this->echeancierModel->query($sql, [
            $dateActuelle, $dateActuelle, $dateActuelle,
            $dateActuelle, $dateActuelle,
            $dateActuelle, $dateActuelle,
            $dateActuelle, $dateActuelle,
            $eleveId
        ]);
    }
    
    /**
     * Génère un rapport de vérification
     */
    private function genererRapport($exclus, $avertis, $relances) {
        $rapport = "=== RAPPORT DE VÉRIFICATION DES EXCLUSIONS ===\n";
        $rapport .= "Date : " . date('d/m/Y H:i:s') . "\n\n";
        
        $rapport .= "📊 STATISTIQUES :\n";
        $rapport .= "- Exclusions appliquées : " . count($exclus) . "\n";
        $rapport .= "- Avertissements envoyés : " . count($avertis) . "\n";
        $rapport .= "- Relances envoyées : " . count($relances) . "\n\n";
        
        if (!empty($exclus)) {
            $rapport .= "🔴 ÉLÈVES EXCLUS :\n";
            foreach ($exclus as $e) {
                $rapport .= "- {$e['nom']} {$e['prenom']} ({$e['matricule']}) - {$e['classe_nom']}\n";
                $rapport .= "  Impayé : " . number_format($e['total_impaye'], 0, ',', ' ') . " FCFA\n";
            }
            $rapport .= "\n";
        }
        
        if (!empty($avertis)) {
            $rapport .= "⚠️ AVERTISSEMENTS :\n";
            foreach ($avertis as $e) {
                $rapport .= "- {$e['nom']} {$e['prenom']} ({$e['matricule']}) - {$e['classe_nom']}\n";
            }
            $rapport .= "\n";
        }
        
        return $rapport;
    }
    
    /**
     * Notifie les administrateurs
     */
    private function notifierAdministrateurs($rapport) {
        // TODO: Implémenter envoyerNotification dans NotificationService
        // $this->notificationService->envoyerNotification([
        //     'destinataire_type' => 'administrateurs',
        //     'titre' => '📋 Rapport quotidien - Exclusions automatiques',
        //     'message' => $rapport,
        //     'priorite' => 'normale',
        //     'type' => 'rapport_exclusions'
        // ]);
    }
    
    /**
     * Enregistre une action dans les logs
     */
    private function enregistrerLog($data) {
        try {
            require_once __DIR__ . '/../Models/LogActivite.php';
            
            LogActivite::log(
                $data['type'], 
                'Finance', 
                "Action automatique sur l'élève #" . $data['eleve_id'],
                'eleves',
                $data['eleve_id']
            );
        } catch (Exception $e) {
            error_log("Erreur enregistrement log : " . $e->getMessage());
        }
    }
    
    /**
     * Débloquer un élève après paiement
     */
    public function debloquerEleve($eleveId, $inscriptionId) {
        try {
            // Vérifier si toutes les échéances sont payées
            $echeancesImpayees = $this->echeancierModel->query(
                "SELECT COUNT(*) as count FROM echeanciers_ecolages 
                 WHERE eleve_id = ? AND montant_restant > 0",
                [$eleveId]
            );
            
            if ($echeancesImpayees[0]['count'] == 0) {
                // Débloquer l'inscription
                $this->inscriptionModel->update($inscriptionId, [
                    'bloquee' => 0,
                    'motif_blocage' => null,
                    'date_deblocage' => date('Y-m-d H:i:s')
                ]);
                
                // Réactiver l'élève
                $this->eleveModel->update($eleveId, [
                    'statut' => 'actif'
                ]);
                
                // Notifier
                // TODO: Implémenter envoyerNotification dans NotificationService
                // $this->notificationService->envoyerNotification([
                //     'destinataire_type' => 'parents',
                //     'eleve_id' => $eleveId,
                //     'titre' => '✅ Réintégration confirmée',
                //     'message' => 'Suite à la régularisation de votre situation, l\'élève est réintégré.',
                //     'priorite' => 'normale',
                //     'type' => 'deblocage'
                // ]);
                
                return ['success' => true, 'message' => 'Élève débloqué avec succès'];
            }
            
            return ['success' => false, 'message' => 'Des échéances restent impayées'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
