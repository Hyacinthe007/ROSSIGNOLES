<?php
declare(strict_types=1);

namespace App\Helpers;

use Exception;

/**
 * Trait Loggable
 * Fournit des méthodes de journalisation automatique pour les opérations critiques
 * 
 * Usage: use Loggable; dans les modèles ou contrôleurs
 */

trait Loggable {
    
    /**
     * Enregistre une activité dans les logs
     * @param string $action Action effectuée (create, update, delete, validate, etc.)
     * @param string $module Module concerné (notes, paiements, bulletins, etc.)
     * @param string $description Description détaillée de l'action
     * @param string|null $entiteType Type d'entité (eleve, facture, bulletin, etc.)
     * @param int|null $entiteId ID de l'entité concernée
     * @param int|null $userId ID de l'utilisateur (si null, utilise $_SESSION['user_id'])
     * @return bool Succès de l'enregistrement
     */
    protected function logActivity($action, $module, $description, $entiteType = null, $entiteId = null, $userId = null) {
        try {
            $logModel = new \App\Models\LogActivite();
            
            // Récupérer l'ID utilisateur depuis la session si non fourni
            if ($userId === null && isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            }
            
            // Récupérer les informations de la requête
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            
            $data = [
                'user_id' => $userId,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'entite_type' => $entiteType,
                'entite_id' => $entiteId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ];
            
            return $logModel->create($data);
            
        } catch (Exception $e) {
            // Ne pas bloquer l'opération si le log échoue
            error_log("Erreur lors de la journalisation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log une création d'entité
     * @param string $module Module concerné
     * @param string $entiteType Type d'entité
     * @param int $entiteId ID de l'entité créée
     * @param array $data Données créées (optionnel)
     * @return bool
     */
    protected function logCreate($module, $entiteType, $entiteId, $data = []) {
        $description = "Création de {$entiteType} #{$entiteId}";
        if (!empty($data)) {
            $description .= " - " . json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        return $this->logActivity('create', $module, $description, $entiteType, $entiteId);
    }
    
    /**
     * Log une modification d'entité
     * @param string $module Module concerné
     * @param string $entiteType Type d'entité
     * @param int $entiteId ID de l'entité modifiée
     * @param array $oldData Anciennes valeurs
     * @param array $newData Nouvelles valeurs
     * @return bool
     */
    protected function logUpdate($module, $entiteType, $entiteId, $oldData = [], $newData = []) {
        $changes = [];
        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $value
                ];
            }
        }
        
        $description = "Modification de {$entiteType} #{$entiteId}";
        if (!empty($changes)) {
            $description .= " - Changements: " . json_encode($changes, JSON_UNESCAPED_UNICODE);
        }
        
        return $this->logActivity('update', $module, $description, $entiteType, $entiteId);
    }
    
    /**
     * Log une suppression d'entité
     * @param string $module Module concerné
     * @param string $entiteType Type d'entité
     * @param int $entiteId ID de l'entité supprimée
     * @param array $data Données supprimées (optionnel)
     * @return bool
     */
    protected function logDelete($module, $entiteType, $entiteId, $data = []) {
        $description = "Suppression de {$entiteType} #{$entiteId}";
        if (!empty($data)) {
            $description .= " - Données: " . json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        return $this->logActivity('delete', $module, $description, $entiteType, $entiteId);
    }
    
    /**
     * Log une validation d'entité
     * @param string $module Module concerné
     * @param string $entiteType Type d'entité
     * @param int $entiteId ID de l'entité validée
     * @param string $commentaire Commentaire optionnel
     * @return bool
     */
    protected function logValidate($module, $entiteType, $entiteId, $commentaire = '') {
        $description = "Validation de {$entiteType} #{$entiteId}";
        if ($commentaire) {
            $description .= " - {$commentaire}";
        }
        return $this->logActivity('validate', $module, $description, $entiteType, $entiteId);
    }
    
    /**
     * Log un paiement
     * @param int $paiementId ID du paiement
     * @param int $factureId ID de la facture
     * @param float $montant Montant du paiement
     * @param string $modePaiement Mode de paiement
     * @return bool
     */
    protected function logPaiement($paiementId, $factureId, $montant, $modePaiement) {
        $description = "Paiement de {$montant} Ar pour la facture #{$factureId} - Mode: {$modePaiement}";
        return $this->logActivity('create', 'paiements', $description, 'paiement', $paiementId);
    }
    
    /**
     * Log une modification de note
     * @param string $typeNote Type de note (interrogation, examen)
     * @param int $noteId ID de la note
     * @param int $eleveId ID de l'élève
     * @param float $ancienneNote Ancienne note
     * @param float $nouvelleNote Nouvelle note
     * @return bool
     */
    protected function logNoteChange($typeNote, $noteId, $eleveId, $ancienneNote, $nouvelleNote) {
        $description = "Modification de note {$typeNote} pour l'élève #{$eleveId}: {$ancienneNote} → {$nouvelleNote}";
        return $this->logActivity('update', 'notes', $description, $typeNote, $noteId);
    }
    
    /**
     * Log une génération de bulletin
     * @param int $bulletinId ID du bulletin
     * @param int $eleveId ID de l'élève
     * @param int $periodeId ID de la période
     * @param float $moyenne Moyenne générale
     * @return bool
     */
    protected function logBulletinGeneration($bulletinId, $eleveId, $periodeId, $moyenne) {
        $description = "Génération du bulletin pour l'élève #{$eleveId} - Période #{$periodeId} - Moyenne: {$moyenne}";
        return $this->logActivity('create', 'bulletins', $description, 'bulletin', $bulletinId);
    }
    
    /**
     * Log une sanction
     * @param int $sanctionId ID de la sanction
     * @param int $eleveId ID de l'élève
     * @param string $typeSanction Type de sanction
     * @param string $motif Motif de la sanction
     * @return bool
     */
    protected function logSanction($sanctionId, $eleveId, $typeSanction, $motif) {
        $description = "Sanction '{$typeSanction}' pour l'élève #{$eleveId} - Motif: {$motif}";
        return $this->logActivity('create', 'discipline', $description, 'sanction', $sanctionId);
    }
    
    /**
     * Log un changement de statut d'inscription
     * @param int $inscriptionId ID de l'inscription
     * @param int $eleveId ID de l'élève
     * @param string $ancienStatut Ancien statut
     * @param string $nouveauStatut Nouveau statut
     * @return bool
     */
    protected function logInscriptionStatusChange($inscriptionId, $eleveId, $ancienStatut, $nouveauStatut) {
        $description = "Changement de statut d'inscription pour l'élève #{$eleveId}: {$ancienStatut} → {$nouveauStatut}";
        return $this->logActivity('update', 'inscriptions', $description, 'inscription', $inscriptionId);
    }
    
    /**
     * Log une exclusion d'élève pour impayé
     * @param int $eleveId ID de l'élève
     * @param int $echeanceId ID de l'échéance
     * @param float $montantDu Montant dû
     * @return bool
     */
    protected function logExclusionImpaye($eleveId, $echeanceId, $montantDu) {
        $description = "Exclusion de l'élève #{$eleveId} pour impayé - Échéance #{$echeanceId} - Montant dû: {$montantDu} Ar";
        return $this->logActivity('update', 'finance', $description, 'eleve', $eleveId);
    }
}
