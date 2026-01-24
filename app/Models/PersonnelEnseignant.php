<?php
/**
 * Modèle PersonnelEnseignant
 * Correspond à la table 'personnels_enseignants'
 */

require_once __DIR__ . '/BaseModel.php';

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
