<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle AnneeScolaire
 */

class AnneeScolaire extends BaseModel {
    protected $table = 'annees_scolaires';
    protected $fillable = [
        'libelle', 'date_debut', 'date_fin', 'date_rentree', 'date_fin_cours', 
        'actif', 'cloturee', 'date_cloture', 'nb_jours_classe', 'nb_semaines'
    ];
    
    /**
     * Obtient l'année scolaire active
     */
    public function getActive() {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE actif = 1 LIMIT 1"
        );
    }
    
    /**
     * Active une année scolaire (désactive automatiquement les autres)
     */
    public function activate($id) {
        try {
            $this->beginTransaction();
            
            // Désactiver toutes les années
            $this->query("UPDATE {$this->table} SET actif = 0");
            
            // Activer l'année spécifiée
            $this->update($id, ['actif' => 1]);
            
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Vérifie si l'année a des inscriptions
     */
    public function hasInscriptions($id) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count FROM inscriptions WHERE annee_scolaire_id = ?",
            [$id]
        );
        return $result && $result['count'] > 0;
    }
    
    /**
     * Obtient les statistiques d'une année scolaire
     */
    public function getStatistiques($id) {
        // Nombre total d'inscriptions
        $inscriptions = $this->queryOne(
            "SELECT COUNT(*) as total FROM inscriptions WHERE annee_scolaire_id = ?",
            [$id]
        );
        
        // Nombre d'élèves unique inscrits
        $eleves = $this->queryOne(
            "SELECT COUNT(DISTINCT eleve_id) as total 
             FROM inscriptions 
             WHERE annee_scolaire_id = ? AND statut = 'validee'",
            [$id]
        );
        
        // Nombre de classes
        $classes = $this->queryOne(
            "SELECT COUNT(*) as total FROM classes WHERE annee_scolaire_id = ?",
            [$id]
        );
        
        // Statistiques financières (Basées sur les échéanciers d'écolage)
        $finances = $this->queryOne(
            "SELECT 
                COALESCE(SUM(ee.montant_du), 0) as total_frais,
                COALESCE(SUM(ee.montant_paye), 0) as total_paye,
                COALESCE(SUM(ee.montant_du - ee.montant_paye), 0) as reste_a_payer
             FROM echeanciers_ecolages ee
             WHERE ee.annee_scolaire_id = ?",
            [$id]
        );
        
        return [
            'total_inscriptions' => $inscriptions['total'] ?? 0,
            'total_eleves_actifs' => $eleves['total'] ?? 0,
            'total_classes' => $classes['total'] ?? 0,
            'total_frais' => $finances['total_frais'] ?? 0,
            'total_paye' => $finances['total_paye'] ?? 0,
            'reste_a_payer' => $finances['reste_a_payer'] ?? 0
        ];
    }
    
    /**
     * Obtient les classes d'une année scolaire
     */
    public function getClasses($id) {
        return $this->query(
            "SELECT c.*, 
                    n.libelle as niveau_libelle,
                    s.libelle as serie_libelle,
                    COUNT(DISTINCT i.eleve_id) as nb_eleves
             FROM classes c
             LEFT JOIN niveaux n ON c.niveau_id = n.id
             LEFT JOIN series s ON c.serie_id = s.id
             LEFT JOIN inscriptions i ON c.id = i.classe_id AND i.statut = 'validee'
             WHERE c.annee_scolaire_id = ?
             GROUP BY c.id
             ORDER BY c.nom ASC",
            [$id]
        );
    }
    
    /**
     * Valide qu'il n'y a pas de chevauchement de dates
     */
    public function validateDates($dateDebut, $dateFin, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE (
                    (date_debut <= ? AND date_fin >= ?) OR
                    (date_debut <= ? AND date_fin >= ?) OR
                    (date_debut >= ? AND date_fin <= ?)
                )";
        
        $params = [$dateDebut, $dateDebut, $dateFin, $dateFin, $dateDebut, $dateFin];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['count'] == 0;
    }
}
