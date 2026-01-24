<?php
/**
 * Modèle Article
 * Gestion des articles scolaires (Logo, Tee-shirt, Carnet, etc.)
 */

require_once __DIR__ . '/BaseModel.php';

class Article extends BaseModel {
    protected $table = 'articles';
    protected $fillable = [
        'code', 'libelle', 'type_article', 'obligatoire', 
        'cycles_concernes', 'actif'
    ];
    
    /**
     * Récupère tous les articles avec leurs tarifs pour une année donnée
     */
    public function getAllWithTarifs($anneeId = null) {
        if (!$anneeId) {
            require_once __DIR__ . '/AnneeScolaire.php';
            $anneeModel = new AnneeScolaire();
            $anneeActive = $anneeModel->getActive();
            $anneeId = $anneeActive['id'] ?? null;
        }
        
        if (!$anneeId) {
            return $this->all(['actif' => 1]);
        }
        
        return $this->query(
            "SELECT a.*, 
                    ta.id as tarif_id,
                    ta.prix_unitaire,
                    ta.taille
             FROM {$this->table} a
             LEFT JOIN tarifs_articles ta ON a.id = ta.article_id 
                 AND ta.annee_scolaire_id = ?
             WHERE a.actif = 1
             ORDER BY a.type_article, a.libelle",
            [$anneeId]
        );
    }
    
    /**
     * Récupère un article avec son tarif pour une année donnée
     */
    public function getWithTarif($articleId, $anneeId) {
        return $this->queryOne(
            "SELECT a.*, 
                    ta.id as tarif_id,
                    ta.prix_unitaire,
                    ta.taille
             FROM {$this->table} a
             LEFT JOIN tarifs_articles ta ON a.id = ta.article_id 
                 AND ta.annee_scolaire_id = ?
             WHERE a.id = ?",
            [$anneeId, $articleId]
        );
    }
    
    /**
     * Récupère les articles disponibles pour un niveau donné
     */
    public function getByNiveau($niveauId, $anneeId) {
        return $this->query(
            "SELECT a.*, 
                    ta.prix_unitaire,
                    ta.taille
             FROM {$this->table} a
             INNER JOIN tarifs_articles ta ON a.id = ta.article_id 
                 AND ta.annee_scolaire_id = ?
             LEFT JOIN articles_niveaux an ON a.id = an.article_id 
                 AND an.annee_scolaire_id = ?
             WHERE a.actif = 1
               AND (an.niveau_id = ? OR an.niveau_id IS NULL)
               AND an.actif = 1
             ORDER BY a.type_article, a.libelle",
            [$anneeId, $anneeId, $niveauId]
        );
    }
    
    /**
     * Vérifie si un code article existe déjà
     */
    public function codeExists($code, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE code = ?";
        $params = [$code];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['count'] > 0;
    }
}
