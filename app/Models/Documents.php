<?php
/**
 * Modèle Documents
 * Correspond à la table 'documents'
 */

require_once __DIR__ . '/BaseModel.php';

class Documents extends BaseModel {
    protected $table = 'documents';
    protected $fillable = [
        'type_document_id', 'entite_type', 'entite_id', 'titre',
        'numero_document', 'fichier_url', 'date_emission', 'date_validite',
        'delivre_par', 'valide_par', 'date_validation', 'statut'
    ];
    
    /**
     * Récupère les documents d'une entité
     */
    public function getByEntite($entiteType, $entiteId) {
        return $this->query(
            "SELECT d.*, td.libelle as type_libelle, td.code as type_code,
                    u1.username as delivre_par_username,
                    u2.username as valide_par_username
             FROM {$this->table} d
             INNER JOIN types_documents td ON d.type_document_id = td.id
             LEFT JOIN users u1 ON d.delivre_par = u1.id
             LEFT JOIN users u2 ON d.valide_par = u2.id
             WHERE d.entite_type = ? AND d.entite_id = ?
             ORDER BY d.date_emission DESC",
            [$entiteType, $entiteId]
        );
    }
    
    /**
     * Récupère les documents par type
     */
    public function getByType($typeDocumentId, $statut = null) {
        $where = "d.type_document_id = ?";
        $params = [$typeDocumentId];
        
        if ($statut) {
            $where .= " AND d.statut = ?";
            $params[] = $statut;
        }
        
        return $this->query(
            "SELECT d.*, td.libelle as type_libelle
             FROM {$this->table} d
             INNER JOIN types_documents td ON d.type_document_id = td.id
             WHERE {$where}
             ORDER BY d.date_emission DESC",
            $params
        );
    }
    
    /**
     * Valide un document
     */
    public function valider($id, $userId) {
        return $this->update($id, [
            'statut' => 'valide',
            'valide_par' => $userId,
            'date_validation' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Annule un document
     */
    public function annuler($id) {
        return $this->update($id, [
            'statut' => 'annule'
        ]);
    }
    
    /**
     * Génère un numéro de document unique
     */
    public function genererNumero($typeCode, $annee = null) {
        if (!$annee) {
            $annee = date('Y');
        }
        
        // Compter les documents de ce type pour cette année
        $count = $this->queryOne(
            "SELECT COUNT(*) as total 
             FROM {$this->table} d
             INNER JOIN types_documents td ON d.type_document_id = td.id
             WHERE td.code = ? AND YEAR(d.date_emission) = ?",
            [$typeCode, $annee]
        );
        
        $numero = ($count['total'] ?? 0) + 1;
        return strtoupper($typeCode) . '-' . $annee . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
