<?php
/**
 * Modèle Niveau
 * Gestion des niveaux scolaires (PS, MS, GS, CP, CE1, etc.)
 */

require_once __DIR__ . '/BaseModel.php';

class Niveau extends BaseModel {
    protected $table = 'niveaux';
    protected $fillable = [
        'code', 'libelle', 'ordre', 'cycle_id', 'actif'
    ];
    
    /**
     * Récupère tous les niveaux actifs avec leurs cycles
     * @return array Liste des niveaux avec infos cycle
     */
    public function getAllWithCycle() {
        return $this->query(
            "SELECT n.*, c.libelle as cycle_nom, c.code as cycle_code, c.ordre as cycle_ordre
             FROM {$this->table} n 
             LEFT JOIN cycles c ON n.cycle_id = c.id 
             WHERE n.actif = 1 
             ORDER BY c.ordre ASC, n.ordre ASC, n.libelle ASC"
        );
    }
    
    /**
     * Récupère les niveaux d'un cycle spécifique
     * @param int $cycleId ID du cycle
     * @return array Liste des niveaux
     */
    public function getByCycle($cycleId) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE cycle_id = ? AND actif = 1 
             ORDER BY ordre ASC, libelle ASC",
            [$cycleId]
        );
    }
    
    /**
     * Récupère les séries d'un niveau
     * @param int $niveauId ID du niveau
     * @return array Liste des séries
     */
    public function getSeries($niveauId = null) {
        $id = $niveauId ?? $this->id;
        return $this->query(
            "SELECT * FROM series 
             WHERE niveau_id = ? AND actif = 1 
             ORDER BY libelle ASC",
            [$id]
        );
    }
    
    /**
     * Récupère les classes d'un niveau pour une année scolaire
     * @param int $niveauId ID du niveau
     * @param int|null $anneeScolaireId ID de l'année scolaire
     * @return array Liste des classes
     */
    public function getClasses($niveauId, $anneeScolaireId = null) {
        $where = "niveau_id = ? AND statut = 'actif' AND deleted_at IS NULL";
        $params = [$niveauId];
        
        if ($anneeScolaireId) {
            $where .= " AND annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        return $this->query(
            "SELECT * FROM classes 
             WHERE {$where} 
             ORDER BY nom ASC",
            $params
        );
    }
    
    /**
     * Récupère les matières associées à un niveau
     * @param int $niveauId ID du niveau
     * @return array Liste des matières avec coefficients
     */
    public function getMatieres($niveauId) {
        return $this->query(
            "SELECT m.*, mn.coefficient, mn.obligatoire, mn.heures_semaine
             FROM matieres m
             INNER JOIN matieres_niveaux mn ON m.id = mn.matiere_id
             WHERE mn.niveau_id = ? AND mn.actif = 1 AND m.actif = 1
             ORDER BY m.nom ASC",
            [$niveauId]
        );
    }
    
    /**
     * Récupère un niveau par son code
     * @param string $code Code du niveau
     * @return array|null Niveau trouvé
     */
    public function getByCode($code) {
        return $this->queryOne(
            "SELECT n.*, c.libelle as cycle_nom
             FROM {$this->table} n
             LEFT JOIN cycles c ON n.cycle_id = c.id
             WHERE n.code = ?",
            [$code]
        );
    }
    
    /**
     * Compte le nombre d'élèves dans un niveau pour une année
     * @param int $niveauId ID du niveau
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return int Nombre d'élèves
     */
    public function countEleves($niveauId, $anneeScolaireId) {
        $result = $this->queryOne(
            "SELECT COUNT(DISTINCT i.eleve_id) as count
             FROM inscriptions i
             INNER JOIN classes c ON i.classe_id = c.id
             WHERE c.niveau_id = ? 
             AND i.annee_scolaire_id = ? 
             AND i.statut = 'validee'",
            [$niveauId, $anneeScolaireId]
        );
        return $result ? (int)$result['count'] : 0;
    }
    
    /**
     * Récupère les détails complets d'un niveau
     * @param int $niveauId ID du niveau
     * @return array|null Détails du niveau
     */
    public function getDetails($niveauId) {
        return $this->queryOne(
            "SELECT n.*, 
                    c.libelle as cycle_nom, 
                    c.code as cycle_code,
                    (SELECT COUNT(*) FROM series WHERE niveau_id = n.id AND actif = 1) as nb_series,
                    (SELECT COUNT(*) FROM matieres_niveaux WHERE niveau_id = n.id AND actif = 1) as nb_matieres
             FROM {$this->table} n
             LEFT JOIN cycles c ON n.cycle_id = c.id
             WHERE n.id = ?",
            [$niveauId]
        );
    }
}
