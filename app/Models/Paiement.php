<?php
/**
 * Modèle Paiement
 * Gestion des paiements avec journalisation automatique
 */

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../Helpers/Loggable.php';

class Paiement extends BaseModel {
    use Loggable;
    
    protected $table = 'paiements';
    protected $fillable = [
        'numero_paiement', 'facture_id', 'date_paiement', 'montant', 
        'mode_paiement_id', 'reference_paiement', 'remarque'
    ];
    
    /**
     * Crée un paiement avec journalisation
     * @param array $data Données du paiement
     * @return int|bool ID du paiement créé ou false
     */
    public function create($data) {
        $paiementId = parent::create($data);
        
        if ($paiementId) {
            // Récupérer le mode de paiement pour le log
            $modePaiement = $this->queryOne(
                "SELECT libelle FROM modes_paiement WHERE id = ?",
                [$data['mode_paiement_id']]
            );
            
            $this->logPaiement(
                $paiementId,
                $data['facture_id'],
                $data['montant'],
                $modePaiement['libelle'] ?? 'Inconnu'
            );
        }
        
        return $paiementId;
    }
    
    /**
     * Supprime un paiement avec journalisation (OPÉRATION CRITIQUE)
     * @param int $id ID du paiement
     * @return bool Succès de l'opération
     */
    public function delete($id) {
        $paiement = $this->find($id);
        
        if (!$paiement) {
            return false;
        }
        
        $success = parent::delete($id);
        
        if ($success) {
            $this->logDelete(
                'paiements',
                'paiement',
                $id,
                [
                    'numero_paiement' => $paiement['numero_paiement'],
                    'facture_id' => $paiement['facture_id'],
                    'montant' => $paiement['montant'],
                    'date_paiement' => $paiement['date_paiement']
                ]
            );
        }
        
        return $success;
    }
    
    /**
     * Obtient les paiements d'un élève (via factures)
     */
    public function getByEleve($eleveId, $anneeScolaireId = null) {
        $sql = "SELECT p.*, f.numero_facture, mp.libelle as mode_paiement
                FROM {$this->table} p
                INNER JOIN factures f ON p.facture_id = f.id
                LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
                WHERE f.eleve_id = ?";
        
        $params = [$eleveId];
        if ($anneeScolaireId) {
            $sql .= " AND f.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " ORDER BY p.date_paiement DESC";
        
        return $this->query($sql, $params);
    }

    /**
     * Obtient tous les paiements avec les détails complets (Filtres supportés)
     */
    public function getAllWithDetails($filters = []) {
        $sql = "SELECT p.*, 
                       f.numero_facture,
                       mp.libelle as mode_paiement,
                       e.id as eleve_id, e.nom as eleve_nom, e.prenom as eleve_prenom, e.matricule as eleve_matricule,
                       c.nom as classe_nom
                FROM {$this->table} p
                INNER JOIN factures f ON p.facture_id = f.id
                INNER JOIN eleves e ON f.eleve_id = e.id
                LEFT JOIN inscriptions i ON e.id = i.eleve_id AND i.statut = 'validee'
                LEFT JOIN classes c ON i.classe_id = c.id
                LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
                WHERE 1=1";
        
        $params = [];
        
        // Filtre par Date (Début/Fin) (basé sur date_paiement)
        if (!empty($filters['date_debut'])) {
            $sql .= " AND p.date_paiement >= ?";
            $params[] = $filters['date_debut'];
        }
        
        if (!empty($filters['date_fin'])) {
            $sql .= " AND p.date_paiement <= ?";
            $params[] = $filters['date_fin'];
        }
        
        // Filtre par Mode de Paiement
        if (!empty($filters['mode_paiement_id'])) {
            $sql .= " AND p.mode_paiement_id = ?";
            $params[] = $filters['mode_paiement_id'];
        }

        // Filtre par Élève (Nom/Prénom/Matricule)
        if (!empty($filters['search'])) {
            $search = "%{$filters['search']}%";
            $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY p.date_paiement DESC";
        
        return $this->query($sql, $params);
    }

    /**
     * Génère le rapport journalier de caisse (Totaux par mode)
     */
    public function getJournalCaisse($dateDebut, $dateFin) {
        $sql = "SELECT mp.libelle as mode, COUNT(p.id) as nombre, SUM(p.montant) as total
                FROM {$this->table} p
                LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
                WHERE p.date_paiement BETWEEN ? AND ?
                GROUP BY p.mode_paiement_id, mp.libelle
                ORDER BY total DESC";
        
        return $this->query($sql, [$dateDebut, $dateFin]);
    }

    /**
     * Obtient le total encaissé sur une période
     */
    public function getTotalEncaisse($dateDebut, $dateFin, $anneeScolaireId = null) {
        $sql = "SELECT SUM(p.montant) as total FROM {$this->table} p";
        $params = [$dateDebut, $dateFin];
        
        if ($anneeScolaireId) {
            $sql .= " INNER JOIN factures f ON p.facture_id = f.id
                     WHERE p.date_paiement BETWEEN ? AND ?
                     AND f.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        } else {
            $sql .= " WHERE p.date_paiement BETWEEN ? AND ?";
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }
}

