<?php
/**
 * Contrôleur des conseils de classe
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/ConseilClasse.php';
require_once APP_PATH . '/Models/DecisionConseil.php';

class ConseilsController extends BaseController {
    private $conseilModel;
    private $decisionModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->conseilModel = new ConseilClasse();
        $this->decisionModel = new DecisionConseil();
    }
    
    /**
     * Liste des conseils de classe
     */
    public function list() {
        $conseils = $this->conseilModel->query(
            "SELECT cc.*, 
                    c.nom as classe_nom,
                    p.nom as periode_nom,
                    a.libelle as annee_libelle,
                    pers.nom as president_nom, pers.prenom as president_prenom
             FROM conseils_classe cc
             JOIN classes c ON cc.classe_id = c.id
             JOIN periodes p ON cc.periode_id = p.id
             JOIN annees_scolaires a ON cc.annee_scolaire_id = a.id
             LEFT JOIN personnels pers ON cc.president_conseil = pers.id
             ORDER BY cc.date_conseil DESC"
        );
        
        $this->view('conseils/list', ['conseils' => $conseils]);
    }
    
    /**
     * Ajout d'un conseil de classe
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'classe_id' => $_POST['classe_id'],
                'periode_id' => $_POST['periode_id'],
                'annee_scolaire_id' => $_POST['annee_scolaire_id'],
                'date_conseil' => $_POST['date_conseil'],
                'president_conseil' => $_POST['president_conseil'] ?? null,
                'secretaire' => $_POST['secretaire'] ?? null,
                'ordre_du_jour' => $_POST['ordre_du_jour'] ?? '',
                'statut' => 'planifie'
            ];
            
            if ($this->conseilModel->create($data)) {
                $_SESSION['success_message'] = "Conseil de classe planifié avec succès.";
                $this->redirect('conseils/list');
            } else {
                $_SESSION['error_message'] = "Erreur lors de la planification du conseil.";
            }
        }
        
        $this->loadFormDependencies();
    }

    /**
     * Modification d'un conseil de classe
     */
    public function edit($id) {
        $conseil = $this->conseilModel->find($id);
        if (!$conseil) {
            $_SESSION['error_message'] = "Conseil non trouvé.";
            $this->redirect('conseils/list');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'date_conseil' => $_POST['date_conseil'],
                'heure_debut' => $_POST['heure_debut'] ?? null,
                'heure_fin' => $_POST['heure_fin'] ?? null,
                'president_conseil' => $_POST['president_conseil'] ?? null,
                'secretaire' => $_POST['secretaire'] ?? null,
                'ordre_du_jour' => $_POST['ordre_du_jour'] ?? '',
                'moyenne_classe' => $_POST['moyenne_classe'] ?? null,
                'taux_reussite' => $_POST['taux_reussite'] ?? null,
                'nb_felicitations' => $_POST['nb_felicitations'] ?? 0,
                'nb_encouragements' => $_POST['nb_encouragements'] ?? 0,
                'appreciation_generale' => $_POST['appreciation_generale'] ?? '',
                'statut' => $_POST['statut'] ?? 'termine'
            ];
            
            if ($this->conseilModel->update($id, $data)) {
                $_SESSION['success_message'] = "Conseil mis à jour avec succès.";
                $this->redirect('conseils/details/' . $id);
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour.";
            }
        }
        
        $this->loadFormDependencies($conseil);
    }

    /**
     * Suppression d'un conseil
     */
    public function delete($id) {
        if ($this->conseilModel->delete($id)) {
            $_SESSION['success_message'] = "Conseil supprimé avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression.";
        }
        $this->redirect('conseils/list');
    }

    /**
     * Affiche les détails d'un conseil de classe
     */
     public function details($id) {
        $conseil = $this->conseilModel->queryOne(
            "SELECT cc.*, 
                    c.nom as classe_nom,
                    p.nom as periode_nom,
                    a.libelle as annee_libelle,
                    pres.nom as president_nom, pres.prenom as president_prenom,
                    sec.nom as secretaire_nom, sec.prenom as secretaire_prenom
             FROM conseils_classe cc
             JOIN classes c ON cc.classe_id = c.id
             JOIN periodes p ON cc.periode_id = p.id
             JOIN annees_scolaires a ON cc.annee_scolaire_id = a.id
             LEFT JOIN personnels pres ON cc.president_conseil = pres.id
             LEFT JOIN personnels sec ON cc.secretaire = sec.id
             WHERE cc.id = ?",
            [$id]
        );
        
        if (!$conseil) {
            http_response_code(404);
            die("Conseil de classe non trouvé");
        }
        
        // Récupérer les décisions du conseil
        $decisions = $this->decisionModel->query(
            "SELECT dc.*, 
                    e.nom as eleve_nom, e.prenom as eleve_prenom, e.matricule
             FROM decisions_conseil dc
             JOIN eleves e ON dc.eleve_id = e.id
             WHERE dc.conseil_classe_id = ?
             ORDER BY e.nom, e.prenom",
            [$id]
        );
        
        $this->view('conseils/details', [
            'conseil' => $conseil,
            'decisions' => $decisions
        ]);
    }

    private function loadFormDependencies($conseil = null) {
        require_once APP_PATH . '/Models/BaseModel.php';
        $baseModel = new BaseModel();
        
        $classes = $baseModel->query("SELECT id, nom FROM classes WHERE statut = 'actif' ORDER BY nom");
        $periodes = $baseModel->query("SELECT id, nom FROM periodes WHERE actif = 1");
        $annees = $baseModel->query("SELECT id, libelle FROM annees_scolaires ORDER BY date_debut DESC");
        $personnels = $baseModel->query("SELECT id, nom, prenom FROM personnels WHERE statut = 'actif' ORDER BY nom");
        
        $view = $conseil ? 'conseils/edit' : 'conseils/add';
        $this->view($view, [
            'conseil' => $conseil,
            'classes' => $classes,
            'periodes' => $periodes,
            'annees' => $annees,
            'personnels' => $personnels
        ]);
    }
}