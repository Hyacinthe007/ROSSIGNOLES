<?php
/**
 * Modèle ParcoursEleves
 * Correspond à la table 'parcours_eleves'
 */

require_once __DIR__ . '/BaseModel.php';

class ParcoursEleves extends BaseModel {
    protected $table = 'parcours_eleves';
    protected $fillable = [
        'eleve_id', 'annee_scolaire_id', 'classe_id', 'inscription_id',
        'resultat', 'mention', 'moyenne_annuelle', 'rang_classe',
        'classe_suivante_id', 'date_debut', 'date_fin', 'saisi_par'
    ];
    
    /**
     * Récupère le parcours complet d'un élève
     */
    public function getByEleve($eleveId) {
        return $this->query(
            "SELECT pe.*, 
                    a.libelle as annee_libelle,
                    c.nom as classe_nom, c.code as classe_code,
                    cs.nom as classe_suivante_nom,
                    n.libelle as niveau_nom
             FROM {$this->table} pe
             INNER JOIN annees_scolaires a ON pe.annee_scolaire_id = a.id
             INNER JOIN classes c ON pe.classe_id = c.id
             INNER JOIN niveaux n ON c.niveau_id = n.id
             LEFT JOIN classes cs ON pe.classe_suivante_id = cs.id
             WHERE pe.eleve_id = ?
             ORDER BY a.date_debut DESC",
            [$eleveId]
        );
    }
    
    /**
     * Récupère le parcours d'une année spécifique
     */
    public function getByAnnee($eleveId, $anneeScolaireId) {
        return $this->queryOne(
            "SELECT pe.*, 
                    c.nom as classe_nom,
                    cs.nom as classe_suivante_nom
             FROM {$this->table} pe
             INNER JOIN classes c ON pe.classe_id = c.id
             LEFT JOIN classes cs ON pe.classe_suivante_id = cs.id
             WHERE pe.eleve_id = ? AND pe.annee_scolaire_id = ?",
            [$eleveId, $anneeScolaireId]
        );
    }
    
    /**
     * Récupère les élèves d'une classe avec leur parcours
     */
    public function getByClasse($classeId, $anneeScolaireId) {
        return $this->query(
            "SELECT pe.*, 
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                    cs.nom as classe_suivante_nom
             FROM {$this->table} pe
             INNER JOIN eleves e ON pe.eleve_id = e.id
             LEFT JOIN classes cs ON pe.classe_suivante_id = cs.id
             WHERE pe.classe_id = ? AND pe.annee_scolaire_id = ?
             ORDER BY pe.rang_classe ASC, e.nom ASC, e.prenom ASC",
            [$classeId, $anneeScolaireId]
        );
    }
    
    /**
     * Récupère les statistiques de parcours pour une classe
     */
    public function getStatistiquesClasse($classeId, $anneeScolaireId) {
        return $this->queryOne(
            "SELECT 
                COUNT(*) as total_eleves,
                AVG(moyenne_annuelle) as moyenne_classe,
                SUM(CASE WHEN resultat = 'admis' OR resultat = 'admis_avec_mention' THEN 1 ELSE 0 END) as nb_admis,
                SUM(CASE WHEN resultat = 'redouble' THEN 1 ELSE 0 END) as nb_redoublants,
                SUM(CASE WHEN mention = 'excellence' THEN 1 ELSE 0 END) as nb_excellence,
                SUM(CASE WHEN mention = 'tres_bien' THEN 1 ELSE 0 END) as nb_tres_bien,
                SUM(CASE WHEN mention = 'bien' THEN 1 ELSE 0 END) as nb_bien,
                SUM(CASE WHEN mention = 'assez_bien' THEN 1 ELSE 0 END) as nb_assez_bien
             FROM {$this->table}
             WHERE classe_id = ? AND annee_scolaire_id = ?",
            [$classeId, $anneeScolaireId]
        );
    }
    
    /**
     * Finalise le parcours d'un élève pour une année
     */
    public function finaliser($id, $resultat, $mention = null, $classeSuivanteId = null) {
        $data = [
            'resultat' => $resultat,
            'date_fin' => date('Y-m-d')
        ];
        
        if ($mention) {
            $data['mention'] = $mention;
        }
        
        if ($classeSuivanteId) {
            $data['classe_suivante_id'] = $classeSuivanteId;
        }
        
        return $this->update($id, $data);
    }
}
