<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ExamenFinal;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Periode;
use App\Models\AnneeScolaire;
use Exception;

/**
 * Contrôleur ExamensController
 * Gère les examens finaux
 */

class ExamensController extends BaseController {
    
    private $examenModel;
    private $classeModel;
    private $matiereModel;
    private $periodeModel;
    private $anneeScolaireModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->examenModel = new ExamenFinal();
        $this->classeModel = new Classe();
        $this->matiereModel = new Matiere();
        $this->periodeModel = new Periode();
        $this->anneeScolaireModel = new AnneeScolaire();
    }
    
    /**
     * Liste des examens
     */
    public function list() {
        $anneeScolaire = $this->anneeScolaireModel->getActive();
        $classeId = $_GET['classe_id'] ?? null;
        $periodeId = $_GET['periode_id'] ?? null;
        
        $sql = "SELECT e.*, 
                       c.nom as classe_nom, 
                       m.nom as matiere_nom, 
                       p.nom as periode_nom,
                       COUNT(ne.id) as nb_notes
                FROM examens_finaux e
                INNER JOIN classes c ON e.classe_id = c.id
                INNER JOIN matieres m ON e.matiere_id = m.id
                INNER JOIN periodes p ON e.periode_id = p.id
                LEFT JOIN notes_examens ne ON e.id = ne.examen_id
                WHERE e.annee_scolaire_id = ?";
        
        $params = [$anneeScolaire['id']];
        
        if ($classeId) {
            $sql .= " AND e.classe_id = ?";
            $params[] = $classeId;
        }
        
        if ($periodeId) {
            $sql .= " AND e.periode_id = ?";
            $params[] = $periodeId;
        }
        
        $sql .= " GROUP BY e.id ORDER BY e.date_examen DESC";
        
        $examens = $this->examenModel->query($sql, $params);
        $classes = $this->classeModel->getAll();
        $periodes = $this->periodeModel->getAll();
        
        $this->view('examens/list', [
            'examens' => $examens,
            'classes' => $classes,
            'periodes' => $periodes,
            'filters' => ['classe_id' => $classeId, 'periode_id' => $periodeId]
        ]);
    }
    
    /**
     * Gère l'upload du fichier sujet
     */
    private function handleFileUpload($file, $isRequired = true) {
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            if ($isRequired) {
                throw new Exception("Le fichier du sujet est obligatoire.");
            }
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur lors du téléchargement du fichier.");
        }

        // Validation du type (PDF ou Images)
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Type de fichier non autorisé. Seuls PDF, JPG et PNG sont acceptés.");
        }

        // Création du dossier si inexistant
        $uploadDir =  __DIR__ . '/../../public/uploads/sujets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('sujet_exam_') . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/sujets/' . $filename;
        } else {
            throw new Exception("Impossible de déplacer le fichier téléchargé.");
        }
    }

    /**
     * Ajouter un examen
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Gestion du fichier sujet (Obligatoire)
                $fichierSujet = $this->handleFileUpload($_FILES['fichier_sujet'] ?? null, true);

                $data = [
                    'classe_id' => $_POST['classe_id'],
                    'matiere_id' => $_POST['matiere_id'],
                    'periode_id' => $_POST['periode_id'],
                    'annee_scolaire_id' => $this->anneeScolaireModel->getActive()['id'],
                    'nom' => $_POST['nom'],
                    'date_examen' => $_POST['date_examen'],
                    'heure_debut' => $_POST['heure_debut'] ?? null,
                    'heure_fin' => $_POST['heure_fin'] ?? null,
                    'duree' => $_POST['duree'] ?? null,
                    'note_sur' => $_POST['note_sur'] ?? 20,
                    'consignes' => $_POST['consignes'] ?? null,
                    'statut' => $_POST['statut'] ?? 'planifie',
                    'fichier_sujet' => $fichierSujet
                ];
                
                if ($this->examenModel->create($data)) {
                    $_SESSION['success'] = "Examen ajouté avec succès";
                    $redirectUrl = '/ROSSIGNOLES/examens/list';
                    if (isset($_GET['iframe'])) $redirectUrl .= '?iframe=1';
                    header('Location: ' . $redirectUrl);
                    exit;
                } else {
                    $_SESSION['error'] = "Erreur lors de l'ajout en base de données";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        $classes = $this->classeModel->getAll();
        $matieres = $this->matiereModel->getAll();
        $periodes = $this->periodeModel->getAll();
        
        $this->view('examens/add', [
            'classes' => $classes,
            'matieres' => $matieres,
            'periodes' => $periodes
        ]);
    }
    
    /**
     * Modifier un examen
     */
    public function edit($id) {
        $examen = $this->examenModel->findById($id);
        
        if (!$examen) {
            $_SESSION['error'] = "Examen introuvable";
            $redirectUrl = '/ROSSIGNOLES/examens/list';
            if (isset($_GET['iframe'])) $redirectUrl .= '?iframe=1';
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Gestion du fichier sujet (Optionnel à la modif)
                $fichierSujet = $this->handleFileUpload($_FILES['fichier_sujet'] ?? null, false);

                $data = [
                    'classe_id' => $_POST['classe_id'],
                    'matiere_id' => $_POST['matiere_id'],
                    'periode_id' => $_POST['periode_id'],
                    'nom' => $_POST['nom'],
                    'date_examen' => $_POST['date_examen'],
                    'heure_debut' => $_POST['heure_debut'],
                    'heure_fin' => $_POST['heure_fin'],
                    'duree' => $_POST['duree'],
                    'note_sur' => $_POST['note_sur'],
                    'consignes' => $_POST['consignes'],
                    'statut' => $_POST['statut']
                ];
                
                if ($fichierSujet) {
                    $data['fichier_sujet'] = $fichierSujet;
                    // TODO: Supprimer l'ancien fichier si besoin
                }

                if ($this->examenModel->update($id, $data)) {
                    $_SESSION['success'] = "Examen modifié avec succès";
                    $redirectUrl = '/ROSSIGNOLES/examens/list';
                    if (isset($_GET['iframe'])) $redirectUrl .= '?iframe=1';
                    header('Location: ' . $redirectUrl);
                    exit;
                } else {
                    $_SESSION['error'] = "Erreur lors de la modification";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        $classes = $this->classeModel->getAll();
        $matieres = $this->matiereModel->getAll();
        $periodes = $this->periodeModel->getAll();
        
        $this->view('examens/edit', [
            'examen' => $examen,
            'classes' => $classes,
            'matieres' => $matieres,
            'periodes' => $periodes
        ]);
    }
    
    /**
     * Supprimer un examen
     */
    public function delete($id) {
        // Vérifier s'il y a des notes associées
        $notes = $this->examenModel->query("SELECT count(*) as count FROM notes_examens WHERE examen_id = ?", [$id]);
        
        if ($notes[0]['count'] > 0) {
            $_SESSION['error'] = "Impossible de supprimer : des notes sont déjà saisies";
            $redirectUrl = '/ROSSIGNOLES/examens/list';
            if (isset($_GET['iframe'])) $redirectUrl .= '?iframe=1';
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        if ($this->examenModel->delete($id)) {
            $_SESSION['success'] = "Examen supprimé";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }
        
        $redirectUrl = '/ROSSIGNOLES/examens/list';
        if (isset($_GET['iframe'])) $redirectUrl .= '?iframe=1';
        header('Location: ' . $redirectUrl);
    }
}
