<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Note (V2)
 * Gère les notes de toutes les évaluations (unifiées)
 */
class Note extends BaseModel
{
    protected $table = 'notes';
    protected $auditable = true;

    protected $fillable = [
        'evaluation_id', 'eleve_id', 'note', 'absent', 'dispense', 'appreciation', 'saisi_par'
    ];

    /**
     * Récupère toutes les notes (examens et interrogations) pour un élève et une période
     * (Méthode V1 compatible V2)
     */
    public function getByElevePeriode($eleveId, $periodeId)
    {
        $sql = "
            SELECT 
                ev.type as type_evaluation,
                ev.id as evaluation_id,
                ev.nom as evaluation_nom,
                ev.date_evaluation,
                ev.note_sur,
                n.note,
                n.absent,
                n.appreciation,
                m.id as matiere_id,
                m.nom as matiere_nom,
                m.code as matiere_code,
                -- Gestion des coefficients (Cible la table coefficients_matieres de la phase 3 en priorité ou fallback legacy)
                COALESCE(
                    (SELECT coefficient FROM coefficients_matieres WHERE matiere_id = ev.matiere_id AND cible_id = ev.classe_id AND cible_type = 'classe' AND actif = 1 LIMIT 1),
                    (SELECT coefficient FROM coefficients_matieres WHERE matiere_id = ev.matiere_id AND cible_id = c.niveau_id AND cible_type = 'niveau' AND actif = 1 LIMIT 1),
                    (SELECT coefficient FROM coefficients_matieres WHERE matiere_id = ev.matiere_id AND cible_id = c.serie_id AND cible_type = 'serie' AND actif = 1 LIMIT 1),
                    1.00
                ) as coefficient,
                ev.personnel_id,
                p.nom as prof_nom, p.prenom as prof_prenom
            FROM {$this->table} n
            JOIN evaluations ev ON n.evaluation_id = ev.id
            JOIN matieres m ON ev.matiere_id = m.id
            JOIN classes c ON ev.classe_id = c.id
            LEFT JOIN personnels p ON ev.personnel_id = p.id
            WHERE n.eleve_id = ? AND ev.periode_id = ?
            ORDER BY m.nom ASC, ev.date_evaluation ASC
        ";
        
        return $this->query($sql, [(int)$eleveId, (int)$periodeId]);
    }

    /**
     * Récupère les notes d'une évaluation spécifique
     */
    public function getByEvaluation(int $evaluationId): array
    {
        $sql = "SELECT n.*, e.matricule, e.nom, e.prenom
                FROM {$this->table} n
                JOIN eleves e ON n.eleve_id = e.id
                WHERE n.evaluation_id = ?
                ORDER BY e.nom ASC, e.prenom ASC";
        
        return $this->query($sql, [$evaluationId]);
    }

    /**
     * Enregistre ou met à jour une note
     */
    public function upsert(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} 
                (evaluation_id, eleve_id, note, absent, appreciation, saisi_par)
                VALUES (:evaluation_id, :eleve_id, :note, :absent, :appreciation, :saisi_par)
                ON DUPLICATE KEY UPDATE 
                note = VALUES(note), 
                absent = VALUES(absent), 
                appreciation = VALUES(appreciation),
                updated_at = CURRENT_TIMESTAMP";
        
        return $this->execute($sql, $data);
    }
}
