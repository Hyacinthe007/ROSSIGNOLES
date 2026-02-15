<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Paiement;
use App\Models\LigneFacture;
use App\Models\Bulletin;
use App\Models\Personnel;
use App\Models\Eleve;
use App\Models\ParentModel;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Periode;
use App\Models\ExamenFinal;
use App\Models\Interrogation;
use App\Models\EcheancierEcolage;
use App\Services\FinanceService;

/**
 * Contrôleur API
 * Fournit des endpoints JSON pour l'export et la consommation de données.
 */

class ApiController extends BaseController {
    
    public function __construct() {
        $this->requireApiAuth();
    }

    /**
     * Vérification d'authentification adaptée pour l'API.
     * Retourne une erreur JSON 401 au lieu de rediriger vers la page de login.
     */
    private function requireApiAuth() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'error',
                'message' => 'Non autorisé. Veuillez vous connecter pour accéder à cette ressource.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * Helper : envoie une réponse JSON standardisée
     */
    private function jsonResponse(array $data, int $count, string $label = 'data') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success',
            'count' => $count,
            $label => $data
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 1. BULLETINS  →  GET /api/bulletins
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Liste des bulletins avec détails (élève, classe, période, année scolaire)
     */
    public function bulletins() {
        $bulletinModel = new Bulletin();
        $bulletins = $bulletinModel->getAllWithDetails();

        $this->jsonResponse($bulletins, count($bulletins));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 2. PERSONNEL  →  GET /api/personnel
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Liste du personnel (enseignants + administratifs)
     */
    public function personnel() {
        $personnelModel = new Personnel();
        $allPersonnel = $personnelModel->all(['statut' => 'actif']);

        $combinedList = [];

        foreach ($allPersonnel as $pers) {
            $matricule = $pers['matricule'] ?? '';
            $telephone = $pers['telephone'] ?? '';

            // Fonction : ENS => Enseignant, sinon => Administration
            $fonction = (strpos(strtoupper($matricule), 'ENS') !== false) ? 'Enseignant' : 'Administration';

            // Formater le téléphone en 03X XX XXX XX
            $formattedPhone = $telephone;
            $digits = preg_replace('/\D/', '', $telephone);
            if (strlen($digits) === 10) {
                $formattedPhone = substr($digits, 0, 3) . ' ' . substr($digits, 3, 2) . ' ' . substr($digits, 5, 3) . ' ' . substr($digits, 8, 2);
            }

            $combinedList[] = [
                'id' => $pers['id'],
                'matricule' => $matricule,
                'nom' => $pers['nom'],
                'prenom' => $pers['prenom'],
                'sexe' => $pers['sexe'],
                'photo' => $pers['photo'] ?? '',
                'telephone' => $formattedPhone,
                'email' => $pers['email'] ?? '',
                'statut' => ucfirst($pers['statut'] ?? 'Inactif'),
                'type' => ($pers['type_personnel'] ?? 'autre') === 'enseignant' ? 'enseignants' : 'personnel',
                'fonction' => $fonction
            ];
        }

        $this->jsonResponse($combinedList, count($combinedList));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 3. ÉLÈVES  →  GET /api/eleves  (optionnel ?search=xxx)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Liste des élèves avec dernière classe et statut financier
     */
    public function eleves() {
        $eleveModel = new Eleve();
        $search = $_GET['search'] ?? '';

        $sql = "SELECT e.*, 
                       p.telephone as parent_telephone, 
                       p.adresse as parent_adresse,
                       (SELECT c.code FROM inscriptions i 
                        INNER JOIN classes c ON i.classe_id = c.id 
                        WHERE i.eleve_id = e.id 
                        ORDER BY i.id DESC LIMIT 1) as derniere_classe,
                       (SELECT CASE 
                            WHEN COUNT(CASE WHEN ee.statut = 'exclusion' THEN 1 END) > 0 THEN 'exclusion'
                            WHEN COUNT(CASE WHEN ee.statut = 'retard' THEN 1 END) > 0 THEN 'retard'
                            WHEN COUNT(CASE WHEN ee.statut = 'impaye' THEN 1 END) > 0 THEN 'impaye'
                            ELSE NULL 
                        END 
                        FROM echeanciers_ecolages ee 
                        WHERE ee.eleve_id = e.id AND ee.montant_restant > 0) as statut_financier
                FROM eleves e
                LEFT JOIN (
                    SELECT ep.eleve_id, p1.telephone, p1.adresse 
                    FROM eleves_parents ep
                    INNER JOIN parents p1 ON ep.parent_id = p1.id
                    GROUP BY ep.eleve_id
                ) p ON e.id = p.eleve_id
                WHERE 1=1";

        $params = [];
        if (!empty($search)) {
            $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY e.nom ASC, e.prenom ASC";
        $eleves = $eleveModel->query($sql, $params);

        $this->jsonResponse($eleves, count($eleves));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 4. REÇUS DE PAIEMENT  →  GET /api/finance/recus  (optionnel ?search=xxx)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * API - Liste des reçus de paiement (JSON)
     */
    public function getRecus() {
        $paiementModel = new Paiement();
        $search = $_GET['search'] ?? '';
        
        $query = "SELECT p.*, f.numero_facture, e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                         c.code as classe_code, mp.libelle as mode_paiement_libelle,
                         (SELECT GROUP_CONCAT(ee.mois_libelle SEPARATOR ', ') 
                          FROM echeanciers_ecolages ee 
                          WHERE ee.derniere_facture_id = f.id) as mois_ecolage
                  FROM paiements p
                  LEFT JOIN factures f ON p.facture_id = f.id
                  LEFT JOIN eleves e ON f.eleve_id = e.id
                  LEFT JOIN inscriptions i ON (f.eleve_id = i.eleve_id AND f.annee_scolaire_id = i.annee_scolaire_id)
                  LEFT JOIN classes c ON i.classe_id = c.id
                  LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id";
        
        if (!empty($search)) {
            $query .= " WHERE e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ? OR f.numero_facture LIKE ? OR p.numero_paiement LIKE ? OR c.code LIKE ?";
            $paiements = $paiementModel->query($query . " ORDER BY p.date_paiement DESC, p.id DESC", ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
        } else {
            $query .= " ORDER BY p.date_paiement DESC, p.id DESC LIMIT 100";
            $paiements = $paiementModel->query($query);
        }
        
        $this->jsonResponse($paiements, count($paiements));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 5. ÉCHÉANCIERS  →  GET /api/finance/echeanciers  (optionnel ?statut=exclusion)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * API - Échéancier de recouvrement / liste des exclus (JSON)
     * ?statut=retard (défaut) : élèves en retard de paiement
     * ?statut=exclusion : élèves exclus pour défaut de paiement
     */
    public function echeanciers() {
        $financeService = new FinanceService();

        $statutFilter = $_GET['statut'] ?? 'retard';

        // Normaliser le filtre de statut
        if ($statutFilter === 'retard_10') {
            $statutFilter = 'retard';
        }

        // Valider le filtre
        if (!in_array($statutFilter, ['retard', 'exclusion'])) {
            $statutFilter = 'retard';
        }

        $echeances = $financeService->getEcheancierRecouvrement(null, $statutFilter);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success',
            'statut_filter' => $statutFilter,
            'count' => count($echeances),
            'data' => $echeances
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 6. PARENTS  →  GET /api/parents  (optionnel ?search=xxx)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * API - Liste des parents avec nombre d'enfants
     */
    public function parents() {
        $parentModel = new ParentModel();
        $search = $_GET['search'] ?? '';

        if (!empty($search)) {
            $parents = $parentModel->query(
                "SELECT p.*, COUNT(DISTINCT ep.eleve_id) as nb_enfants
                 FROM parents p
                 LEFT JOIN eleves_parents ep ON p.id = ep.parent_id
                 LEFT JOIN eleves e ON ep.eleve_id = e.id
                 WHERE p.nom LIKE ? 
                    OR p.prenom LIKE ? 
                    OR p.telephone LIKE ? 
                    OR p.email LIKE ?
                    OR e.nom LIKE ?
                    OR e.prenom LIKE ?
                    OR CONCAT(e.nom, ' ', e.prenom) LIKE ?
                    OR CONCAT(e.prenom, ' ', e.nom) LIKE ?
                 GROUP BY p.id
                 ORDER BY p.nom ASC",
                [
                    "%$search%", "%$search%", "%$search%", "%$search%",
                    "%$search%", "%$search%", "%$search%", "%$search%"
                ]
            );
        } else {
            $parents = $parentModel->query(
                "SELECT p.*, COUNT(DISTINCT ep.eleve_id) as nb_enfants
                 FROM parents p
                 LEFT JOIN eleves_parents ep ON p.id = ep.parent_id
                 GROUP BY p.id
                 ORDER BY p.nom ASC"
            );
        }

        $this->jsonResponse($parents, count($parents));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 7. NOTES  →  GET /api/notes  (optionnel ?classe_id=X&periode_id=Y)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * API - Liste des évaluations (examens + interrogations)
     * Filtres optionnels : ?classe_id=X&periode_id=Y
     */
    public function notes() {
        $anneeModel = new AnneeScolaire();
        $classeModel = new Classe();
        $periodeModel = new Periode();
        $examenModel = new ExamenFinal();
        $interroModel = new Interrogation();

        // Récupérer l'année scolaire active
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;

        // Récupérer les filtres
        $classeId = $_GET['classe_id'] ?? null;
        $periodeId = $_GET['periode_id'] ?? null;

        // Données de référence
        $classes = $anneeId ? $classeModel->getSortedByLevel($anneeId) : [];
        $periodes = $anneeId ? $periodeModel->all(['annee_scolaire_id' => $anneeId], 'numero ASC') : [];

        $evaluations = [];

        if ($classeId && $periodeId) {
            // Récupérer les examens
            $examens = $examenModel->getByClassePeriode($classeId, $periodeId);

            // Récupérer les interrogations
            $interros = $interroModel->getByClassePeriode($classeId, $periodeId);

            $evaluations = array_merge($examens, $interros);

            // Trier par date (descendant)
            usort($evaluations, function($a, $b) {
                $dateA = $a['type'] === 'examen' ? $a['date_examen'] : $a['date_interrogation'];
                $dateB = $b['type'] === 'examen' ? $b['date_examen'] : $b['date_interrogation'];
                return strtotime($dateB) - strtotime($dateA);
            });
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success',
            'annee_active' => $anneeActive,
            'classes' => $classes,
            'periodes' => $periodes,
            'selected_classe' => $classeId,
            'selected_periode' => $periodeId,
            'count' => count($evaluations),
            'data' => $evaluations
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // EXPORT CSV (existant)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * API - Export des reçus en Excel/CSV
     */
    public function exportRecusExcel() {
        $paiementModel = new Paiement();
        $search = $_GET['search'] ?? '';

        $query = "SELECT p.*, f.numero_facture, e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                         c.code as classe_code, mp.libelle as mode_paiement_libelle,
                         (SELECT GROUP_CONCAT(ee.mois_libelle SEPARATOR ', ') 
                          FROM echeanciers_ecolages ee 
                          WHERE ee.derniere_facture_id = f.id) as mois_ecolage
                  FROM paiements p
                  LEFT JOIN factures f ON p.facture_id = f.id
                  LEFT JOIN eleves e ON f.eleve_id = e.id
                  LEFT JOIN inscriptions i ON (f.eleve_id = i.eleve_id AND f.annee_scolaire_id = i.annee_scolaire_id)
                  LEFT JOIN classes c ON i.classe_id = c.id
                  LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id";

        if (!empty($search)) {
            $query .= " WHERE e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ? OR f.numero_facture LIKE ?";
            $paiements = $paiementModel->query($query, ["%$search%", "%$search%", "%$search%", "%$search%"]);
        } else {
            $query .= " ORDER BY p.date_paiement DESC";
            $paiements = $paiementModel->query($query);
        }

        $filename = "api_export_recus_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel
        
        fputcsv($output, ['Date', 'Matricule', 'Élève', 'Classe', 'Mois Écolage', 'N° Facture', 'Mode', 'Montant'], ';');
        
        foreach ($paiements as $p) {
            $mois = !empty($p['mois_ecolage']) ? explode(', ', $p['mois_ecolage']) : [null];
            $nombreMois = count($mois);
            $montantParMois = $nombreMois > 0 ? $p['montant'] / $nombreMois : $p['montant'];
            
            foreach ($mois as $m) {
                fputcsv($output, [
                    date('d/m/Y', strtotime($p['date_paiement'])),
                    $p['matricule'],
                    $p['eleve_nom'] . ' ' . $p['eleve_prenom'],
                    $p['classe_code'] ?? 'N/A',
                    $m ?? '-',
                    $p['numero_facture'],
                    $p['mode_paiement_libelle'],
                    (int)$montantParMois
                ], ';');
            }
        }
        
        fclose($output);
        exit;
    }

    /**
     * API - Liste des élèves d'une classe
     * ?classe_id=X
     */
    public function classesEleves() {
        $classeId = $_GET['classe_id'] ?? null;
        
        // Récupérer l'année scolaire active
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;

        if (!$anneeId) {
            $anneeActiveData = $anneeModel->queryOne("SELECT * FROM annees_scolaires WHERE actif = 1 LIMIT 1");
            $anneeId = $anneeActiveData ? $anneeActiveData['id'] : null;
        }
        
        $classeModel = new Classe();
        $eleves = $classeModel->getElevesWithPaymentStatus($classeId, $anneeId);
        
        $this->jsonResponse($eleves, count($eleves), 'eleves');
    }
}
