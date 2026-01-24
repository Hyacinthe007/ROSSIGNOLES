<?php
/**
 * Modèle EnseignantsClasses
 * Table: enseignants_classes
 * Gère l'affectation des enseignants (personnels) aux classes pour enseigner des matières
 */

require_once __DIR__ . '/BaseModel.php';

class EnseignantsClasses extends BaseModel {
    protected $table = 'enseignants_classes';
    protected $fillable = [
        'personnel_id', 'classe_id', 'matiere_id', 'annee_scolaire_id', 
        'volume_horaire', 'actif'
    ];
    
    /**
     * Récupère toutes les affectations, groupées par classe et matière
     */
    public function getAllAssignments($anneeScolaireId = null) {
        $sql = "SELECT ec.*, 
                       c.nom as classe_libelle, c.code as classe_code,
                       m.nom as matiere_libelle, m.code as matiere_code,
                       p.nom as enseignant_nom, p.prenom as enseignant_prenom,
                       p.matricule as enseignant_matricule
                FROM {$this->table} ec
                JOIN classes c ON ec.classe_id = c.id
                JOIN matieres m ON ec.matiere_id = m.id
                JOIN personnels p ON ec.personnel_id = p.id
                WHERE p.statut = 'actif'";
        
        $params = [];
        if ($anneeScolaireId) {
            $sql .= " AND ec.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        // Ordre par classe puis matière
        $sql .= " ORDER BY c.nom ASC, m.nom ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Récupère toutes les affectations d'un enseignant pour une année
     */
    public function getAffectationsEnseignant($personnelId, $anneeScolaireId = null) {
        $sql = "SELECT ec.*, 
                       c.nom as classe_nom, c.code as classe_code,
                       m.nom as matiere_nom, m.code as matiere_code,
                       n.libelle as niveau_nom,
                       s.libelle as serie_nom
                FROM {$this->table} ec
                JOIN classes c ON ec.classe_id = c.id
                JOIN matieres m ON ec.matiere_id = m.id
                JOIN niveaux n ON c.niveau_id = n.id
                LEFT JOIN series s ON c.serie_id = s.id
                WHERE ec.personnel_id = ?";
        
        $params = [$personnelId];
        
        if ($anneeScolaireId) {
            $sql .= " AND ec.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " ORDER BY c.nom ASC, m.nom ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Récupère tous les enseignants d'une classe
     */
    public function getEnseignantsClasse($classeId, $anneeScolaireId = null) {
        $sql = "SELECT ec.*, 
                       p.matricule, p.nom, p.prenom, p.email, p.telephone,
                       m.nom as matiere_nom, m.code as matiere_code
                FROM {$this->table} ec
                JOIN personnels p ON ec.personnel_id = p.id
                JOIN matieres m ON ec.matiere_id = m.id
                WHERE ec.classe_id = ? AND p.statut = 'actif'";
        
        $params = [$classeId];
        
        if ($anneeScolaireId) {
            $sql .= " AND ec.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " ORDER BY p.nom ASC, p.prenom ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Récupère l'enseignant d'une matière pour une classe
     */
    public function getEnseignantMatiere($classeId, $matiereId, $anneeScolaireId) {
        return $this->queryOne(
            "SELECT ec.*, 
                    p.matricule, p.nom, p.prenom, p.email, p.telephone
             FROM {$this->table} ec
             JOIN personnels p ON ec.personnel_id = p.id
             WHERE ec.classe_id = ? AND ec.matiere_id = ? 
                   AND ec.annee_scolaire_id = ? AND p.statut = 'actif'",
            [$classeId, $matiereId, $anneeScolaireId]
        );
    }
    
    /**
     * Vérifie si un enseignant est affecté à une classe/matière
     */
    public function isAffecte($personnelId, $classeId, $matiereId, $anneeScolaireId) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count FROM {$this->table}
             WHERE personnel_id = ? AND classe_id = ? 
                   AND matiere_id = ? AND annee_scolaire_id = ?",
            [$personnelId, $classeId, $matiereId, $anneeScolaireId]
        );
        return $result && $result['count'] > 0;
    }
    
    /**
     * Récupère les statistiques d'un enseignant
     */
    public function getStatistiquesEnseignant($personnelId, $anneeScolaireId) {
        return $this->queryOne(
            "SELECT 
                COUNT(DISTINCT ec.classe_id) as nb_classes,
                COUNT(DISTINCT ec.matiere_id) as nb_matieres,
                COUNT(*) as nb_affectations
             FROM {$this->table} ec
             WHERE ec.personnel_id = ? AND ec.annee_scolaire_id = ?",
            [$personnelId, $anneeScolaireId]
        );
    }
    
    /**
     * Supprime toutes les affectations d'un enseignant pour une année
     */
    public function supprimerAffectationsEnseignant($personnelId, $anneeScolaireId) {
        return $this->execute(
            "DELETE FROM {$this->table} 
             WHERE personnel_id = ? AND annee_scolaire_id = ?",
            [$personnelId, $anneeScolaireId]
        );
    }
    
    /**
     * Supprime toutes les affectations d'une classe
     */
    public function supprimerAffectationsClasse($classeId) {
        return $this->execute(
            "DELETE FROM {$this->table} WHERE classe_id = ?",
            [$classeId]
        );
    }
}
