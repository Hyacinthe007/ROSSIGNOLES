<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle DocumentPersonnel
 * Gère les documents et pièces justificatives du personnel
 */
class DocumentPersonnel extends BaseModel {
    protected $table = 'documents_personnel';
    protected $fillable = [
        'personnel_id',
        'type_document',
        'libelle',
        'nom_fichier',
        'chemin_fichier',
        'taille_fichier',
        'type_mime',
        'numero_document',
        'date_emission',
        'date_expiration',
        'lieu_emission',
        'nom_enfant',
        'remarques',
        'statut',
        'valide_par',
        'date_validation',
        'motif_refus',
        'telecharge_par'
    ];

    /**
     * Récupère tous les documents d'un membre du personnel
     */
    public function getByPersonnel(int $personnelId) {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE personnel_id = ? ORDER BY created_at DESC",
            [$personnelId]
        );
    }

    /**
     * Récupère un document spécifique par son type pour un membre du personnel
     */
    public function getByType(int $personnelId, string $type) {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE personnel_id = ? AND type_document = ?",
            [$personnelId, $type]
        );
    }

    /**
     * Récupère les documents en attente de validation
     */
    public function getPending() {
        return $this->query(
            "SELECT d.*, p.nom, p.prenom 
             FROM {$this->table} d
             INNER JOIN personnels p ON d.personnel_id = p.id
             WHERE d.statut = 'en_attente'
             ORDER BY d.created_at ASC"
        );
    }
}
