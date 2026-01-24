<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle PersonnelEnseignant
 */

class PersonnelEnseignant extends BaseModel {
    protected $table = 'personnels_enseignants';
    protected $fillable = [
        'personnel_id', 'diplome', 'specialite', 'matieres_enseignees', 
        'grade', 'anciennete_annees', 'charge_horaire_hebdo', 'charge_horaire_max'
    ];
    
    /**
     * Obtient les détails en joignant avec la table personnels
     */
    public function getDetails($id) {
        return $this->queryOne(
            "SELECT pe.*, p.*
             FROM {$this->table} pe
             INNER JOIN personnels p ON pe.personnel_id = p.id
             WHERE pe.id = ?",
            [$id]
        );
    }
}
