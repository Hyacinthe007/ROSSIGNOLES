<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Eleve;
use App\Models\LogActivite;
use App\Models\ParentModel;
use App\Models\AnneeScolaire;
use App\Models\Paiement;
use App\Models\Facture;
use App\Services\PdfService;

/**
 * Contrôleur des élèves
 */

class ElevesController extends BaseController {
    private $eleveModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->eleveModel = new Eleve();
    }
    
    public function list() {
        $search = $_GET['search'] ?? '';
        
        // Requête globale sans distinction d'année scolaire
        $sql = "SELECT e.*, 
                       p.telephone as parent_telephone, 
                       p.adresse as parent_adresse,
                       (SELECT c.code FROM inscriptions i 
                        INNER JOIN classes c ON i.classe_id = c.id 
                        WHERE i.eleve_id = e.id 
                        ORDER BY i.id DESC LIMIT 1) as derniere_classe,
                       (SELECT i.id FROM inscriptions i 
                        WHERE i.eleve_id = e.id 
                        ORDER BY i.id DESC LIMIT 1) as derniere_inscription_id,
                       (SELECT CASE 
                            WHEN COUNT(CASE WHEN ee.statut = 'exclusion' THEN 1 END) > 0 THEN 'exclusion'
                            WHEN COUNT(CASE WHEN ee.statut = 'retard' THEN 1 END) > 0 THEN 'retard'
                            WHEN COUNT(CASE WHEN ee.statut = 'impaye' THEN 1 END) > 0 THEN 'impaye'
                            ELSE NULL 
                        END 
                        FROM echeanciers_ecolages ee 
                        WHERE ee.eleve_id = e.id AND ee.montant_restant > 0) as statut_financier
                FROM eleves e
                LEFT JOIN (
                    SELECT ep.eleve_id, p1.telephone, p1.adresse 
                    FROM eleves_parents ep
                    INNER JOIN parents p1 ON ep.parent_id = p1.id
                    GROUP BY ep.eleve_id
                ) p ON e.id = p.eleve_id
                WHERE 1=1";

        $params = [];
        if (!empty($search)) {
            $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY e.nom ASC, e.prenom ASC";
        $eleves = $this->eleveModel->query($sql, $params);

        $this->view('eleves/list', [
            'eleves' => $eleves,
            'search' => $search
        ]);
    }

    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation des données
            $validator = new \App\Core\Validator($_POST);
            $isValid = $validator->validate([
                'nom' => 'required|min:2',
                'prenom' => 'required|min:2',
                'sexe' => 'required',
                'date_naissance' => 'date'
            ]);

            if (!$isValid) {
                $_SESSION['errors'] = $validator->getErrors();
                $_SESSION['old'] = $_POST;
                $this->redirect('eleves/add');
            }

            // Générer le matricule automatiquement si non fourni
            $matricule = $_POST['matricule'] ?? '';
            if (empty($matricule)) {
                $matricule = generateMatricule('eleve', 'eleves');
            }
            
            $data = [
                'matricule' => $matricule,
                'nom' => mb_strtoupper($_POST['nom'] ?? ''),
                'prenom' => ucwords(strtolower($_POST['prenom'] ?? '')),
                'sexe' => $_POST['sexe'] ?? 'M',
                'date_naissance' => $_POST['date_naissance'] ?? null,
                'lieu_naissance' => $_POST['lieu_naissance'] ?? '',
                'photo' => null, 
                'statut' => 'nouveau',
                'date_inscription' => date('Y-m-d')
            ];
            
            // ... (reste du code)
            
            // Upload photo
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                // Logique d'upload... (simplifiée pour l'exemple)
            }
            
            $id = $this->eleveModel->create($data);
            
            // Gestion du parent si fourni
            if (!empty($_POST['parent_nom'])) {
                $parentModel = new ParentModel();
                
                $parentData = [
                    'nom' => $_POST['parent_nom'],
                    'prenom' => $_POST['parent_prenom'] ?? '',
                    'telephone' => $_POST['parent_telephone'] ?? '',
                    'email' => $_POST['parent_email'] ?? '',
                    'adresse' => $_POST['parent_adresse'] ?? ''
                ];
                
                $parentId = $parentModel->create($parentData);
                
                // Lien
                $parentModel->query(
                    "INSERT INTO eleves_parents (eleve_id, parent_id, lien_parente) VALUES (?, ?, ?)",
                    [$id, $parentId, $_POST['parent_lien'] ?? 'Tuteur']
                );
            }
            
            // Logging
            LogActivite::log(
                'Nouvel Élève', 
                'Scolarité', 
                "Création de l'élève {$_POST['nom']} {$_POST['prenom']} (Matricule: $matricule)",
                'eleves',
                $id
            );
            
            session_set_flash('success', "L'élève {$data['nom']} {$data['prenom']} a été ajouté avec succès.");
            $this->redirect('eleves/details/' . $id);
        } else {
            $matriculeAuto = generateMatricule('eleve', 'eleves');
            $this->view('eleves/add', ['matricule_auto' => $matriculeAuto]);
        }
    }
    
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // ... (logique d'upload photo similaire à add)
            
            $data = [
                'matricule' => $_POST['matricule'],
                'nom' => mb_strtoupper($_POST['nom']),
                'prenom' => $_POST['prenom'],
                'sexe' => $_POST['sexe'],
                'date_naissance' => $_POST['date_naissance'],
                'lieu_naissance' => $_POST['lieu_naissance'],
            ];
            
            $this->eleveModel->update($id, $data);
            
            // Mise à jour des informations du parent (Principal / Tuteur)
            if (!empty($_POST['parent_nom'])) {
                $parentModel = new ParentModel();
                
                // Vérifier si l'élève a déjà un parent lié
                $parents = $this->eleveModel->getParents($id);
                $parentId = null;
                
                if (!empty($parents)) {
                    // Update du premier parent trouvé (simplification)
                    $parentId = $parents[0]['id'];
                    $parentData = [
                        'nom' => $_POST['parent_nom'],
                        'prenom' => $_POST['parent_prenom'] ?? '',
                        'telephone' => $_POST['parent_telephone'] ?? '',
                        'email' => $_POST['parent_email'] ?? '',
                        'adresse' => $_POST['parent_adresse'] ?? '',
                        'profession' => $_POST['parent_profession'] ?? ''
                    ];
                    $parentModel->update($parentId, $parentData);
                    
                    // Update lien de parenté
                    if (isset($_POST['parent_lien'])) {
                        $parentModel->query(
                            "UPDATE eleves_parents SET lien_parente = ? WHERE eleve_id = ? AND parent_id = ?", 
                            [$_POST['parent_lien'], $id, $parentId]
                        );
                    }
                } else {
                    // Création d'un nouveau parent et liaison
                    // Vérifier existence par téléphone pour éviter doublon
                    $existingParent = null;
                    if (!empty($_POST['parent_telephone'])) {
                        $existingParent = $parentModel->queryOne(
                            "SELECT id FROM parents WHERE telephone = ?", 
                            [$_POST['parent_telephone']]
                        );
                    }

                    if ($existingParent) {
                        $parentId = $existingParent['id'];
                        // On pourrait mettre à jour les infos ici aussi, mais attention aux effets de bord
                    } else {
                        $parentData = [
                            'nom' => $_POST['parent_nom'],
                            'prenom' => $_POST['parent_prenom'] ?? '',
                            'telephone' => $_POST['parent_telephone'] ?? '',
                            'email' => $_POST['parent_email'] ?? '',
                            'adresse' => $_POST['parent_adresse'] ?? '',
                            'profession' => $_POST['parent_profession'] ?? ''
                        ];
                        $parentId = $parentModel->create($parentData);
                    }

                    // Créer le lien
                    $parentModel->query(
                        "INSERT INTO eleves_parents (eleve_id, parent_id, lien_parente) VALUES (?, ?, ?)",
                        [$id, $parentId, $_POST['parent_lien'] ?? 'Tuteur']
                    );
                }
            }
            
            // Logging
            LogActivite::log(
                'Modification Élève', 
                'Scolarité', 
                "Mise à jour de l'élève ID: $id",
                'eleves',
                $id
            );
            
            $this->redirect('/eleves/details/' . $id);
        } else {
            $eleve = $this->eleveModel->getDetails($id);
            if (!$eleve) {
                // 404
            }
            
            $parents = $this->eleveModel->getParents($id);
            $parent = !empty($parents) ? $parents[0] : [];
            
            $this->view('eleves/edit', ['eleve' => $eleve, 'parent' => $parent]);
        }
    }
    
    public function details($id) {
        $eleve = $this->eleveModel->getDetails($id); // Utilise la nouvelle méthode avec LEFT JOIN inscriptions
        if (!$eleve) {
            http_response_code(404);
            die("Élève non trouvé");
        }
        
        // Année scolaire active pour contextes
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;
        
        $classe = $this->eleveModel->getCurrentClasse($id, $anneeId);
        $parents = $this->eleveModel->getParents($id);
        
        // Echeancier
        $echeancier = $this->eleveModel->getEcheancierPaiement($id, $anneeId);
        
        // Situation financière
        $situationFinanciere = $this->eleveModel->getSituationFinanciere($id, $anneeId);
        
        // Historique des paiements détaillé
        $paiementModel = new Paiement();
        $factureModel = new Facture();
        
        $paiements = $paiementModel->getByEleve($id, $anneeId);
        
        $lignesFacture = $factureModel->query(
            "SELECT lf.*, tf.libelle as type_frais 
             FROM lignes_facture lf
             INNER JOIN factures f ON lf.facture_id = f.id
             LEFT JOIN types_frais tf ON lf.type_frais_id = tf.id
             WHERE f.eleve_id = ? AND f.annee_scolaire_id = ? AND f.statut != 'annulee'",
            [$id, $anneeId]
        );
        
        $this->view('eleves/details', [
            'eleve' => $eleve,
            'classe' => $classe,
            'inscription' => $classe,
            'parents' => $parents,
            'echeancier' => $echeancier,
            'situationFinanciere' => $situationFinanciere,
            'anneeActive' => $anneeActive,
            'paiements' => $paiements,
            'lignesFacture' => $lignesFacture
        ]);
    }
    
    
    public function inscription() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Logique d'inscription
            $this->redirect('/eleves/list');
        } else {
            $this->view('eleves/inscription');
        }
    }
    
    /**
     * Export PDF de la liste des élèves
     */
    public function exportPdf() {
        $eleves = $this->eleveModel->query("SELECT * FROM eleves ORDER BY nom ASC, prenom ASC");
        
        // Générer le contenu HTML pour le PDF
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: \'Outfit\', sans-serif; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #4A5568; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f5f5f5; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <h1>Liste des Élèves</h1>
    <p style="text-align: center; color: #666;">Généré le ' . date('d/m/Y à H:i') . '</p>
    <table>
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Sexe</th>
                <th>Date de naissance</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($eleves as $eleve) {
            $html .= '<tr>
                <td>' . htmlspecialchars($eleve['matricule']) . '</td>
                <td>' . htmlspecialchars($eleve['nom']) . '</td>
                <td>' . htmlspecialchars($eleve['prenom']) . '</td>
                <td>' . htmlspecialchars($eleve['sexe']) . '</td>
                <td>' . date('d/m/Y', strtotime($eleve['date_naissance'])) . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
    </table>
    <div class="footer">
        <p>Total: ' . count($eleves) . ' élève(s)</p>
    </div>
</body>
</html>';
        
        // Envoyer les headers pour le téléchargement
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="liste_eleves_' . date('Y-m-d') . '.pdf"');
        
        // Pour une vraie génération PDF, il faudrait une bibliothèque comme TCPDF ou DomPDF
        // Pour l'instant, on envoie le HTML qui peut être imprimé en PDF par le navigateur
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: inline; filename="liste_eleves_' . date('Y-m-d') . '.html"');
        echo $html;
        exit;
    }
    
    /**
     * Export Excel (CSV) de la liste des élèves
     */
    public function exportExcel() {
        $eleves = $this->eleveModel->query("SELECT * FROM eleves ORDER BY nom ASC, prenom ASC");
        
        // Générer le fichier CSV
        $filename = 'liste_eleves_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Ouvrir le flux de sortie
        $output = fopen('php://output', 'w');
        
        // Ajouter le BOM UTF-8 pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // En-têtes du CSV
        fputcsv($output, ['Matricule', 'Nom', 'Prénom', 'Sexe', 'Date de naissance', 'Lieu de naissance'], ';');
        
        // Données
        foreach ($eleves as $eleve) {
            fputcsv($output, [
                $eleve['matricule'],
                $eleve['nom'],
                $eleve['prenom'],
                $eleve['sexe'],
                date('d/m/Y', strtotime($eleve['date_naissance'])),
                $eleve['lieu_naissance'] ?? '',
            ], ';');
        }
        
        fclose($output);
    }

    /**
     * Visualise le parcours scolaire complet d'un élève
     */
    public function parcours($id) {
        $eleve = $this->eleveModel->getDetails($id);
        if (!$eleve) {
            http_response_code(404);
            die("Élève non trouvé");
        }
        
        // Récupérer l'historique des inscriptions
        $inscriptions = $this->eleveModel->query(
            "SELECT i.*, c.nom as classe_nom, a.libelle as annee_scolaire
             FROM inscriptions i
             INNER JOIN classes c ON i.classe_id = c.id
             INNER JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
             WHERE i.eleve_id = ? AND i.statut = 'validee'
             ORDER BY a.date_debut DESC",
            [$id]
        );
        
        $this->view('eleves/parcours', [
            'eleve' => $eleve,
            'inscriptions' => $inscriptions
        ]);
    }

    /**
     * Exporte le parcours scolaire en PDF
     */
    public function exportParcoursPdf($id) {
        $eleve = $this->eleveModel->getDetails($id);
        if (!$eleve) die("Élève non trouvé");

        $inscriptions = $this->eleveModel->query(
            "SELECT i.*, c.nom as classe_nom, a.libelle as annee_scolaire
             FROM inscriptions i
             INNER JOIN classes c ON i.classe_id = c.id
             INNER JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
             WHERE i.eleve_id = ? AND i.statut = 'validee'
             ORDER BY a.date_debut DESC",
            [$id]
        );

        $html = $this->renderView('pdf/parcours_scolaire', [
            'eleve' => $eleve,
            'inscriptions' => $inscriptions
        ]);

        $pdfService = new PdfService();
        $pdfService->generatePdf($html, "Parcours_" . $eleve['matricule'] . ".pdf");
    }
    /**
     * Génère un certificat de scolarité pour l'année en cours
     */
    public function certificatScolaire($id) {
        $eleve = $this->eleveModel->getDetails($id);
        if (!$eleve) {
            http_response_code(404);
            die("Élève non trouvé");
        }
        
        // Récupérer l'inscription active
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;
        
        $inscription = $this->eleveModel->getCurrentClasse($id, $anneeId);
        
        if (!$inscription || $eleve['statut'] !== 'actif') {
            die("L'élève n'est pas inscrit activement pour cette année scolaire.");
        }
        
        $pdfService = new PdfService();
        
        $html = $this->renderView('pdf/certificat_scolaire', [
            'eleve' => $eleve,
            'inscription' => $inscription,
            'annee_scolaire' => $anneeActive['libelle'],
            'date_actuelle' => date('d/m/Y')
        ]);
        
        $pdfService->generateCertificatScolaire($html, "certificat_scolaire_" . $eleve['matricule'] . ".pdf");
    }
}

