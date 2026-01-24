<?php
/**
 * Modèle PostesAdministratifs
 * Correspond à la table 'postes_administratifs'
 */

require_once __DIR__ . '/BaseModel.php';

class PostesAdministratifs extends BaseModel {
    protected $table = 'postes_administratifs';
    protected $fillable = [
        'code', 'libelle', 'description', 'departement',
        'niveau_hierarchique', 'actif'
    ];
    
    /**
     * Récupère tous les postes actifs
     */
    public function getActifs() {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE actif = 1 
             ORDER BY niveau_hierarchique DESC, libelle ASC"
        );
    }
    
    /**
     * Récupère les postes par département
     */
    public function getByDepartement($departement) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE departement = ? AND actif = 1 
             ORDER BY niveau_hierarchique DESC, libelle ASC",
            [$departement]
        );
    }
    
    /**
     * Récupère un poste par son code
     */
    public function getByCode($code) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE code = ?",
            [$code]
        );
    }
}
