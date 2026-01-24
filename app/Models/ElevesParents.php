<?php
/**
 * Modèle ElevesParents
 * Table: eleves_parents
 * Gère la relation entre élèves et parents/tuteurs
 */

require_once __DIR__ . '/BaseModel.php';

class ElevesParents extends BaseModel {
    protected $table = 'eleves_parents';
    protected $fillable = [
        'eleve_id', 'parent_id', 'lien_parente'
    ];
    
    /**
     * Récupère tous les parents d'un élève
     */
    public function getParentsEleve($eleveId) {
        return $this->query(
            "SELECT ep.*, 
                    p.nom, p.prenom, p.telephone, p.email, p.adresse
             FROM {$this->table} ep
             JOIN parents p ON ep.parent_id = p.id
             WHERE ep.eleve_id = ?
             ORDER BY 
                FIELD(ep.lien_parente, 'pere', 'mere', 'tuteur', 'grand_parent', 'oncle', 'tante', 'autre')",
            [$eleveId]
        );
    }
    
    /**
     * Récupère tous les enfants d'un parent
     */
    public function getEnfantsParent($parentId) {
        return $this->query(
            "SELECT ep.*, 
                    e.matricule, e.nom, e.prenom, e.date_naissance, 
                    e.sexe, e.statut, e.photo
             FROM {$this->table} ep
             JOIN eleves e ON ep.eleve_id = e.id
             WHERE ep.parent_id = ?
             ORDER BY e.nom ASC, e.prenom ASC",
            [$parentId]
        );
    }
    
    /**
     * Récupère le parent principal (père ou mère en priorité)
     */
    public function getParentPrincipal($eleveId) {
        return $this->queryOne(
            "SELECT ep.*, 
                    p.nom, p.prenom, p.telephone, p.email, p.adresse
             FROM {$this->table} ep
             JOIN parents p ON ep.parent_id = p.id
             WHERE ep.eleve_id = ?
             ORDER BY 
                FIELD(ep.lien_parente, 'pere', 'mere', 'tuteur', 'grand_parent', 'oncle', 'tante', 'autre')
             LIMIT 1",
            [$eleveId]
        );
    }
    
    /**
     * Vérifie si un lien existe déjà
     */
    public function lienExiste($eleveId, $parentId) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count FROM {$this->table}
             WHERE eleve_id = ? AND parent_id = ?",
            [$eleveId, $parentId]
        );
        return $result && $result['count'] > 0;
    }
    
    /**
     * Récupère le contact d'urgence d'un élève
     */
    public function getContactUrgence($eleveId) {
        // Priorité: père, mère, tuteur, autres
        return $this->queryOne(
            "SELECT p.nom, p.prenom, p.telephone, p.email, ep.lien_parente
             FROM {$this->table} ep
             JOIN parents p ON ep.parent_id = p.id
             WHERE ep.eleve_id = ? AND p.telephone IS NOT NULL
             ORDER BY 
                FIELD(ep.lien_parente, 'pere', 'mere', 'tuteur', 'grand_parent', 'oncle', 'tante', 'autre')
             LIMIT 1",
            [$eleveId]
        );
    }
}
