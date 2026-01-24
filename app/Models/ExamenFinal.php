<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle ExamenFinal
 */

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
    
    /**
     * Récupère les examens d'une classe pour une période donnée
     * @param int $classeId ID de la classe
     * @param int $periodeId ID de la période
     * @return array Liste des examens avec nom de matière
     */
    public function getByClassePeriode($classeId, $periodeId) {
        return $this->query(
            "SELECT e.*, m.nom as matiere_nom, 'examen' as type 
             FROM {$this->table} e 
             JOIN matieres m ON e.matiere_id = m.id 
             WHERE e.classe_id = ? AND e.periode_id = ? 
             ORDER BY e.date_examen DESC",
            [$classeId, $periodeId]
        );
    }
    
    /**
     * Récupère les détails d'un examen avec les informations de matière et classe
     * @param int $id ID de l'examen
     * @return array|null Détails de l'examen
     */
    public function getDetailsWithRelations($id) {
        return $this->queryOne(
            "SELECT e.*, m.nom as matiere_nom, c.nom as classe_nom 
             FROM {$this->table} e 
             JOIN matieres m ON e.matiere_id = m.id 
             JOIN classes c ON e.classe_id = c.id 
             WHERE e.id = ?", 
            [$id]
        );
    }
    
    /**
     * Récupère les élèves d'une classe avec leurs notes pour cet examen
     * @param int $examenId ID de l'examen
     * @param int $classeId ID de la classe
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Liste des élèves avec leurs notes
     */
    public function getElevesWithNotes($examenId, $classeId, $anneeScolaireId) {
        return $this->query(
            "SELECT e.id, e.nom, e.prenom, e.matricule, n.note, n.absent, n.appreciation 
             FROM eleves e 
             INNER JOIN inscriptions i ON i.eleve_id = e.id 
             LEFT JOIN notes_examens n ON (n.eleve_id = e.id AND n.examen_id = ?)
             WHERE i.classe_id = ? AND i.annee_scolaire_id = ? 
             ORDER BY e.nom, e.prenom",
            [$examenId, $classeId, $anneeScolaireId]
        );
    }
}
