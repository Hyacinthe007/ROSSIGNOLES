<?php
/**
 * Modèle Periode
 */

require_once __DIR__ . '/BaseModel.php';

class Periode extends BaseModel {
    protected $table = 'periodes';
    protected $fillable = [
        'annee_scolaire_id', 'nom', 'numero', 'date_debut', 'date_fin', 'actif'
    ];
    
    /**
     * Obtient la période active pour une date donnée
     */
    public function getActiveForDate($date) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} 
             WHERE ? BETWEEN date_debut AND date_fin AND actif = 1",
            [$date]
        );
    }
}
