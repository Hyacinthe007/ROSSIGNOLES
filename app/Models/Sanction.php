<?php
/**
 * Modèle Sanction
 * Gestion des sanctions disciplinaires avec journalisation automatique
 */

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../Helpers/Loggable.php';

class Sanction extends BaseModel {
    use Loggable;
    
    protected $table = 'sanctions';
    protected $fillable = [
        'eleve_id', 'classe_id', 'annee_scolaire_id', 'type_sanction_id', 'date_sanction',
        'date_debut', 'date_fin', 'duree_jours', 'motif',
        'description_incident', 'mesures_educatives', 'emis_par',
        'valide_par', 'date_validation', 'statut', 'parent_notifie',
        'date_notification'
    ];
    
    /**
     * Crée une sanction avec journalisation
     * @param array $data Données de la sanction
     * @return int|bool ID de la sanction créée ou false
     */
    public function create($data) {
        $sanctionId = parent::create($data);
        
        if ($sanctionId) {
            // Récupérer le type de sanction pour le log
            $typeSanction = $this->queryOne(
                "SELECT libelle FROM types_sanctions WHERE id = ?",
                [$data['type_sanction_id']]
            );
            
            $this->logSanction(
                $sanctionId,
                $data['eleve_id'],
                $typeSanction['libelle'] ?? 'Sanction',
                $data['motif']
            );
        }
        
        return $sanctionId;
    }
    
    /**
     * Met à jour une sanction avec journalisation
     * @param int $id ID de la sanction
     * @param array $newData Nouvelles données
     * @return bool Succès de l'opération
     */
    public function update($id, $newData) {
        // Récupérer l'ancienne sanction
        $oldData = $this->find($id);
        
        if (!$oldData) {
            return false;
        }
        
        $success = parent::update($id, $newData);
        
        if ($success) {
            // Logger la validation de la sanction
            if (isset($newData['statut']) && $newData['statut'] == 'validee' && $oldData['statut'] != 'validee') {
                $this->logValidate(
                    'discipline',
                    'sanction',
                    $id,
                    "Validation de la sanction pour l'élève #{$oldData['eleve_id']}"
                );
            }
            
            // Logger l'annulation de la sanction
            if (isset($newData['statut']) && $newData['statut'] == 'annulee' && $oldData['statut'] != 'annulee') {
                $this->logActivity(
                    'cancel',
                    'discipline',
                    "Annulation de la sanction pour l'élève #{$oldData['eleve_id']} - Motif: " . ($newData['motif_annulation'] ?? 'Non spécifié'),
                    'sanction',
                    $id
                );
            }
            
            // Logger les changements généraux
            $this->logUpdate('discipline', 'sanction', $id, $oldData, $newData);
        }
        
        return $success;
    }
    
    /**
     * Supprime une sanction avec journalisation (OPÉRATION CRITIQUE)
     * @param int $id ID de la sanction
     * @return bool Succès de l'opération
     */
    public function delete($id) {
        $sanction = $this->find($id);
        
        if (!$sanction) {
            return false;
        }
        
        $success = parent::delete($id);
        
        if ($success) {
            $this->logDelete(
                'discipline',
                'sanction',
                $id,
                [
                    'eleve_id' => $sanction['eleve_id'],
                    'type_sanction_id' => $sanction['type_sanction_id'],
                    'motif' => $sanction['motif'],
                    'date_sanction' => $sanction['date_sanction']
                ]
            );
        }
        
        return $success;
    }
    
    /**
     * Obtient les sanctions d'un élève
     * @param int $eleveId ID de l'élève
     * @param int|null $anneeScolaireId ID de l'année scolaire
     * @return array Liste des sanctions
     */
    public function getByEleve($eleveId, $anneeScolaireId = null) {
        $sql = "SELECT s.*, 
                       ts.libelle as type_sanction, ts.gravite,
                       c.nom as classe_nom,
                       p1.nom as emis_par_nom, p1.prenom as emis_par_prenom,
                       p2.nom as valide_par_nom, p2.prenom as valide_par_prenom
                FROM {$this->table} s
                INNER JOIN types_sanctions ts ON s.type_sanction_id = ts.id
                INNER JOIN classes c ON s.classe_id = c.id
                LEFT JOIN personnels p1 ON s.emis_par = p1.id
                LEFT JOIN personnels p2 ON s.valide_par = p2.id
                WHERE s.eleve_id = ?";
                
        $params = [$eleveId];
        
        if ($anneeScolaireId) {
            $sql .= " AND s.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " ORDER BY s.date_sanction DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtient les sanctions d'une classe
     * @param int $classeId ID de la classe
     * @param int|null $anneeScolaireId ID de l'année scolaire
     * @return array Liste des sanctions
     */
    public function getByClasse($classeId, $anneeScolaireId = null) {
        $sql = "SELECT s.*, 
                       e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                       ts.libelle as type_sanction, ts.gravite
                FROM {$this->table} s
                INNER JOIN eleves e ON s.eleve_id = e.id
                INNER JOIN types_sanctions ts ON s.type_sanction_id = ts.id
                WHERE s.classe_id = ?";
                
        $params = [$classeId];
        
        if ($anneeScolaireId) {
            $sql .= " AND s.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " ORDER BY s.date_sanction DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Obtient les sanctions en attente de validation
     * @param int|null $anneeScolaireId ID de l'année scolaire
     * @return array Liste des sanctions
     */
    public function getEnAttente($anneeScolaireId = null) {
        $sql = "SELECT s.*, 
                       e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                       c.nom as classe_nom,
                       ts.libelle as type_sanction, ts.gravite,
                       p.nom as emis_par_nom, p.prenom as emis_par_prenom
                FROM {$this->table} s
                INNER JOIN eleves e ON s.eleve_id = e.id
                INNER JOIN classes c ON s.classe_id = c.id
                INNER JOIN types_sanctions ts ON s.type_sanction_id = ts.id
                LEFT JOIN personnels p ON s.emis_par = p.id
                WHERE s.statut = 'en_attente'";
                
        $params = [];
        
        if ($anneeScolaireId) {
            $sql .= " AND s.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        $sql .= " ORDER BY s.date_sanction DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre de sanctions d'un élève par gravité
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Statistiques des sanctions
     */
    public function countByGravite($eleveId, $anneeScolaireId) {
        return $this->query(
            "SELECT ts.gravite, COUNT(*) as nombre
             FROM {$this->table} s
             INNER JOIN types_sanctions ts ON s.type_sanction_id = ts.id
             WHERE s.eleve_id = ? 
             AND s.annee_scolaire_id = ?
             AND s.statut IN ('validee', 'executee')
             GROUP BY ts.gravite
             ORDER BY ts.gravite DESC",
            [$eleveId, $anneeScolaireId]
        );
    }
}
