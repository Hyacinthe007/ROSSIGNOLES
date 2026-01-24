<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Relance;
use App\Models\Eleve;
use App\Models\Facture;
use App\Models\BaseModel;
use Exception;
use PDOException;

/**
 * Service de relances
 */

class RelanceService {
    private $relanceModel;
    private $notificationService;
    
    public function __construct() {
        $this->relanceModel = new Relance();
        $this->notificationService = new NotificationService();
    }
    
    /**
     * Envoie une relance (SMS ou Email)
     */
    public function envoyerRelance($factureId, $eleveId, $canal, $message, $userId = null) {
        // Récupérer les informations de l'élève et de la facture
        $eleveModel = new Eleve();
        $factureModel = new Facture();
        
        $eleve = $eleveModel->find($eleveId);
        $facture = $factureModel->find($factureId);

        if (!$eleve || !$facture) {
            throw new Exception("Élève ou facture non trouvé");
        }
        
        $reste = $facture['montant_restant'] ?? 0;
        
        // Préparer le message
        $messageComplet = $message ?: $this->genererMessageRelance($eleve, $facture, $reste);
        
        // Créer l'enregistrement de relance (table relances basée sur les factures)
        $data = [
            'facture_id' => $factureId,
            'type' => 'impaye',
            'date_relance' => date('Y-m-d'),
            'statut' => 'programmee',
            'message' => $messageComplet,
            'envoye_par' => $userId,
        ];
        
        $relanceId = $this->relanceModel->create($data);
        
        // Envoyer selon le canal
        try {
            if ($canal === 'email') {
                $this->envoyerEmail($eleve, $facture, $reste, $messageComplet);
            } elseif ($canal === 'sms') {
                $this->envoyerSMS($eleve, $facture, $reste, $messageComplet);
            }
            
            // Mettre à jour le statut et la date d'envoi effective
            $this->relanceModel->update($relanceId, [
                'statut' => 'envoyee',
                'envoye_le' => date('Y-m-d H:i:s'),
            ]);
            
            return $relanceId;
        } catch (Exception $e) {
            // En cas d'erreur, mettre à jour le statut
            $this->relanceModel->update($relanceId, [
                'statut' => 'echouee',
            ]);
            throw $e;
        }
    }
    
    /**
     * Génère un message de relance par défaut
     */
    private function genererMessageRelance($eleve, $facture, $reste) {
        $message = "Bonjour " . ($eleve['prenom'] ?? '') . ",\n\n";
        $message .= "Nous vous rappelons que vous avez un impayé de " . formatMoney($reste) . " ";
        $message .= "sur la facture " . ($facture['numero_facture'] ?? 'N/A') . ".\n\n";
        $message .= "Veuillez régulariser votre situation dans les plus brefs délais.\n\n";
        $message .= "Cordialement,\nL'administration";
        
        return $message;
    }
    
    /**
     * Envoie un email de relance
     */
    private function envoyerEmail($eleve, $facture, $reste, $message) {
        if (empty($eleve['email'])) {
            throw new Exception("L'élève n'a pas d'adresse email");
        }
        
        // Utiliser le service de notifications pour l'envoi
        $this->notificationService->send(
            null,
            'email',
            'Rappel de paiement - Impayé',
            $message,
            ['type' => 'relance', 'facture_id' => $facture['id']]
        );
    }
    
    /**
     * Envoie un SMS de relance
     */
    private function envoyerSMS($eleve, $facture, $reste, $message) {
        if (empty($eleve['telephone'])) {
            throw new Exception("L'élève n'a pas de numéro de téléphone");
        }
        
        // Utiliser le service de notifications pour l'envoi
        $this->notificationService->send(
            null,
            'sms',
            'Rappel de paiement',
            $message,
            ['type' => 'relance', 'facture_id' => $facture['id']]
        );
    }
    
    /**
     * Obtient les statistiques des relances
     */
    public function getStats($anneeScolaireId = null) {
        $db = BaseModel::getDBConnection();
        
        if (!$anneeScolaireId) {
            try {
                $annee = $db->query("SELECT id FROM annees_scolaires WHERE actif = 1 LIMIT 1")->fetch();
                $anneeScolaireId = $annee ? $annee['id'] : null;
            } catch (PDOException $e) {
                $annee = $db->query("SELECT id FROM annees_scolaires ORDER BY date_debut DESC LIMIT 1")->fetch();
                $anneeScolaireId = $annee ? $annee['id'] : null;
            }
        }
        
        $stats = [
            'total_relances' => 0,
            'relances_envoyees' => 0,
            'relances_echec' => 0,
            'total_impayes' => 0,
            'montant_total_impaye' => 0
        ];
        
        try {
            // Statistiques des relances
            $relances = $this->relanceModel->query(
                "SELECT statut, COUNT(*) as total 
                 FROM relances 
                 GROUP BY statut"
            );
            
            foreach ($relances as $r) {
                $stats['total_relances'] += $r['total'];
                if ($r['statut'] === 'envoyee') {
                    $stats['relances_envoyees'] = $r['total'];
                } elseif ($r['statut'] === 'echec') {
                    $stats['relances_echec'] = $r['total'];
                }
            }
            
            // Statistiques des impayés (basées sur les factures)
            if ($anneeScolaireId) {
                $stmt = $db->prepare(
                    "SELECT COUNT(*) as total,
                            SUM(montant_restant) as montant_total
                     FROM factures
                     WHERE annee_scolaire_id = ?
                       AND montant_restant > 0
                       AND statut IN ('impayee', 'partiellement_payee')"
                );
                $stmt->execute([$anneeScolaireId]);
                $impayes = $stmt->fetch();
                
                $stats['total_impayes'] = $impayes['total'] ?? 0;
                $stats['montant_total_impaye'] = $impayes['montant_total'] ?? 0;
            }
        } catch (PDOException $e) {
            error_log("Erreur lors du calcul des statistiques: " . $e->getMessage());
        }
        
        return $stats;
    }
}

