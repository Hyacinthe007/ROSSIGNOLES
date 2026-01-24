<?php
/**
 * Modèle InscriptionArticle
 * Gestion des articles sélectionnés lors de l'inscription
 */

require_once __DIR__ . '/BaseModel.php';

class InscriptionArticle extends BaseModel {
    protected $table = 'inscriptions_articles';
    protected $fillable = [
        'inscription_id', 'article_id', 'quantite', 'prix_unitaire', 
        'montant_total', 'paye', 'facture_id'
    ];
    
    /**
     * Récupère les articles d'une inscription
     */
    public function getByInscription($inscriptionId) {
        return $this->query(
            "SELECT ia.*, a.code, a.libelle, a.type_article
             FROM {$this->table} ia
             INNER JOIN articles a ON ia.article_id = a.id
             WHERE ia.inscription_id = ?
             ORDER BY a.libelle",
            [$inscriptionId]
        );
    }
    
    /**
     * Ajoute un article à une inscription
     */
    public function addToInscription($inscriptionId, $articleId, $prixUnitaire, $quantite = 1, $paye = false) {
        $montantTotal = $prixUnitaire * $quantite;
        
        return $this->create([
            'inscription_id' => $inscriptionId,
            'article_id' => $articleId,
            'quantite' => $quantite,
            'prix_unitaire' => $prixUnitaire,
            'montant_total' => $montantTotal,
            'paye' => $paye ? 1 : 0
        ]);
    }
    
    /**
     * Calcule le montant total des articles d'une inscription
     */
    public function getTotalMontant($inscriptionId, $payeOnly = false) {
        $sql = "SELECT COALESCE(SUM(montant_total), 0) as total
                FROM {$this->table}
                WHERE inscription_id = ?";
        $params = [$inscriptionId];
        
        if ($payeOnly) {
            $sql .= " AND paye = 1";
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }
    
    /**
     * Marque les articles comme payés
     */
    public function markAsPaid($inscriptionId, $factureId = null) {
        $data = ['paye' => 1];
        if ($factureId) {
            $data['facture_id'] = $factureId;
        }
        
        return $this->query(
            "UPDATE {$this->table} 
             SET paye = ?, facture_id = ?, updated_at = NOW()
             WHERE inscription_id = ?",
            [1, $factureId, $inscriptionId]
        );
    }
    
    /**
     * Supprime un article d'une inscription
     */
    public function removeFromInscription($inscriptionId, $articleId) {
        return $this->query(
            "DELETE FROM {$this->table} 
             WHERE inscription_id = ? AND article_id = ?",
            [$inscriptionId, $articleId]
        );
    }
    
    /**
     * Récupère les articles non payés d'un élève
     */
    public function getUnpaidByEleve($eleveId, $anneeId) {
        return $this->query(
            "SELECT ia.*, a.code, a.libelle, a.type_article, i.date_inscription
             FROM {$this->table} ia
             INNER JOIN inscriptions i ON ia.inscription_id = i.id
             INNER JOIN articles a ON ia.article_id = a.id
             WHERE i.eleve_id = ? 
               AND i.annee_scolaire_id = ?
               AND ia.paye = 0
             ORDER BY i.date_inscription DESC, a.libelle",
            [$eleveId, $anneeId]
        );
    }
}
