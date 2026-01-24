<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Serie
 * Gestion des séries scolaires (S, L, A, etc.)
 */

class Serie extends BaseModel {
    protected $table = 'series';
    protected $fillable = [
        'code', 'libelle', 'niveau_id', 'description', 'actif'
    ];
    
    /**
     * Récupère toutes les séries avec leurs niveaux
     * @return array Liste des séries
     */
    public function getAllWithNiveau() {
        return $this->query(
            "SELECT s.*, 
                    n.libelle as niveau_libelle, 
                    n.code as niveau_code,
                    n.cycle_id,
                    c.libelle as cycle_nom
             FROM {$this->table} s
             LEFT JOIN niveaux n ON s.niveau_id = n.id
             LEFT JOIN cycles c ON n.cycle_id = c.id
             ORDER BY c.ordre ASC, n.ordre ASC, s.libelle ASC"
        );
    }
    
    /**
     * Récupère les séries d'un niveau
     * @param int $niveauId ID du niveau
     * @return array Liste des séries
     */
    public function getByNiveau($niveauId) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE niveau_id = ? AND actif = 1 
             ORDER BY libelle ASC",
            [$niveauId]
        );
    }
    
    /**
     * Récupère le niveau d'une série
     * @param int|null $serieId ID de la série
     * @return array|null Niveau
     */
    public function getNiveau($serieId = null) {
        $id = $serieId ?? $this->niveau_id;
        return $this->queryOne(
            "SELECT n.*, c.libelle as cycle_nom
             FROM niveaux n
             LEFT JOIN cycles c ON n.cycle_id = c.id
             WHERE n.id = (SELECT niveau_id FROM {$this->table} WHERE id = ?)",
            [$id]
        );
    }
    
    /**
     * Récupère les matières d'une série avec coefficients
     * @param int $serieId ID de la série
     * @return array Liste des matières
     */
    public function getMatieres($serieId) {
        return $this->query(
            "SELECT m.*, 
                    ms.coefficient, 
                    ms.obligatoire, 
                    ms.heures_semaine
             FROM matieres m
             INNER JOIN matieres_series ms ON m.id = ms.matiere_id
             WHERE ms.serie_id = ? AND ms.actif = 1 AND m.actif = 1
             ORDER BY m.nom ASC",
            [$serieId]
        );
    }
    
    /**
     * Récupère les classes d'une série pour une année
     * @param int $serieId ID de la série
     * @param int|null $anneeScolaireId ID de l'année scolaire
     * @return array Liste des classes
     */
    public function getClasses($serieId, $anneeScolaireId = null) {
        $where = "serie_id = ? AND statut = 'actif' AND deleted_at IS NULL";
        $params = [$serieId];
        
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
     * Récupère une série par son code
     * @param string $code Code de la série
     * @return array|null Série trouvée
     */
    public function getByCode($code) {
        return $this->queryOne(
            "SELECT s.*, 
                    n.libelle as niveau_libelle,
                    n.code as niveau_code
             FROM {$this->table} s
             LEFT JOIN niveaux n ON s.niveau_id = n.id
             WHERE s.code = ?",
            [$code]
        );
    }
    
    /**
     * Compte le nombre d'élèves dans une série pour une année
     * @param int $serieId ID de la série
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return int Nombre d'élèves
     */
    public function countEleves($serieId, $anneeScolaireId) {
        $result = $this->queryOne(
            "SELECT COUNT(DISTINCT i.eleve_id) as count
             FROM inscriptions i
             INNER JOIN classes c ON i.classe_id = c.id
             WHERE c.serie_id = ? 
             AND i.annee_scolaire_id = ? 
             AND i.statut = 'validee'",
            [$serieId, $anneeScolaireId]
        );
        return $result ? (int)$result['count'] : 0;
    }
    
    /**
     * Récupère les détails complets d'une série
     * @param int $serieId ID de la série
     * @return array|null Détails de la série
     */
    public function getDetails($serieId) {
        return $this->queryOne(
            "SELECT s.*, 
                    n.libelle as niveau_libelle,
                    n.code as niveau_code,
                    c.libelle as cycle_nom,
                    (SELECT COUNT(*) FROM matieres_series WHERE serie_id = s.id AND actif = 1) as nb_matieres,
                    (SELECT COUNT(*) FROM classes WHERE serie_id = s.id AND statut = 'actif') as nb_classes
             FROM {$this->table} s
             LEFT JOIN niveaux n ON s.niveau_id = n.id
             LEFT JOIN cycles c ON n.cycle_id = c.id
             WHERE s.id = ?",
            [$serieId]
        );
    }
    
    /**
     * Vérifie si une série est utilisée dans des classes
     * @param int $serieId ID de la série
     * @return bool True si la série est utilisée
     */
    public function isUsed($serieId) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count FROM classes WHERE serie_id = ?",
            [$serieId]
        );
        return $result && $result['count'] > 0;
    }
}
