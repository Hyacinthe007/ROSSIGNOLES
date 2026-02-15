<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle pour la table evenements_calendrier
 * Résultat de la fusion de calendrier_scolaire + jours_feries
 */
class EvenementCalendrier extends BaseModel
{
    protected $table = 'evenements_calendrier';
    protected $auditable = true;

    protected $fillable = [
        'annee_scolaire_id', 'type', 'libelle', 'date_debut', 'date_fin',
        'description', 'concerne', 'bloque_cours', 'couleur', 'actif'
    ];

    /**
     * Types d'événements disponibles
     */
    const TYPES = [
        'vacances'     => 'Vacances scolaires',
        'ferie'        => 'Jour férié',
        'pont'         => 'Pont',
        'examen'       => 'Examen',
        'conseil'      => 'Conseil de classe',
        'rentree'      => 'Rentrée',
        'sortie'       => 'Sortie / Fin de cours',
        'pedagogique'  => 'Journée pédagogique',
        'autre'        => 'Autre',
    ];

    /**
     * Couleurs par défaut pour chaque type
     */
    const COULEURS = [
        'vacances'    => '#10b981',
        'ferie'       => '#ef4444',
        'pont'        => '#f59e0b',
        'examen'      => '#6366f1',
        'conseil'     => '#8b5cf6',
        'rentree'     => '#3b82f6',
        'sortie'      => '#ec4899',
        'pedagogique' => '#14b8a6',
        'autre'       => '#6b7280',
    ];

    /**
     * Récupère les événements d'une année scolaire
     */
    public function getByAnneeScolaire(int $anneeScolaireId, ?string $type = null): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE annee_scolaire_id = ? AND actif = 1";
        $params = [$anneeScolaireId];

        if ($type !== null) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY date_debut ASC";

        return $this->query($sql, $params);
    }

    /**
     * Récupère les événements dans une plage de dates
     */
    public function getByDateRange(int $anneeScolaireId, string $dateDebut, string $dateFin): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE annee_scolaire_id = ? 
                AND actif = 1
                AND date_debut <= ? AND date_fin >= ?
                ORDER BY date_debut ASC";

        return $this->query($sql, [$anneeScolaireId, $dateFin, $dateDebut]);
    }

    /**
     * Vérifie si une date est un jour bloqué (vacances, férié, etc.)
     */
    public function isDateBloquee(int $anneeScolaireId, string $date): bool
    {
        $sql = "SELECT COUNT(*) as nb FROM {$this->table} 
                WHERE annee_scolaire_id = ? 
                AND actif = 1
                AND bloque_cours = 1
                AND ? BETWEEN date_debut AND date_fin";

        $result = $this->queryOne($sql, [$anneeScolaireId, $date]);
        return ($result['nb'] ?? 0) > 0;
    }

    /**
     * Récupère les événements à venir (pour le dashboard)
     */
    public function getUpcoming(int $anneeScolaireId, int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE annee_scolaire_id = ? 
                AND actif = 1
                AND date_fin >= CURDATE()
                ORDER BY date_debut ASC
                LIMIT ?";

        return $this->query($sql, [$anneeScolaireId, $limit]);
    }

    /**
     * Compte les jours non ouvrés pour une période
     */
    public function countJoursNonOuvres(int $anneeScolaireId, string $dateDebut, string $dateFin): int
    {
        $sql = "SELECT SUM(DATEDIFF(
                    LEAST(date_fin, ?), 
                    GREATEST(date_debut, ?)
                ) + 1) AS total_jours
                FROM {$this->table}
                WHERE annee_scolaire_id = ?
                AND actif = 1
                AND bloque_cours = 1
                AND date_debut <= ? AND date_fin >= ?";

        $result = $this->queryOne($sql, [$dateFin, $dateDebut, $anneeScolaireId, $dateFin, $dateDebut]);
        return (int)($result['total_jours'] ?? 0);
    }
}
