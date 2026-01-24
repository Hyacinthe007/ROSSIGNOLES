<?php
/**
 * Modèle ConseilClasse
 * Gestion des conseils de classe par période
 */

require_once __DIR__ . '/BaseModel.php';

class ConseilClasse extends BaseModel {
    protected $table = 'conseils_classe';
    protected $fillable = [
        'classe_id', 'periode_id', 'annee_scolaire_id', 'date_conseil',
        'heure_debut', 'heure_fin', 'president_conseil', 'secretaire',
        'ordre_du_jour', 'pv_url', 'moyenne_classe', 'taux_reussite',
        'nb_felicitations', 'nb_encouragements', 'nb_avertissements_travail',
        'nb_avertissements_conduite', 'appreciation_generale', 'points_forts',
        'points_amelioration', 'recommendations', 'statut', 'date_cloture'
    ];
    
    /**
     * Récupère les décisions d'un conseil de classe
     * @param int $conseilId ID du conseil
     * @return array Liste des décisions avec infos élèves
     */
    public function getDecisions($conseilId) {
        return $this->query(
            "SELECT dc.*, 
                    e.matricule, e.nom, e.prenom, e.photo,
                    b.moyenne_generale, b.rang
             FROM decisions_conseil dc
             JOIN eleves e ON dc.eleve_id = e.id
             LEFT JOIN bulletins b ON dc.bulletin_id = b.id
             WHERE dc.conseil_classe_id = ?
             ORDER BY e.nom, e.prenom",
            [$conseilId]
        );
    }
    
    /**
     * Récupère les détails complets d'un conseil
     * @param int $id ID du conseil
     * @return array|null Détails du conseil
     */
    public function getDetails($id) {
        return $this->queryOne(
            "SELECT cc.*, 
                    c.nom as classe_nom, c.code as classe_code,
                    p.nom as periode_nom, p.numero as periode_numero,
                    a.libelle as annee_libelle,
                    pers1.nom as president_nom, pers1.prenom as president_prenom,
                    pers2.nom as secretaire_nom, pers2.prenom as secretaire_prenom
             FROM {$this->table} cc
             JOIN classes c ON cc.classe_id = c.id
             JOIN periodes p ON cc.periode_id = p.id
             JOIN annees_scolaires a ON cc.annee_scolaire_id = a.id
             LEFT JOIN personnels pers1 ON cc.president_conseil = pers1.id
             LEFT JOIN personnels pers2 ON cc.secretaire = pers2.id
             WHERE cc.id = ?",
            [$id]
        );
    }
    
    /**
     * Récupère les conseils de classe d'une année scolaire
     * @param int $anneeScolaireId ID de l'année scolaire
     * @param string|null $statut Statut du conseil (prevu, en_cours, cloture, annule)
     * @return array Liste des conseils
     */
    public function getByAnnee($anneeScolaireId, $statut = null) {
        $where = "cc.annee_scolaire_id = ?";
        $params = [$anneeScolaireId];
        
        if ($statut) {
            $where .= " AND cc.statut = ?";
            $params[] = $statut;
        }
        
        return $this->query(
            "SELECT cc.*, 
                    c.nom as classe_nom, c.code as classe_code,
                    p.nom as periode_nom, p.numero as periode_numero,
                    pers1.nom as president_nom, pers1.prenom as president_prenom,
                    pers2.nom as secretaire_nom, pers2.prenom as secretaire_prenom
             FROM {$this->table} cc
             INNER JOIN classes c ON cc.classe_id = c.id
             INNER JOIN periodes p ON cc.periode_id = p.id
             LEFT JOIN personnels pers1 ON cc.president_conseil = pers1.id
             LEFT JOIN personnels pers2 ON cc.secretaire = pers2.id
             WHERE {$where}
             ORDER BY cc.date_conseil DESC, p.numero ASC",
            $params
        );
    }
    
    /**
     * Récupère les conseils d'une classe
     * @param int $classeId ID de la classe
     * @param int|null $anneeScolaireId ID de l'année scolaire (optionnel)
     * @return array Liste des conseils
     */
    public function getByClasse($classeId, $anneeScolaireId = null) {
        $where = "cc.classe_id = ?";
        $params = [$classeId];
        
        if ($anneeScolaireId) {
            $where .= " AND cc.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        return $this->query(
            "SELECT cc.*, 
                    p.nom as periode_nom, p.numero as periode_numero,
                    a.libelle as annee_libelle
             FROM {$this->table} cc
             INNER JOIN periodes p ON cc.periode_id = p.id
             INNER JOIN annees_scolaires a ON cc.annee_scolaire_id = a.id
             WHERE {$where}
             ORDER BY p.numero ASC",
            $params
        );
    }
    
    /**
     * Récupère les conseils d'une période
     * @param int $periodeId ID de la période
     * @param int|null $anneeScolaireId ID de l'année scolaire
     * @return array Liste des conseils
     */
    public function getByPeriode($periodeId, $anneeScolaireId = null) {
        $where = "cc.periode_id = ?";
        $params = [$periodeId];
        
        if ($anneeScolaireId) {
            $where .= " AND cc.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        }
        
        return $this->query(
            "SELECT cc.*, 
                    c.nom as classe_nom, c.code as classe_code
             FROM {$this->table} cc
             INNER JOIN classes c ON cc.classe_id = c.id
             WHERE {$where}
             ORDER BY c.nom ASC",
            $params
        );
    }
    
    /**
     * Clôture un conseil de classe
     * @param int $id ID du conseil
     * @return bool Succès de l'opération
     */
    public function cloturer($id) {
        return $this->update($id, [
            'statut' => 'cloture',
            'date_cloture' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Calcule et met à jour les statistiques d'un conseil
     * @param int $id ID du conseil
     * @return array|null Statistiques calculées
     */
    public function calculerStatistiques($id) {
        // Récupérer les décisions du conseil
        $stats = $this->queryOne(
            "SELECT 
                COUNT(DISTINCT dc.eleve_id) as nb_eleves,
                AVG(b.moyenne_generale) as moyenne_classe,
                SUM(CASE WHEN b.moyenne_generale >= 10 THEN 1 ELSE 0 END) as nb_reussis,
                SUM(CASE WHEN dc.distinction = 'felicitations' THEN 1 ELSE 0 END) as nb_felicitations,
                SUM(CASE WHEN dc.distinction = 'encouragements' THEN 1 ELSE 0 END) as nb_encouragements,
                SUM(CASE WHEN dc.avertissement = 'travail' OR dc.avertissement = 'travail_et_conduite' THEN 1 ELSE 0 END) as nb_avert_travail,
                SUM(CASE WHEN dc.avertissement = 'conduite' OR dc.avertissement = 'travail_et_conduite' THEN 1 ELSE 0 END) as nb_avert_conduite
             FROM decisions_conseil dc
             LEFT JOIN bulletins b ON dc.bulletin_id = b.id
             WHERE dc.conseil_classe_id = ?",
            [$id]
        );
        
        if ($stats && $stats['nb_eleves'] > 0) {
            $tauxReussite = ($stats['nb_reussis'] / $stats['nb_eleves']) * 100;
            
            $this->update($id, [
                'moyenne_classe' => round($stats['moyenne_classe'], 2),
                'taux_reussite' => round($tauxReussite, 2),
                'nb_felicitations' => $stats['nb_felicitations'],
                'nb_encouragements' => $stats['nb_encouragements'],
                'nb_avertissements_travail' => $stats['nb_avert_travail'],
                'nb_avertissements_conduite' => $stats['nb_avert_conduite']
            ]);
        }
        
        return $stats;
    }
    
    /**
     * Vérifie si un conseil existe pour une classe et une période
     * @param int $classeId ID de la classe
     * @param int $periodeId ID de la période
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return bool True si le conseil existe
     */
    public function exists($classeId, $periodeId, $anneeScolaireId) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count 
             FROM {$this->table} 
             WHERE classe_id = ? AND periode_id = ? AND annee_scolaire_id = ?",
            [$classeId, $periodeId, $anneeScolaireId]
        );
        return $result && $result['count'] > 0;
    }
}
