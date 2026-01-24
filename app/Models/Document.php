<?php
require_once __DIR__ . '/BaseModel.php';

class Document extends BaseModel {
    protected $table = 'documents';
    protected $fillable = [
        'type_document_id', 'entite_type', 'entite_id', 'annee_scolaire_id',
        'titre', 'numero_document', 'fichier_url', 'date_emission', 'date_validite',
        'delivre_par', 'valide_par', 'date_validation', 'statut'
    ];
    
    /**
     * Récupère les documents liés à une entité (ex: 'eleve', 123)
     */
    public function getByEntite($entiteType, $entiteId) {
        return $this->query(
            "SELECT d.*, td.libelle as type_libelle, td.code as type_code, u.username as depose_par
             FROM {$this->table} d
             LEFT JOIN types_documents td ON d.type_document_id = td.id
             LEFT JOIN users u ON d.delivre_par = u.id
             WHERE d.entite_type = ? AND d.entite_id = ?
             ORDER BY d.created_at DESC",
            [$entiteType, $entiteId]
        );
    }
}
