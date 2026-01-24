<?php
/**
 * Modèle ExamenFinal
 */

require_once __DIR__ . '/BaseModel.php';

class ExamenFinal extends BaseModel {
    protected $table = 'examens_finaux';
    protected $fillable = [
        'classe_id', 'matiere_id', 'enseignant_id', 'periode_id', 'annee_scolaire_id',
        'nom', 'date_examen', 'heure_debut', 'heure_fin', 'duree', 'note_sur',
        'sujet_url', 'bareme_url', 'description', 'consignes', 'statut'
    ];
    
    /**
     * Obtient les notes de cet examen
     */
    public function getNotes() {
        return $this->query(
            "SELECT ne.*, e.nom, e.prenom, e.matricule
             FROM notes_examens ne
             INNER JOIN eleves e ON ne.eleve_id = e.id
             WHERE ne.examen_id = ?
             ORDER BY e.nom, e.prenom",
            [$this->id]
        );
    }
}
