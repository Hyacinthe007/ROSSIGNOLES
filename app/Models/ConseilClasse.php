<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle ConseilClasse
 * Gestion des conseils de classe par période
 */

class ConseilClasse extends BaseModel {
    protected $table = 'conseils_classe';
    protected $fillable = [
        'classe_id', 'periode_id', 'annee_scolaire_id', 'date_conseil',
        'heure_debut', 'heure_fin', 'president_conseil', 'secretaire',
        'ordre_du_jour', 'pv_url', 'moyenne_classe', 'taux_reussite',
        'nb_felicitations', 'nb_encouragements', 'nb_avertissements_travail',
        'nb_avertissements_conduite', 'nb_blames', 'appreciation_generale',
        'statut', 'date_validation', 'valide_par'
    ];

    /**
     * Récupère les participants présents au conseil
     */
    public function getParticipants($conseilId) {
        return $this->query(
            "SELECT p.*, ccp.role
             FROM personnels p
             INNER JOIN conseils_classe_participants ccp ON p.id = ccp.personnel_id
             WHERE ccp.conseil_classe_id = ?",
            [$conseilId]
        );
    }

    /**
     * Récupère le conseil pour une classe et une période données
     */
    public function getByClassePeriode($classeId, $periodeId) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE classe_id = ? AND periode_id = ?",
            [$classeId, $periodeId]
        );
    }
}
