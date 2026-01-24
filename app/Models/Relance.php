<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Relance
 */

class Relance extends BaseModel {
    protected $table = 'relances';
    protected $fillable = [
        'facture_id',
        'type',
        'date_relance',
        'message',
        'statut',
        'envoye_par',
        'envoye_le',
    ];
    
    /**
     * Obtient les détails complets d'une relance
     */
    public function getDetails($id) {
        return $this->queryOne(
            "SELECT r.*,
                    e.nom as eleve_nom,
                    e.prenom as eleve_prenom,
                    e.matricule as eleve_matricule,
                    f.montant_total,
                    f.montant_paye,
                    f.montant_restant,
                    u.username as envoye_par_nom
             FROM {$this->table} r
             INNER JOIN factures f ON r.facture_id = f.id
             INNER JOIN eleves e ON f.eleve_id = e.id
             LEFT JOIN users u ON r.envoye_par = u.id
             WHERE r.id = ?",
            [$id]
        );
    }
    
    /**
     * Obtient toutes les relances avec détails
     */
    public function getAllWithDetails($filters = [], $orderBy = 'r.date_envoi DESC') {
        $sql = "SELECT r.*,
                       e.nom as eleve_nom,
                       e.prenom as eleve_prenom,
                       e.matricule as eleve_matricule,
                       f.montant_total,
                       f.montant_paye,
                       f.montant_restant
                FROM {$this->table} r
                INNER JOIN factures f ON r.facture_id = f.id
                INNER JOIN eleves e ON f.eleve_id = e.id
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['facture_id'])) {
            $sql .= " AND r.facture_id = ?";
            $params[] = $filters['facture_id'];
        }

        if (isset($filters['type'])) {
            $sql .= " AND r.type = ?";
            $params[] = $filters['type'];
        }

        if (isset($filters['statut'])) {
            $sql .= " AND r.statut = ?";
            $params[] = $filters['statut'];
        }
        
        $sql .= " ORDER BY " . $orderBy;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtient l'historique des relances pour une facture
     */
    public function getByFacture($factureId) {
        return $this->query(
            "SELECT r.*, u.username as envoye_par_nom
             FROM {$this->table} r
             LEFT JOIN users u ON r.envoye_par = u.id
             WHERE r.facture_id = ?
             ORDER BY r.date_relance DESC",
            [$factureId]
        );
    }
}

