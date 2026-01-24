<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Periode;
use App\Models\AnneeScolaire;

/**
 * Contrôleur des périodes (Trimestres/Semestres)
 */

class PeriodesController extends BaseController {
    private $periodeModel;
    private $anneeScolaireModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->periodeModel = new Periode();
        $this->anneeScolaireModel = new AnneeScolaire();
    }
    
    public function list() {
        $anneeId = $_GET['annee_id'] ?? null;
        
        if (!$anneeId) {
            $anneeActive = $this->anneeScolaireModel->queryOne("SELECT id FROM annees_scolaires WHERE actif = 1");
            $anneeId = $anneeActive['id'] ?? null;
        }
        
        if (!$anneeId) {
            $_SESSION['error'] = "Veuillez d'abord créer une année scolaire.";
            $this->redirect('annees-scolaires/list');
        }
        
        $periodes = $this->periodeModel->all(['annee_scolaire_id' => $anneeId], 'numero ASC');
        $annee = $this->anneeScolaireModel->find($anneeId);
        $annees = $this->anneeScolaireModel->all([], 'date_debut DESC');
        
        $this->view('periodes/list', [
            'periodes' => $periodes,
            'annee' => $annee,
            'annees' => $annees
        ]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $anneeId = $_POST['annee_scolaire_id'] ?? null;
            $nom = $_POST['nom'] ?? '';
            $numero = $_POST['numero'] ?? '';
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            $actif = isset($_POST['actif']) ? 1 : 0;
            
            if ($anneeId && $nom && $dateDebut && $dateFin) {
                $data = [
                    'annee_scolaire_id' => $anneeId,
                    'nom' => $nom,
                    'numero' => $numero,
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'actif' => $actif
                ];
                
                $this->periodeModel->create($data);
                $_SESSION['success'] = "Période ajoutée avec succès.";
                $this->redirect('periodes/list?annee_id=' . $anneeId);
            } else {
                $_SESSION['error'] = "Tous les champs sont obligatoires.";
            }
        }
        
        $anneeId = $_GET['annee_id'] ?? null;
        $annee = $this->anneeScolaireModel->find($anneeId);
        if (!$annee) {
            $this->redirect('periodes/list');
        }
        
        $this->view('periodes/add', ['annee' => $annee]);
    }
    
    public function edit($id) {
        $periode = $this->periodeModel->find($id);
        if (!$periode) {
            $this->redirect('periodes/list');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $numero = $_POST['numero'] ?? '';
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            $actif = isset($_POST['actif']) ? 1 : 0;
            
            if ($nom && $dateDebut && $dateFin) {
                $data = [
                    'nom' => $nom,
                    'numero' => $numero,
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'actif' => $actif
                ];
                
                $this->periodeModel->update($id, $data);
                $_SESSION['success'] = "Période mise à jour avec succès.";
                $this->redirect('periodes/list?annee_id=' . $periode['annee_scolaire_id']);
            }
        }
        
        $this->view('periodes/edit', ['periode' => $periode]);
    }
    
    public function delete($id) {
        $periode = $this->periodeModel->find($id);
        if ($periode) {
            $this->periodeModel->delete($id);
            $_SESSION['success'] = "Période supprimée.";
            $this->redirect('periodes/list?annee_id=' . $periode['annee_scolaire_id']);
        }
        $this->redirect('periodes/list');
    }
}
