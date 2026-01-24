<?php
declare(strict_types=1);

namespace App\Models;

class PassageConditionnel extends BaseModel {
    protected $table = 'passages_conditionnels';
    protected $fillable = [
        'parcours_id',
        'eleve_id',
        'annee_scolaire_id',
        'type_condition',
        'matiere_id',
        'description_condition',
        'note_minimale_requise',
        'delai_limite',
        'statut',
        'note_obtenue',
        'date_evaluation',
    ];
    
    public function getDetails($id) {
        return $this->queryOne(
            "SELECT 
                pc.*,
                e.matricule,
                e.nom  AS eleve_nom,
                e.prenom AS eleve_prenom,
                a.libelle AS annee_libelle,
                m.nom AS matiere_nom,
                pe.classe_id,
                c.nom AS classe_nom
             FROM {$this->table} pc
             JOIN eleves e ON pc.eleve_id = e.id
             JOIN annees_scolaires a ON pc.annee_scolaire_id = a.id
             LEFT JOIN matieres m ON pc.matiere_id = m.id
             LEFT JOIN parcours_eleves pe ON pc.parcours_id = pe.id
             LEFT JOIN classes c ON pe.classe_id = c.id
             WHERE pc.id = ?",
            [$id]
        );
    }
}
