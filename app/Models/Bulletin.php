<?php
/**
 * Modèle Bulletin
 * Gestion des bulletins scolaires avec journalisation automatique
 */

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../Helpers/Loggable.php';

class Bulletin extends BaseModel {
    use Loggable;
    
    protected $table = 'bulletins';
    protected $fillable = [
        'eleve_id', 'classe_id', 'periode_id', 'annee_scolaire_id', 
        'moyenne_generale', 'total_points', 'total_coefficients', 
        'rang', 'appreciation_generale', 'decision_conseil',
        'statut', 'date_validation', 'valide_par', 'peut_etre_imprime'
    ];
    
    /**
     * Crée un bulletin avec journalisation
     * @param array $data Données du bulletin
     * @return int|bool ID du bulletin créé ou false
     */
    public function create($data) {
        $bulletinId = parent::create($data);
        
        if ($bulletinId) {
            $this->logBulletinGeneration(
                $bulletinId,
                $data['eleve_id'],
                $data['periode_id'],
                $data['moyenne_generale'] ?? 0
            );
        }
        
        return $bulletinId;
    }
    
    /**
     * Met à jour un bulletin avec journalisation (CRITIQUE si déjà validé)
     * @param int $id ID du bulletin
     * @param array $newData Nouvelles données
     * @return bool Succès de l'opération
     */
    public function update($id, $newData) {
        // Récupérer l'ancien bulletin
        $oldData = $this->find($id);
        
        if (!$oldData) {
            return false;
        }
        
        $success = parent::update($id, $newData);
        
        if ($success) {
            // Logger la validation du bulletin
            if (isset($newData['statut']) && $newData['statut'] == 'valide' && $oldData['statut'] != 'valide') {
                $this->logValidate(
                    'bulletins',
                    'bulletin',
                    $id,
                    "Validation du bulletin - Élève #{$oldData['eleve_id']} - Période #{$oldData['periode_id']} - Moyenne: {$oldData['moyenne_generale']}"
                );
            }
            
            // ALERTE : Modification après validation (TRÈS CRITIQUE)
            if ($oldData['statut'] == 'valide' && isset($newData['moyenne_generale']) && $oldData['moyenne_generale'] != $newData['moyenne_generale']) {
                $this->logActivity(
                    'update_after_validation',
                    'bulletins',
                    "⚠️ ALERTE CRITIQUE: Modification de bulletin validé - Élève #{$oldData['eleve_id']} - Moyenne: {$oldData['moyenne_generale']} → {$newData['moyenne_generale']}",
                    'bulletin',
                    $id
                );
            }
            
            // Logger les changements généraux
            if (isset($newData['moyenne_generale']) || isset($newData['rang']) || isset($newData['decision_conseil'])) {
                $this->logUpdate('bulletins', 'bulletin', $id, $oldData, $newData);
            }
        }
        
        return $success;
    }
    
    /**
     * Supprime un bulletin avec journalisation (OPÉRATION CRITIQUE)
     * @param int $id ID du bulletin
     * @return bool Succès de l'opération
     */
    public function delete($id) {
        $bulletin = $this->find($id);
        
        if (!$bulletin) {
            return false;
        }
        
        // Interdire la suppression si le bulletin est validé
        if ($bulletin['statut'] == 'valide' || $bulletin['statut'] == 'imprime' || $bulletin['statut'] == 'envoye') {
            error_log("Tentative de suppression d'un bulletin validé (ID: {$id})");
            $this->logActivity(
                'delete_attempt_blocked',
                'bulletins',
                "⛔ TENTATIVE BLOQUÉE: Suppression d'un bulletin validé - Élève #{$bulletin['eleve_id']} - Statut: {$bulletin['statut']}",
                'bulletin',
                $id
            );
            return false;
        }
        
        $success = parent::delete($id);
        
        if ($success) {
            $this->logDelete(
                'bulletins',
                'bulletin',
                $id,
                [
                    'eleve_id' => $bulletin['eleve_id'],
                    'periode_id' => $bulletin['periode_id'],
                    'moyenne_generale' => $bulletin['moyenne_generale'],
                    'statut' => $bulletin['statut']
                ]
            );
        }
        
        return $success;
    }
    
    /**
     * Obtient un bulletin avec les détails
     * @param int $id ID du bulletin
     * @return array|null Détails du bulletin
     */
    public function getDetails($id) {
        return $this->queryOne(
            "SELECT b.*, 
                    e.nom as eleve_nom, e.prenom as eleve_prenom, e.matricule,
                    p.nom as periode_nom, p.numero as periode_numero, p.date_debut, p.date_fin,
                    c.nom as classe_nom, c.code as classe_code,
                    u.username as valide_par_nom
             FROM {$this->table} b
             INNER JOIN eleves e ON b.eleve_id = e.id
             INNER JOIN periodes p ON b.periode_id = p.id
             INNER JOIN classes c ON b.classe_id = c.id
             LEFT JOIN users u ON b.valide_par = u.id
             WHERE b.id = ?",
            [$id]
        );
    }
    
    /**
     * Obtient les notes (matières) du bulletin
     * @param int $bulletinId ID du bulletin
     * @return array Liste des notes par matière
     */
    public function getMatieres($bulletinId) {
        return $this->query(
            "SELECT bn.*, m.nom as matiere_nom, m.code as matiere_code
             FROM bulletins_notes bn
             INNER JOIN matieres m ON bn.matiere_id = m.id
             WHERE bn.bulletin_id = ?
             ORDER BY m.nom",
            [$bulletinId]
        );
    }
    
    /**
     * Obtient les bulletins d'un élève pour une année
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Liste des bulletins
     */
    public function getByEleve($eleveId, $anneeScolaireId) {
        return $this->query(
            "SELECT b.*, p.nom as periode_nom, p.numero as periode_numero
             FROM {$this->table} b
             INNER JOIN periodes p ON b.periode_id = p.id
             WHERE b.eleve_id = ? AND b.annee_scolaire_id = ?
             ORDER BY p.numero ASC",
            [$eleveId, $anneeScolaireId]
        );
    }
    
    /**
     * Obtient les bulletins d'une classe pour une période
     * @param int $classeId ID de la classe
     * @param int $periodeId ID de la période
     * @return array Liste des bulletins
     */
    public function getByClassePeriode($classeId, $periodeId) {
        return $this->query(
            "SELECT b.*, 
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom
             FROM {$this->table} b
             INNER JOIN eleves e ON b.eleve_id = e.id
             WHERE b.classe_id = ? AND b.periode_id = ?
             ORDER BY b.rang ASC, e.nom ASC",
            [$classeId, $periodeId]
        );
    }
    
    /**
     * Vérifie si un bulletin existe pour un élève et une période
     * @param int $eleveId ID de l'élève
     * @param int $periodeId ID de la période
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return bool True si le bulletin existe
     */
    public function exists($eleveId, $periodeId, $anneeScolaireId) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count 
             FROM {$this->table} 
             WHERE eleve_id = ? AND periode_id = ? AND annee_scolaire_id = ?",
            [$eleveId, $periodeId, $anneeScolaireId]
        );
        return $result && $result['count'] > 0;
    }
}
