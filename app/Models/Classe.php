<?php
/**
 * Modèle Classe
 */

require_once __DIR__ . '/BaseModel.php';

class Classe extends BaseModel {
    protected $table = 'classes';
    protected $fillable = [
        'nom', 'code', 'niveau_id', 'serie_id', 'professeur_principal_id', 
        'annee_scolaire_id', 'capacite', 'seuil_admission', 'effectif_actuel', 
        'salle', 'statut'
    ];
    
    /**
     * Récupère les classes actives (non supprimées)
     */
    public function getActives($anneeScolaireId = null) {
        $sql = "SELECT * FROM {$this->table} WHERE statut = 'actif' AND deleted_at IS NULL";
        $params = [];
        
        if ($anneeScolaireId) {
            $sql .= " AND annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " ORDER BY nom ASC";
        return $this->query($sql, $params);
    }
    
    /**
     * Obtient les détails complets d'une classe
     */
    public function getDetails($id) {
        return $this->queryOne(
            "SELECT c.*, n.libelle as niveau_nom, s.libelle as serie_nom,
                    an.libelle as annee_scolaire
             FROM {$this->table} c
             LEFT JOIN niveaux n ON c.niveau_id = n.id
             LEFT JOIN series s ON c.serie_id = s.id
             LEFT JOIN annees_scolaires an ON c.annee_scolaire_id = an.id
             WHERE c.id = ?",
            [$id]
        );
    }
    
    /**
     * Obtient les élèves d'une classe
     */
    public function getEleves($classeId, $anneeScolaireId = null) {
        $sql = "SELECT e.*, i.date_inscription, i.statut as inscription_statut,
                       i.type_inscription, i.statut_dossier
                FROM eleves e
                INNER JOIN inscriptions i ON e.id = i.eleve_id
                WHERE i.classe_id = ? AND i.statut = 'validee'";
        
        $params = [$classeId];
        
        if ($anneeScolaireId) {
            $sql .= " AND i.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " ORDER BY e.nom, e.prenom";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Récupère toutes les classes avec leurs informations de niveau et cycle
     * Triées par ordre de cycle et de niveau
     */
    public function getAllWithCycleAndNiveau() {
        return $this->query(
            "SELECT c.*, n.libelle as niveau_libelle, cy.libelle as cycle_libelle
             FROM {$this->table} c
             JOIN niveaux n ON c.niveau_id = n.id
             JOIN cycles cy ON n.cycle_id = cy.id
             WHERE c.statut = 'actif' AND c.deleted_at IS NULL
             ORDER BY cy.ordre ASC, n.ordre ASC, c.nom ASC"
        );
    }
    
    /**
     * Récupère toutes les classes avec niveau, cycle et nombre d'élèves
     * @param int $anneeId ID de l'année scolaire
     * @return array Liste des classes avec détails
     */
    public function getAllWithNiveauAndCount($anneeId) {
        return $this->query(
            "SELECT c.*, n.libelle as niveau_nom, n.ordre as niveau_ordre, cy.libelle as cycle_nom,
             (SELECT COUNT(*) FROM inscriptions i WHERE i.classe_id = c.id AND i.annee_scolaire_id = ?) as nb_eleves
             FROM {$this->table} c
             INNER JOIN niveaux n ON c.niveau_id = n.id
             LEFT JOIN cycles cy ON n.cycle_id = cy.id
             WHERE c.statut = 'actif' AND c.deleted_at IS NULL 
             ORDER BY n.ordre ASC, c.nom ASC",
            [$anneeId]
        );
    }

    /**
     * Récupère les détails d'une classe avec son niveau
     */
    public function getDetailsWithNiveau($id) {
        return $this->queryOne(
            "SELECT c.*, n.ordre as niveau_ordre, n.libelle as niveau_nom
             FROM {$this->table} c
             INNER JOIN niveaux n ON c.niveau_id = n.id
             WHERE c.id = ?",
            [$id]
        );
    }

    /**
     * Récupère la classe précédente d'un élève
     */
    public function getPreviousByEleve($eleveId, $anneeActiveId) {
        return $this->queryOne(
            "SELECT c.*, n.libelle as niveau_nom, n.ordre as niveau_ordre
             FROM {$this->table} c
             INNER JOIN inscriptions i ON c.id = i.classe_id
             INNER JOIN niveaux n ON c.niveau_id = n.id
             WHERE i.eleve_id = ? 
             AND i.annee_scolaire_id < ?
             ORDER BY i.annee_scolaire_id DESC
             LIMIT 1",
            [$eleveId, $anneeActiveId]
        );
    }

    /**
     * Suggère une classe basée sur l'ordre du niveau
     */
    public function getSuggestedByNiveauOrder($niveauOrdre) {
        return $this->queryOne(
            "SELECT c.*, n.libelle as niveau_nom, n.ordre as niveau_ordre
             FROM {$this->table} c
             INNER JOIN niveaux n ON c.niveau_id = n.id
             WHERE n.ordre = ? 
             AND c.statut = 'actif' 
             AND c.deleted_at IS NULL
             ORDER BY c.nom ASC
             LIMIT 1",
            [$niveauOrdre]
        );
    }

    /**
     * Récupère toutes les classes avec détails et effectif pour une année donnée
     */
    public function getAllWithDetailsAndEffectif($anneeId) {
        return $this->query(
            "SELECT c.*, 
                    n.libelle as niveau_nom,
                    s.libelle as serie_nom,
                    an.libelle as annee_scolaire_libelle,
                    (SELECT COUNT(*) FROM inscriptions i WHERE i.classe_id = c.id AND i.statut = 'validee') as effectif
             FROM {$this->table} c
             LEFT JOIN niveaux n ON c.niveau_id = n.id
             LEFT JOIN series s ON c.serie_id = s.id
             LEFT JOIN annees_scolaires an ON c.annee_scolaire_id = an.id
             WHERE c.annee_scolaire_id = ? AND c.statut = 'actif' AND c.deleted_at IS NULL
             ORDER BY n.ordre ASC, c.nom ASC",
            [$anneeId]
        );
    }

    /**
     * Récupère les associations de classes avec filtres
     */
    public function getAssociationsWithFilters($filters = []) {
        $sql = "SELECT c.id as classe_id, c.nom as classe_nom, c.code as classe_code,
                       c.niveau_id, c.serie_id, c.annee_scolaire_id,
                       n.id as niveau_id, n.libelle as niveau_nom,
                       s.id as serie_id_join, s.libelle as serie_nom,
                       an.id as annee_id, an.libelle as annee_scolaire_libelle
                FROM {$this->table} c
                LEFT JOIN niveaux n ON c.niveau_id = n.id
                LEFT JOIN series s ON c.serie_id = s.id
                LEFT JOIN annees_scolaires an ON c.annee_scolaire_id = an.id
                WHERE c.statut = 'actif' AND c.deleted_at IS NULL";
        
        $params = [];
        
        if (!empty($filters['show_unassociated'])) {
            $sql .= " AND c.niveau_id IS NULL";
        }
        
        if (!empty($filters['niveau_id'])) {
            $sql .= " AND c.niveau_id = ?";
            $params[] = $filters['niveau_id'];
        }
        
        if (!empty($filters['serie_id'])) {
            $sql .= " AND c.serie_id = ?";
            $params[] = $filters['serie_id'];
        }
        
        if (!empty($filters['annee_scolaire_id'])) {
            $sql .= " AND c.annee_scolaire_id = ?";
            $params[] = $filters['annee_scolaire_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.nom LIKE ? OR c.code LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY c.nom ASC";
        
        return $this->query($sql, $params);
    }

    /**
     * Calcule les statistiques des associations
     */
    public function getAssociationStats() {
        $stats = [
            'total_classes' => 0,
            'classes_associees' => 0,
            'classes_non_associees' => 0,
            'repartition_niveaux' => []
        ];
        
        // Total des classes actives
        $totalResult = $this->queryOne("SELECT COUNT(*) as total FROM {$this->table} WHERE statut = 'actif' AND deleted_at IS NULL");
        $stats['total_classes'] = (int)($totalResult['total'] ?? 0);
        
        // Classes associées (avec niveau)
        $associeesResult = $this->queryOne("SELECT COUNT(*) as total FROM {$this->table} WHERE statut = 'actif' AND deleted_at IS NULL AND niveau_id IS NOT NULL");
        $stats['classes_associees'] = (int)($associeesResult['total'] ?? 0);
        
        // Classes non associées
        $stats['classes_non_associees'] = $stats['total_classes'] - $stats['classes_associees'];
        
        // Répartition par niveau
        $stats['repartition_niveaux'] = $this->query(
            "SELECT n.libelle as niveau_nom, 
                    COUNT(c.id) as nombre_classes
             FROM {$this->table} c
             INNER JOIN niveaux n ON c.niveau_id = n.id
             WHERE c.statut = 'actif' AND c.deleted_at IS NULL
             GROUP BY n.id, n.libelle
             ORDER BY n.ordre ASC"
        );
        
        return $stats;
    }

    /**
     * Récupère les élèves d'une classe avec filtrage sur le statut de paiement
     */
    public function getElevesWithPaymentStatus($classeId = null, $anneeId = null) {
        $paymentFilter = "";
        $params = [];

        if ($anneeId) {
            $paymentFilter = " AND NOT EXISTS (
                SELECT 1 FROM echeanciers_ecolages ee 
                WHERE ee.eleve_id = e.id 
                AND ee.annee_scolaire_id = ? 
                AND ee.statut IN ('retard', 'exclusion') 
                AND ee.montant_restant > 0
            )";
        }

        $sql = "SELECT e.*, i.date_inscription, i.statut as inscription_statut, i.type_inscription,
                       c.id as classe_id, c.nom as classe_nom, c.code as classe_code
                FROM eleves e
                INNER JOIN inscriptions i ON e.id = i.eleve_id
                LEFT JOIN classes c ON i.classe_id = c.id
                WHERE i.statut = 'validee'";

        if ($classeId) {
            $sql .= " AND i.classe_id = ?";
            $params[] = $classeId;
        }

        if ($anneeId) {
            $sql .= " AND i.annee_scolaire_id = ?";
            $params[] = $anneeId;
            $params[] = $anneeId; // Pour le paymentFilter
        } elseif (!$classeId) {
            $sql .= " AND 1=0";
        }

        if ($classeId) {
            $sql .= " ORDER BY e.nom ASC, e.prenom ASC";
        } else {
            $sql .= " ORDER BY c.nom ASC, e.nom ASC, e.prenom ASC";
        }

        return $this->query($sql . $paymentFilter, $params);
    }
}

