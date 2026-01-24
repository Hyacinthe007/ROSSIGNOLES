<?php
declare(strict_types=1);

namespace App\Models;

class Absence extends BaseModel {
    protected $table = 'absences';
    protected $fillable = [
        'eleve_id', 'classe_id', 'annee_scolaire_id', 'date_absence', 'type', 'periode',
        'heure_debut', 'heure_fin', 'justifiee', 'motif', 
        'piece_justificative', 'saisi_par', 'valide_par', 
        'date_validation'
    ];
    
    /**
     * Obtient les absences d'un élève
     */
    public function getByEleve($eleveId, $anneeScolaireId = null) {
        $sql = "SELECT a.*, c.nom as classe_nom
                FROM {$this->table} a
                INNER JOIN classes c ON a.classe_id = c.id
                WHERE a.eleve_id = ?";
                
        $params = [$eleveId];
        
        if ($anneeScolaireId) {
            $sql .= " AND c.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " ORDER BY a.date_absence DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtient les statistiques d'absences
     */
    public function getStats($eleveId, $anneeScolaireId = null) {
        // Logique simplifiée sans jointure complexe si possible, 
        // ou utilisation de la vue si elle est créée (vue_stats_absences)
        // Ici on fait une requête directe
        
        // Comme la table absences n'a pas annee_scolaire_id direct (c'est via classe),
        // on assume que l'obtention par élève couvre l'historique pertinent ou on joint classes
        
        $sql = "SELECT 
                    SUM(CASE WHEN type = 'absence' THEN 1 ELSE 0 END) as nb_absences,
                    SUM(CASE WHEN type = 'retard' THEN 1 ELSE 0 END) as nb_retards,
                    SUM(CASE WHEN justifiee = 0 THEN 1 ELSE 0 END) as nb_non_justifiees
                FROM {$this->table} a
                INNER JOIN classes c ON a.classe_id = c.id
                WHERE a.eleve_id = ?";
                
        $params = [$eleveId];
        
        if ($anneeScolaireId) {
            $sql .= " AND c.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        return $this->queryOne($sql, $params);
    }
}

