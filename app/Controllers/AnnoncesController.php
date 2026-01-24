<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Annonce;

/**
 * Contrôleur des annonces
 */

class AnnoncesController extends BaseController {
    private $annonceModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->annonceModel = new Annonce();
    }
    
    public function list() {
        $annonces = $this->annonceModel->all([], 'created_at DESC');
        $this->view('annonces/list', ['annonces' => $annonces]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => $_POST['titre'] ?? '',
                'contenu' => $_POST['contenu'] ?? '',
                'type' => $_POST['type'] ?? 'generale',
                'cible' => $_POST['cible'] ?? 'tous',
                'classe_id' => !empty($_POST['classe_id']) ? $_POST['classe_id'] : null,
                'date_debut' => !empty($_POST['date_debut']) ? $_POST['date_debut'] : date('Y-m-d'),
                'date_fin' => !empty($_POST['date_fin']) ? $_POST['date_fin'] : date('Y-m-d', strtotime('+7 days')),
                'publie_par' => $_SESSION['user_id'],
                'actif' => 1
            ];
            
            if ($this->annonceModel->create($data)) {
                $_SESSION['success_message'] = "Annonce publiée avec succès.";
                $this->redirect('annonces/list');
            } else {
                $_SESSION['error_message'] = "Erreur lors de la publication de l'annonce.";
            }
        }
        
        $this->view('annonces/add');
    }

    public function edit($id) {
        $annonce = $this->annonceModel->find($id);
        if (!$annonce) {
            $_SESSION['error_message'] = "Annonce non trouvée.";
            $this->redirect('annonces/list');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => $_POST['titre'] ?? '',
                'contenu' => $_POST['contenu'] ?? '',
                'type' => $_POST['type'] ?? 'generale',
                'cible' => $_POST['cible'] ?? 'tous',
                'classe_id' => !empty($_POST['classe_id']) ? $_POST['classe_id'] : null,
                'date_debut' => !empty($_POST['date_debut']) ? $_POST['date_debut'] : date('Y-m-d'),
                'date_fin' => !empty($_POST['date_fin']) ? $_POST['date_fin'] : date('Y-m-d', strtotime('+7 days')),
                'actif' => isset($_POST['actif']) ? 1 : 0
            ];
            
            if ($this->annonceModel->update($id, $data)) {
                $_SESSION['success_message'] = "Annonce mise à jour avec succès.";
                $this->redirect('annonces/list');
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour de l'annonce.";
            }
        }
        
        $this->view('annonces/edit', ['annonce' => $annonce]);
    }

    public function delete($id) {
        if ($this->annonceModel->delete($id)) {
            $_SESSION['success_message'] = "Annonce supprimée avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression de l'annonce.";
        }
        $this->redirect('annonces/list');
    }
}
