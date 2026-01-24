<?php
/**
 * Modèle TypesDocuments
 * Correspond à la table 'types_documents'
 */

require_once __DIR__ . '/BaseModel.php';

class TypesDocuments extends BaseModel {
    protected $table = 'types_documents';
    protected $fillable = [
        'code', 'libelle', 'description', 'template_url', 'actif'
    ];
    
    /**
     * Récupère tous les types de documents actifs
     */
    public function getActifs() {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE actif = 1 
             ORDER BY libelle ASC"
        );
    }
    
    /**
     * Récupère un type de document par son code
     */
    public function getByCode($code) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE code = ?",
            [$code]
        );
    }
}
