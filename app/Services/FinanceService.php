<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BaseModel;
use PDOException;

/**
 * Service financier
 */

class FinanceService {
    
    /**
     * Obtient les statistiques financières complètes
     */
    public function getStats($anneeScolaireId = null, $periode = 'tous') {
        $db = BaseModel::getDBConnection();
        
        if (!$anneeScolaireId) {
            try {
                $annee = $db->query("SELECT id FROM annees_scolaires WHERE actif = 1 LIMIT 1")->fetch();
                $anneeScolaireId = $annee ? $annee['id'] : null;
            } catch (PDOException $e) {
                // Fallback
                $annee = $db->query("SELECT id FROM annees_scolaires ORDER BY date_debut DESC LIMIT 1")->fetch();
                $anneeScolaireId = $annee ? $annee['id'] : null;
            }
        }
        
        // Calculer les dates de filtrage selon la période
        $dateFilter = '';
        $dateParams = [];
        
        switch ($periode) {
            case 'aujourdhui':
                $dateFilter = " AND DATE(p.date_paiement) = CURDATE()";
                break;
            case 'mois_ci':
                $dateFilter = " AND MONTH(p.date_paiement) = MONTH(CURDATE()) AND YEAR(p.date_paiement) = YEAR(CURDATE())";
                break;
            case 'dernier_mois':
                $dateFilter = " AND p.date_paiement >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                break;
            case 'trois_mois':
                $dateFilter = " AND p.date_paiement >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                break;
            default: // 'tous'
                $dateFilter = '';
        }
        
        $stats = [
            'total_recu' => 0,
            'total_attendu' => 0,
            'impayes' => 0,
            'eleves_a_jour' => 0,
            'eleves_retard' => 0,
            'eleves_total' => 0,
            'inscriptions_droit_paye' => 0, 
            'echeances_mois_courant' => [],
            'echeances_en_retard' => [],
        ];
        
        if (!$anneeScolaireId) return $stats;
        
        // Statistiques écolage depuis paiements (filtré par période)
        if ($periode === 'tous') {
            // Pour "tous", utiliser les échéanciers comme avant
            $stmt = $db->prepare("
                SELECT 
                    COALESCE(SUM(e.montant_du), 0) as total_attendu,
                    COALESCE(SUM(e.montant_paye), 0) as total_recu
                FROM echeanciers_ecolages e
                WHERE e.annee_scolaire_id = ?
            ");
            $stmt->execute([$anneeScolaireId]);
        } else {
            // Pour les périodes spécifiques, calculer depuis les paiements
            $stmt = $db->prepare("
                SELECT 
                    COALESCE(SUM(p.montant), 0) as total_recu
                FROM paiements p
                INNER JOIN factures f ON p.facture_id = f.id
                WHERE f.annee_scolaire_id = ?
                $dateFilter
            ");
            $stmt->execute([$anneeScolaireId]);
        }
        $result = $stmt->fetch();
        
        $stats['total_recu'] = $result['total_recu'] ?? 0;
        
        // Pour total_attendu, on garde la logique des échéanciers
        if ($periode !== 'tous') {
            $stats['total_attendu'] = $stats['total_recu']; // Simplification pour les périodes filtrées
        } else {
            $stats['total_attendu'] = $result['total_attendu'] ?? 0;
        }
        
        $stats['impayes'] = $stats['total_attendu'] - $stats['total_recu'];
        
        // Calculer le total des articles vendus (filtré par période si nécessaire)
        if ($periode === 'tous') {
            // Pour "tous", calculer depuis inscriptions_articles
            $stmtArticles = $db->prepare("
                SELECT COALESCE(SUM(ia.prix_unitaire * ia.quantite), 0) as total_articles
                FROM inscriptions_articles ia
                INNER JOIN inscriptions i ON ia.inscription_id = i.id
                WHERE i.annee_scolaire_id = ?
                  AND ia.paye = 1
            ");
            $stmtArticles->execute([$anneeScolaireId]);
        } else {
            // Pour les périodes spécifiques, calculer depuis les paiements avec remarque contenant "Articles"
            $stmtArticles = $db->prepare("
                SELECT COALESCE(SUM(p.montant), 0) as total_articles
                FROM paiements p
                INNER JOIN factures f ON p.facture_id = f.id
                WHERE f.annee_scolaire_id = ?
                  AND (p.remarque LIKE '%Articles%' OR p.remarque LIKE '%articles%')
                  $dateFilter
            ");
            $stmtArticles->execute([$anneeScolaireId]);
        }
        $resultArticles = $stmtArticles->fetch();
        $stats['total_articles_vendus'] = $resultArticles['total_articles'] ?? 0;
        
        // Compter les élèves inscrits
        $stmt2 = $db->prepare("
            SELECT COUNT(DISTINCT i.eleve_id) as total_inscriptions
            FROM inscriptions i
            WHERE i.annee_scolaire_id = ? AND i.statut = 'validee'
        ");
        $stmt2->execute([$anneeScolaireId]);
        $result2 = $stmt2->fetch();
        $stats['eleves_total'] = $result2['total_inscriptions'] ?? 0;
        
        // Compter les élèves à jour (tous les mois payés ou pas de retard)
        $stmt3 = $db->prepare("
            SELECT COUNT(DISTINCT e.eleve_id) as eleves_a_jour
            FROM inscriptions i
            INNER JOIN eleves el ON i.eleve_id = el.id
            LEFT JOIN echeanciers_ecolages e ON i.eleve_id = e.eleve_id 
                AND i.annee_scolaire_id = e.annee_scolaire_id
                AND e.statut NOT IN ('paye', 'exonere')
                AND e.montant_restant > 0
            WHERE i.annee_scolaire_id = ? 
                AND i.statut = 'validee'
                AND e.id IS NULL
        ");
        $stmt3->execute([$anneeScolaireId]);
        $result3 = $stmt3->fetch();
        $stats['eleves_a_jour'] = $result3['eleves_a_jour'] ?? 0;
        
        // Compter les élèves en retard (au moins une échéance impayée)
        $stmt4 = $db->prepare("
            SELECT COUNT(DISTINCT e.eleve_id) as eleves_retard
            FROM echeanciers_ecolages e
            INNER JOIN inscriptions i ON e.eleve_id = i.eleve_id 
                AND e.annee_scolaire_id = i.annee_scolaire_id
            WHERE e.annee_scolaire_id = ? 
                AND i.statut = 'validee'
                AND e.statut NOT IN ('paye', 'exonere')
                AND e.montant_restant > 0
        ");
        $stmt4->execute([$anneeScolaireId]);
        $result4 = $stmt4->fetch();
        $stats['eleves_retard'] = $result4['eleves_retard'] ?? 0;
        
        // Compter les inscriptions avec droits payés
        $stmt5 = $db->prepare("
            SELECT COUNT(*) as inscriptions_payees
            FROM inscriptions i
            LEFT JOIN factures f ON i.facture_inscription_id = f.id
            WHERE i.annee_scolaire_id = ? 
                AND i.statut = 'validee'
                AND (f.montant_paye >= f.montant_total OR i.facture_inscription_id IS NULL)
        ");
        $stmt5->execute([$anneeScolaireId]);
        $result5 = $stmt5->fetch();
        $stats['inscriptions_droit_paye'] = $result5['inscriptions_payees'] ?? 0;
        
        // Échéances en cours (mois actuel)
        $moisActuel = date('n'); // 1-12
        $anneeActuelle = date('Y');
        
        $stmt6 = $db->prepare("
            SELECT e.*, el.nom as eleve_nom, el.prenom as eleve_prenom, el.matricule,
                   c.nom as classe_nom
            FROM echeanciers_ecolages e
            INNER JOIN eleves el ON e.eleve_id = el.id
            INNER JOIN inscriptions i ON e.eleve_id = i.eleve_id AND e.annee_scolaire_id = i.annee_scolaire_id
            INNER JOIN classes c ON i.classe_id = c.id
            WHERE e.annee_scolaire_id = ?
              AND e.mois = ?
              AND e.annee = ?
              AND i.statut = 'validee'
            ORDER BY e.statut ASC, el.nom ASC
            LIMIT 20
        ");
        $stmt6->execute([$anneeScolaireId, $moisActuel, $anneeActuelle]);
        $stats['echeances_mois_courant'] = $stmt6->fetchAll();
        
        // Échéances en retard
        $dateActuelle = date('Y-m-d');
        
        $stmt7 = $db->prepare("
            SELECT e.*, el.nom as eleve_nom, el.prenom as eleve_prenom, el.matricule,
                   c.nom as classe_nom,
                   p.telephone as parent_telephone, p.nom as parent_nom
            FROM echeanciers_ecolages e
            INNER JOIN eleves el ON e.eleve_id = el.id
            INNER JOIN inscriptions i ON e.eleve_id = i.eleve_id AND e.annee_scolaire_id = i.annee_scolaire_id
            INNER JOIN classes c ON i.classe_id = c.id
            LEFT JOIN eleves_parents ep ON el.id = ep.eleve_id
            LEFT JOIN parents p ON ep.parent_id = p.id
            WHERE e.annee_scolaire_id = ?
              AND e.statut IN ('retard', 'exclusion', 'impaye')
              AND e.montant_restant > 0
              AND e.date_limite < ?
              AND i.statut = 'validee'
            GROUP BY e.id
            ORDER BY e.date_limite ASC
            LIMIT 20
        ");
        $stmt7->execute([$anneeScolaireId, $dateActuelle]);
        $stats['echeances_en_retard'] = $stmt7->fetchAll();
        
        return $stats;
    }
    

    /**
     * Obtient la liste des impayés pour le recouvrement (Échéancier)
     */
    public function getEcheancierRecouvrement($anneeScolaireId = null, $statut = null) {
        $db = BaseModel::getDBConnection();
        
        if (!$anneeScolaireId) {
            $annee = $db->query("SELECT id FROM annees_scolaires WHERE actif = 1 LIMIT 1")->fetch();
            $anneeScolaireId = $annee ? $annee['id'] : null;
        }
        
        $params = [$anneeScolaireId];
        
        if ($statut === 'exclusion') {
            $statutSql = "AND e.statut = 'exclusion'";
        } elseif ($statut === 'retard') {
            // Le recouvrement inclut les impayés simples (après le 10) et les retards plus longs
            // MAIS exclut les élèves qui ont au moins une échéance en statut 'exclusion'
            $statutSql = "AND e.statut IN ('impayee', 'retard')
                          AND NOT EXISTS (
                              SELECT 1 FROM echeanciers_ecolages ee
                              WHERE ee.eleve_id = e.eleve_id
                              AND ee.annee_scolaire_id = e.annee_scolaire_id
                              AND ee.statut = 'exclusion'
                              AND ee.montant_restant > 0
                          )";
        } else {
            // Par défaut, tout ce qui n'est pas payé/exonéré
            $statutSql = "AND e.statut IN ('impayee', 'retard', 'exclusion')";
        }

        // Utiliser une sous-requête pour récupérer le premier parent de chaque élève
        $sql = "
            SELECT e.*, el.nom as eleve_nom, el.prenom as eleve_prenom, el.matricule,
                   c.nom as classe_nom, c.code as classe_code,
                   (SELECT p.telephone 
                    FROM eleves_parents ep 
                    INNER JOIN parents p ON ep.parent_id = p.id 
                    WHERE ep.eleve_id = el.id 
                    LIMIT 1) as parent_telephone,
                   (SELECT p.nom 
                    FROM eleves_parents ep 
                    INNER JOIN parents p ON ep.parent_id = p.id 
                    WHERE ep.eleve_id = el.id 
                    LIMIT 1) as parent_nom
            FROM echeanciers_ecolages e
            INNER JOIN eleves el ON e.eleve_id = el.id
            INNER JOIN inscriptions i ON e.eleve_id = i.eleve_id AND e.annee_scolaire_id = i.annee_scolaire_id
            INNER JOIN classes c ON i.classe_id = c.id
            WHERE e.annee_scolaire_id = ? 
              AND i.statut = 'validee'
              AND e.montant_restant > 0
              $statutSql
            ORDER BY e.annee ASC, e.mois ASC, el.nom ASC
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
