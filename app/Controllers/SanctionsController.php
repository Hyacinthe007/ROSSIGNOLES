<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Sanction;

/**
 * Contrôleur des sanctions
 */

class SanctionsController extends BaseController {
    private $sanctionModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->sanctionModel = new Sanction();
    }
    
    public function list() {
        $sanctions = $this->sanctionModel->all([], 'date_sanction DESC');
        $this->view('sanctions/list', ['sanctions' => $sanctions]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'eleve_id' => $_POST['eleve_id'] ?? '',
                'type_sanction_id' => $_POST['type_sanction_id'] ?? '',
                'date_sanction' => $_POST['date_sanction'] ?? date('Y-m-d'),
                'motif' => $_POST['motif'] ?? '',
                'donne_par' => $_SESSION['user_id'] ?? null,
            ];
            
            $id = $this->sanctionModel->create($data);
            $this->redirect('sanctions/details/' . $id);
        } else {
            $eleves = $this->sanctionModel->query("SELECT id, matricule, nom, prenom FROM eleves WHERE statut = 'actif' ORDER BY nom ASC");
            $types = $this->sanctionModel->query("SELECT * FROM types_sanctions WHERE actif = 1 ORDER BY libelle ASC");
            $this->view('sanctions/add', ['eleves' => $eleves, 'types' => $types]);
        }
    }
    
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'eleve_id' => $_POST['eleve_id'] ?? '',
                'type_sanction_id' => $_POST['type_sanction_id'] ?? '',
                'date_sanction' => $_POST['date_sanction'] ?? '',
                'motif' => $_POST['motif'] ?? '',
            ];
            
            $this->sanctionModel->update($id, $data);
            $this->redirect('sanctions/details/' . $id);
        } else {
            $sanction = $this->sanctionModel->find($id);
            if (!$sanction) {
                http_response_code(404);
                die("Sanction non trouvée");
            }
            $eleves = $this->sanctionModel->query("SELECT id, matricule, nom, prenom FROM eleves WHERE statut = 'actif' ORDER BY nom ASC");
            $types = $this->sanctionModel->query("SELECT * FROM types_sanctions WHERE actif = 1 ORDER BY libelle ASC");
            $this->view('sanctions/edit', ['sanction' => $sanction, 'eleves' => $eleves, 'types' => $types]);
        }
    }
    
    public function details($id) {
        $sanction = $this->sanctionModel->queryOne(
            "SELECT s.*, ts.libelle as type_sanction_libelle, ts.gravite as type_gravite
             FROM sanctions s
             INNER JOIN types_sanctions ts ON s.type_sanction_id = ts.id
             WHERE s.id = ?",
            [$id]
        );
        if (!$sanction) {
            http_response_code(404);
            die("Sanction non trouvée");
        }
        $eleve = $this->sanctionModel->queryOne("SELECT * FROM eleves WHERE id = ?", [$sanction['eleve_id']]);
        $this->view('sanctions/details', ['sanction' => $sanction, 'eleve' => $eleve]);
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->sanctionModel->delete($id);
            $this->redirect('sanctions/list');
        } else {
            $sanction = $this->sanctionModel->find($id);
            if (!$sanction) {
                http_response_code(404);
                die("Sanction non trouvée");
            }
            $this->view('sanctions/delete', ['sanction' => $sanction]);
        }
    }
}

