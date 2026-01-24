<?php
/**
 * Modèle EcolagesPayes
 * Correspond à la table 'ecolages_payes'
 */

require_once __DIR__ . '/BaseModel.php';

class EcolagesPayes extends BaseModel {
    protected $table = 'ecolages_payes';
    protected $fillable = [
        'eleve_id', 'annee_scolaire_id', 'mois', 'annee',
        'ligne_facture_id', 'paiement_id', 'montant'
    ];
    
    /**
     * Récupère les écolages payés par un élève
     */
    public function getByEleve($eleveId, $anneeScolaireId = null) {
        $where = "ep.eleve_id = ?";
        $params = [$eleveId];
        
        if ($anneeScolaireId) {
            $where .= " AND ep.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        return $this->query(
            "SELECT ep.*, 
                    p.numero_paiement, p.date_paiement, p.mode_paiement_id,
                    mp.libelle as mode_paiement
             FROM {$this->table} ep
             INNER JOIN paiements p ON ep.paiement_id = p.id
             INNER JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
             WHERE {$where}
             ORDER BY ep.annee DESC, ep.mois DESC",
            $params
        );
    }
    
    /**
     * Vérifie si un mois est payé
     */
    public function isMoisPaye($eleveId, $anneeScolaireId, $mois, $annee) {
        $result = $this->queryOne(
            "SELECT id FROM {$this->table} 
             WHERE eleve_id = ? 
             AND annee_scolaire_id = ? 
             AND mois = ? 
             AND annee = ?",
            [$eleveId, $anneeScolaireId, $mois, $annee]
        );
        return $result !== null;
    }
    
    /**
     * Récupère les mois payés pour une année scolaire
     */
    public function getMoisPayes($eleveId, $anneeScolaireId) {
        $result = $this->query(
            "SELECT mois, annee, montant, created_at as date_paiement
             FROM {$this->table}
             WHERE eleve_id = ? AND annee_scolaire_id = ?
             ORDER BY annee ASC, mois ASC",
            [$eleveId, $anneeScolaireId]
        );
        
        $moisPayes = [];
        foreach ($result as $row) {
            $moisPayes[] = [
                'mois' => (int)$row['mois'],
                'annee' => (int)$row['annee'],
                'montant' => (float)$row['montant'],
                'date_paiement' => $row['date_paiement']
            ];
        }
        
        return $moisPayes;
    }
    
    /**
     * Calcule le total payé par un élève
     */
    public function getTotalPaye($eleveId, $anneeScolaireId) {
        $result = $this->queryOne(
            "SELECT SUM(montant) as total
             FROM {$this->table}
             WHERE eleve_id = ? AND annee_scolaire_id = ?",
            [$eleveId, $anneeScolaireId]
        );
        
        return (float)($result['total'] ?? 0);
    }
    
    /**
     * Récupère les statistiques de paiement pour une année
     */
    public function getStatistiquesAnnee($anneeScolaireId) {
        return $this->queryOne(
            "SELECT 
                COUNT(DISTINCT eleve_id) as nb_eleves_payants,
                COUNT(*) as nb_paiements,
                SUM(montant) as total_collecte,
                AVG(montant) as montant_moyen
             FROM {$this->table}
             WHERE annee_scolaire_id = ?",
            [$anneeScolaireId]
        );
    }
}
