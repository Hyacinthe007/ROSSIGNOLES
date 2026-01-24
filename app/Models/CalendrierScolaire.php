<?php
/**
 * Modèle CalendrierScolaire
 * Gère les vacances, jours fériés et autres événements bloquant les cours
 */

require_once __DIR__ . '/BaseModel.php';

class CalendrierScolaire extends BaseModel {
    protected $table = 'calendrier_scolaire';
    protected $fillable = [
        'annee_scolaire_id', 'type', 'libelle', 'date_debut', 'date_fin', 'description', 'bloque_cours'
    ];

    /**
     * Récupère les événements pour une année scolaire
     */
    public function getByAnnee($anneeScolaireId) {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE annee_scolaire_id = ? ORDER BY date_debut ASC",
            [$anneeScolaireId]
        );
    }

    /**
     * Vérifie si une date est un jour de repos (vacances ou férié bloquant)
     */
    public function isRepos($date, $anneeScolaireId) {
        $result = $this->queryOne(
            "SELECT * FROM {$this->table} 
             WHERE annee_scolaire_id = ? 
             AND ? BETWEEN date_debut AND date_fin 
             AND bloque_cours = 1",
            [$anneeScolaireId, $date]
        );
        return $result;
    }

    /**
     * Récupère les événements bloquants pour une période donnée
     */
    public function getEventsInRange($startDate, $endDate, $anneeScolaireId) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE annee_scolaire_id = ? 
             AND (
                (date_debut BETWEEN ? AND ?) OR 
                (date_fin BETWEEN ? AND ?) OR
                (date_debut <= ? AND date_fin >= ?)
             )",
            [$anneeScolaireId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate]
        );
    }
}
