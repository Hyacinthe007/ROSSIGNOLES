<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\EcheancierService;
use App\Models\Eleve;
use App\Models\AnneeScolaire;
use App\Models\Inscription;
use App\Services\PdfService;
use Exception;

/**
 * Contrôleur EcheancierController
 * Gère l'affichage et la manipulation des échéanciers d'écolage
 */

class EcheancierController extends BaseController {
    
    private $echeancierService;
    private $eleveModel;
    private $anneeScolaireModel;
    
    public function __construct() {
        $this->echeancierService = new EcheancierService();
        $this->eleveModel = new Eleve();
        $this->anneeScolaireModel = new AnneeScolaire();
    }
    
    /**
     * Affiche l'échéancier d'un élève
     */
    public function view() {
        $eleveId = $_GET['eleve_id'] ?? null;
        $anneeScolaireId = $_GET['annee_scolaire_id'] ?? null;
        
        if (!$eleveId || !$anneeScolaireId) {
            $_SESSION['error'] = "Paramètres manquants";
            header('Location: /eleves/list');
            exit;
        }
        
        // Récupérer l'élève
        $eleve = $this->eleveModel->findById($eleveId);
        if (!$eleve) {
            $_SESSION['error'] = "Élève introuvable";
            header('Location: /eleves/list');
            exit;
        }
        
        // Récupérer l'année scolaire
        $anneeScolaire = $this->anneeScolaireModel->findById($anneeScolaireId);
        
        // Récupérer l'échéancier avec statistiques
        $data = $this->echeancierService->getEcheancierAvecStatistiques($eleveId, $anneeScolaireId);
        
        // Mettre à jour les statuts
        $this->echeancierService->updateStatutsEcheancier($eleveId, $anneeScolaireId);
        
        $this->view('echeancier/view', ['eleve' => $eleve, 'anneeScolaire' => $anneeScolaire, 'data' => $data]);
    }
    
    /**
     * Liste tous les élèves avec leurs échéanciers
     */
    public function list() {
        $anneeScolaireId = $_GET['annee_scolaire_id'] ?? null;
        
        if (!$anneeScolaireId) {
            $anneeScolaire = $this->anneeScolaireModel->getActive();
            $anneeScolaireId = $anneeScolaire['id'] ?? null;
        }
        
        if (!$anneeScolaireId) {
            $_SESSION['error'] = "Aucune année scolaire active";
            header('Location: /dashboard');
            exit;
        }
        
        // Récupérer tous les élèves avec échéancier
        $echeanciers = $this->echeancierService->echeancierModel->query(
            "SELECT DISTINCT 
                ee.eleve_id,
                e.matricule,
                e.nom,
                e.prenom,
                c.nom as classe_nom,
                SUM(ee.montant_du) as total_du,
                SUM(ee.montant_paye) as total_paye,
                SUM(ee.montant_restant) as total_restant,
                COUNT(CASE WHEN ee.statut IN ('retard', 'retard_grave', 'exclusion') THEN 1 END) as nb_retards
            FROM echeanciers_ecolages ee
            INNER JOIN eleves e ON ee.eleve_id = e.id
            INNER JOIN inscriptions i ON (ee.eleve_id = i.eleve_id AND ee.annee_scolaire_id = i.annee_scolaire_id)
            INNER JOIN classes c ON i.classe_id = c.id
            WHERE ee.annee_scolaire_id = ?
            GROUP BY ee.eleve_id, e.matricule, e.nom, e.prenom, c.nom
            ORDER BY e.nom ASC, e.prenom ASC",
            [$anneeScolaireId]
        );
        
        $anneeScolaire = $this->anneeScolaireModel->findById($anneeScolaireId);
        $annesScolaires = $this->anneeScolaireModel->getAll();
        
        $this->view('echeancier/list', [
            'echeanciers' => $echeanciers,
            'anneeScolaire' => $anneeScolaire,
            'annesScolaires' => $annesScolaires
        ]);
    }
    
    /**
     * Affiche les élèves en retard
     */
    public function retards() {
        $anneeScolaireId = $_GET['annee_scolaire_id'] ?? null;
        
        if (!$anneeScolaireId) {
            $anneeScolaire = $this->anneeScolaireModel->getActive();
            $anneeScolaireId = $anneeScolaire['id'] ?? null;
        }
        
        $elevesEnRetard = $this->echeancierService->getElevesEnRetard($anneeScolaireId);
        $anneeScolaire = $this->anneeScolaireModel->findById($anneeScolaireId);
        $annesScolaires = $this->anneeScolaireModel->getAll();
        
        $this->view('echeancier/retards', [
            'elevesEnRetard' => $elevesEnRetard,
            'anneeScolaire' => $anneeScolaire,
            'annesScolaires' => $annesScolaires
        ]);
    }
    
    /**
     * Génère manuellement un échéancier
     */
    public function generer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inscriptionId = $_POST['inscription_id'] ?? null;
            
            if (!$inscriptionId) {
                $_SESSION['error'] = "ID d'inscription manquant";
                header('Location: /inscriptions/list');
                exit;
            }
            
            $userId = $_SESSION['user_id'] ?? null;
            
            $resultat = $this->echeancierService->genererEcheancierInscription($inscriptionId, $userId);
            
            if ($resultat['success']) {
                $_SESSION['success'] = $resultat['message'] . " - " . $resultat['nb_echeances'] . " échéances créées";
            } else {
                $_SESSION['error'] = $resultat['message'];
            }
            
            header('Location: /inscriptions/details?id=' . $inscriptionId);
            exit;
        }
        
        // Afficher le formulaire
        $inscriptionId = $_GET['inscription_id'] ?? null;
        
        if (!$inscriptionId) {
            $_SESSION['error'] = "ID d'inscription manquant";
            header('Location: /inscriptions/list');
            exit;
        }
        
        $inscriptionModel = new Inscription();
        $inscription = $inscriptionModel->getDetails($inscriptionId);
        
        if (!$inscription) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: /inscriptions/list');
            exit;
        }
        
        $this->view('echeancier/generer', ['inscription' => $inscription]);
    }
    
    /**
     * Supprime un échéancier
     */
    public function supprimer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eleveId = $_POST['eleve_id'] ?? null;
            $anneeScolaireId = $_POST['annee_scolaire_id'] ?? null;
            
            if (!$eleveId || !$anneeScolaireId) {
                $_SESSION['error'] = "Paramètres manquants";
                header('Location: /echeancier/list');
                exit;
            }
            
            try {
                $this->echeancierService->supprimerEcheancier($eleveId, $anneeScolaireId);
                $_SESSION['success'] = "Échéancier supprimé avec succès";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            
            header('Location: /echeancier/list');
            exit;
        }
    }
    
    /**
     * Exporte l'échéancier en PDF
     */
    public function exportPdf() {
        $eleveId = $_GET['eleve_id'] ?? null;
        $anneeScolaireId = $_GET['annee_scolaire_id'] ?? null;
        
        if (!$eleveId || !$anneeScolaireId) {
            $_SESSION['error'] = "Paramètres manquants";
            header('Location: /echeancier/list');
            exit;
        }
        
        // Récupérer les données
        $eleve = $this->eleveModel->findById($eleveId);
        $anneeScolaire = $this->anneeScolaireModel->findById($anneeScolaireId);
        $data = $this->echeancierService->getEcheancierAvecStatistiques($eleveId, $anneeScolaireId);
        
        // Générer le PDF
        $pdfService = new PdfService();
        
        $html = $this->renderEcheancierPdf($eleve, $anneeScolaire, $data);
        
        $pdfService->generatePdf(
            $html,
            "Echeancier_{$eleve['matricule']}_{$anneeScolaire['libelle']}.pdf"
        );
    }
    
    /**
     * Génère le HTML pour le PDF de l'échéancier
     */
    private function renderEcheancierPdf($eleve, $anneeScolaire, $data) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Outfit', sans-serif; font-size: 11pt; color: #333; line-height: 1.4; }
                h1 { text-align: center; color: #1a56db; text-transform: uppercase; margin-bottom: 30px; border-bottom: 2px solid #1a56db; padding-bottom: 10px; }
                .header-table { width: 100%; margin-bottom: 30px; border: none; }
                .info-box { background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background-color: #1a56db; color: white; padding: 12px 8px; text-align: left; font-size: 10pt; }
                td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10pt; }
                .total-row { background-color: #f3f4f6; font-weight: bold; }
                .statut { padding: 4px 8px; border-radius: 4px; font-size: 9pt; font-weight: bold; text-align: center; }
                .statut-paye { color: #059669; background: #ecfdf5; }
                .statut-retard { color: #d97706; background: #fffbeb; }
                .statut-exclusion { color: #dc2626; background: #fef2f2; }
                .footer { margin-top: 50px; text-align: center; font-size: 9pt; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; }
            </style>
        </head>
        <body>
            <h1>Échéancier d'écolage</h1>
            
            <table class="header-table">
                <tr>
                    <td width="60%" style="border:none;">
                        <div class="info-box">
                            <strong>Élève :</strong> <?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) ?><br>
                            <strong>Matricule :</strong> <?= htmlspecialchars($eleve['matricule']) ?><br>
                            <strong>Année scolaire :</strong> <?= htmlspecialchars($anneeScolaire['libelle']) ?>
                        </div>
                    </td>
                    <td width="40%" style="border:none; text-align: right; vertical-align: top;">
                        <strong>Date d'émission :</strong> <?= date('d/m/Y') ?><br>
                        <strong>Ref :</strong> ECH-<?= $eleve['id'] ?>-<?= date('ymd') ?>
                    </td>
                </tr>
            </table>
            
            <table>
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Date limite</th>
                        <th>Montant dû</th>
                        <th>Montant payé</th>
                        <th>Reste</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['echeances'] as $echeance): ?>
                    <tr>
                        <td><?= htmlspecialchars($echeance['mois_libelle']) ?></td>
                        <td><?= date('d/m/Y', strtotime($echeance['date_limite'])) ?></td>
                        <td style="text-align: right;"><?= number_format($echeance['montant_du'], 0, ',', ' ') ?> Ar</td>
                        <td style="text-align: right;"><?= number_format($echeance['montant_paye'], 0, ',', ' ') ?> Ar</td>
                        <td style="text-align: right;"><?= number_format($echeance['montant_restant'], 0, ',', ' ') ?> Ar</td>
                        <td style="text-align: center;">
                            <span class="statut statut-<?= $echeance['statut'] ?>">
                                <?= strtoupper(str_replace('_', ' ', $echeance['statut'])) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2">TOTAL GÉNÉRAL</td>
                        <td style="text-align: right;"><?= number_format($data['statistiques']['total_du'], 0, ',', ' ') ?> Ar</td>
                        <td style="text-align: right;"><?= number_format($data['statistiques']['total_paye'], 0, ',', ' ') ?> Ar</td>
                        <td style="text-align: right;"><?= number_format($data['statistiques']['total_restant'], 0, ',', ' ') ?> Ar</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top: 30px;">
                <p><strong>Taux de paiement :</strong> <?= $data['statistiques']['taux_paiement'] ?>%</p>
                <p><strong>Échéances payées :</strong> <?= $data['statistiques']['nb_echeances_payees'] ?> / <?= $data['statistiques']['nb_echeances_total'] ?></p>
                <p><strong>Échéances en retard :</strong> <?= $data['statistiques']['nb_echeances_en_retard'] ?></p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * API : Récupère l'échéancier en JSON
     */
    public function api() {
        header('Content-Type: application/json');
        
        $eleveId = $_GET['eleve_id'] ?? null;
        $anneeScolaireId = $_GET['annee_scolaire_id'] ?? null;
        
        if (!$eleveId || !$anneeScolaireId) {
            echo json_encode(['error' => 'Paramètres manquants']);
            exit;
        }
        
        $data = $this->echeancierService->getEcheancierAvecStatistiques($eleveId, $anneeScolaireId);
        echo json_encode($data);
    }
}
