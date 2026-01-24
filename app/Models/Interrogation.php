<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Interrogation
 */

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
    
    /**
     * Récupère les interrogations d'une classe pour une période donnée
     * @param int $classeId ID de la classe
     * @param int $periodeId ID de la période
     * @return array Liste des interrogations avec nom de matière
     */
    public function getByClassePeriode($classeId, $periodeId) {
        return $this->query(
            "SELECT i.*, m.nom as matiere_nom, 'interrogation' as type 
             FROM {$this->table} i 
             JOIN matieres m ON i.matiere_id = m.id 
             WHERE i.classe_id = ? AND i.periode_id = ? 
             ORDER BY i.date_interrogation DESC",
            [$classeId, $periodeId]
        );
    }
    
    /**
     * Récupère les détails d'une interrogation avec les informations de matière et classe
     * @param int $id ID de l'interrogation
     * @return array|null Détails de l'interrogation
     */
    public function getDetailsWithRelations($id) {
        return $this->queryOne(
            "SELECT i.*, m.nom as matiere_nom, c.nom as classe_nom 
             FROM {$this->table} i 
             JOIN matieres m ON i.matiere_id = m.id 
             JOIN classes c ON i.classe_id = c.id 
             WHERE i.id = ?", 
            [$id]
        );
    }
    
    /**
     * Récupère les élèves d'une classe avec leurs notes pour cette interrogation
     * @param int $interrogationId ID de l'interrogation
     * @param int $classeId ID de la classe
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Liste des élèves avec leurs notes
     */
    public function getElevesWithNotes($interrogationId, $classeId, $anneeScolaireId) {
        return $this->query(
            "SELECT e.id, e.nom, e.prenom, e.matricule, n.note, n.absent, n.appreciation 
             FROM eleves e 
             INNER JOIN inscriptions i ON i.eleve_id = e.id 
             LEFT JOIN notes_interrogations n ON (n.eleve_id = e.id AND n.interrogation_id = ?)
             WHERE i.classe_id = ? AND i.annee_scolaire_id = ? 
             ORDER BY e.nom, e.prenom",
            [$interrogationId, $classeId, $anneeScolaireId]
        );
    }
}
