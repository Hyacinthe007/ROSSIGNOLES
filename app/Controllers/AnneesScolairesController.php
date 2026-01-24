<?php
/**
 * Contrôleur des années scolaires
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/AnneeScolaire.php';

class AnneesScolairesController extends BaseController {
    private $anneeScolaireModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->anneeScolaireModel = new AnneeScolaire();
    }
    
    public function list() {
        $annees = $this->anneeScolaireModel->all([], 'date_debut DESC');
        $this->view('annees_scolaires/list', ['annees' => $annees]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $libelle = $_POST['libelle'] ?? '';
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            $activer = isset($_POST['activer']) && $_POST['activer'] == '1';
            
            // Validation
            $errors = [];
            
            if (empty($libelle)) {
                $errors[] = "Le libellé est requis";
            }
            
            if (empty($dateDebut)) {
                $errors[] = "La date de début est requise";
            }
            
            if (empty($dateFin)) {
                $errors[] = "La date de fin est requise";
            }
            
            if ($dateDebut && $dateFin && $dateDebut >= $dateFin) {
                $errors[] = "La date de fin doit être après la date de début";
            }
            
            // Vérifier le chevauchement
            if ($dateDebut && $dateFin && !$this->anneeScolaireModel->validateDates($dateDebut, $dateFin)) {
                $errors[] = "Cette période chevauche une année scolaire existante";
            }
            
            if (empty($errors)) {
                $data = [
                    'libelle' => $libelle,
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'actif' => $activer ? 1 : 0
                ];
                
                try {
                    if ($activer) {
                        // Désactiver toutes les autres années
                        $this->anneeScolaireModel->query("UPDATE annees_scolaires SET actif = 0");
                    }
                    
                    $id = $this->anneeScolaireModel->create($data);
                    $_SESSION['success'] = "Année scolaire créée avec succès";
                    $this->redirect('/annees-scolaires/details/' . $id);
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur lors de la création : " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode("<br>", $errors);
            }
        }
        
        $this->view('annees_scolaires/add');
    }
    
    public function edit($id) {
        $annee = $this->anneeScolaireModel->find($id);
        if (!$annee) {
            http_response_code(404);
            die("Année scolaire non trouvée");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $libelle = $_POST['libelle'] ?? '';
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            
            // Validation
            $errors = [];
            
            if (empty($libelle)) {
                $errors[] = "Le libellé est requis";
            }
            
            if (empty($dateDebut)) {
                $errors[] = "La date de début est requise";
            }
            
            if (empty($dateFin)) {
                $errors[] = "La date de fin est requise";
            }
            
            if ($dateDebut && $dateFin && $dateDebut >= $dateFin) {
                $errors[] = "La date de fin doit être après la date de début";
            }
            
            // Vérifier le chevauchement (exclure l'année actuelle)
            if ($dateDebut && $dateFin && !$this->anneeScolaireModel->validateDates($dateDebut, $dateFin, $id)) {
                $errors[] = "Cette période chevauche une année scolaire existante";
            }
            
            if (empty($errors)) {
                $data = [
                    'libelle' => $libelle,
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin
                ];
                
                try {
                    $this->anneeScolaireModel->update($id, $data);
                    $_SESSION['success'] = "Année scolaire modifiée avec succès";
                    $this->redirect('/annees-scolaires/details/' . $id);
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode("<br>", $errors);
            }
        }
        
        $this->view('annees_scolaires/edit', ['annee' => $annee]);
    }
    
    public function details($id) {
        $annee = $this->anneeScolaireModel->find($id);
        if (!$annee) {
            http_response_code(404);
            die("Année scolaire non trouvée");
        }
        
        $statistiques = $this->anneeScolaireModel->getStatistiques($id);
        $classes = $this->anneeScolaireModel->getClasses($id);
        
        $this->view('annees_scolaires/details', [
            'annee' => $annee,
            'statistiques' => $statistiques,
            'classes' => $classes
        ]);
    }
    
    public function activate($id) {
        $annee = $this->anneeScolaireModel->find($id);
        if (!$annee) {
            http_response_code(404);
            die("Année scolaire non trouvée");
        }
        
        try {
            $this->anneeScolaireModel->activate($id);
            $_SESSION['success'] = "Année scolaire activée avec succès";
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de l'activation : " . $e->getMessage();
        }
        
        $this->redirect('/annees-scolaires/list');
    }
}
