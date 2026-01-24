<?php
/**
 * Contrôleur des classes
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Classe.php';

class ClassesController extends BaseController {
    private $classeModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->classeModel = new Classe();
    }
    
    public function list() {
        // Enable error reporting for debugging
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        try {
            // Récupérer l'année scolaire active
            require_once APP_PATH . '/Models/AnneeScolaire.php';
            $anneeModel = new AnneeScolaire();
            $anneeActive = $anneeModel->getActive();
            $anneeId = $anneeActive ? $anneeActive['id'] : null;

            if ($anneeId) {
                // Classes de l'année en cours
                // Added join to annees_scolaires to get the label
                $sql = "SELECT c.*, 
                            n.libelle as niveau_nom,
                            s.libelle as serie_nom,
                            an.libelle as annee_scolaire_libelle,
                            (SELECT COUNT(*) FROM inscriptions i WHERE i.classe_id = c.id AND i.statut = 'validee') as effectif
                        FROM classes c
                        LEFT JOIN niveaux n ON c.niveau_id = n.id
                        LEFT JOIN series s ON c.serie_id = s.id
                        LEFT JOIN annees_scolaires an ON c.annee_scolaire_id = an.id
                        WHERE c.annee_scolaire_id = ? AND c.statut = 'actif' AND c.deleted_at IS NULL
                        ORDER BY n.ordre ASC, c.nom ASC";
                $classes = $this->classeModel->query($sql, [$anneeId]);
            } else {
                $classes = [];
            }
        } catch (PDOException $e) {
            $classes = [];
            error_log($e->getMessage());
            // If dev environment, show error
            echo "Database Error: " . $e->getMessage();
        } catch (Exception $e) {
            $classes = [];
            error_log($e->getMessage());
            echo "General Error: " . $e->getMessage();
        }
        
        $this->view('classes/list', ['classes' => $classes, 'anneeActive' => $anneeActive]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'] ?? generateMatricule('classe', 'classes');
            
            $data = [
                'code' => $code,
                'nom' => $_POST['nom'] ?? '',
                'niveau_id' => $_POST['niveau_id'] ?? null,
                'serie_id' => !empty($_POST['serie_id']) ? $_POST['serie_id'] : null,
                'annee_scolaire_id' => $_POST['annee_scolaire_id'] ?? null,
                'professeur_principal_id' => !empty($_POST['enseignant_principal_id']) ? $_POST['enseignant_principal_id'] : null,
                'capacite' => $_POST['capacite'] ?? 40,
                'seuil_admission' => $_POST['seuil_admission'] ?? 10.00,
                'salle' => $_POST['salle'] ?? null,
            ];
            
            $id = $this->classeModel->create($data);
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
            $this->redirect('classes/list' . $iframeParam);
        } else {
            $niveaux = $this->classeModel->query("SELECT n.*, c.libelle as cycle_libelle FROM niveaux n LEFT JOIN cycles c ON n.cycle_id = c.id WHERE n.actif = 1 ORDER BY n.ordre ASC");
            // Les séries seront chargées via AJAX en fonction du niveau
            $series = [];
            
            require_once APP_PATH . '/Models/Personnel.php';
            $personnelModel = new Personnel();
            $enseignants = $personnelModel->query("SELECT * FROM personnels WHERE type_personnel = 'enseignant' AND statut = 'actif' ORDER BY nom ASC");

            require_once APP_PATH . '/Models/AnneeScolaire.php';
            $anneeModel = new AnneeScolaire();
            $anneeActive = $anneeModel->getActive();
            
            $codeAuto = generateMatricule('classe', 'classes');
            
            $this->view('classes/add', [
                'niveaux' => $niveaux,
                'series' => $series,
                'enseignants' => $enseignants,
                'anneeActive' => $anneeActive,
                'code_auto' => $codeAuto
            ]);
        }
    }
    
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'] ?? '',
                'nom' => $_POST['nom'] ?? '',
                'niveau_id' => $_POST['niveau_id'] ?? null,
                'serie_id' => !empty($_POST['serie_id']) ? $_POST['serie_id'] : null,
                'professeur_principal_id' => !empty($_POST['enseignant_principal_id']) ? $_POST['enseignant_principal_id'] : null,
            ];
            
            $this->classeModel->update($id, $data);
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
            $this->redirect('classes/list' . $iframeParam);
        } else {
            $classe = $this->classeModel->getDetails($id);
            if (!$classe) {
                http_response_code(404);
                die("Classe non trouvée");
            }
            
            $niveaux = $this->classeModel->query("SELECT n.*, c.libelle as cycle_libelle FROM niveaux n LEFT JOIN cycles c ON n.cycle_id = c.id WHERE n.actif = 1 ORDER BY n.ordre ASC");
            
            // Récupérer les séries filtrées par le niveau actuel de la classe
            $series = [];
            if (!empty($classe['niveau_id'])) {
                $series = $this->classeModel->query("SELECT * FROM series WHERE niveau_id = ? ORDER BY libelle ASC", [$classe['niveau_id']]);
            }
            
            require_once APP_PATH . '/Models/Personnel.php';
            $personnelModel = new Personnel();
            $enseignants = $personnelModel->query("SELECT * FROM personnels WHERE type_personnel = 'enseignant' AND statut = 'actif' ORDER BY nom ASC");
            
            // Récupérer les années scolaires
            require_once APP_PATH . '/Models/AnneeScolaire.php';
            $anneeModel = new AnneeScolaire();
            $annees = $anneeModel->query("SELECT * FROM annees_scolaires ORDER BY date_debut DESC");
            
            // Récupérer les établissements (si la table existe)
            $etablissements = [];
            try {
                $etablissements = $this->classeModel->query("SELECT * FROM etablissements ORDER BY nom ASC");
            } catch (PDOException $e) {
                error_log("Table etablissements n'existe pas");
            }
            
            // Récupérer les salles (si la table existe)
            $salles = [];
            try {
                $salles = $this->classeModel->query("SELECT * FROM salles ORDER BY nom ASC");
            } catch (PDOException $e) {
                error_log("Table salles n'existe pas");
            }
            
            $this->view('classes/edit', [
                'classe' => $classe,
                'niveaux' => $niveaux,
                'series' => $series,
                'enseignants' => $enseignants,
                'annees' => $annees,
                'etablissements' => $etablissements,
                'salles' => $salles
            ]);
        }
    }
    
    public function details($id) {
        $classe = $this->classeModel->getDetails($id);
        if (!$classe) {
            http_response_code(404);
            die("Classe non trouvée");
        }
        
        // Récupérer l'année scolaire active
        $anneeActive = null;
        try {
            $anneeActiveData = $this->classeModel->queryOne("SELECT id FROM annees_scolaires WHERE actif = 1 LIMIT 1");
            if ($anneeActiveData) {
                $anneeActive = $anneeActiveData['id'];
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'année active: " . $e->getMessage());
        }
        
        // Récupérer les élèves avec l'année scolaire active
        $eleves = $this->classeModel->getEleves($id, $anneeActive);
        
        // Emploi du temps (placeholder)
        // $edt = $this->classeModel->getEmploiDuTemps($id);
        $edt = [];

        $this->view('classes/details', [
            'classe' => $classe,
            'eleves' => $eleves,
            'edt' => $edt
        ]);
    }
    
    public function associer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classeId = $_POST['classe_id'] ?? null;
            $niveauId = $_POST['niveau_id'] ?? null;
            $serieId = !empty($_POST['serie_id']) ? $_POST['serie_id'] : null;
            $anneeId = !empty($_POST['annee_scolaire_id']) ? $_POST['annee_scolaire_id'] : null;
            
            if ($classeId && $niveauId) {
                $data = [
                    'niveau_id' => $niveauId,
                    'serie_id' => $serieId,
                ];
                
                if ($anneeId) {
                    $data['annee_scolaire_id'] = $anneeId;
                }
                
                $this->classeModel->update($classeId, $data);
                $_SESSION['success_message'] = 'Association mise à jour avec succès !';
                $this->redirect('classes/associer');
            } else {
                $_SESSION['error_message'] = 'Veuillez sélectionner une classe et un niveau.';
                $this->redirect('classes/associer');
            }
        } else {
            // Récupérer les filtres
            $filterNiveau = $_GET['filter_niveau'] ?? '';
            $filterSection = $_GET['filter_section'] ?? '';
            $filterAnnee = $_GET['filter_annee'] ?? '';
            $filterSearch = $_GET['search'] ?? '';
            $showUnassociated = isset($_GET['show_unassociated']);
            
            // Récupérer toutes les classes actives
            $classes = $this->classeModel->query("SELECT * FROM classes WHERE statut = 'actif' AND deleted_at IS NULL ORDER BY nom ASC");
            
            // Récupérer les niveaux
            $niveaux = [];
            try {
                $niveaux = $this->classeModel->query("SELECT * FROM niveaux WHERE actif = 1 ORDER BY ordre ASC");
            } catch (PDOException $e) {
                error_log("Table niveaux n'existe pas");
            }
            
            // Récupérer les séries
            $series = [];
            try {
                $series = $this->classeModel->query("SELECT * FROM series WHERE actif = 1 ORDER BY libelle ASC");
            } catch (PDOException $e) {
                error_log("Table series n'existe pas");
            }
            
            // Récupérer les années scolaires
            $annees = [];
            try {
                $annees = $this->classeModel->query("SELECT * FROM annees_scolaires ORDER BY date_debut DESC");
            } catch (PDOException $e) {
                error_log("Table annees_scolaires n'existe pas");
            }
            
            // Construire la requête des associations avec filtres
            $sql = "SELECT c.id as classe_id, c.nom as classe_nom, c.code as classe_code,
                           c.niveau_id, c.serie_id, c.annee_scolaire_id,
                           n.id as niveau_id, n.libelle as niveau_nom,
                           s.id as serie_id_join, s.libelle as serie_nom,
                           an.id as annee_id, an.libelle as annee_scolaire_libelle
                    FROM classes c
                    LEFT JOIN niveaux n ON c.niveau_id = n.id
                    LEFT JOIN series s ON c.serie_id = s.id
                    LEFT JOIN annees_scolaires an ON c.annee_scolaire_id = an.id
                    WHERE c.statut = 'actif' AND c.deleted_at IS NULL";
            
            $params = [];
            
            // Appliquer les filtres
            if ($showUnassociated) {
                $sql .= " AND c.niveau_id IS NULL";
            }
            
            if (!empty($filterNiveau)) {
                $sql .= " AND c.niveau_id = ?";
                $params[] = $filterNiveau;
            }
            
            if (!empty($filterSection)) {
                $sql .= " AND c.serie_id = ?";
                $params[] = $filterSection;
            }
            
            if (!empty($filterAnnee)) {
                $sql .= " AND c.annee_scolaire_id = ?";
                $params[] = $filterAnnee;
            }
            
            if (!empty($filterSearch)) {
                $sql .= " AND (c.nom LIKE ? OR c.code LIKE ?)";
                $searchTerm = '%' . $filterSearch . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $sql .= " ORDER BY c.nom ASC";
            
            // Récupérer les associations
            $associations = [];
            try {
                $associations = $this->classeModel->query($sql, $params);
            } catch (PDOException $e) {
                error_log("Erreur lors de la récupération des associations: " . $e->getMessage());
            }
            
            // Calculer les statistiques
            $stats = $this->calculateAssociationStats();
            
            $this->view('classes/associer', [
                'classes' => $classes,
                'niveaux' => $niveaux,
                'series' => $series,
                'annees' => $annees,
                'associations' => $associations,
                'stats' => $stats,
                'filters' => [
                    'niveau' => $filterNiveau,
                    'section' => $filterSection,
                    'annee' => $filterAnnee,
                    'search' => $filterSearch,
                    'show_unassociated' => $showUnassociated
                ]
            ]);
        }
    }
    
    public function eleves() {
        $classeId = $_GET['classe_id'] ?? null;
        
        // Récupérer l'année scolaire active
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;

        if (!$anneeId) {
            try {
                $anneeActiveData = $this->classeModel->queryOne("SELECT * FROM annees_scolaires WHERE actif = 1 LIMIT 1");
                if ($anneeActiveData) {
                    $anneeId = $anneeActiveData['id'];
                    $anneeActive = $anneeActiveData;
                }
            } catch (PDOException $e) {
                error_log("Erreur lors de la récupération de l'année active: " . $e->getMessage());
            }
        }
        
        // Récupérer les classes de l'année scolaire en cours pour le filtre
        $classes = [];
        if ($anneeId) {
            $classes = $this->classeModel->query(
                "SELECT * FROM classes WHERE statut = 'actif' AND deleted_at IS NULL AND annee_scolaire_id = ? ORDER BY nom ASC",
                [$anneeId]
            );
        }
        
        // Récupérer les élèves
        $eleves = [];
        $selectedClasse = null;
        
        try {
            $paymentFilter = "";
            $params = [];

            if ($anneeId) {
                $paymentFilter = " AND NOT EXISTS (
                    SELECT 1 FROM echeanciers_ecolages ee 
                    WHERE ee.eleve_id = e.id 
                    AND ee.annee_scolaire_id = ? 
                    AND ee.statut IN ('retard', 'exclusion') 
                    AND ee.montant_restant > 0
                )";
            }

            if ($classeId) {
                // Élèves d'une classe spécifique
                $selectedClasse = $this->classeModel->find($classeId);
                
                $sql = "SELECT e.*, i.date_inscription, i.statut as inscription_statut, i.type_inscription,
                               c.id as classe_id, c.nom as classe_nom, c.code as classe_code
                        FROM eleves e
                        INNER JOIN inscriptions i ON e.id = i.eleve_id
                        LEFT JOIN classes c ON i.classe_id = c.id
                        WHERE i.classe_id = ? AND i.statut = 'validee'";
                
                $params = [$classeId];
                if ($anneeId) {
                    $sql .= " AND i.annee_scolaire_id = ?";
                    $params[] = $anneeId;
                    
                    // Appliquer le filtre de paiement à temps
                    $sql .= $paymentFilter;
                    $params[] = $anneeId;
                }
                
                $sql .= " ORDER BY e.nom ASC, e.prenom ASC";
                $eleves = $this->classeModel->query($sql, $params);
            } else {
                // Tous les élèves de l'année en cours
                $sql = "SELECT e.*, i.date_inscription, i.statut as inscription_statut, i.type_inscription,
                               c.id as classe_id, c.nom as classe_nom, c.code as classe_code
                        FROM eleves e
                        INNER JOIN inscriptions i ON e.id = i.eleve_id
                        LEFT JOIN classes c ON i.classe_id = c.id
                        WHERE i.statut = 'validee'";
                
                $params = [];
                if ($anneeId) {
                    $sql .= " AND i.annee_scolaire_id = ?";
                    $params[] = $anneeId;
                    
                    // Appliquer le filtre de paiement à temps
                    $sql .= $paymentFilter;
                    $params[] = $anneeId;
                } else {
                    // Si pas d'année, on ne montre rien par défaut selon la demande
                    $sql .= " AND 1=0";
                }
                
                $sql .= " ORDER BY c.nom ASC, e.nom ASC, e.prenom ASC";
                
                $eleves = $this->classeModel->query($sql, $params);
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des élèves: " . $e->getMessage());
        }
        
        $this->view('classes/eleves', [
            'classes' => $classes,
            'anneeActive' => $anneeActive,
            'eleves' => $eleves,
            'selectedClasse' => $selectedClasse
        ]);
    }
    
    /**
     * Mise à jour inline d'une association (AJAX)
     */
    public function updateAssociation() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $classeId = $input['classe_id'] ?? null;
        $field = $input['field'] ?? null;
        $value = $input['value'] ?? null;
        
        if (!$classeId || !$field) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            return;
        }
        
        // Valider le champ
        $allowedFields = ['niveau_id', 'serie_id', 'annee_scolaire_id'];
        if (!in_array($field, $allowedFields)) {
            echo json_encode(['success' => false, 'message' => 'Champ non autorisé']);
            return;
        }
        
        // Convertir valeur vide en NULL
        if ($value === '' || $value === 'null') {
            $value = null;
        }
        
        try {
            $this->classeModel->update($classeId, [$field => $value]);
            echo json_encode([
                'success' => true,
                'message' => 'Association mise à jour avec succès'
            ]);
        } catch (Exception $e) {
            error_log("Erreur updateAssociation: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ]);
        }
    }
    
    /**
     * Mise à jour en masse des associations (AJAX)
     */
    public function bulkUpdateAssociations() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $classeIds = $input['classe_ids'] ?? [];
        $field = $input['field'] ?? null;
        $value = $input['value'] ?? null;
        
        if (empty($classeIds) || !$field) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            return;
        }
        
        // Valider le champ
        $allowedFields = ['niveau_id', 'serie_id', 'annee_scolaire_id'];
        if (!in_array($field, $allowedFields)) {
            echo json_encode(['success' => false, 'message' => 'Champ non autorisé']);
            return;
        }
        
        // Convertir valeur vide en NULL
        if ($value === '' || $value === 'null') {
            $value = null;
        }
        
        try {
            $updatedCount = 0;
            foreach ($classeIds as $classeId) {
                $this->classeModel->update($classeId, [$field => $value]);
                $updatedCount++;
            }
            
            echo json_encode([
                'success' => true,
                'message' => "$updatedCount classe(s) mise(s) à jour",
                'updated_count' => $updatedCount
            ]);
        } catch (Exception $e) {
            error_log("Erreur bulkUpdateAssociations: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour en masse'
            ]);
        }
    }
    
    /**
     * Récupérer les statistiques des associations (AJAX)
     */
    public function getAssociationStats() {
        header('Content-Type: application/json');
        
        try {
            $stats = $this->calculateAssociationStats();
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            error_log("Erreur getAssociationStats: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors du calcul des statistiques'
            ]);
        }
    }
    
    /**
     * Calculer les statistiques des associations
     */
    private function calculateAssociationStats() {
        $stats = [
            'total_classes' => 0,
            'classes_associees' => 0,
            'classes_non_associees' => 0,
            'repartition_niveaux' => []
        ];
        
        try {
            // Total des classes actives
            $totalResult = $this->classeModel->queryOne("SELECT COUNT(*) as total FROM classes WHERE statut = 'actif' AND deleted_at IS NULL");
            $stats['total_classes'] = (int)($totalResult['total'] ?? 0);
            
            // Classes associées (avec niveau)
            $associeesResult = $this->classeModel->queryOne("SELECT COUNT(*) as total FROM classes WHERE statut = 'actif' AND deleted_at IS NULL AND niveau_id IS NOT NULL");
            $stats['classes_associees'] = (int)($associeesResult['total'] ?? 0);
            
            // Classes non associées
            $stats['classes_non_associees'] = $stats['total_classes'] - $stats['classes_associees'];
            
            // Répartition par niveau
            $repartition = $this->classeModel->query(
                "SELECT n.libelle as niveau_nom, 
                        COUNT(c.id) as nombre_classes
                 FROM classes c
                 INNER JOIN niveaux n ON c.niveau_id = n.id
                 WHERE c.statut = 'actif' AND c.deleted_at IS NULL
                 GROUP BY n.id, n.libelle
                 ORDER BY n.ordre ASC"
            );
            
            $stats['repartition_niveaux'] = $repartition;
            
        } catch (PDOException $e) {
            error_log("Erreur calculateAssociationStats: " . $e->getMessage());
        }
        
        return $stats;
    }

    /**
     * Récupère les séries associées à un niveau (AJAX)
     */
    public function getSeriesByNiveau($id) {
        header('Content-Type: application/json');
        try {
            $series = $this->classeModel->query(
                "SELECT id, libelle, code FROM series WHERE niveau_id = ? AND actif = 1 ORDER BY libelle ASC",
                [$id]
            );
            echo json_encode($series);
        } catch (PDOException $e) {
            error_log("Erreur getSeriesByNiveau: " . $e->getMessage());
            echo json_encode([]);
        }
    }
}

