<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Parent (Tuteur)
 */

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

    /**
     * Trouve un parent par son numéro de téléphone
     */
    public function getByTelephone($telephone) {
        return $this->queryOne(
            "SELECT id FROM {$this->table} WHERE telephone = ? LIMIT 1",
            [$telephone]
        );
    }

    /**
     * Crée un lien entre un parent et un élève
     */
    public function linkToEleve($parentId, $eleveId, $lienParente = 'pere') {
        return $this->query(
            "INSERT INTO eleves_parents (eleve_id, parent_id, lien_parente) VALUES (?, ?, ?)",
            [$eleveId, $parentId, $lienParente]
        );
    }
}

