<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle MatieresSeries
 */

class MatieresSeries extends BaseModel {
    protected $table = 'matieres_series';
    protected $fillable = [
        'matiere_id', 'serie_id', 'coefficient', 'obligatoire', 
        'heures_semaine', 'actif'
    ];
    
    /**
     * Récupère toutes les matières d'une série
     */
    public function getMatieresParSerie($serieId) {
        return $this->query(
            "SELECT ms.*, m.code, m.nom as matiere_nom, m.description
             FROM {$this->table} ms
             JOIN matieres m ON ms.matiere_id = m.id
             WHERE ms.serie_id = ? AND ms.actif = 1
             ORDER BY m.nom ASC",
            [$serieId]
        );
    }
    
    /**
     * Récupère toutes les séries pour une matière
     */
    public function getSeriesParMatiere($matiereId) {
        return $this->query(
            "SELECT ms.*, s.code, s.libelle as serie_nom, n.libelle as niveau_nom
             FROM {$this->table} ms
             JOIN series s ON ms.serie_id = s.id
             JOIN niveaux n ON s.niveau_id = n.id
             WHERE ms.matiere_id = ? AND ms.actif = 1
             ORDER BY n.ordre ASC, s.libelle ASC",
            [$matiereId]
        );
    }
    
    /**
     * Récupère le coefficient d'une matière pour une série
     */
    public function getCoefficient($matiereId, $serieId) {
        $result = $this->queryOne(
            "SELECT coefficient FROM {$this->table} 
             WHERE matiere_id = ? AND serie_id = ? AND actif = 1",
            [$matiereId, $serieId]
        );
        return $result ? $result['coefficient'] : 1.00;
    }
    
    /**
     * Vérifie si une matière est obligatoire pour une série
     */
    public function isObligatoire($matiereId, $serieId) {
        $result = $this->queryOne(
            "SELECT obligatoire FROM {$this->table} 
             WHERE matiere_id = ? AND serie_id = ? AND actif = 1",
            [$matiereId, $serieId]
        );
        return $result ? (bool)$result['obligatoire'] : false;
    }
    
    /**
     * Récupère les statistiques des matières par série
     */
    public function getStatistiquesParSerie($serieId) {
        return $this->queryOne(
            "SELECT 
                COUNT(*) as nb_matieres,
                SUM(CASE WHEN obligatoire = 1 THEN 1 ELSE 0 END) as nb_obligatoires,
                SUM(coefficient) as total_coefficients,
                SUM(heures_semaine) as total_heures
             FROM {$this->table}
             WHERE serie_id = ? AND actif = 1",
            [$serieId]
        );
    }
}
