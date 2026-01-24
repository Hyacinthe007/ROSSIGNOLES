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
}

