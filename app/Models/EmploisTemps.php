<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle EmploisTemps
 * Table: emplois_temps
 * Gère les emplois du temps des classes
 */

class EmploisTemps extends BaseModel {
    protected $table = 'emplois_temps';
    protected $fillable = [
        'classe_id', 'matiere_id', 'personnel_id', 'annee_scolaire_id',
        'jour_semaine', 'heure_debut', 'heure_fin', 'remarque', 'actif'
    ];
    
    /**
     * Récupère l'emploi du temps d'une classe
     */
    public function getEmploiTempsClasse($classeId, $anneeScolaireId) {
        return $this->query(
            "SELECT et.*, 
                    m.nom as matiere_nom, m.code as matiere_code, m.couleur,
                    p.nom as enseignant_nom, p.prenom as enseignant_prenom,
                    CONCAT(p.nom, ' ', p.prenom) as enseignant,
                    c.nom as classe_nom, c.code as classe_code
             FROM {$this->table} et
             JOIN matieres m ON et.matiere_id = m.id
             JOIN classes c ON et.classe_id = c.id
             LEFT JOIN personnels p ON et.personnel_id = p.id
             WHERE et.classe_id = ? AND et.annee_scolaire_id = ? AND et.actif = 1
             ORDER BY 
                FIELD(et.jour_semaine, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'),
                et.heure_debut ASC",
            [$classeId, $anneeScolaireId]
        );
    }
    
    /**
     * Récupère l'emploi du temps d'un enseignant
     */
    public function getEmploiTempsEnseignant($personnelId, $anneeScolaireId) {
        return $this->query(
            "SELECT et.*, 
                    m.nom as matiere_nom, m.code as matiere_code, m.couleur,
                    c.nom as classe_nom, c.code as classe_code,
                    n.libelle as niveau_nom
             FROM {$this->table} et
             JOIN matieres m ON et.matiere_id = m.id
             JOIN classes c ON et.classe_id = c.id
             JOIN niveaux n ON c.niveau_id = n.id
             WHERE et.personnel_id = ? AND et.annee_scolaire_id = ? AND et.actif = 1
             ORDER BY 
                FIELD(et.jour_semaine, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'),
                et.heure_debut ASC",
            [$personnelId, $anneeScolaireId]
        );
    }
    
    /**
     * Récupère les cours d'un jour pour une classe
     */
    public function getCoursJour($classeId, $jourSemaine, $anneeScolaireId) {
        return $this->query(
            "SELECT et.*, 
                    m.nom as matiere_nom, m.code as matiere_code, m.couleur,
                    p.nom as enseignant_nom, p.prenom as enseignant_prenom
             FROM {$this->table} et
             JOIN matieres m ON et.matiere_id = m.id
             LEFT JOIN personnels p ON et.personnel_id = p.id
             WHERE et.classe_id = ? AND et.jour_semaine = ? 
                   AND et.annee_scolaire_id = ? AND et.actif = 1
             ORDER BY et.heure_debut ASC",
            [$classeId, $jourSemaine, $anneeScolaireId]
        );
    }
    
    /**
     * Vérifie les conflits d'horaire pour un enseignant
     */
    public function hasConflitEnseignant($personnelId, $jourSemaine, $heureDebut, $heureFin, $anneeScolaireId, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}
                WHERE personnel_id = ? 
                  AND jour_semaine = ?
                  AND annee_scolaire_id = ?
                  AND actif = 1
                  AND (
                    (heure_debut < ? AND heure_fin > ?) OR
                    (heure_debut >= ? AND heure_debut < ?) OR
                    (heure_fin > ? AND heure_fin <= ?)
                  )";
        
        $params = [$personnelId, $jourSemaine, $anneeScolaireId, $heureFin, $heureDebut, $heureDebut, $heureFin, $heureDebut, $heureFin];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Vérifie les conflits d'horaire pour une classe
     */
    public function hasConflitClasse($classeId, $jourSemaine, $heureDebut, $heureFin, $anneeScolaireId, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}
                WHERE classe_id = ? 
                  AND jour_semaine = ?
                  AND annee_scolaire_id = ?
                  AND actif = 1
                  AND (
                    (heure_debut < ? AND heure_fin > ?) OR
                    (heure_debut >= ? AND heure_debut < ?) OR
                    (heure_fin > ? AND heure_fin <= ?)
                  )";
        
        $params = [$classeId, $jourSemaine, $anneeScolaireId, $heureFin, $heureDebut, $heureDebut, $heureFin, $heureDebut, $heureFin];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Récupère les statistiques d'un enseignant
     */
    public function getStatistiquesEnseignant($personnelId, $anneeScolaireId) {
        return $this->queryOne(
            "SELECT 
                COUNT(*) as nb_cours,
                COUNT(DISTINCT classe_id) as nb_classes,
                COUNT(DISTINCT matiere_id) as nb_matieres,
                SUM(TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut)) / 3600) as total_heures
             FROM {$this->table}
             WHERE personnel_id = ? AND annee_scolaire_id = ? AND actif = 1",
            [$personnelId, $anneeScolaireId]
        );
    }
    
    /**
     * Récupère l'emploi du temps en format matriciel
     */
    public function getEmploiTempsMatriciel($anneeScolaireId, $classeId = null, $personnelId = null) {
        $sql = "SELECT et.*, 
                       m.nom as matiere_nom, m.code as matiere_code, m.couleur,
                       c.nom as classe_nom, c.code as classe_code,
                       p.nom as enseignant_nom, p.prenom as enseignant_prenom,
                       CONCAT(p.nom, ' ', p.prenom) as enseignant
                FROM {$this->table} et
                JOIN matieres m ON et.matiere_id = m.id
                JOIN classes c ON et.classe_id = c.id
                LEFT JOIN personnels p ON et.personnel_id = p.id
                WHERE et.annee_scolaire_id = ? AND et.actif = 1";
        
        $params = [$anneeScolaireId];
        
        if ($classeId) {
            $sql .= " AND et.classe_id = ?";
            $params[] = $classeId;
        }
        
        if ($personnelId) {
            $sql .= " AND et.personnel_id = ?";
            $params[] = $personnelId;
        }
        
        $sql .= " ORDER BY 
                  FIELD(et.jour_semaine, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'),
                  et.heure_debut ASC";
        
        return $this->query($sql, $params);
    }
}
