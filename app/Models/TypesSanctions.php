<?php
/**
 * Modèle TypesSanctions
 * Correspond à la table 'types_sanctions'
 */

require_once __DIR__ . '/BaseModel.php';

class TypesSanctions extends BaseModel {
    protected $table = 'types_sanctions';
    protected $fillable = [
        'code', 'libelle', 'gravite', 'description', 'actif'
    ];
    
    /**
     * Récupère tous les types de sanctions actifs
     */
    public function getActifs() {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE actif = 1 
             ORDER BY gravite ASC, libelle ASC"
        );
    }
    
    /**
     * Récupère un type de sanction par son code
     */
    public function getByCode($code) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE code = ?",
            [$code]
        );
    }
    
    /**
     * Récupère les types de sanctions par niveau de gravité
     */
    public function getByGravite($gravite) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE gravite = ? AND actif = 1 
             ORDER BY libelle ASC",
            [$gravite]
        );
    }
}
