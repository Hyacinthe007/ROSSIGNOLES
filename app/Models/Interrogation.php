<?php
/**
 * Modèle Interrogation
 */

require_once __DIR__ . '/BaseModel.php';

class Interrogation extends BaseModel {
    protected $table = 'interrogations';
    protected $fillable = [
        'classe_id', 'matiere_id', 'enseignant_id', 'periode_id', 'annee_scolaire_id',
        'nom', 'date_interrogation', 'duree', 'note_sur', 'description', 'statut'
    ];
    
    /**
     * Obtient les notes de cette interrogation
     */
    public function getNotes() {
        return $this->query(
            "SELECT ni.*, e.nom, e.prenom, e.matricule
             FROM notes_interrogations ni
             INNER JOIN eleves e ON ni.eleve_id = e.id
             WHERE ni.interrogation_id = ?
             ORDER BY e.nom, e.prenom",
            [$this->id]
        );
    }
}
