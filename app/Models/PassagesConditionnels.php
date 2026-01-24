<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle PassagesConditionnels
 * Correspond à la table 'passages_conditionnels'
 */

class PassagesConditionnels extends BaseModel {
    protected $table = 'passages_conditionnels';
    protected $fillable = [
        'parcours_id', 'eleve_id', 'annee_scolaire_id', 'type_condition',
        'matiere_id', 'description_condition', 'note_minimale_requise',
        'delai_limite', 'statut', 'note_obtenue', 'date_evaluation'
    ];
    
    /**
     * Récupère les conditions d'un parcours
     */
    public function getByParcours($parcoursId) {
        return $this->query(
            "SELECT pc.*, 
                    m.nom as matiere_nom
             FROM {$this->table} pc
             LEFT JOIN matieres m ON pc.matiere_id = m.id
             WHERE pc.parcours_id = ?
             ORDER BY pc.delai_limite ASC",
            [$parcoursId]
        );
    }
    
    /**
     * Récupère les conditions d'un élève
     */
    public function getByEleve($eleveId, $anneeScolaireId = null) {
        $where = "pc.eleve_id = ?";
        $params = [$eleveId];
        
        if ($anneeScolaireId) {
            $where .= " AND pc.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        return $this->query(
            "SELECT pc.*, 
                    m.nom as matiere_nom,
                    a.libelle as annee_libelle
             FROM {$this->table} pc
             LEFT JOIN matieres m ON pc.matiere_id = m.id
             INNER JOIN annees_scolaires a ON pc.annee_scolaire_id = a.id
             WHERE {$where}
             ORDER BY pc.delai_limite ASC",
            $params
        );
    }
    
    /**
     * Récupère les conditions en attente ou en cours
     */
    public function getEnCours($anneeScolaireId = null) {
        $where = "pc.statut IN ('en_attente', 'en_cours')";
        $params = [];
        
        if ($anneeScolaireId) {
            $where .= " AND pc.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        return $this->query(
            "SELECT pc.*, 
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                    m.nom as matiere_nom,
                    c.nom as classe_nom
             FROM {$this->table} pc
             INNER JOIN eleves e ON pc.eleve_id = e.id
             INNER JOIN parcours_eleves pe ON pc.parcours_id = pe.id
             INNER JOIN classes c ON pe.classe_id = c.id
             LEFT JOIN matieres m ON pc.matiere_id = m.id
             WHERE {$where}
             ORDER BY pc.delai_limite ASC",
            $params
        );
    }
    
    /**
     * Valide une condition
     */
    public function valider($id, $noteObtenue = null) {
        $data = [
            'statut' => 'validee',
            'date_evaluation' => date('Y-m-d')
        ];
        
        if ($noteObtenue !== null) {
            $data['note_obtenue'] = $noteObtenue;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Invalide une condition
     */
    public function invalider($id, $noteObtenue = null) {
        $data = [
            'statut' => 'non_validee',
            'date_evaluation' => date('Y-m-d')
        ];
        
        if ($noteObtenue !== null) {
            $data['note_obtenue'] = $noteObtenue;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Vérifie si toutes les conditions d'un parcours sont validées
     */
    public function toutesValidees($parcoursId) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN statut = 'validee' THEN 1 ELSE 0 END) as validees
             FROM {$this->table}
             WHERE parcours_id = ?",
            [$parcoursId]
        );
        
        return $result && $result['total'] > 0 && $result['total'] == $result['validees'];
    }
}
