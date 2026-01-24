<?php
/**
 * Modèle InscriptionsHistorique
 * Table: inscriptions_historique
 * Historise les changements de statut des inscriptions
 */

require_once __DIR__ . '/BaseModel.php';

class InscriptionsHistorique extends BaseModel {
    protected $table = 'inscriptions_historique';
    protected $fillable = [
        'inscription_id', 'ancien_statut', 'nouveau_statut', 
        'commentaire', 'modifie_par'
    ];
    
    /**
     * Enregistre un changement de statut
     */
    public function enregistrerChangement($inscriptionId, $ancienStatut, $nouveauStatut, $commentaire = null, $modifiePar = null) {
        return $this->create([
            'inscription_id' => $inscriptionId,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'commentaire' => $commentaire,
            'modifie_par' => $modifiePar
        ]);
    }
    
    /**
     * Récupère l'historique d'une inscription
     */
    public function getHistorique($inscriptionId) {
        return $this->query(
            "SELECT ih.*, 
                    u.username as modifie_par_username,
                    CONCAT(u.username, ' (', u.user_type, ')') as modifie_par_info
             FROM {$this->table} ih
             LEFT JOIN users u ON ih.modifie_par = u.id
             WHERE ih.inscription_id = ?
             ORDER BY ih.created_at DESC",
            [$inscriptionId]
        );
    }
    
    /**
     * Récupère le dernier changement d'une inscription
     */
    public function getDernierChangement($inscriptionId) {
        return $this->queryOne(
            "SELECT ih.*, 
                    u.username as modifie_par_username
             FROM {$this->table} ih
             LEFT JOIN users u ON ih.modifie_par = u.id
             WHERE ih.inscription_id = ?
             ORDER BY ih.created_at DESC
             LIMIT 1",
            [$inscriptionId]
        );
    }
    
    /**
     * Récupère les statistiques des changements de statut
     */
    public function getStatistiquesChangements($anneeScolaireId = null) {
        $sql = "SELECT 
                    ih.nouveau_statut,
                    COUNT(*) as nb_changements,
                    COUNT(DISTINCT ih.inscription_id) as nb_inscriptions
                FROM {$this->table} ih
                JOIN inscriptions i ON ih.inscription_id = i.id";
        
        $params = [];
        
        if ($anneeScolaireId) {
            $sql .= " WHERE i.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " GROUP BY ih.nouveau_statut
                  ORDER BY nb_changements DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Récupère l'historique complet avec détails de l'inscription
     */
    public function getHistoriqueComplet($inscriptionId) {
        return $this->query(
            "SELECT ih.*, 
                    u.username as modifie_par_username,
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                    i.type_inscription, i.statut_dossier as statut_actuel
             FROM {$this->table} ih
             LEFT JOIN users u ON ih.modifie_par = u.id
             JOIN inscriptions i ON ih.inscription_id = i.id
             JOIN eleves e ON i.eleve_id = e.id
             WHERE ih.inscription_id = ?
             ORDER BY ih.created_at DESC",
            [$inscriptionId]
        );
    }
}
