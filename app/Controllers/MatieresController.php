<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Matiere;
use App\Models\MatieresSeries;
use App\Models\MatieresNiveaux;
use PDOException;

/**
 * Contrôleur des matières
 */

class MatieresController extends BaseController {
    private $matiereModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->matiereModel = new Matiere();
    }
    
    public function list() {
        $matieres = $this->matiereModel->all(['actif' => 1], 'nom ASC');
        $this->view('matieres/list', ['matieres' => $matieres]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Générer le code automatiquement si non fourni
            $code = $_POST['code'] ?? '';
            if (empty($code)) {
                $code = generateMatricule('matiere', 'matieres');
            }
            
            $data = [
                'code' => $code,
                'nom' => $_POST['nom'] ?? '',
                'description' => $_POST['description'] ?? '',
                'actif' => 1,
            ];
            
            $id = $this->matiereModel->create($data);
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
            $this->redirect('matieres/list' . $iframeParam);
        } else {
            // Générer un code automatique pour pré-remplir
            $codeAuto = generateMatricule('matiere', 'matieres');
            $this->view('matieres/add', ['code_auto' => $codeAuto]);
        }
    }
    
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'] ?? '',
                'nom' => $_POST['nom'] ?? '',
                'description' => $_POST['description'] ?? '',
            ];
            
            $this->matiereModel->update($id, $data);
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
            $this->redirect('matieres/list' . $iframeParam);
        } else {
            $matiere = $this->matiereModel->find($id);
            if (!$matiere) {
                http_response_code(404);
                die("Matière non trouvée");
            }
            $this->view('matieres/edit', ['matiere' => $matiere]);
        }
    }
    
    public function details($id) {
        $matiere = $this->matiereModel->find($id);
        if (!$matiere) {
            http_response_code(404);
            die("Matière non trouvée");
        }

        require_once APP_PATH . '/Models/MatieresSeries.php';
        require_once APP_PATH . '/Models/MatieresNiveaux.php';
        $msModel = new MatieresSeries();
        $mnModel = new MatieresNiveaux();

        $coefficientsSeries = $msModel->getSeriesParMatiere($id);
        $coefficientsNiveaux = $mnModel->getNiveauxParMatiere($id);

        $this->view('matieres/details', [
            'matiere' => $matiere,
            'coefficientsSeries' => $coefficientsSeries,
            'coefficientsNiveaux' => $coefficientsNiveaux
        ]);
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->matiereModel->delete($id); // Suppression définitive
                $_SESSION['success_message'] = 'Matière supprimée avec succès !';
                $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
                $this->redirect('matieres/list' . $iframeParam);
            } catch (PDOException $e) {
                if ($e->getCode() == '23000') {
                    $_SESSION['error_message'] = "Impossible de supprimer cette matière car elle est liée à des classes ou d'autres données.";
                } else {
                    $_SESSION['error_message'] = "Erreur lors de la suppression : " . $e->getMessage();
                }
                $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
                $this->redirect('matieres/list' . $iframeParam);
            }
        } else {
            $matiere = $this->matiereModel->find($id);
            if (!$matiere) {
                http_response_code(404);
                die("Matière non trouvée");
            }
            $this->view('matieres/delete', ['matiere' => $matiere]);
        }
    }
}

