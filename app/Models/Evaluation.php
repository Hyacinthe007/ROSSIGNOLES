<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Evaluation (V2)
 * Unifie interrogations et examens finaux
 */
class Evaluation extends BaseModel
{
    protected $table = 'evaluations';
    protected $auditable = true;

    protected $fillable = [
        'type', 'classe_id', 'matiere_id', 'personnel_id', 'periode_id',
        'annee_scolaire_id', 'nom', 'date_evaluation', 'heure_debut',
        'heure_fin', 'duree_minutes', 'note_sur', 'poids', 'description',
        'consignes', 'sujet_url', 'statut'
    ];

    /**
     * Types d'évaluations
     */
    const TYPES = [
        'interrogation' => 'Interrogation / Contrôle',
        'devoir'        => 'Devoir à la maison',
        'examen'        => 'Examen final',
        'tp'            => 'Travaux Pratiques',
        'oral'          => 'Examen Oral',
    ];

    /**
     * Récupère les évaluations d'une classe pour une période
     */
    public function getByClassePeriode(int $classeId, int $periodeId, ?string $type = null): array
    {
        $sql = "SELECT e.*, m.nom as matiere_nom, m.code as matiere_code, 
                       p.nom as prof_nom, p.prenom as prof_prenom
                FROM {$this->table} e
                JOIN matieres m ON e.matiere_id = m.id
                LEFT JOIN personnels p ON e.personnel_id = p.id
                WHERE e.classe_id = ? AND e.periode_id = ?";
        
        $params = [$classeId, $periodeId];

        if ($type !== null) {
            $sql .= " AND e.type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY e.date_evaluation DESC, m.nom ASC";

        return $this->query($sql, $params);
    }

    /**
     * Récupère les détails d'une évaluation avec relations (matière, classe)
     */
    public function getDetailsWithRelations(int $id): ?array
    {
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
     * Récupère les élèves d'une classe avec leurs notes pour cette évaluation
     */
    public function getElevesWithNotes(int $evaluationId, int $classeId, int $anneeScolaireId): array
    {
        return $this->query(
            "SELECT e.id, e.nom, e.prenom, e.matricule, n.note, n.absent, n.appreciation 
             FROM eleves e 
             INNER JOIN inscriptions i ON i.eleve_id = e.id 
             LEFT JOIN notes n ON (n.eleve_id = e.id AND n.evaluation_id = ?)
             WHERE i.classe_id = ? AND i.annee_scolaire_id = ? 
             AND i.statut = 'validee'
             ORDER BY e.nom, e.prenom",
            [$evaluationId, $classeId, $anneeScolaireId]
        );
    }

    /**
     * Calcule les statistiques d'une évaluation
     */
    public function getStats(int $evaluationId): array
    {
        $sql = "SELECT 
                    COUNT(n.id) as nb_notes,
                    MIN(n.note) as note_min,
                    MAX(n.note) as note_max,
                    AVG(n.note) as moyenne,
                    SUM(CASE WHEN n.note >= (e.note_sur / 2) THEN 1 ELSE 0 END) as nb_admis,
                    SUM(CASE WHEN n.note < (e.note_sur / 2) THEN 1 ELSE 0 END) as nb_echoues,
                    SUM(CASE WHEN n.absent = 1 THEN 1 ELSE 0 END) as nb_absents
                FROM {$this->table} e
                LEFT JOIN notes n ON e.id = n.evaluation_id
                WHERE e.id = ?
                GROUP BY e.id";
        
        return $this->queryOne($sql, [$evaluationId]) ?: [];
    }
}
