<?php
/**
 * Modèle Parent (Tuteur)
 */

require_once __DIR__ . '/BaseModel.php';

class ParentModel extends BaseModel {
    protected $table = 'parents';
    protected $fillable = [
        'nom', 'prenom', 'telephone', 'email', 'adresse', 'profession', 'sexe', 'type_parent'
    ];
    
    /**
     * Obtient les enfants d'un parent
     */
    public function getEnfants($parentId) {
        return $this->query(
            "SELECT e.*, ep.lien_parente
             FROM eleves e
             INNER JOIN eleves_parents ep ON e.id = ep.eleve_id
             WHERE ep.parent_id = ?",
            [$parentId]
        );
    }
}

