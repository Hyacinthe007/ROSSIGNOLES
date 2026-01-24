<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle PersonnelAdministratif
 */

class PersonnelAdministratif extends BaseModel {
    protected $table = 'personnels_administratifs';
    protected $fillable = [
        'personnel_id', 'poste_id', 'departement', 'niveau_acces', 
        'responsable_id'
    ];
    
    /**
     * Obtient les détails en joignant avec la table personnels
     */
    public function getDetails($id) {
        return $this->queryOne(
            "SELECT pa.*, p.*, post.libelle as poste_libelle
             FROM {$this->table} pa
             INNER JOIN personnels p ON pa.personnel_id = p.id
             LEFT JOIN postes_administratifs post ON pa.poste_id = post.id
             WHERE pa.id = ?",
            [$id]
        );
    }
}
