<?php
/**
 * Modèle Matiere
 */

require_once __DIR__ . '/BaseModel.php';

class Matiere extends BaseModel {
    protected $table = 'matieres';
    protected $fillable = ['code', 'nom', 'description', 'couleur', 'actif'];
    
    /**
     * Obtient les matières par niveau (via les séries)
     */
    public function getByNiveau($niveauId) {
        return $this->query(
            "SELECT DISTINCT m.* 
             FROM {$this->table} m
             INNER JOIN matieres_series ms ON m.id = ms.matiere_id
             INNER JOIN series s ON ms.serie_id = s.id
             WHERE s.niveau_id = ? AND m.actif = 1 AND s.actif = 1",
            [$niveauId]
        );
    }
}

