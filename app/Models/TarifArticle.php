<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle TarifArticle
 */

class TarifArticle extends BaseModel {
    protected $table = 'tarifs_articles';
    protected $fillable = [
        'article_id', 'annee_scolaire_id', 'prix_unitaire', 'taille'
    ];
    
    /**
     * Récupère le tarif d'un article pour une année donnée
     */
    public function getByArticleAndAnnee($articleId, $anneeId, $taille = null) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE article_id = ? AND annee_scolaire_id = ?";
        $params = [$articleId, $anneeId];
        
        if ($taille !== null) {
            $sql .= " AND taille = ?";
            $params[] = $taille;
        } else {
            $sql .= " AND taille IS NULL";
        }
        
        return $this->queryOne($sql, $params);
    }
    
    /**
     * Crée ou met à jour un tarif
     */
    public function createOrUpdate($data) {
        $existing = $this->getByArticleAndAnnee(
            $data['article_id'], 
            $data['annee_scolaire_id'],
            $data['taille'] ?? null
        );
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->create($data);
        }
    }
    
    /**
     * Récupère tous les tarifs pour une année donnée
     */
    public function getByAnnee($anneeId) {
        return $this->query(
            "SELECT ta.*, a.libelle as article_libelle, a.code as article_code
             FROM {$this->table} ta
             INNER JOIN articles a ON ta.article_id = a.id
             WHERE ta.annee_scolaire_id = ?
             ORDER BY a.libelle",
            [$anneeId]
        );
    }
}
