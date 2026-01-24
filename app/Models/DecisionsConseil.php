<?php
/**
 * Modèle DecisionsConseil
 * Correspond à la table 'decisions_conseil'
 */

require_once __DIR__ . '/BaseModel.php';

class DecisionsConseil extends BaseModel {
    protected $table = 'decisions_conseil';
    protected $fillable = [
        'conseil_classe_id', 'eleve_id', 'bulletin_id', 'distinction',
        'avertissement', 'appreciation_conseil', 'conseils_eleve',
        'decision_passage'
    ];
    
    /**
     * Récupère les décisions d'un conseil
     */
    public function getByConseil($conseilId) {
        return $this->query(
            "SELECT dc.*, 
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                    b.moyenne_generale, b.rang
             FROM {$this->table} dc
             INNER JOIN eleves e ON dc.eleve_id = e.id
             LEFT JOIN bulletins b ON dc.bulletin_id = b.id
             WHERE dc.conseil_classe_id = ?
             ORDER BY b.rang ASC, e.nom ASC, e.prenom ASC",
            [$conseilId]
        );
    }
    
    /**
     * Récupère les décisions pour un élève
     */
    public function getByEleve($eleveId, $anneeScolaireId = null) {
        $where = "dc.eleve_id = ?";
        $params = [$eleveId];
        
        if ($anneeScolaireId) {
            $where .= " AND cc.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        return $this->query(
            "SELECT dc.*, 
                    cc.date_conseil,
                    p.nom as periode_nom,
                    c.nom as classe_nom
             FROM {$this->table} dc
             INNER JOIN conseils_classe cc ON dc.conseil_classe_id = cc.id
             INNER JOIN periodes p ON cc.periode_id = p.id
             INNER JOIN classes c ON cc.classe_id = c.id
             WHERE {$where}
             ORDER BY cc.date_conseil DESC",
            $params
        );
    }
    
    /**
     * Récupère les élèves avec une distinction spécifique
     */
    public function getByDistinction($conseilId, $distinction) {
        return $this->query(
            "SELECT dc.*, 
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                    b.moyenne_generale
             FROM {$this->table} dc
             INNER JOIN eleves e ON dc.eleve_id = e.id
             LEFT JOIN bulletins b ON dc.bulletin_id = b.id
             WHERE dc.conseil_classe_id = ? AND dc.distinction = ?
             ORDER BY b.moyenne_generale DESC",
            [$conseilId, $distinction]
        );
    }
    
    /**
     * Récupère les élèves avec un avertissement
     */
    public function getByAvertissement($conseilId, $avertissement) {
        return $this->query(
            "SELECT dc.*, 
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                    b.moyenne_generale
             FROM {$this->table} dc
             INNER JOIN eleves e ON dc.eleve_id = e.id
             LEFT JOIN bulletins b ON dc.bulletin_id = b.id
             WHERE dc.conseil_classe_id = ? 
             AND (dc.avertissement = ? OR dc.avertissement = 'travail_et_conduite')
             ORDER BY e.nom ASC, e.prenom ASC",
            [$conseilId, $avertissement]
        );
    }
}
