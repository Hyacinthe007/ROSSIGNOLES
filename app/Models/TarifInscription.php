<?php
/**
 * Modèle TarifInscription
 * Table: tarifs_inscription
 */

require_once __DIR__ . '/BaseModel.php';

class TarifInscription extends BaseModel {
    protected $table = 'tarifs_inscription';
    protected $fillable = [
        'annee_scolaire_id', 'niveau_id', 'frais_inscription', 
        'ecolage_mensuel', 'mois_debut_annee', 'frais_supplementaires', 
        'description_frais_supp', 'date_debut_inscription', 
        'date_fin_inscription', 'actif'
    ];
    
    /**
     * Récupère le tarif pour une année et un niveau donnés
     */
    public function getByAnneeAndNiveau($anneeId, $niveauId) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} 
             WHERE annee_scolaire_id = ? AND niveau_id = ? AND actif = 1",
            [$anneeId, $niveauId]
        );
    }

    /**
     * Récupère tous les tarifs avec les libellés correspondant
     */
    public function getAllDetails() {
        return $this->query(
            "SELECT ti.*, n.libelle as niveau_libelle, n.ordre as niveau_ordre, 
                    c.libelle as cycle_libelle, c.code as cycle_code, c.ordre as cycle_ordre,
                    a.libelle as annee_libelle
             FROM {$this->table} ti
             JOIN niveaux n ON ti.niveau_id = n.id
             LEFT JOIN cycles c ON n.cycle_id = c.id
             JOIN annees_scolaires a ON ti.annee_scolaire_id = a.id
             ORDER BY a.date_debut DESC, c.ordre ASC, n.ordre ASC"
        );
    }
}
