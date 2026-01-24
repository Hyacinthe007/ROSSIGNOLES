<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle TypeFraisNiveau
 * Gère les montants des frais par niveau (table types_frais_niveaux)
 */

class TypeFraisNiveau extends BaseModel {
    protected $table = 'types_frais_niveaux';
    protected $fillable = [
        'type_frais_id', 'niveau_id', 'montant'
    ];
    
    /**
     * Récupère les détails (Libellé frais, Nom niveau)
     */
    public function getDetails() {
        return $this->query(
            "SELECT tfn.*, tf.libelle as type_frais_libelle, n.libelle as niveau_libelle
             FROM {$this->table} tfn
             JOIN types_frais tf ON tfn.type_frais_id = tf.id
             JOIN niveaux n ON tfn.niveau_id = n.id
             ORDER BY n.ordre, tf.libelle"
        );
    }
    
    /**
     * Récupère le montant d'un type de frais pour un niveau
     */
    public function getMontant($typeFraisId, $niveauId) {
        $result = $this->queryOne(
            "SELECT montant FROM {$this->table} 
             WHERE type_frais_id = ? AND niveau_id = ?",
            [$typeFraisId, $niveauId]
        );
        return $result ? $result['montant'] : 0.00;
    }
    
    /**
     * Récupère tous les frais pour un niveau
     */
    public function getFraisParNiveau($niveauId) {
        return $this->query(
            "SELECT tfn.*, tf.libelle, tf.categorie
             FROM {$this->table} tfn
             JOIN types_frais tf ON tfn.type_frais_id = tf.id
             WHERE tfn.niveau_id = ? AND tf.actif = 1
             ORDER BY tf.categorie ASC, tf.libelle ASC",
            [$niveauId]
        );
    }
    
    /**
     * Récupère tous les niveaux pour un type de frais
     */
    public function getNiveauxParTypeFrais($typeFraisId) {
        return $this->query(
            "SELECT tfn.*, n.code, n.libelle as niveau_nom, n.cycle
             FROM {$this->table} tfn
             JOIN niveaux n ON tfn.niveau_id = n.id
             WHERE tfn.type_frais_id = ? AND n.actif = 1
             ORDER BY n.ordre ASC",
            [$typeFraisId]
        );
    }
    
    /**
     * Met à jour ou crée un tarif
     */
    public function upsertMontant($typeFraisId, $niveauId, $montant) {
        // Vérifier si existe
        $existing = $this->queryOne(
            "SELECT id FROM {$this->table} 
             WHERE type_frais_id = ? AND niveau_id = ?",
            [$typeFraisId, $niveauId]
        );
        
        if ($existing) {
            // Mettre à jour
            return $this->update($existing['id'], ['montant' => $montant]);
        } else {
            // Créer
            return $this->create([
                'type_frais_id' => $typeFraisId,
                'niveau_id' => $niveauId,
                'montant' => $montant
            ]);
        }
    }
    
    /**
     * Récupère le total des frais obligatoires pour un niveau
     */
    public function getTotalFraisObligatoires($niveauId, $categorie = null) {
        $sql = "SELECT SUM(tfn.montant) as total
                FROM {$this->table} tfn
                JOIN types_frais tf ON tfn.type_frais_id = tf.id
                WHERE tfn.niveau_id = ? AND tf.actif = 1";
        
        $params = [$niveauId];
        
        if ($categorie) {
            $sql .= " AND tf.categorie = ?";
            $params[] = $categorie;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result ? ($result['total'] ?? 0.00) : 0.00;
    }
}
