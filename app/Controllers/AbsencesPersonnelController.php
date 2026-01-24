<?php
/**
 * Contrôleur des absences du personnel
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/AbsencePersonnel.php';
require_once APP_PATH . '/Models/Personnel.php';

class AbsencesPersonnelController extends BaseController {
    private $absenceModel;
    private $personnelModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->absenceModel = new AbsencePersonnel();
        $this->personnelModel = new Personnel();
    }
    
    /**
     * Liste des absences du personnel
     */
    public function list() {
        $filters = [];
        
        // Récupérer les filtres depuis la requête
        if (!empty($_GET['personnel_id'])) {
            $filters['personnel_id'] = $_GET['personnel_id'];
        }
        
        if (!empty($_GET['type_absence'])) {
            $filters['type_absence'] = $_GET['type_absence'];
        }
        
        if (!empty($_GET['statut'])) {
            $filters['statut'] = $_GET['statut'];
        }
        
        if (!empty($_GET['date_debut'])) {
            $filters['date_debut'] = $_GET['date_debut'];
        }
        
        if (!empty($_GET['date_fin'])) {
            $filters['date_fin'] = $_GET['date_fin'];
        }
        
        $absences = $this->absenceModel->getAllWithFilters($filters);
        
        // Récupérer tous les personnels pour le filtre
        $personnels = $this->personnelModel->query(
            "SELECT id, matricule, nom, prenom FROM personnels WHERE statut = 'actif' ORDER BY nom ASC"
        );
        
        $this->view('absences_personnel/list', [
            'absences' => $absences,
            'personnels' => $personnels,
            'filters' => $filters
        ]);
    }
    
    /**
     * Formulaire d'ajout d'une absence
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentUserId = $_SESSION['user_id'] ?? null;
            
            $data = [
                'personnel_id' => $_POST['personnel_id'] ?? '',
                'type_absence' => $_POST['type_absence'] ?? 'absence_autorisee',
                'date_debut' => $_POST['date_debut'] ?? '',
                'date_fin' => $_POST['date_fin'] ?? '',
                'nb_jours' => $_POST['nb_jours'] ?? 0,
                'motif' => $_POST['motif'] ?? '',
                'statut' => $_POST['statut'] ?? 'demande',
                'demande_par' => $currentUserId,
                'date_demande' => date('Y-m-d H:i:s'),
            ];
            
            // Ajouter les champs optionnels si présents
            if (!empty($_POST['piece_justificative'])) {
                $data['piece_justificative'] = $_POST['piece_justificative'];
            }
            
            if (!empty($_POST['remplace_par'])) {
                $data['remplace_par'] = $_POST['remplace_par'];
            }
            
            if (!empty($_POST['commentaire_remplacement'])) {
                $data['commentaire_remplacement'] = $_POST['commentaire_remplacement'];
            }
            
            $id = $this->absenceModel->create($data);
            $_SESSION['success'] = "Absence enregistrée avec succès";
            $this->redirect('absences_personnel/details/' . $id);
        } else {
            // Récupérer tous les personnels actifs
            $personnels = $this->personnelModel->query(
                "SELECT id, matricule, nom, prenom FROM personnels WHERE statut = 'actif' ORDER BY nom ASC"
            );
            
            $this->view('absences_personnel/add', [
                'personnels' => $personnels
            ]);
        }
    }
    
    /**
     * Formulaire d'édition d'une absence
     */
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'personnel_id' => $_POST['personnel_id'] ?? '',
                'type_absence' => $_POST['type_absence'] ?? 'absence_autorisee',
                'date_debut' => $_POST['date_debut'] ?? '',
                'date_fin' => $_POST['date_fin'] ?? '',
                'nb_jours' => $_POST['nb_jours'] ?? 0,
                'motif' => $_POST['motif'] ?? '',
                'statut' => $_POST['statut'] ?? 'demande',
            ];
            
            // Ajouter les champs optionnels si présents
            if (isset($_POST['piece_justificative'])) {
                $data['piece_justificative'] = $_POST['piece_justificative'];
            }
            
            if (!empty($_POST['remplace_par'])) {
                $data['remplace_par'] = $_POST['remplace_par'];
            } elseif (isset($_POST['remplace_par'])) {
                // Si le champ est présent mais vide, on le met à NULL
                $data['remplace_par'] = null;
            }
            
            if (isset($_POST['commentaire_remplacement'])) {
                $data['commentaire_remplacement'] = $_POST['commentaire_remplacement'];
            }
            
            // Si le statut change à "validee" et que l'absence n'était pas encore validée
            if ($_POST['statut'] === 'validee') {
                $absenceActuelle = $this->absenceModel->find($id);
                if ($absenceActuelle && $absenceActuelle['statut'] !== 'validee') {
                    $data['valide_par'] = $_SESSION['user_id'] ?? null;
                    $data['date_validation'] = date('Y-m-d H:i:s');
                }
            }
            
            $this->absenceModel->update($id, $data);
            $_SESSION['success'] = "Absence mise à jour avec succès";
            $this->redirect('absences_personnel/details/' . $id);
        } else {
            $absence = $this->absenceModel->getWithDetails($id);
            if (!$absence) {
                http_response_code(404);
                die("Absence non trouvée");
            }
            
            // Récupérer tous les personnels actifs
            $personnels = $this->personnelModel->query(
                "SELECT id, matricule, nom, prenom FROM personnels WHERE statut = 'actif' ORDER BY nom ASC"
            );
            
            $this->view('absences_personnel/edit', [
                'absence' => $absence,
                'personnels' => $personnels
            ]);
        }
    }
    
    /**
     * Affiche les détails d'une absence
     */
    public function details($id) {
        $absence = $this->absenceModel->getWithDetails($id);
        if (!$absence) {
            http_response_code(404);
            die("Absence non trouvée");
        }
        
        $this->view('absences_personnel/details', ['absence' => $absence]);
    }
    
    /**
     * Supprime une absence
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->absenceModel->delete($id);
            $_SESSION['success'] = "Absence supprimée avec succès";
            $this->redirect('absences_personnel/list');
        } else {
            $absence = $this->absenceModel->getWithDetails($id);
            if (!$absence) {
                http_response_code(404);
                die("Absence non trouvée");
            }
            
            $this->view('absences_personnel/delete', ['absence' => $absence]);
        }
    }
    
    /**
     * Valide une demande d'absence
     */
    public function valider($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'statut' => 'validee',
                'valide_par' => $_SESSION['user_id'] ?? null,
                'date_validation' => date('Y-m-d H:i:s')
            ];
            
            // Ajouter le remplaçant si spécifié
            if (!empty($_POST['remplace_par'])) {
                $data['remplace_par'] = $_POST['remplace_par'];
            }
            
            if (!empty($_POST['commentaire_remplacement'])) {
                $data['commentaire_remplacement'] = $_POST['commentaire_remplacement'];
            }
            
            $this->absenceModel->update($id, $data);
            $_SESSION['success'] = "Absence validée avec succès";
            $this->redirect('absences_personnel/details/' . $id);
        }
    }
    
    /**
     * Refuse une demande d'absence
     */
    public function refuser($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'statut' => 'refusee',
                'valide_par' => $_SESSION['user_id'] ?? null,
                'date_validation' => date('Y-m-d H:i:s'),
                'motif_refus' => $_POST['motif_refus'] ?? ''
            ];
            
            $this->absenceModel->update($id, $data);
            $_SESSION['success'] = "Absence refusée avec succès";
            $this->redirect('absences_personnel/details/' . $id);
        }
    }
}