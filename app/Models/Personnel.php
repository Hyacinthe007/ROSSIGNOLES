<?php
/**
 * Modèle Personnel
 * Correspond à la table 'personnels' (table de base pour tous les employés)
 */

require_once __DIR__ . '/BaseModel.php';

class Personnel extends BaseModel {
    protected $table = 'personnels';
    protected $fillable = [
        'matricule', 'nom', 'prenom', 'date_naissance', 'lieu_naissance', 
        'situation_matrimoniale', 'nb_enfants', 'sexe', 
        'cin', 'numero_cnaps', 'iban',
        'telephone', 'email', 'adresse', 'photo', 
        'date_embauche', 'date_fin_contrat', 'statut', 
        'type_contrat', 'type_personnel',
        'diplome', 'annee_obtention_diplome',
        'urgence_nom', 'urgence_telephone', 'urgence_lien'
    ];
    
    /**
     * Soft delete : marque comme supprimé
     */
    public function softDelete($id) {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }
    
    /**
     * Récupère les personnels actifs (non supprimés)
     */
    public function getActifs($typePersonnel = null) {
        $sql = "SELECT * FROM {$this->table} WHERE statut = 'actif' AND deleted_at IS NULL";
        $params = [];
        
        if ($typePersonnel) {
            $sql .= " AND type_personnel = ?";
            $params[] = $typePersonnel;
        }
        
        $sql .= " ORDER BY nom ASC, prenom ASC";
        return $this->query($sql, $params);
    }
    
    /**
     * Obtient les détails complets d'un membre du personnel
     */
    public function getDetails($id) {
        return $this->find($id);
    }
    
    /**
     * Récupère tous les enseignants actifs avec leurs détails
     */
    public function getEnseignants($actifOnly = true) {
        $where = "p.type_personnel = 'enseignant' AND p.deleted_at IS NULL";
        if ($actifOnly) {
            $where .= " AND p.statut = 'actif'";
        }
        
        return $this->query(
            "SELECT p.*, pe.specialite, pe.grade, pe.anciennete_annees
             FROM {$this->table} p
             LEFT JOIN personnels_enseignants pe ON p.id = pe.personnel_id
             WHERE {$where}
             ORDER BY p.nom ASC, p.prenom ASC"
        );
    }
    
    /**
     * Récupère tous les personnels administratifs actifs avec leurs détails
     */
    public function getAdministratifs($actifOnly = true) {
        $where = "p.type_personnel = 'administratif' AND p.deleted_at IS NULL";
        if ($actifOnly) {
            $where .= " AND p.statut = 'actif'";
        }
        
        return $this->query(
            "SELECT p.*, pa.departement, pa.niveau_acces, po.libelle as poste_libelle
             FROM {$this->table} p
             LEFT JOIN personnels_administratifs pa ON p.id = pa.personnel_id
             LEFT JOIN postes_administratifs po ON pa.poste_id = po.id
             WHERE {$where}
             ORDER BY p.nom ASC, p.prenom ASC"
        );
    }
    
    /**
     * Récupère un personnel avec ses détails spécifiques (enseignant ou administratif)
     */
    public function getDetailsComplets($id) {
        $personnel = $this->find($id);
        if (!$personnel) {
            return null;
        }
        
        // Charger les détails spécifiques selon le type
        if ($personnel['type_personnel'] === 'enseignant') {
            $details = $this->queryOne(
                "SELECT * FROM personnels_enseignants WHERE personnel_id = ?",
                [$id]
            );
            if ($details) {
                unset($details['id']);
                $personnel = array_merge($personnel, $details);
            }
        } elseif ($personnel['type_personnel'] === 'administratif') {
            $details = $this->queryOne(
                "SELECT pa.*, po.libelle as poste_libelle, po.code as poste_code
                 FROM personnels_administratifs pa
                 LEFT JOIN postes_administratifs po ON pa.poste_id = po.id
                 WHERE pa.personnel_id = ?",
                [$id]
            );
            if ($details) {
                unset($details['id']);
                $personnel = array_merge($personnel, $details);
            }
        }
        
        return $personnel;
    }
    
    /**
     * Filtre les personnels par type
     */
    public function getByType($type, $actifOnly = true) {
        $where = "type_personnel = ? AND deleted_at IS NULL";
        $params = [$type];
        
        if ($actifOnly) {
            $where .= " AND statut = 'actif'";
        }
        
        return $this->query(
            "SELECT * FROM {$this->table} WHERE {$where} ORDER BY nom ASC, prenom ASC",
            $params
        );
    }
}

