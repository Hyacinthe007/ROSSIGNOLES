<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle PaieBulletin
 * Gestion des bulletins de paie mensuels
 */
class PaieBulletin extends BaseModel {
    protected $table = 'paie_bulletins';
    protected $fillable = [
        'personnel_id', 'periode', 'date_edition',
        'salaire_brut', 'montant_cnaps_sal', 'montant_ostie_sal',
        'base_imposable_irsa', 'irsa_brut', 'reduction_charges_famille', 'irsa_net',
        'total_retenues_diverses', 'salaire_net',
        'montant_cnaps_pat', 'montant_ostie_pat', 'montant_fmfp_pat',
        'cout_total_employeur', 'statut'
    ];
    
    /**
     * Récupère un bulletin par personnel et période
     */
    public function getByPersonnelPeriode(int $personnelId, string $periode) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} 
             WHERE personnel_id = ? AND periode = ?",
            [$personnelId, $periode]
        );
    }
    
    /**
     * Récupère tous les bulletins d'une période avec infos personnel
     */
    public function getByPeriode(string $periode) {
        return $this->query(
            "SELECT pb.*, 
                    p.matricule, p.nom, p.prenom, p.type_personnel, p.type_contrat
             FROM {$this->table} pb
             INNER JOIN personnels p ON pb.personnel_id = p.id
             WHERE pb.periode = ?
             ORDER BY p.nom ASC, p.prenom ASC",
            [$periode]
        );
    }
    
    /**
     * Récupère l'historique des bulletins d'un personnel
     */
    public function getHistoriquePersonnel(int $personnelId, int $limit = 12) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE personnel_id = ?
             ORDER BY periode DESC
             LIMIT ?",
            [$personnelId, $limit]
        );
    }
    
    /**
     * Récupère les statistiques d'une période
     */
    public function getStatistiquesPeriode(string $periode) {
        return $this->queryOne(
            "SELECT 
                COUNT(*) as nb_bulletins,
                SUM(salaire_brut) as total_brut,
                SUM(salaire_net) as total_net,
                SUM(cout_total_employeur) as total_cout_employeur,
                SUM(montant_cnaps_pat + montant_ostie_pat + montant_fmfp_pat) as total_charges_patronales,
                SUM(irsa_net) as total_irsa
             FROM {$this->table}
             WHERE periode = ?",
            [$periode]
        );
    }
    
    /**
     * Valide un bulletin
     */
    public function valider(int $id) {
        return $this->update($id, ['statut' => 'valide']);
    }
    
    /**
     * Marque un bulletin comme payé
     */
    public function marquerPaye(int $id) {
        return $this->update($id, ['statut' => 'paye']);
    }
}
