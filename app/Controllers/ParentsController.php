<?php
/**
 * Contrôleur des parents
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Parent.php';

class ParentsController extends BaseController {
    private $parentModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->parentModel = new ParentModel();
    }
    
    public function list() {
        $search = $_GET['search'] ?? '';
        
        if (!empty($search)) {
            // Recherche étendue : parent (nom, prénom, téléphone, email) + enfants (nom, prénom)
            $parents = $this->parentModel->query(
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
            // Récupérer tous les parents avec le nombre d'enfants
            $parents = $this->parentModel->query(
                "SELECT p.*, COUNT(DISTINCT ep.eleve_id) as nb_enfants
                 FROM parents p
                 LEFT JOIN eleves_parents ep ON p.id = ep.parent_id
                 GROUP BY p.id
                 ORDER BY p.nom ASC"
            );
        }
        
        $this->view('parents/list', [
            'parents' => $parents,
            'search' => $search
        ]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'] ?? '',
                'prenom' => $_POST['prenom'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'email' => $_POST['email'] ?? '',
                'adresse' => $_POST['adresse'] ?? '',
                'sexe' => $_POST['sexe'] ?? '',
                'type_parent' => $_POST['type_parent'] ?? '',
                'profession' => $_POST['profession'] ?? ''
            ];
            
            $id = $this->parentModel->create($data);
            $this->redirect('parents/details/' . $id);
        } else {
            $this->view('parents/add');
        }
    }
    
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'] ?? '',
                'prenom' => $_POST['prenom'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'email' => $_POST['email'] ?? '',
                'adresse' => $_POST['adresse'] ?? '',
                'sexe' => $_POST['sexe'] ?? '',
                'type_parent' => $_POST['type_parent'] ?? '',
                'profession' => $_POST['profession'] ?? ''
            ];
            
            $this->parentModel->update($id, $data);
            $this->redirect('parents/details/' . $id);
        } else {
            $parent = $this->parentModel->find($id);
            if (!$parent) {
                http_response_code(404);
                die("Parent non trouvé");
            }
            $this->view('parents/edit', ['parent' => $parent]);
        }
    }
    
    public function details($id) {
        $parent = $this->parentModel->find($id);
        if (!$parent) {
            http_response_code(404);
            die("Parent non trouvé");
        }
        
        $enfants = $this->parentModel->getEnfants($id);
        
        $this->view('parents/details', [
            'parent' => $parent,
            'enfants' => $enfants
        ]);
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->parentModel->delete($id);
            $this->redirect('parents/list');
        } else {
            $parent = $this->parentModel->find($id);
            if (!$parent) {
                http_response_code(404);
                die("Parent non trouvé");
            }
            $this->view('parents/delete', ['parent' => $parent]);
        }
    }
}

