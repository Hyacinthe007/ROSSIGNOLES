<?php
/**
 * Contrôleur du calendrier scolaire (vacances et jours fériés)
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/CalendrierScolaire.php';
require_once APP_PATH . '/Models/AnneeScolaire.php';

class CalendrierController extends BaseController {
    private $model;
    private $anneeModel;

    public function __construct() {
        $this->requireAuth();
        $this->model = new CalendrierScolaire();
        $this->anneeModel = new AnneeScolaire();
    }

    public function list() {
        $anneeId = $_GET['annee_id'] ?? null;
        if (!$anneeId) {
            $activeAnnee = $this->anneeModel->getActive();
            $anneeId = $activeAnnee ? $activeAnnee['id'] : null;
        }

        $annees = $this->anneeModel->all([], 'date_debut DESC');
        $annee = $anneeId ? $this->anneeModel->find($anneeId) : null;
        $events = $anneeId ? $this->model->getByAnnee($anneeId) : [];

        $this->view('calendrier/list', [
            'events' => $events,
            'annees' => $annees,
            'annee' => $annee
        ]);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'annee_scolaire_id' => $_POST['annee_scolaire_id'],
                'type' => $_POST['type'],
                'libelle' => $_POST['libelle'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'description' => $_POST['description'] ?? null,
                'bloque_cours' => isset($_POST['bloque_cours']) ? 1 : 0
            ];

            try {
                $this->model->create($data);
                $_SESSION['success'] = "Événement ajouté au calendrier.";
                $iframeParam = isset($_GET['iframe']) ? '?iframe=1&annee_id=' . $data['annee_scolaire_id'] : '?annee_id=' . $data['annee_scolaire_id'];
                $this->redirect('calendrier/list' . $iframeParam);
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur : " . $e->getMessage();
            }
        }

        $annees = $this->anneeModel->all([], 'date_debut DESC');
        $activeAnnee = $this->anneeModel->getActive();
        
        $this->view('calendrier/form', [
            'annees' => $annees,
            'activeAnnee' => $activeAnnee
        ]);
    }

    public function edit($id) {
        $event = $this->model->find($id);
        if (!$event) die("Événement non trouvé");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'annee_scolaire_id' => $_POST['annee_scolaire_id'],
                'type' => $_POST['type'],
                'libelle' => $_POST['libelle'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'description' => $_POST['description'] ?? null,
                'bloque_cours' => isset($_POST['bloque_cours']) ? 1 : 0
            ];

            try {
                $this->model->update($id, $data);
                $_SESSION['success'] = "Événement mis à jour.";
                $iframeParam = isset($_GET['iframe']) ? '?iframe=1&annee_id=' . $data['annee_scolaire_id'] : '?annee_id=' . $data['annee_scolaire_id'];
                $this->redirect('calendrier/list' . $iframeParam);
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur : " . $e->getMessage();
            }
        }

        $annees = $this->anneeModel->all([], 'date_debut DESC');
        $this->view('calendrier/form', [
            'event' => $event,
            'annees' => $annees
        ]);
    }

    public function delete($id) {
        $event = $this->model->find($id);
        if ($event) {
            $anneeId = $event['annee_scolaire_id'];
            $this->model->delete($id);
            $_SESSION['success'] = "Événement supprimé.";
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1&annee_id=' . $anneeId : '?annee_id=' . $anneeId;
            $this->redirect('calendrier/list' . $iframeParam);
        }
    }
}
