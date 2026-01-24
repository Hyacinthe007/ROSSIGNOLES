<?php
/**
 * Modèle ExigenceDocumentInscription
 * Définit quels documents sont requis par année et type d'inscription
 */

require_once __DIR__ . '/BaseModel.php';

class ExigenceDocumentInscription extends BaseModel {
    protected $table = 'exigences_documents_inscription';
    protected $fillable = [
        'annee_scolaire_id', 'type_inscription', 'type_document', 
        'obligatoire', 'bloquant', 'libelle', 'description', 
        'format_accepte', 'taille_max_mo', 'validite_jours', 
        'nombre_exemplaires', 'instructions', 'message_aide', 
        'exemple_url', 'ordre', 'actif'
    ];
    
    public function getByAnneeAndType($anneeId, $typeInscription) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE annee_scolaire_id = ? AND type_inscription = ? AND actif = 1
             ORDER BY ordre",
            [$anneeId, $typeInscription]
        );
    }
}
