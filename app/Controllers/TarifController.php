<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\TarifInscription;
use App\Models\AnneeScolaire;
use App\Models\Niveau;
use Exception;

/**
 * Contrôleur TarifController
 * Gère les tarifs d'inscription et d'écolage par niveau
 */

class TarifController extends BaseController {
    
    /**
     * Liste des tarifs
     */
    public function liste() {
        $this->requireAuth();
        
        $model = new TarifInscription();
        $anneeModel = new AnneeScolaire();
        
        // Filtres
        $filters = [];
        $anneeId = isset($_GET['annee_scolaire_id']) ? $_GET['annee_scolaire_id'] : null;
        
        if (!$anneeId) {
            // Par défaut, afficher l'année active
            $anneeActive = $anneeModel->queryOne("SELECT id FROM annees_scolaires WHERE actif = 1 LIMIT 1");
            if ($anneeActive) {
                $anneeId = $anneeActive['id'];
            }
        }
        
        // Récupérer tous les tarifs (filtrés par année si nécessaire)
        // La méthode getAllDetails fait une jointure, on filtrera en PHP ou on ajoutera un WHERE si besoin
        $allTarifs = $model->getAllDetails();
        $tarifs = [];
        
        if ($anneeId) {
            foreach ($allTarifs as $t) {
                if ($t['annee_scolaire_id'] == $anneeId) {
                    $tarifs[] = $t;
                }
            }
        } else {
            $tarifs = $allTarifs;
        }
        
        $annees = $anneeModel->all([], 'date_debut DESC');
        
        $this->view('tarifs/liste', [
            'tarifs' => $tarifs,
            'annees' => $annees,
            'selectedAnnee' => $anneeId
        ]);
    }
    
    /**
     * Formulaire de création
     */
    public function nouveau() {
        $this->requireAuth();
        
        $anneeModel = new AnneeScolaire();
        $niveauModel = new Niveau();
        
        $annees = $anneeModel->all(['actif' => 1], 'date_debut DESC');
        
        // Récupérer les niveaux avec le libellé de leur cycle
        $niveaux = $niveauModel->query(
            "SELECT n.*, c.libelle as cycle_libelle 
             FROM niveaux n 
             LEFT JOIN cycles c ON n.cycle_id = c.id 
             WHERE n.actif = 1 
             ORDER BY n.ordre ASC"
        );
        
        $this->view('tarifs/formulaire', [
            'tarif' => null,
            'annees' => $annees,
            'niveaux' => $niveaux
        ]);
    }
    
    /**
     * Enregistrer un nouveau tarif
     */
    public function creer() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/tarifs/nouveau');
            return;
        }
        
        try {
            $model = new TarifInscription();
            
            // Validation
            if (empty($_POST['niveau_id']) || empty($_POST['annee_scolaire_id'])) {
                throw new Exception("Le niveau et l'année scolaire sont obligatoires");
            }
            
            // Vérifier si un tarif existe déjà
            $existing = $model->getByAnneeAndNiveau($_POST['annee_scolaire_id'], $_POST['niveau_id']);
            if ($existing) {
                throw new Exception("Un tarif existe déjà pour ce niveau et cette année scolaire");
            }
            
            $data = [
                'annee_scolaire_id' => $_POST['annee_scolaire_id'],
                'niveau_id' => $_POST['niveau_id'],
                'frais_inscription' => floatval($_POST['frais_inscription'] ?? 0),
                'ecolage_mensuel' => floatval($_POST['ecolage_mensuel'] ?? 0),
                'mois_debut_annee' => intval($_POST['mois_debut_annee'] ?? 9),
                'actif' => isset($_POST['actif']) ? 1 : 0
            ];
            
            $model->create($data);
            
            $_SESSION['success'] = "Tarif créé avec succès";
            $this->redirect('/tarifs/liste');
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
            $this->redirect('/tarifs/nouveau');
        }
    }
    
    /**
     * Formulaire de modification
     */
    public function modifier($id) {
        $this->requireAuth();
        
        $model = new TarifInscription();
        $tarif = $model->find($id);
        
        if (!$tarif) {
            $_SESSION['error'] = "Tarif introuvable";
            $this->redirect('/tarifs/liste');
            return;
        }
        
        $anneeModel = new AnneeScolaire();
        $niveauModel = new Niveau();
        
        $annees = $anneeModel->all(['actif' => 1], 'date_debut DESC');
        
        // Récupérer les niveaux avec le libellé de leur cycle
        $niveaux = $niveauModel->query(
            "SELECT n.*, c.libelle as cycle_libelle 
             FROM niveaux n 
             LEFT JOIN cycles c ON n.cycle_id = c.id 
             WHERE n.actif = 1 
             ORDER BY n.ordre ASC"
        );
        
        $this->view('tarifs/formulaire', [
            'tarif' => $tarif,
            'annees' => $annees,
            'niveaux' => $niveaux
        ]);
    }
    
    /**
     * Mettre à jour un tarif
     */
    public function mettreAJour($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/tarifs/modifier/' . $id);
            return;
        }
        
        try {
            $model = new TarifInscription();
            
            // Validation
            if (empty($_POST['niveau_id']) || empty($_POST['annee_scolaire_id'])) {
                throw new Exception("Le niveau et l'année scolaire sont obligatoires");
            }
            
            $data = [
                'annee_scolaire_id' => $_POST['annee_scolaire_id'],
                'niveau_id' => $_POST['niveau_id'],
                'frais_inscription' => floatval($_POST['frais_inscription'] ?? 0),
                'ecolage_mensuel' => floatval($_POST['ecolage_mensuel'] ?? 0),
                'mois_debut_annee' => intval($_POST['mois_debut_annee'] ?? 9),
                'actif' => isset($_POST['actif']) ? 1 : 0
            ];
            
            $model->update($id, $data);
            
            $_SESSION['success'] = "Tarif modifié avec succès";
            $this->redirect('/tarifs/liste');
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
            $this->redirect('/tarifs/modifier/' . $id);
        }
    }
    
    /**
     * Suppression (désactivation)
     */
    public function supprimer($id) {
        $this->requireAuth();
        try {
            $model = new TarifInscription();
            $model->delete($id); 
            $_SESSION['success'] = "Tarif supprimé";
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }
        $this->redirect('/tarifs/liste');
    }
}
