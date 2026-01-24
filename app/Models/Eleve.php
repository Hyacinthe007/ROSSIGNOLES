<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Eleve
 */

class Eleve extends BaseModel {
    protected $table = 'eleves';
    protected $fillable = [
        'matricule', 'nom', 'prenom', 'date_naissance', 
        'lieu_naissance', 'sexe', 'photo', 'statut', 'date_inscription'
    ];
    
    /**
     * Obtient les informations complètes d'un élève
     */
    public function getDetails($id) {
        return $this->queryOne(
            "SELECT e.*, c.nom as classe_actuelle, c.code as classe_code
             FROM {$this->table} e
             LEFT JOIN inscriptions i ON e.id = i.eleve_id AND i.statut = 'validee'
             LEFT JOIN classes c ON i.classe_id = c.id
             WHERE e.id = ?
             ORDER BY i.created_at DESC LIMIT 1",
            [$id]
        );
    }
    
    /**
     * Obtient la classe actuelle d'un élève
     */
    public function getCurrentClasse($eleveId, $anneeScolaireId = null) {
        $sql = "SELECT i.*, c.nom as classe_nom, c.code as classe_code, 
                       n.libelle as niveau_nom
                FROM inscriptions i
                INNER JOIN classes c ON i.classe_id = c.id
                LEFT JOIN niveaux n ON c.niveau_id = n.id
                WHERE i.eleve_id = ? AND i.statut = 'validee'";
        
        $params = [$eleveId];
        
        if ($anneeScolaireId) {
            $sql .= " AND i.annee_scolaire_id = ?";
            $params[] = $anneeScolaireId;
        } else {
            // Si pas d'année précisée, on prend la dernière inscription active
            $sql .= " ORDER BY i.created_at DESC LIMIT 1";
        }
        
        return $this->queryOne($sql, $params);
    }
    
    /**
     * Obtient les parents d'un élève
     */
    public function getParents($eleveId) {
        return $this->query(
            "SELECT p.*, ep.lien_parente
             FROM parents p
             INNER JOIN eleves_parents ep ON p.id = ep.parent_id
             WHERE ep.eleve_id = ?",
            [$eleveId]
        );
    }
    
    /**
     * Obtient l'échéancier de paiement d'un élève
     */
    public function getEcheancierPaiement($eleveId, $anneeScolaireId = null) {
        if (!$anneeScolaireId) {
            // Récupérer l'inscription active pour avoir l'année
            $inscription = $this->queryOne(
                "SELECT annee_scolaire_id FROM inscriptions 
                 WHERE eleve_id = ? AND statut = 'validee'
                 ORDER BY created_at DESC LIMIT 1",
                [$eleveId]
            );
            $anneeScolaireId = $inscription ? $inscription['annee_scolaire_id'] : null;
        }
        
        if (!$anneeScolaireId) {
            return [];
        }
        
        return $this->query(
            "SELECT 
                ee.*,
                ee.mois_libelle as nom_mois,
                ee.montant_restant as reste_a_payer
             FROM echeanciers_ecolages ee
             WHERE ee.eleve_id = ? AND ee.annee_scolaire_id = ?
             ORDER BY ee.annee, ee.mois",
            [$eleveId, $anneeScolaireId]
        );
    }
    
    /**
     * Obtient la situation financière globale d'un élève
     */
    /**
     * Obtient la situation financière globale d'un élève pour une année
     */
    public function getSituationFinanciere($eleveId, $anneeScolaireId = null) {
        if (!$anneeScolaireId) {
            $inscription = $this->getInscriptionActive($eleveId);
            $anneeScolaireId = $inscription['annee_scolaire_id'] ?? null;
        }

        if (!$anneeScolaireId) {
            return [
                'total_a_payer' => 0,
                'total_paye' => 0,
                'total_reste' => 0,
                'taux_paiement' => 0,
                'nb_echeances_impayees' => 0
            ];
        }

        // 1. Total Ecolage (Echeancier)
        $ecolage = $this->queryOne(
            "SELECT SUM(montant_du) as total, 
                    SUM(CASE WHEN statut IN ('retard', 'impaye', 'partiel') THEN 1 ELSE 0 END) as nb_impayees 
             FROM echeanciers_ecolages 
             WHERE eleve_id = ? AND annee_scolaire_id = ?",
            [$eleveId, $anneeScolaireId]
        );
        $totalEcolage = $ecolage['total'] ?? 0;

        // 2. Total Droit Inscription
        // On cherche les lignes de facture de type 'inscription' liées à cet élève et cette année
        $droit = $this->queryOne(
            "SELECT SUM(lf.montant) as total 
             FROM lignes_factures lf
             JOIN factures f ON lf.facture_id = f.id
             JOIN types_frais tf ON lf.type_frais_id = tf.id
             WHERE f.eleve_id = ? AND f.annee_scolaire_id = ? AND f.statut != 'annulee'
             AND tf.categorie = 'inscription'",
            [$eleveId, $anneeScolaireId]
        );
        $totalDroit = $droit['total'] ?? 0;

        $totalAPayer = $totalEcolage + $totalDroit;

        // 3. Total Payé (Somme des paiements valides pour cette année)
        // Les paiements sont liés aux factures, qui sont liées à l'année
        $paiements = $this->queryOne(
            "SELECT SUM(p.montant) as total
             FROM paiements p
             JOIN factures f ON p.facture_id = f.id
             WHERE f.eleve_id = ? AND f.annee_scolaire_id = ? AND f.statut != 'annulee'",
            [$eleveId, $anneeScolaireId]
        );
        $totalPaye = $paiements['total'] ?? 0;

        $totalReste = max(0, $totalAPayer - $totalPaye);
        $tauxPaiement = ($totalAPayer > 0) ? ($totalPaye / $totalAPayer) * 100 : 0;

        return [
            'total_a_payer' => $totalAPayer,
            'total_paye' => $totalPaye,
            'total_reste' => $totalReste,
            'taux_paiement' => $tauxPaiement,
            'nb_echeances_impayees' => $ecolage['nb_impayees'] ?? 0
        ];
    }
    
    /**
     * Vérifie si l'élève a des retards de paiement
     */
    public function hasRetardPaiement($eleveId) {
        $retard = $this->queryOne(
            "SELECT COUNT(*) as nb_retards 
             FROM echeanciers_ecolages 
             WHERE eleve_id = ? AND statut IN ('retard', 'exclusion') AND montant_restant > 0",
            [$eleveId]
        );
        
        return $retard && $retard['nb_retards'] > 0;
    }
    
    /**
     * Obtient l'inscription active d'un élève
     */
    public function getInscriptionActive($eleveId) {
        return $this->queryOne(
            "SELECT i.*, c.nom as classe_nom, a.libelle as annee_scolaire
             FROM inscriptions i
             INNER JOIN classes c ON i.classe_id = c.id
             INNER JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
             WHERE i.eleve_id = ? AND i.statut = 'validee'
             ORDER BY i.created_at DESC LIMIT 1",
            [$eleveId]
        );
    }
    
    /**
     * Récupère les élèves éligibles à la réinscription pour une année donnée
     * @param int $anneeActiveId ID de l'année scolaire active
     * @return array Liste des élèves éligibles avec leur dernière classe
     */
    public function getElevesEligiblesReinscription($anneeActiveId) {
        return $this->query(
            "SELECT DISTINCT e.id, e.matricule, e.nom, e.prenom, e.sexe, e.date_naissance, 
                    e.lieu_naissance, e.photo, e.statut,
                    (SELECT c.nom 
                     FROM inscriptions i2 
                     INNER JOIN classes c ON i2.classe_id = c.id 
                     WHERE i2.eleve_id = e.id 
                     AND i2.annee_scolaire_id < ?
                     ORDER BY i2.annee_scolaire_id DESC 
                     LIMIT 1) as classe_actuelle
             FROM eleves e
             WHERE e.statut NOT IN ('inactif', 'supprime')
             AND EXISTS (
                 SELECT 1 FROM inscriptions i 
                 WHERE i.eleve_id = e.id 
                 AND i.annee_scolaire_id < ?
             )
             AND NOT EXISTS (
                 SELECT 1 FROM inscriptions i 
                 WHERE i.eleve_id = e.id 
                 AND i.annee_scolaire_id = ?
             )
             ORDER BY e.nom ASC, e.prenom ASC",
            [$anneeActiveId, $anneeActiveId, $anneeActiveId]
        );
    }
}

