<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Cycle
 */

class Cycle extends BaseModel {
    protected $table = 'cycles';
    protected $fillable = [
        'code', 'libelle', 'ordre', 'description', 'actif'
    ];
    
    /**
     * Récupère tous les cycles actifs
     * @return array Liste des cycles
     */
    public function getAllActifs() {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE actif = 1 
             ORDER BY ordre ASC, libelle ASC"
        );
    }
    
    /**
     * Récupère les niveaux d'un cycle
     * @param int $cycleId ID du cycle
     * @return array Liste des niveaux
     */
    public function getNiveaux($cycleId) {
        return $this->query(
            "SELECT * FROM niveaux 
             WHERE cycle_id = ? AND actif = 1 
             ORDER BY ordre ASC, libelle ASC",
            [$cycleId]
        );
    }
    
    /**
     * Récupère les classes d'un cycle pour une année scolaire
     * @param int $cycleId ID du cycle
     * @param int|null $anneeScolaireId ID de l'année scolaire
     * @return array Liste des classes
     */
    public function getClasses($cycleId, $anneeScolaireId = null) {
        $where = "n.cycle_id = ? AND c.statut = 'actif' AND c.deleted_at IS NULL";
        $params = [$cycleId];
        
        if ($anneeScolaireId) {
            $where .= " AND c.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        return $this->query(
            "SELECT c.*, n.libelle as niveau_nom
             FROM classes c
             INNER JOIN niveaux n ON c.niveau_id = n.id
             WHERE {$where}
             ORDER BY n.ordre ASC, c.nom ASC",
            $params
        );
    }
    
    /**
     * Récupère un cycle par son code
     * @param string $code Code du cycle
     * @return array|null Cycle trouvé
     */
    public function getByCode($code) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE code = ?",
            [$code]
        );
    }
    
    /**
     * Compte le nombre d'élèves dans un cycle pour une année
     * @param int $cycleId ID du cycle
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return int Nombre d'élèves
     */
    public function countEleves($cycleId, $anneeScolaireId) {
        $result = $this->queryOne(
            "SELECT COUNT(DISTINCT i.eleve_id) as count
             FROM inscriptions i
             INNER JOIN classes c ON i.classe_id = c.id
             INNER JOIN niveaux n ON c.niveau_id = n.id
             WHERE n.cycle_id = ? 
             AND i.annee_scolaire_id = ? 
             AND i.statut = 'validee'",
            [$cycleId, $anneeScolaireId]
        );
        return $result ? (int)$result['count'] : 0;
    }
    
    /**
     * Récupère les détails complets d'un cycle
     * @param int $cycleId ID du cycle
     * @return array|null Détails du cycle
     */
    public function getDetails($cycleId) {
        return $this->queryOne(
            "SELECT cy.*, 
                    (SELECT COUNT(*) FROM niveaux WHERE cycle_id = cy.id AND actif = 1) as nb_niveaux
             FROM {$this->table} cy
             WHERE cy.id = ?",
            [$cycleId]
        );
    }
    
    /**
     * Récupère les statistiques d'un cycle pour une année
     * @param int $cycleId ID du cycle
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Statistiques du cycle
     */
    public function getStatistiques($cycleId, $anneeScolaireId) {
        return $this->queryOne(
            "SELECT 
                COUNT(DISTINCT i.eleve_id) as nb_eleves,
                COUNT(DISTINCT c.id) as nb_classes,
                COUNT(DISTINCT n.id) as nb_niveaux,
                SUM(CASE WHEN e.sexe = 'M' THEN 1 ELSE 0 END) as nb_garcons,
                SUM(CASE WHEN e.sexe = 'F' THEN 1 ELSE 0 END) as nb_filles
             FROM niveaux n
             LEFT JOIN classes c ON n.id = c.niveau_id AND c.annee_scolaire_id = ?
             LEFT JOIN inscriptions i ON c.id = i.classe_id AND i.statut = 'validee'
             LEFT JOIN eleves e ON i.eleve_id = e.id
             WHERE n.cycle_id = ? AND n.actif = 1",
            [$anneeScolaireId, $cycleId]
        );
    }
}
