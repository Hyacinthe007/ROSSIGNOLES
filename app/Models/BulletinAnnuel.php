<?php
/**
 * Modèle BulletinAnnuel
 * Gère les bulletins de fin d'année scolaire
 */

require_once __DIR__ . '/BaseModel.php';

class BulletinAnnuel extends BaseModel {
    
    protected $table = 'bulletins_annuels';
    
    /**
     * Récupère le bulletin annuel d'un élève
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array|null Bulletin annuel ou null
     */
    public function getBulletinAnnuel($eleveId, $anneeScolaireId) {
        $sql = "SELECT 
                    ba.*,
                    e.matricule,
                    e.nom,
                    e.prenom,
                    e.date_naissance,
                    c.nom as classe,
                    c.code as code_classe,
                    n.libelle as niveau,
                    s.libelle as serie,
                    a.libelle as annee_scolaire,
                    a.date_debut,
                    a.date_fin
                FROM bulletins_annuels ba
                INNER JOIN eleves e ON ba.eleve_id = e.id
                INNER JOIN inscriptions i ON (
                    i.eleve_id = ba.eleve_id 
                    AND i.annee_scolaire_id = ba.annee_scolaire_id
                )
                INNER JOIN classes c ON i.classe_id = c.id
                INNER JOIN niveaux n ON c.niveau_id = n.id
                LEFT JOIN series s ON c.serie_id = s.id
                INNER JOIN annees_scolaires a ON ba.annee_scolaire_id = a.id
                WHERE ba.eleve_id = ? 
                  AND ba.annee_scolaire_id = ?";
        
        $result = $this->query($sql, [$eleveId, $anneeScolaireId]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Récupère tous les bulletins annuels d'une classe
     * 
     * @param int $classeId ID de la classe
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Liste des bulletins annuels
     */
    public function getBulletinsClasse($classeId, $anneeScolaireId) {
        $sql = "SELECT 
                    ba.*,
                    e.matricule,
                    e.nom,
                    e.prenom,
                    c.nom as classe
                FROM bulletins_annuels ba
                INNER JOIN eleves e ON ba.eleve_id = e.id
                INNER JOIN inscriptions i ON (
                    i.eleve_id = ba.eleve_id 
                    AND i.annee_scolaire_id = ba.annee_scolaire_id
                )
                INNER JOIN classes c ON i.classe_id = c.id
                WHERE i.classe_id = ? 
                  AND ba.annee_scolaire_id = ?
                ORDER BY ba.rang_annuel ASC, e.nom, e.prenom";
        
        return $this->query($sql, [$classeId, $anneeScolaireId]);
    }
    
    /**
     * Génère ou met à jour le bulletin annuel d'un élève
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return bool Succès de l'opération
     */
    public function genererBulletinAnnuel($eleveId, $anneeScolaireId) {
        try {
            // Appeler la procédure stockée
            $sql = "CALL generer_bulletin_annuel(?, ?)";
            $this->query($sql, [$eleveId, $anneeScolaireId]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur génération bulletin annuel: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifie si un élève peut recevoir son bulletin annuel
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array ['peut_recevoir' => bool, 'raison' => string]
     */
    public function peutRecevoirBulletin($eleveId, $anneeScolaireId) {
        $sql = "SELECT 
                    COUNT(*) as nb_bulletins_valides,
                    COALESCE(SUM(ech.montant_restant), 0) as total_impaye
                FROM bulletins b
                LEFT JOIN echeanciers_ecolages ech ON (
                    ech.eleve_id = b.eleve_id
                    AND ech.annee_scolaire_id = b.annee_scolaire_id
                    AND ech.statut IN ('retard', 'partiellement_paye', 'impaye_exclu', 'exclusion')
                )
                WHERE b.eleve_id = ?
                  AND b.annee_scolaire_id = ?
                  AND b.statut = 'valide'";
        
        $result = $this->query($sql, [$eleveId, $anneeScolaireId]);
        
        if (!$result || count($result) == 0) {
            return [
                'peut_recevoir' => false,
                'raison' => 'Aucun bulletin trimestriel validé'
            ];
        }
        
        $data = $result[0];
        
        // Vérifier qu'il y a au moins 3 bulletins validés
        if ($data['nb_bulletins_valides'] < 3) {
            return [
                'peut_recevoir' => false,
                'raison' => 'Tous les bulletins trimestriels doivent être validés (' . 
                           $data['nb_bulletins_valides'] . '/3)'
            ];
        }
        
        // Vérifier qu'il n'y a pas d'impayés
        if ($data['total_impaye'] > 0) {
            return [
                'peut_recevoir' => false,
                'raison' => 'Écolage impayé : ' . number_format($data['total_impaye'], 0, ',', ' ') . ' Ar'
            ];
        }
        
        return [
            'peut_recevoir' => true,
            'raison' => 'Toutes les conditions sont remplies'
        ];
    }
    
    /**
     * Récupère les statistiques des bulletins annuels
     * 
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array Statistiques
     */
    public function getStatistiques($anneeScolaireId) {
        $sql = "SELECT 
                    COUNT(*) as total_bulletins,
                    SUM(CASE WHEN decision_finale = 'admis' THEN 1 ELSE 0 END) as nb_admis,
                    SUM(CASE WHEN decision_finale = 'redouble' THEN 1 ELSE 0 END) as nb_redoublants,
                    SUM(CASE WHEN decision_finale = 'en_attente' THEN 1 ELSE 0 END) as nb_en_attente,
                    SUM(CASE WHEN bloque_par_impaye = 1 THEN 1 ELSE 0 END) as nb_bloques_impaye,
                    ROUND(AVG(moyenne_annuelle), 2) as moyenne_generale,
                    ROUND(MIN(moyenne_annuelle), 2) as moyenne_min,
                    ROUND(MAX(moyenne_annuelle), 2) as moyenne_max
                FROM bulletins_annuels
                WHERE annee_scolaire_id = ?";
        
        $result = $this->query($sql, [$anneeScolaireId]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Récupère le parcours complet d'un élève (tous ses bulletins annuels)
     * 
     * @param int $eleveId ID de l'élève
     * @return array Liste des bulletins annuels
     */
    public function getParcoursEleve($eleveId) {
        $sql = "SELECT 
                    ba.*,
                    a.libelle as annee_scolaire,
                    c.nom as classe,
                    n.libelle as niveau
                FROM bulletins_annuels ba
                INNER JOIN annees_scolaires a ON ba.annee_scolaire_id = a.id
                INNER JOIN inscriptions i ON (
                    i.eleve_id = ba.eleve_id 
                    AND i.annee_scolaire_id = ba.annee_scolaire_id
                )
                INNER JOIN classes c ON i.classe_id = c.id
                INNER JOIN niveaux n ON c.niveau_id = n.id
                WHERE ba.eleve_id = ?
                ORDER BY a.date_debut DESC";
        
        return $this->query($sql, [$eleveId]);
    }
}
