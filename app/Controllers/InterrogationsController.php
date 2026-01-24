<?php
/**
 * Contrôleur InterrogationsController
 * Gère les interrogations et devoirs
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Interrogation.php';
require_once APP_PATH . '/Models/Classe.php';
require_once APP_PATH . '/Models/Matiere.php';
require_once APP_PATH . '/Models/Periode.php';
require_once APP_PATH . '/Models/AnneeScolaire.php';

class InterrogationsController extends BaseController {
    
    private $interrogationModel;
    private $classeModel;
    private $matiereModel;
    private $periodeModel;
    private $anneeScolaireModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->interrogationModel = new Interrogation();
        $this->classeModel = new Classe();
        $this->matiereModel = new Matiere();
        $this->periodeModel = new Periode();
        $this->anneeScolaireModel = new AnneeScolaire();
    }
    
    /**
     * Liste des interrogations
     */
    public function list() {
        $anneeScolaire = $this->anneeScolaireModel->getActive();
        $classeId = $_GET['classe_id'] ?? null;
        $matiereId = $_GET['matiere_id'] ?? null;
        
        $sql = "SELECT i.*, 
                       c.nom as classe_nom, 
                       m.nom as matiere_nom, 
                       p.nom as periode_nom,
                       COUNT(ni.id) as nb_notes
                FROM interrogations i
                INNER JOIN classes c ON i.classe_id = c.id
                INNER JOIN matieres m ON i.matiere_id = m.id
                INNER JOIN periodes p ON i.periode_id = p.id
                LEFT JOIN notes_interrogations ni ON i.id = ni.interrogation_id
                WHERE i.annee_scolaire_id = ?";
        
        $params = [$anneeScolaire['id']];
        
        if ($classeId) {
            $sql .= " AND i.classe_id = ?";
            $params[] = $classeId;
        }
        
        if ($matiereId) {
            $sql .= " AND i.matiere_id = ?";
            $params[] = $matiereId;
        }
        
        // Tri par classe puis par date (du plus récent au plus ancien)
        $sql .= " GROUP BY i.id ORDER BY c.nom ASC, i.date_interrogation DESC";
        
        $interrogations = $this->interrogationModel->query($sql, $params);
        $classes = $this->classeModel->getAll();
        $matieres = $this->matiereModel->getAll();
        
        $this->view('interrogations/list', [
            'interrogations' => $interrogations,
            'classes' => $classes,
            'matieres' => $matieres,
            'filters' => ['classe_id' => $classeId, 'matiere_id' => $matiereId]
        ]);
    }
    
    /**
     * Ajouter une interrogation
     */
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
        $filename = uniqid('sujet_') . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/sujets/' . $filename;
        } else {
            throw new Exception("Impossible de déplacer le fichier téléchargé.");
        }
    }

    /**
     * Ajouter une interrogation
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
                    'date_interrogation' => $_POST['date_interrogation'],
                    'duree' => $_POST['duree'] ?? null,
                    'note_sur' => $_POST['note_sur'] ?? 20,
                    'description' => $_POST['description'] ?? null,
                    'statut' => $_POST['statut'] ?? 'planifiee',
                    'personnel_id' => $_SESSION['user_reference_id'] ?? null,
                    'fichier_sujet' => $fichierSujet
                ];
                
                if ($this->interrogationModel->create($data)) {
                    $_SESSION['success'] = "Interrogation ajoutée avec succès";
                    $redirectUrl = '/ROSSIGNOLES/interrogations/list';
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
        
        $this->view('interrogations/add', [
            'classes' => $classes,
            'matieres' => $matieres,
            'periodes' => $periodes
        ]);
    }
    
    /**
     * Modifier une interrogation
     */
    public function edit($id) {
        $interrogation = $this->interrogationModel->findById($id);
        
        if (!$interrogation) {
            $_SESSION['error'] = "Interrogation introuvable";
            $redirectUrl = '/ROSSIGNOLES/interrogations/list';
            if (isset($_GET['iframe'])) $redirectUrl .= '?iframe=1';
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Gestion du fichier sujet (Optionnel à la modif, sauf si aucun fichier précédent)
                // Si on upload un nouveau fichier, on le prend. Sinon on garde l'ancien.
                // MAIS si c'est "obligatoire d'avoir un fichier", on doit s'assurer qu'il y en a un au final.
                
                $fichierSujet = $this->handleFileUpload($_FILES['fichier_sujet'] ?? null, false);
                
                $data = [
                    'classe_id' => $_POST['classe_id'],
                    'matiere_id' => $_POST['matiere_id'],
                    'periode_id' => $_POST['periode_id'],
                    'nom' => $_POST['nom'],
                    'date_interrogation' => $_POST['date_interrogation'],
                    'duree' => $_POST['duree'],
                    'note_sur' => $_POST['note_sur'],
                    'description' => $_POST['description'],
                    'statut' => $_POST['statut']
                ];

                if ($fichierSujet) {
                    $data['fichier_sujet'] = $fichierSujet;
                    // TODO: Supprimer l'ancien fichier si besoin
                }
                
                if ($this->interrogationModel->update($id, $data)) {
                    $_SESSION['success'] = "Interrogation modifiée avec succès";
                    $redirectUrl = '/ROSSIGNOLES/interrogations/list';
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
        
        $this->view('interrogations/edit', [
            'interrogation' => $interrogation,
            'classes' => $classes,
            'matieres' => $matieres,
            'periodes' => $periodes
        ]);
    }
    
    /**
     * Supprimer une interrogation
     */
    public function delete($id) {
        // Vérifier s'il y a des notes associées
        $notes = $this->interrogationModel->query("SELECT count(*) as count FROM notes_interrogations WHERE interrogation_id = ?", [$id]);
        
        if ($notes[0]['count'] > 0) {
            $_SESSION['error'] = "Impossible de supprimer : des notes sont déjà saisies";
            $redirectUrl = '/ROSSIGNOLES/interrogations/list';
            if (isset($_GET['iframe'])) $redirectUrl .= '?iframe=1';
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        if ($this->interrogationModel->delete($id)) {
            $_SESSION['success'] = "Interrogation supprimée";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }
        
        $redirectUrl = '/ROSSIGNOLES/interrogations/list';
        if (isset($_GET['iframe'])) $redirectUrl .= '?iframe=1';
        header('Location: ' . $redirectUrl);
    }
}
