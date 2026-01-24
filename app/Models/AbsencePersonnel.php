<?php
/**
 * Modèle pour les absences du personnel
 */

require_once APP_PATH . '/Models/BaseModel.php';

class AbsencePersonnel extends BaseModel {
    protected $table = 'absences_personnels';
    protected $fillable = [
        'personnel_id', 'annee_scolaire_id', 'type_absence', 'date_debut', 'date_fin',
        'nb_jours', 'nb_heures', 'motif', 'piece_justificative', 'statut',
        'demande_par', 'date_demande', 'valide_par', 'date_validation', 'motif_refus',
        'remplace_par', 'commentaire_remplacement', 'deduit_salaire', 'montant_deduction'
    ];
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Récupère toutes les absences d'un membre du personnel
     */
    public function getByPersonnel($personnelId) {
        return $this->query(
            "SELECT ap.*, p.nom as personnel_nom, p.prenom as personnel_prenom
             FROM absences_personnels ap
             JOIN personnels p ON ap.personnel_id = p.id
             WHERE ap.personnel_id = ?
             ORDER BY ap.date_debut DESC",
            [$personnelId]
        );
    }
    
    /**
     * Récupère une absence avec les détails du personnel
     */
    public function getWithDetails($id) {
        return $this->queryOne(
            "SELECT ap.*, 
                    p.nom as personnel_nom, p.prenom as personnel_prenom, p.matricule,
                    rp.nom as remplace_nom, rp.prenom as remplace_prenom,
                    CONCAT(demandeur.nom, ' ', demandeur.prenom) as demande_par_nom,
                    CONCAT(valideur.nom, ' ', valideur.prenom) as valide_par_nom
             FROM absences_personnels ap
             JOIN personnels p ON ap.personnel_id = p.id
             LEFT JOIN personnels rp ON ap.remplace_par = rp.id
             LEFT JOIN personnels demandeur ON ap.demande_par = demandeur.id
             LEFT JOIN personnels valideur ON ap.valide_par = valideur.id
             WHERE ap.id = ?",
            [$id]
        );
    }
    
    /**
     * Récupère les absences avec filtres
     */
    public function getAllWithFilters($filters = []) {
        $where = [];
        $params = [];
        
        if (!empty($filters['personnel_id'])) {
            $where[] = "ap.personnel_id = ?";
            $params[] = $filters['personnel_id'];
        }
        
        if (!empty($filters['type_absence'])) {
            $where[] = "ap.type_absence = ?";
            $params[] = $filters['type_absence'];
        }
        
        if (!empty($filters['statut'])) {
            $where[] = "ap.statut = ?";
            $params[] = $filters['statut'];
        }
        
        if (!empty($filters['date_debut'])) {
            $where[] = "ap.date_debut >= ?";
            $params[] = $filters['date_debut'];
        }
        
        if (!empty($filters['date_fin'])) {
            $where[] = "ap.date_fin <= ?";
            $params[] = $filters['date_fin'];
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        return $this->query(
            "SELECT ap.*, 
                    p.nom as personnel_nom, p.prenom as personnel_prenom, p.matricule,
                    rp.nom as remplace_nom, rp.prenom as remplace_prenom
             FROM absences_personnels ap
             JOIN personnels p ON ap.personnel_id = p.id
             LEFT JOIN personnels rp ON ap.remplace_par = rp.id
             {$whereClause}
             ORDER BY ap.date_debut DESC",
            $params
        );
    }
}