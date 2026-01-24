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
                $classes = $this->classeModel->getAllWithDetailsAndEffectif($anneeId);
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
            require_once APP_PATH . '/Models/Niveau.php';
            $niveauModel = new Niveau();
            $niveaux = $niveauModel->getAllWithCycle();
            // Les séries seront chargées via AJAX en fonction du niveau
            $series = [];
            
            require_once APP_PATH . '/Models/Personnel.php';
            $personnelModel = new Personnel();
            $enseignants = $personnelModel->getEnseignants(true);

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
            
            require_once APP_PATH . '/Models/Niveau.php';
            $niveauModel = new Niveau();
            $niveaux = $niveauModel->getAllWithCycle();
            
            // Récupérer les séries filtrées par le niveau actuel de la classe
            $series = [];
            if (!empty($classe['niveau_id'])) {
                require_once APP_PATH . '/Models/Serie.php';
                $serieModel = new Serie();
                $series = $serieModel->getByNiveau($classe['niveau_id']);
            }
            
            require_once APP_PATH . '/Models/Personnel.php';
            $personnelModel = new Personnel();
            $enseignants = $personnelModel->getEnseignants(true);
            
            // Récupérer les années scolaires
            require_once APP_PATH . '/Models/AnneeScolaire.php';
            $anneeModel = new AnneeScolaire();
            $annees = $anneeModel->all([], 'date_debut DESC');
            
            try {
                $etablissements = $this->classeModel->query("SELECT * FROM etablissements ORDER BY nom ASC");
            } catch (PDOException $e) {
                error_log("Table etablissements n'existe pas");
            }
            
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
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        $anneeModel = new AnneeScolaire();
        $anneeActiveData = $anneeModel->getActive();
        $anneeActive = $anneeActiveData ? $anneeActiveData['id'] : null;
        
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
            require_once APP_PATH . '/Models/Niveau.php';
            $niveauModel = new Niveau();
            $niveaux = $niveauModel->all(['actif' => 1], 'ordre ASC');
            
            // Récupérer les séries
            require_once APP_PATH . '/Models/Serie.php';
            $serieModel = new Serie();
            $series = $serieModel->all(['actif' => 1], 'libelle ASC');
            
            // Récupérer les années scolaires
            $annees = [];
            try {
                $annees = $this->classeModel->query("SELECT * FROM annees_scolaires ORDER BY date_debut DESC");
            } catch (PDOException $e) {
                error_log("Table annees_scolaires n'existe pas");
            }
            
            // Récupérer les associations
            $filters = [
                'show_unassociated' => $showUnassociated,
                'niveau_id' => $filterNiveau,
                'serie_id' => $filterSection,
                'annee_scolaire_id' => $filterAnnee,
                'search' => $filterSearch
            ];
            $associations = $this->classeModel->getAssociationsWithFilters($filters);
            
            // Calculer les statistiques
            $stats = $this->classeModel->getAssociationStats();
            
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
        $eleves = $this->classeModel->getElevesWithPaymentStatus($classeId, $anneeId);
        $selectedClasse = $classeId ? $this->classeModel->find($classeId) : null;
        
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
            $stats = $this->classeModel->getAssociationStats();
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
     * Récupère les séries associées à un niveau (AJAX)
     */
    public function getSeriesByNiveau($id) {
        header('Content-Type: application/json');
        require_once APP_PATH . '/Models/Serie.php';
        $serieModel = new Serie();
        $series = $serieModel->getByNiveau($id);
        echo json_encode($series);
    }
}

