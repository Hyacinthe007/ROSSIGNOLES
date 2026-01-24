<?php
/**
 * Contrôleur des enseignants
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Personnel.php';
require_once APP_PATH . '/Models/PersonnelEnseignant.php';
require_once APP_PATH . '/Models/AnneeScolaire.php';

class EnseignantsController extends BaseController {
    
    /**
     * Liste des enseignants (Personnel avec type enseignant)
     */
    public function list() {
        $this->requireAuth();
        $personnelModel = new Personnel();
        
        // On récupère tous les personnels qui ont un enregistrement correspondant dans personnels_enseignants
        // ou filtré par type_personnel = 'enseignant'
        $enseignants = $personnelModel->query(
            "SELECT p.*, pe.specialite, pe.grade 
             FROM personnels p
             INNER JOIN personnels_enseignants pe ON p.id = pe.personnel_id
             WHERE p.statut = 'actif' AND p.type_personnel = 'enseignant'
             ORDER BY p.nom ASC"
        );
        
        $this->view('enseignants/list', ['enseignants' => $enseignants]);
    }
    
    /**
     * Ajout d'un enseignant -> Redirection vers le wizard global
     */
    public function add() {
        // On redirige vers la création de personnel étape 1 (ou pré-selectionné étape 2 avec enseignant si on voulait)
        // Ici on envoie vers le wizard global
        $this->redirect('personnel/nouveau?etape=2&type_personnel=enseignant'); 
        // Note: Il faudrait que le controller personnel gère ce param GET pour pré-remplir la session
        // Pour l'instant on renvoie vers le choix
        // $this->redirect('personnel/nouveau');
    }
    
    /**
     * Édition -> Redirection vers l'édition standard du personnel
     */
    public function edit($id) {
        // Redirection vers l'édition globale du personnel
        // Mais il faut retouver l'ID du personnel si $id est l'ID enseignant ?
        // Dans l'URL, c'est généralement l'ID de l'entité listée.
        // Si la liste affiche p.id, alors $id est p.id.
        $this->redirect('personnel/edit/' . $id);
    }

    /**
     * Détails d'un enseignant
     */
    public function details($id) {
        $this->requireAuth();
        $personnelModel = new Personnel();
        $personnel = $personnelModel->find($id);

        if (!$personnel || $personnel['type_personnel'] !== 'enseignant') {
            http_response_code(404);
            die("Enseignant non trouvé");
        }
        
        // Charger les détails enseignant
        $ensModel = new PersonnelEnseignant();
        $detailsEns = $ensModel->queryOne("SELECT * FROM personnels_enseignants WHERE personnel_id = ?", [$id]);
        if ($detailsEns) {
            $personnel = array_merge($personnel, $detailsEns);
        }
        
        // Année scolaire active pour filtrer les cours
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;

        // Récupérer les classes et matières via enseignants_classes (qui utilise personnel_id)
        $classes = $ensModel->query(
            "SELECT DISTINCT c.* 
             FROM classes c
             INNER JOIN enseignants_classes ec ON c.id = ec.classe_id
             WHERE ec.personnel_id = ? AND ec.annee_scolaire_id = ?
             ORDER BY c.nom", 
             [$id, $anneeId]
        );

        $matieres = $ensModel->query(
            "SELECT DISTINCT m.* 
             FROM matieres m
             INNER JOIN enseignants_classes ec ON m.id = ec.matiere_id
             WHERE ec.personnel_id = ? AND ec.annee_scolaire_id = ?
             ORDER BY m.nom",
             [$id, $anneeId]
        );
        
        $this->view('enseignants/details', [
            'enseignant' => $personnel,
            'classes' => $classes,
            'matieres' => $matieres,
            'anneeActive' => $anneeActive,
             // Pour la vue qui attend peut-être 'documents' et 'absences', on peut les passer vides ou les charger via PersonnelController logic
            'documents' => [],
            'absences' => []
        ]);
    }
    
    public function delete($id) {
        $this->redirect('personnel/delete/' . $id);
    }
}
