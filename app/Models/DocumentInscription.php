<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle DocumentInscription
 * Gère les fichiers/documents liés à une inscription
 */

class DocumentInscription extends BaseModel {
    protected $table = 'documents_inscription';
    protected $fillable = [
        'inscription_id', 'eleve_id', 'type_document', 'ordre_affichage',
        'nom_fichier', 'chemin_fichier', 'taille_fichier', 'type_mime',
        'statut', 'obligatoire_pour_validation', 'valide_par', 'date_validation',
        'motif_refus', 'numero_document', 'date_emission', 'date_expiration',
        'lieu_emission', 'remarques', 'telecharge_par'
    ];
    
    /**
     * Récupère les documents d'une inscription
     */
    public function getByInscription($inscriptionId) {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE inscription_id = ? ORDER BY type_document",
            [$inscriptionId]
        );
    }
}
