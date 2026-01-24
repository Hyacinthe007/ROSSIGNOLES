<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle MatieresNiveaux
 */

class MatieresNiveaux extends BaseModel {
    protected $table = 'matieres_niveaux';
    protected $fillable = [
        'matiere_id', 'niveau_id', 'coefficient', 'obligatoire', 
        'heures_semaine', 'actif'
    ];
    
    /**
     * Récupère toutes les matières d'un niveau
     */
    public function getMatieresParNiveau($niveauId) {
        return $this->query(
            "SELECT mn.*, m.code, m.nom as matiere_nom, m.description
             FROM {$this->table} mn
             JOIN matieres m ON mn.matiere_id = m.id
             WHERE mn.niveau_id = ? AND mn.actif = 1
             ORDER BY m.nom ASC",
            [$niveauId]
        );
    }
    
    /**
     * Récupère tous les niveaux pour une matière
     */
    public function getNiveauxParMatiere($matiereId) {
        return $this->query(
            "SELECT mn.*, n.code, n.libelle as niveau_nom
             FROM {$this->table} mn
             JOIN niveaux n ON mn.niveau_id = n.id
             WHERE mn.matiere_id = ? AND mn.actif = 1
             ORDER BY n.ordre ASC",
            [$matiereId]
        );
    }
    
    /**
     * Récupère le coefficient d'une matière pour un niveau
     */
    public function getCoefficient($matiereId, $niveauId) {
        $result = $this->queryOne(
            "SELECT coefficient FROM {$this->table} 
             WHERE matiere_id = ? AND niveau_id = ? AND actif = 1",
            [$matiereId, $niveauId]
        );
        return $result ? $result['coefficient'] : 1.00;
    }
}
