<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\PaieParametreCotisation;
use App\Models\PaieTrancheIrsa;
use App\Models\PaieContrat;
use App\Models\PaieBulletin;
use App\Services\PaieService;

class PaieController extends BaseController {
    
    private $paieService;
    private $parametreCotisationModel;
    private $trancheIrsaModel;
    private $contratModel;
    private $bulletinModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->paieService = new PaieService();
        $this->parametreCotisationModel = new PaieParametreCotisation();
        $this->trancheIrsaModel = new PaieTrancheIrsa();
        $this->contratModel = new PaieContrat();
        $this->bulletinModel = new PaieBulletin();
    }
    
    /**
     * Page d'accueil de la paie
     */
    public function index() {
        $this->requirePermission('paie.read');
        
        // Période par défaut : mois en cours
        $periodeDefaut = date('Y-m');
        
        $data = [
            'title' => 'Gestion de la Paie',
            'periode_defaut' => $periodeDefaut
        ];
        
        $this->view('paie/index', $data);
    }
    
    /**
     * Simulateur de salaire
     */
    public function simulateur() {
        $this->requirePermission('paie.read');
        
        $data = [
            'title' => 'Simulateur de Salaire Net'
        ];
        
        $this->view('paie/simulateur', $data);
    }
    
    /**
     * Configuration des paramètres de paie
     */
    public function configuration() {
        $this->requirePermission('paie.update');
        
        // Initialiser les valeurs par défaut si nécessaire
        $this->parametreCotisationModel->initialiserDefauts();
        $this->trancheIrsaModel->initialiserDefauts2026();
        
        $data = [
            'title' => 'Configuration de la Paie',
            'cotisations' => $this->parametreCotisationModel->getActifs(),
            'tranches_irsa' => $this->trancheIrsaModel->getByAnnee(2026)
        ];
        
        $this->view('paie/configuration', $data);
    }
    
    /**
     * Mise à jour des paramètres de cotisations
     */
    public function updateCotisations() {
        $this->requirePermission('paie.update');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/paie/configuration');
            return;
        }
        
        try {
            $cotisations = $_POST['cotisations'] ?? [];
            
            foreach ($cotisations as $nom => $taux) {
                $this->parametreCotisationModel->updateTaux(
                    $nom,
                    (float)$taux['salarial'],
                    (float)$taux['patronal']
                );
            }
            
            $_SESSION['success_message'] = 'Paramètres de cotisations mis à jour avec succès';
        } catch (\Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la mise à jour : ' . $e->getMessage();
        }
        
        $this->redirect('/paie/configuration');
    }
    
    /**
     * Mise à jour des tranches IRSA
     */
    public function updateIrsa() {
        $this->requirePermission('paie.update');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/paie/configuration');
            return;
        }
        
        try {
            $tranches = $_POST['tranches'] ?? [];
            $annee = (int)($_POST['annee'] ?? 2026);
            
            if (!empty($tranches)) {
                $this->trancheIrsaModel->updateTranches($annee, $tranches);
                $_SESSION['success_message'] = "Grille IRSA $annee mise à jour avec succès";
            } else {
                $_SESSION['error_message'] = 'La grille IRSA ne peut pas être vide';
            }
        } catch (\Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la mise à jour IRSA : ' . $e->getMessage();
        }
        
        $this->redirect('/paie/configuration');
    }
    
    /**
     * Liste des contrats
     */
    public function contrats() {
        $this->requirePermission('paie.read');
        
        $data = [
            'title' => 'Contrats de Paie',
            'contrats' => $this->contratModel->getAllContratsActifs()
        ];
        
        $this->view('paie/contrats', $data);
    }
    
    /**
     * Formulaire d'ajout/modification de contrat
     */
    public function contratForm() {
        $this->requirePermission('paie.update');
        
        $personnelId = $_GET['personnel_id'] ?? null;
        $contrat = null;
        $personnelModel = new \App\Models\Personnel();
        
        if ($personnelId) {
            $contrat = $this->contratModel->getContratActif((int)$personnelId);
            // Si on modifie, on veut les infos fusionnées pour le formulaire
            if ($contrat) {
                $personnel = $personnelModel->find((int)$personnelId);
                $contrat = array_merge($personnel, $contrat);
            }
        }
        
        // Liste des personnels qui n'ont pas encore de contrat (pour le nouveau contrat)
        // On récupère tous les personnels actifs
        $tousActifs = $personnelModel->getActifs();
        $contratsExistants = $this->contratModel->getAllContratsActifs();
        $idsAvecContrat = array_column($contratsExistants, 'personnel_id');
        
        $personnelsSansContrat = array_filter($tousActifs, function($p) use ($idsAvecContrat) {
            return !in_array($p['id'], $idsAvecContrat);
        });
        
        $data = [
            'title' => $contrat ? 'Modifier le Contrat' : 'Nouveau Contrat',
            'contrat' => $contrat,
            'personnels_sans_contrat' => $personnelsSansContrat
        ];
        
        $this->view('paie/contrat_form', $data);
    }
    
    /**
     * Sauvegarde d'un contrat
     */
    public function saveContrat() {
        $this->requirePermission('paie.update');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/paie/contrats');
            return;
        }
        
        try {
            $personnelId = (int)$_POST['personnel_id'];
            $salaireBrutBase = (float)$_POST['salaire_brut_base'];
            $nbEnfants = (int)($_POST['nb_enfants'] ?? 0);
            $typeContrat = $_POST['type_contrat'] ?? 'cdi';
            $soumisCotisations = isset($_POST['soumis_cotisations']);
            
            // 1. Mettre à jour les infos de base dans la table personnels
            $personnelModel = new \App\Models\Personnel();
            $personnelModel->update($personnelId, [
                'type_contrat' => $typeContrat,
                'nb_enfants' => $nbEnfants
            ]);
            
            // 2. Mettre à jour ou créer le contrat de paie
            $this->contratModel->upsertContrat($personnelId, $salaireBrutBase, $soumisCotisations);
            
            $_SESSION['success_message'] = 'Contrat et informations personnels mis à jour avec succès';
        } catch (\Exception $e) {
            $_SESSION['error_message'] = 'Erreur : ' . $e->getMessage();
        }
        
        $this->redirect('/paie/contrats');
    }
    
    /**
     * Liste des bulletins de paie
     */
    public function bulletins() {
        $this->requirePermission('paie.read');
        
        $periode = $_GET['periode'] ?? date('Y-m');
        
        $data = [
            'title' => 'Bulletins de Paie',
            'periode' => $periode,
            'bulletins' => $this->bulletinModel->getByPeriode($periode),
            'statistiques' => $this->bulletinModel->getStatistiquesPeriode($periode)
        ];
        
        $this->view('paie/bulletins', $data);
    }
    
    /**
     * Génération des bulletins en masse
     */
    public function genererBulletins() {
        $this->requirePermission('paie.create');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/paie/bulletins');
            return;
        }
        
        try {
            $periode = $_POST['periode'] ?? date('Y-m');
            $resultats = $this->paieService->genererBulletinsMasse($periode);
            
            $message = sprintf(
                '%d bulletin(s) créé(s), %d erreur(s)',
                $resultats['succes'],
                $resultats['erreurs']
            );
            
            $_SESSION['success_message'] = $message;
        } catch (\Exception $e) {
            $_SESSION['error_message'] = 'Erreur : ' . $e->getMessage();
        }
        
        $this->redirect('/paie/bulletins?periode=' . $periode);
    }
    
    /**
     * Détail d'un bulletin
     */
    public function bulletinDetail() {
        $this->requirePermission('paie.read');
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/paie/bulletins');
            return;
        }
        
        $bulletin = $this->bulletinModel->queryOne(
            "SELECT pb.*, p.nom, p.prenom, p.matricule, p.nb_enfants, p.type_contrat, p.type_personnel
             FROM paie_bulletins pb
             JOIN personnels p ON pb.personnel_id = p.id
             LEFT JOIN paie_contrats pc ON pb.personnel_id = pc.personnel_id
             WHERE pb.id = ?",
            [(int)$id]
        );

        if (!$bulletin) {
            $_SESSION['error_message'] = 'Bulletin introuvable';
            $this->redirect('/paie/bulletins');
            return;
        }
        
        $data = [
            'title' => 'Détail du Bulletin',
            'bulletin' => $bulletin
        ];
        
        $this->view('paie/bulletin_detail', $data);
    }
    
    /**
     * Validation d'un bulletin
     */
    public function validerBulletin() {
        $this->requirePermission('paie.validate');
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $this->redirect('/paie/bulletins');
            return;
        }
        
        try {
            $this->bulletinModel->valider((int)$id);
            $_SESSION['success_message'] = 'Bulletin validé avec succès';
        } catch (\Exception $e) {
            $_SESSION['error_message'] = 'Erreur : ' . $e->getMessage();
        }
        
        $this->redirect('/paie/bulletins');
    }
}
