<?php
/**
 * Contrôleur des absences
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Absence.php';

class AbsencesController extends BaseController {
    private $absenceModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->absenceModel = new Absence();
    }
    
    public function list() {
        // Détection automatique du type basé sur l'URL
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $autoType = null;
        
        if (strpos($requestUri, '/retards/') !== false) {
            $autoType = 'retard';
        } elseif (strpos($requestUri, '/presences/') !== false) {
            $autoType = 'absence'; // Présences = Absences
        }
        
        // Filtrer par type si spécifié dans l'URL ou détecté automatiquement
        $type = $_GET['type'] ?? $autoType;
        $where = [];
        $params = [];
        
        if ($type && in_array($type, ['absence', 'retard'])) {
            $where[] = "type = ?";
            $params[] = $type;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $absences = $this->absenceModel->query(
            "SELECT a.*, e.matricule, e.nom, e.prenom, c.nom as classe_nom
             FROM absences a
             JOIN eleves e ON a.eleve_id = e.id
             JOIN classes c ON a.classe_id = c.id
             {$whereClause}
             ORDER BY a.date_absence DESC",
            $params
        );
        
        $this->view('absences/list', [
            'absences' => $absences,
            'type_filtre' => $type
        ]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eleveId = $_POST['eleve_id'] ?? '';
            
            // Récupérer la classe de l'élève via son inscription active
            require_once APP_PATH . '/Models/Eleve.php';
            $eleveModel = new Eleve();
            $inscription = $eleveModel->getInscriptionActive($eleveId);
            
            if (!$inscription) {
                // Erreur: Élève non inscrit
                $_SESSION['error'] = "Cet élève n'a pas d'inscription active.";
                $this->view('absences/add');
                return;
            }

            $currentUserId = $_SESSION['user_id'] ?? null;

            $data = [
                'eleve_id' => $eleveId,
                'classe_id' => $inscription['classe_id'],
                'date_absence' => $_POST['date_absence'] ?? '',
                'type' => $_POST['type'] ?? 'absence',
                'periode' => $_POST['periode'] ?? 'journee',
                'heure_debut' => !empty($_POST['heure_debut']) ? $_POST['heure_debut'] : null,
                'heure_fin' => !empty($_POST['heure_fin']) ? $_POST['heure_fin'] : null,
                'motif' => $_POST['motif'] ?? '',
                'justifiee' => isset($_POST['justifiee']) ? 1 : 0,
                'saisi_par' => $currentUserId,
            ];
            
            $id = $this->absenceModel->create($data);
            $this->redirect('absences/details/' . $id);
        } else {
            // Plus besoin de charger tous les élèves, l'autocomplete le fera
            $this->view('absences/add');
        }
    }
    
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'eleve_id' => $_POST['eleve_id'] ?? '',
                'date_absence' => $_POST['date_absence'] ?? '',
                'type' => $_POST['type'] ?? 'absence',
                'periode' => $_POST['periode'] ?? 'journee',
                'heure_debut' => !empty($_POST['heure_debut']) ? $_POST['heure_debut'] : null,
                'heure_fin' => !empty($_POST['heure_fin']) ? $_POST['heure_fin'] : null,
                'motif' => $_POST['motif'] ?? '',
                'justifiee' => isset($_POST['justifiee']) ? 1 : 0,
            ];
            
            // Note: On ne met pas à jour classe_id lors de l'édition pour éviter incohérence 
            // si l'élève a changé de classe entre temps (historique).
            
            $this->absenceModel->update($id, $data);
            $this->redirect('absences/details/' . $id);
        } else {
            $absence = $this->absenceModel->find($id);
            if (!$absence) {
                http_response_code(404);
                die("Absence non trouvée");
            }
            $eleves = $this->absenceModel->query("SELECT id, matricule, nom, prenom FROM eleves WHERE statut = 'actif' ORDER BY nom ASC");
            $this->view('absences/edit', ['absence' => $absence, 'eleves' => $eleves]);
        }
    }

    /**
     * Recherche autocomplete pour les élèves
     */
    public function searchEleves() {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            $this->json([]);
            return;
        }
        
        // Essayer avec statut='actif', sinon sans
        try {
            $eleves = $this->absenceModel->query(
                "SELECT id, matricule, nom, prenom 
                 FROM eleves 
                 WHERE (matricule LIKE ? OR nom LIKE ? OR prenom LIKE ? OR CONCAT(nom, ' ', prenom) LIKE ?)
                 AND statut = 'actif'
                 ORDER BY nom, prenom
                 LIMIT 10",
                ["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"]
            );
        } catch (PDOException $e) {
             // Fallback générique si erreur (ex: colonne statut introuvable, peu probable ici)
             error_log("Erreur recherche élèves: " . $e->getMessage());
             $this->json([]);
             return;
        }
        
        $results = [];
        foreach ($eleves as $eleve) {
            $results[] = [
                'id' => $eleve['id'],
                'matricule' => $eleve['matricule'] ?? '',
                'nom' => $eleve['nom'] ?? '',
                'prenom' => $eleve['prenom'] ?? '',
                'display' => ($eleve['matricule'] ?? '') . ' - ' . ($eleve['nom'] ?? '') . ' ' . ($eleve['prenom'] ?? '')
            ];
        }
        
        $this->json($results);
    }
    
    /**
     * Affiche les détails d'une absence/retard
     */
    public function details($id) {
        $absence = $this->absenceModel->queryOne(
            "SELECT a.*, 
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                    c.nom as classe_nom,
                    u1.username as saisi_par_username,
                    u2.username as valide_par_username
             FROM absences a
             JOIN eleves e ON a.eleve_id = e.id
             JOIN classes c ON a.classe_id = c.id
             LEFT JOIN users u1 ON a.saisi_par = u1.id
             LEFT JOIN users u2 ON a.valide_par = u2.id
             WHERE a.id = ?",
            [$id]
        );
        
        if (!$absence) {
            http_response_code(404);
            die("Absence/Retard non trouvé(e)");
        }
        
        $this->view('absences/details', ['absence' => $absence]);
    }
    
    /**
     * Supprime une absence/retard
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->absenceModel->delete($id);
            $_SESSION['success'] = "Absence/Retard supprimé(e) avec succès";
            $this->redirect('absences/list');
        } else {
            $absence = $this->absenceModel->find($id);
            if (!$absence) {
                http_response_code(404);
                die("Absence/Retard non trouvé(e)");
            }
            $this->view('absences/delete', ['absence' => $absence]);
        }
    }
}

