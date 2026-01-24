<?php
/**
 * Modèle MatieresClasses
 * Table: matieres_classes
 * Gère l'association entre matières et classes avec coefficients spécifiques à l'année
 */

require_once __DIR__ . '/BaseModel.php';

class MatieresClasses extends BaseModel {
    protected $table = 'matieres_classes';
    protected $fillable = [
        'matiere_id', 'classe_id', 'annee_scolaire_id', 
        'coefficient', 'heures_semaine', 'obligatoire'
    ];
    
    /**
     * Récupère toutes les matières d'une classe pour une année donnée
     */
    public function getMatieresParClasse($classeId, $anneeId) {
        return $this->query(
            "SELECT mc.*, m.code, m.nom as matiere_nom, m.description
             FROM {$this->table} mc
             JOIN matieres m ON mc.matiere_id = m.id
             WHERE mc.classe_id = ? AND mc.annee_scolaire_id = ?
             ORDER BY m.nom ASC",
            [$classeId, $anneeId]
        );
    }
    
    /**
     * Récupère le coefficient d'une matière pour une classe
     */
    public function getCoefficient($matiereId, $classeId, $anneeId) {
        $result = $this->queryOne(
            "SELECT coefficient FROM {$this->table} 
             WHERE matiere_id = ? AND classe_id = ? AND annee_scolaire_id = ?",
            [$matiereId, $classeId, $anneeId]
        );
        return $result ? $result['coefficient'] : null;
    }
}
