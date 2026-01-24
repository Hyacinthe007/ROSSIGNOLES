<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Bulletin;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Periode;
use App\Models\Eleve;
use App\Services\BulletinService;
use App\Services\PdfService;
use Exception;

/**
 * Contrôleur des bulletins
 */

class BulletinsController extends BaseController {
    private $bulletinModel;
    private $bulletinService;
    
    public function __construct() {
        $this->requireAuth();
        $this->bulletinModel = new Bulletin();
        $this->bulletinService = new BulletinService();
    }
    
    public function list() {
        // Récupérer les bulletins avec les informations des élèves, classes et périodes
        $bulletins = $this->bulletinModel->getAllWithDetails();
        
        $this->view('bulletins/list', ['bulletins' => $bulletins]);
    }
    
    public function generate($eleveId, $periodeId) {
        $bulletin = $this->bulletinService->generate($eleveId, $periodeId);
        $this->view('bulletins/view', ['bulletin' => $bulletin]);
    }
    
    /**
     * Interface de génération automatique des bulletins
     */
    public function generer() {
        $classeModel = new Classe();
        $periodeModel = new Periode();
        $anneeScolaireModel = new AnneeScolaire();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classeId = $_POST['classe_id'] ?? null;
            $periodeId = $_POST['periode_id'] ?? null;
            $anneeScolaireId = $_POST['annee_scolaire_id'] ?? null;
            $validerAutomatiquement = isset($_POST['valider_automatiquement']) && $_POST['valider_automatiquement'] == '1';
            
            if ($classeId && $periodeId && $anneeScolaireId) {
                $startTime = microtime(true);
                $resultat = $this->bulletinService->genererBulletins($classeId, $periodeId, $anneeScolaireId);
                $executionTime = round(microtime(true) - $startTime, 2);
                
                if ($resultat['success']) {
                    // Calculer les statistiques
                    $stats = $this->calculerStatistiques($resultat['bulletins'] ?? []);
                    $stats['execution_time'] = $executionTime;
                    $stats['classe_id'] = $classeId;
                    $stats['periode_id'] = $periodeId;
                    
                    // Si validation automatique demandée
                    if ($validerAutomatiquement) {
                        try {
                            $sql = "UPDATE bulletins SET statut = 'valide', date_validation = NOW(), valide_par = ? 
                                    WHERE classe_id = ? AND periode_id = ? AND annee_scolaire_id = ? AND statut = 'brouillon'";
                            
                            $this->bulletinModel->execute($sql, [$_SESSION['user_id'] ?? null, $classeId, $periodeId, $anneeScolaireId]);
                            
                            $stats['auto_valide'] = true;
                            $_SESSION['success'] = $resultat['message'] . ' Les bulletins ont été automatiquement validés.';
                        } catch (Exception $e) {
                            $_SESSION['warning'] = $resultat['message'] . ' Mais erreur lors de la validation automatique : ' . $e->getMessage();
                        }
                    } else {
                        $_SESSION['success'] = $resultat['message'];
                    }
                    
                    // Stocker en session pour affichage
                    $_SESSION['generation_stats'] = $stats;
                } else {
                    $_SESSION['error'] = $resultat['message'];
                }
                
                // Rediriger vers la même page pour afficher les stats
                header('Location: /ROSSIGNOLES/bulletins/generer');
                exit;
            } else {
                $_SESSION['error'] = 'Veuillez remplir tous les champs';
            }
        }
        
        // Récupérer les données pour le formulaire
        $classes = $classeModel->getAllWithCycleAndNiveau();
        $periodes = $periodeModel->getAll();
        $anneesScolaires = $anneeScolaireModel->getAll();
        
        // Récupérer les stats si disponibles
        $stats = $_SESSION['generation_stats'] ?? null;
        unset($_SESSION['generation_stats']); // Nettoyer après affichage
        
        $this->view('bulletins/generer', [
            'classes' => $classes,
            'periodes' => $periodes,
            'anneesScolaires' => $anneesScolaires,
            'stats' => $stats
        ]);
    }
    
    /**
     * API pour récupérer le nombre d'élèves d'une classe
     */
    public function getElevesCount() {
        header('Content-Type: application/json');
        
        $classeId = $_GET['classe_id'] ?? null;
        $anneeScolaireId = $_GET['annee_scolaire_id'] ?? null;
        
        if (!$classeId || !$anneeScolaireId) {
            echo json_encode(['error' => 'Paramètres manquants']);
            exit;
        }
        
        try {
            $count = $this->bulletinService->getElevesClasse($classeId, $anneeScolaireId);
            $nombreEleves = is_array($count) ? count($count) : 0;
            
            // Vérifier aussi le nombre de matières
            $matieres = $this->bulletinService->getMatieresClasse($classeId);
            $nombreMatieres = is_array($matieres) ? count($matieres) : 0;
            
            echo json_encode([
                'success' => true,
                'nombre_eleves' => $nombreEleves,
                'nombre_matieres' => $nombreMatieres
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Calcule les statistiques après génération
     */
    private function calculerStatistiques($bulletins) {
        if (empty($bulletins)) {
            return null;
        }
        
        $moyennes = array_column($bulletins, 'moyenne_generale');
        $totalEleves = count($bulletins);
        
        // Calculer les statistiques
        $moyenneClasse = array_sum($moyennes) / $totalEleves;
        $moyenneMax = max($moyennes);
        $moyenneMin = min($moyennes);
        
        // Médiane
        sort($moyennes);
        $middle = floor($totalEleves / 2);
        $mediane = $totalEleves % 2 == 0 
            ? ($moyennes[$middle - 1] + $moyennes[$middle]) / 2 
            : $moyennes[$middle];
        
        // Taux de réussite (>= 10)
        $reussis = count(array_filter($moyennes, function($m) { return $m >= 10; }));
        $tauxReussite = ($reussis / $totalEleves) * 100;
        
        // Distribution par mention
        $mentions = [
            'Excellent' => 0,
            'Très bien' => 0,
            'Bien' => 0,
            'Assez bien' => 0,
            'Passable' => 0,
            'Insuffisant' => 0
        ];
        
        foreach ($moyennes as $moyenne) {
            if ($moyenne >= 16) $mentions['Excellent']++;
            elseif ($moyenne >= 14) $mentions['Très bien']++;
            elseif ($moyenne >= 12) $mentions['Bien']++;
            elseif ($moyenne >= 10) $mentions['Assez bien']++;
            elseif ($moyenne >= 8) $mentions['Passable']++;
            else $mentions['Insuffisant']++;
        }
        
        // Top 3 élèves
        usort($bulletins, function($a, $b) {
            return $b['moyenne_generale'] <=> $a['moyenne_generale'];
        });
        $top3 = array_slice($bulletins, 0, 3);
        
        return [
            'total_eleves' => $totalEleves,
            'moyenne_classe' => round($moyenneClasse, 2),
            'moyenne_max' => round($moyenneMax, 2),
            'moyenne_min' => round($moyenneMin, 2),
            'mediane' => round($mediane, 2),
            'taux_reussite' => round($tauxReussite, 2),
            'reussis' => $reussis,
            'mentions' => $mentions,
            'top3' => $top3
        ];
    }
    
    public function pdf($id) {
        $bulletin = $this->bulletinModel->getDetails($id);
        if (!$bulletin) {
            $_SESSION['error'] = "Bulletin introuvable";
            $this->redirect('bulletins/list');
        }

        $matieres = $this->bulletinModel->getMatieres($id);
        
        // Calculer quelques stats si non présentes
        $bulletin['moyenne_classe'] = 12.5; // Placeholder ou calcul réel si dispo
        
        $html = $this->renderBulletinHtml($bulletin, $matieres);
        
        $pdfService = new PdfService();
        $pdfService->generateBulletin($html, "Bulletin_{$bulletin['matricule']}_{$bulletin['periode_nom']}.pdf");
    }

    /**
     * Rendu HTML du bulletin pour le PDF
     */
    private function renderBulletinHtml($bulletin, $matieres) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Outfit', sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.4; margin: 0; padding: 0; }
                .container { padding: 30px; }
                .header-table { width: 100%; border-bottom: 2px solid #1a56db; margin-bottom: 20px; padding-bottom: 15px; }
                .school-name { font-size: 18pt; font-weight: bold; color: #1a56db; }
                .document-title { font-size: 16pt; font-weight: bold; text-align: center; text-transform: uppercase; background: #f3f4f6; padding: 10px; margin: 20px 0; border-radius: 5px; }
                
                .info-grid { width: 100%; margin-bottom: 30px; }
                .info-box { padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; }
                
                table.grades-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                table.grades-table th { background: #1a56db; color: white; padding: 10px 5px; font-size: 9pt; text-transform: uppercase; border: 1px solid #1e40af; }
                table.grades-table td { padding: 8px 5px; border: 1px solid #e5e7eb; font-size: 9.5pt; }
                .center { text-align: center; }
                .right { text-align: right; }
                .bold { font-weight: bold; }
                
                .summary-table { width: 100%; margin-top: 20px; }
                .summary-box { border: 2px solid #1a56db; padding: 15px; border-radius: 8px; }
                .decision { margin-top: 20px; padding: 15px; background: #eff6ff; border-left: 5px solid #1a56db; font-weight: bold; }
                
                .signatures { margin-top: 40px; }
                .signature-box { width: 33%; float: left; text-align: center; }
                .signature-title { font-weight: bold; text-decoration: underline; margin-bottom: 50px; }
                
                .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 8pt; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 10px; }
            </style>
        </head>
        <body>
            <div class="container">
                <table class="header-table">
                    <tr>
                        <td width="60%">
                            <div class="school-name">École ROSSIGNOLES</div>
                            <div style="font-size: 9pt;">Éducation de Qualité - Avenir Radieux</div>
                            <div style="font-size: 9pt;">Antananarivo, Madagascar</div>
                        </td>
                        <td width="40%" style="text-align: right;">
                            <div style="font-weight: bold;">Année Scolaire : <?= htmlspecialchars($bulletin['annee_libelle'] ?? '2024-2025') ?></div>
                            <div style="font-size: 9pt;">Date : <?= date('d/m/Y') ?></div>
                        </td>
                    </tr>
                </table>

                <div class="document-title">Bulletin de Notes - <?= htmlspecialchars($bulletin['periode_nom']) ?></div>

                <table class="info-grid">
                    <tr>
                        <td width="55%">
                            <div class="info-box">
                                <strong>Élève :</strong> <span style="font-size: 11pt;"><?= htmlspecialchars($bulletin['eleve_nom'] . ' ' . $bulletin['eleve_prenom']) ?></span><br>
                                <strong>Matricule :</strong> <?= htmlspecialchars($bulletin['matricule']) ?><br>
                                <strong>Classe :</strong> <?= htmlspecialchars($bulletin['classe_nom']) ?>
                            </div>
                        </td>
                        <td width="45%" style="padding-left: 20px;">
                            <div class="info-box">
                                <strong>Effectif :</strong> <?= $bulletin['effectif'] ?> élèves<br>
                                <strong>Rang :</strong> <span style="font-weight: bold; color: #1a56db;"><?= $bulletin['rang'] ?><?= ($bulletin['rang'] == 1 ? 'er' : 'e') ?></span><br>
                                <strong>Moyenne de classe :</strong> <?= number_format($bulletin['moyenne_classe'] ?? 12.5, 2) ?>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="grades-table">
                    <thead>
                        <tr>
                            <th width="40%">Matières</th>
                            <th width="10%">Coef.</th>
                            <th width="15%">Moyenne / 20</th>
                            <th width="15%">Total</th>
                            <th width="20%">Appréciations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matieres as $m): ?>
                        <tr>
                            <td class="bold"><?= htmlspecialchars($m['matiere_nom']) ?></td>
                            <td class="center"><?= $m['coefficient'] ?></td>
                            <td class="center"><?= number_format($m['moyenne'], 2) ?></td>
                            <td class="center bold"><?= number_format($m['points'], 2) ?></td>
                            <td class="center" style="font-size: 8pt;"><?= htmlspecialchars($m['appreciation']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="summary-box">
                            <td class="right bold">TOTAL GÉNÉRAL</td>
                            <td class="center bold"><?= $bulletin['total_coefficients'] ?></td>
                            <td class="center">-</td>
                            <td class="center bold" style="background: #f3f4f6;"><?= number_format($bulletin['total_points'], 2) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <table class="summary-table">
                    <tr>
                        <td width="30%">
                            <div style="background: #1a56db; color: white; padding: 15px; border-radius: 8px; text-align: center;">
                                <div style="font-size: 9pt; text-transform: uppercase;">Moyenne Générale</div>
                                <div style="font-size: 20pt; font-weight: bold;"><?= number_format($bulletin['moyenne_generale'], 2) ?></div>
                                <div style="font-size: 8pt;">Sur 20</div>
                            </div>
                        </td>
                        <td width="70%" style="padding-left: 20px; vertical-align: top;">
                            <div class="info-box">
                                <strong>Observation :</strong> <?= htmlspecialchars($bulletin['appreciation_generale']) ?>
                            </div>
                            <div class="decision">
                                DÉCISION DU CONSEIL : <?= htmlspecialchars($bulletin['decision_conseil']) ?>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="signatures">
                    <div class="signature-box">
                        <div class="signature-title">L'Élève</div>
                    </div>
                    <div class="signature-box" style="visibility: hidden;">
                        &nbsp;
                    </div>
                    <div class="signature-box">
                        <div class="signature-title">Le Directeur</div>
                    </div>
                </div>

                <div class="footer">
                    École ROSSIGNOLES - Document officiel généré le <?= date('d/m/Y à H:i:s') ?>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Valide un bulletin individuel
     */
    public function valider($id) {
        $this->requireAuth();
        
        $data = [
            'statut' => 'valide',
            'date_validation' => date('Y-m-d H:i:s'),
            'valide_par' => $_SESSION['user_id'] ?? null
        ];
        
        if ($this->bulletinModel->update($id, $data)) {
            $_SESSION['success'] = "Le bulletin a été validé avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la validation du bulletin.";
        }
        
        $this->redirect('bulletins/list');
    }

    /**
     * Valide tous les bulletins d'une classe pour une période
     */
    public function validerTout() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classeId = $_POST['classe_id'] ?? null;
            $periodeId = $_POST['periode_id'] ?? null;
            
            if ($classeId && $periodeId) {
                try {
                    $sql = "UPDATE bulletins SET statut = 'valide', date_validation = NOW(), valide_par = ? 
                            WHERE classe_id = ? AND periode_id = ? AND statut = 'brouillon'";
                    
                    $this->bulletinModel->execute($sql, [$_SESSION['user_id'] ?? null, $classeId, $periodeId]);
                    $_SESSION['success'] = "Tous les bulletins de la classe ont été validés.";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur lors de la validation groupée : " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Paramètres manquants pour la validation groupée.";
            }
        }
        
        $this->redirect('bulletins/list');
    }
}

