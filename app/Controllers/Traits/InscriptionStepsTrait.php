<?php
declare(strict_types=1);

namespace App\Controllers\Traits;

use App\Models\AnneeScolaire;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\ParentModel;
use App\Models\DocumentsInscription;
use App\Models\TarifInscription;
use App\Models\Article;
use App\Models\ModePaiement;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\LogActivite;
use PDOException;
use Exception;

/**
 * Trait InscriptionStepsTrait
 * Contient la logique des étapes du wizard d'inscription pour alléger InscriptionsController
 */
trait InscriptionStepsTrait {
    
    protected function etape1ChoixType() {
        if (isset($_SESSION['inscription_data']['parent_data']) && !empty($_SESSION['inscription_data']['parent_data'])) {
            $this->redirect('inscriptions/nouveau?etape=2');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['inscription_data']['type_inscription'] = $_POST['type_inscription'];
            $this->redirect('inscriptions/nouveau?etape=2');
            return;
        }
        $this->view('inscriptions/etape1_type');
    }
    
    protected function etape2SelectionEleve() {
        if (!isset($_SESSION['inscription_data'])) {
            $_SESSION['error'] = "Votre session a expiré. Veuillez recommencer.";
            $this->redirect('inscriptions/nouveau?etape=1');
            return;
        }
        
        $type = $_SESSION['inscription_data']['type_inscription'] ?? 'nouvelle';
        $savedData = $_SESSION['inscription_data']['eleve_data'] ?? [];
        $savedParentData = $_SESSION['inscription_data']['parent_data'] ?? [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($type === 'nouvelle') {
                $photoPath = $savedData['photo'] ?? null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = PUBLIC_PATH . '/uploads/eleves/photos/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $filename = 'eleve_' . time() . '.' . $extension;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $filename)) {
                        $photoPath = 'uploads/eleves/photos/' . $filename;
                    }
                }
                
                $_SESSION['inscription_data']['eleve_data'] = [
                    'nom' => $_POST['nom'],
                    'prenom' => $_POST['prenom'],
                    'date_naissance' => $_POST['date_naissance'],
                    'lieu_naissance' => $_POST['lieu_naissance'],
                    'sexe' => $_POST['sexe'],
                    'adresse' => $_POST['adresse'],
                    'matricule' => $_POST['matricule'] ?? null,
                    'photo' => $photoPath
                ];
                
                $_SESSION['inscription_data']['parent_data'] = [
                    'nom' => $_POST['parent_nom'],
                    'prenom' => $_POST['parent_prenom'],
                    'telephone' => $_POST['parent_telephone'],
                    'email' => $_POST['parent_email'] ?? null,
                    'adresse' => $_POST['parent_adresse'] ?? null,
                    'lien_parente' => $_POST['lien_parente']
                ];
                $_SESSION['inscription_data']['eleve_nouveau'] = true;
                
                // Créer ou mettre à jour l'élève immédiatement pour pouvoir utiliser son ID en étape 4
                $eleveModel = new Eleve();
                $eleveData = $_SESSION['inscription_data']['eleve_data'];
                
                // Générer un matricule si non fourni
                if (empty($eleveData['matricule'])) {
                    if (!function_exists('generateMatricule')) {
                        require_once APP_PATH . '/Helpers/functions.php';
                    }
                    $eleveData['matricule'] = generateMatricule('eleve', 'eleves');
                }
                
                // Ajouter le statut 'brouillon' pour les nouvelles inscriptions
                $eleveData['statut'] = 'brouillon';
                
                if (empty($_SESSION['inscription_data']['eleve_id'])) {
                    // Créer l'élève la première fois avec statut 'brouillon'
                    $_SESSION['inscription_data']['eleve_id'] = $eleveModel->create($eleveData);
                } else {
                    // Mettre à jour l'élève s'il existe déjà
                    $eleveModel->update($_SESSION['inscription_data']['eleve_id'], $eleveData);
                }
                
                // Créer le parent et la liaison si nécessaire (première fois seulement)
                if (!empty($_SESSION['inscription_data']['parent_data']) && empty($_SESSION['inscription_data']['parent_created'])) {
                    $parentModel = new ParentModel();
                    
                    // Vérifier si un parent avec le même téléphone existe déjà
                    $parentExistant = null;
                    if (!empty($_SESSION['inscription_data']['parent_data']['telephone'])) {
                        $parentExistant = $parentModel->getByTelephone($_SESSION['inscription_data']['parent_data']['telephone']);
                    }
                    
                    // Créer le parent s'il n'existe pas
                    if ($parentExistant) {
                        $parentId = $parentExistant['id'];
                    } else {
                        $parentId = $parentModel->create([
                            'nom' => $_SESSION['inscription_data']['parent_data']['nom'],
                            'prenom' => $_SESSION['inscription_data']['parent_data']['prenom'],
                            'telephone' => $_SESSION['inscription_data']['parent_data']['telephone'],
                            'email' => $_SESSION['inscription_data']['parent_data']['email'] ?? null,
                            'adresse' => $_SESSION['inscription_data']['parent_data']['adresse'] ?? null
                        ]);
                    }
                    
                    // Créer ou mettre à jour la relation eleves_parents
                    $parentModel->linkToEleve($parentId, $_SESSION['inscription_data']['eleve_id'], $_SESSION['inscription_data']['parent_data']['lien_parente'] ?? 'pere');
                    $_SESSION['inscription_data']['parent_created'] = true;
                }
            } else {
                $eleveId = $_POST['eleve_id'];
                if (empty($eleveId)) {
                    $_SESSION['error'] = "Veuillez sélectionner un élève.";
                    $this->redirect('inscriptions/nouveau?etape=2');
                    return;
                }
                
                $eleveModel = new Eleve();
                $anneeModel = new AnneeScolaire();
                $anneeActive = $anneeModel->getActive();
                if ($anneeActive) {
                    $validationReinscription = $eleveModel->validerEligibiliteReinscription($eleveId, $anneeActive['id']);
                    if (!$validationReinscription['success']) {
                        $_SESSION['error'] = $validationReinscription['message'];
                        $this->redirect('inscriptions/nouveau?etape=2');
                        return;
                    }
                }
                $_SESSION['inscription_data']['eleve_id'] = $eleveId;
                $_SESSION['inscription_data']['eleve_nouveau'] = false;
            }
            $this->redirect('inscriptions/nouveau?etape=3');
            return;
        }
        
        $eleveModel = new Eleve();
        $eleves = [];
        $nextMatricule = null;
        
        if ($type === 'reinscription') {
            $anneeModel = new AnneeScolaire();
            $anneeActive = $anneeModel->getActive();
            if ($anneeActive) {
                $eleves = $eleveModel->getElevesEligiblesReinscription($anneeActive['id']);
            }
        } else {
            if (!function_exists('generateMatricule')) require_once APP_PATH . '/Helpers/functions.php';
            $nextMatricule = generateMatricule('eleve', 'eleves');
        }
        
        $this->view('inscriptions/etape2_eleve', [
            'type' => $type,
            'eleves' => $eleves,
            'nextMatricule' => $nextMatricule,
            'savedData' => $savedData,
            'savedParentData' => $savedParentData
        ]);
    }

    protected function etape3ChoixClasse() {
        if (!isset($_SESSION['inscription_data'])) {
            $_SESSION['error'] = "Veuillez d'abord compléter les étapes précédentes.";
            $this->redirect('inscriptions/nouveau?etape=1');
            return;
        }
        
        $typeInscription = $_SESSION['inscription_data']['type_inscription'] ?? 'nouvelle';
        if ($typeInscription === 'nouvelle') {
            if (empty($_SESSION['inscription_data']['eleve_data'])) {
                $_SESSION['error'] = "Veuillez d'abord renseigner les informations de l'élève.";
                $this->redirect('inscriptions/nouveau?etape=2');
                return;
            }
        } else {
            if (empty($_SESSION['inscription_data']['eleve_id'])) {
                $_SESSION['error'] = "Veuillez d'abord sélectionner un élève.";
                $this->redirect('inscriptions/nouveau?etape=2');
                return;
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['classe_id'])) {
                $_SESSION['error'] = "Veuillez sélectionner une classe.";
                $this->redirect('inscriptions/nouveau?etape=3');
                return;
            }
            
            $classeIdChoisie = $_POST['classe_id'];
            $classeModel = new Classe();
            $classeChoisie = $classeModel->find($classeIdChoisie);
            
            if (!$classeChoisie) {
                $_SESSION['error'] = "La classe sélectionnée n'existe pas.";
                $this->redirect('inscriptions/nouveau?etape=3');
                return;
            }
            
            if ($typeInscription === 'reinscription' && !empty($_SESSION['inscription_data']['eleve_id'])) {
                $eleveId = $_SESSION['inscription_data']['eleve_id'];
                $anneeModel = new AnneeScolaire();
                $anneeActive = $anneeModel->getActive();
                if ($anneeActive) {
                    $classePrecedente = $classeModel->getPreviousByEleve($eleveId, $anneeActive['id']);
                    $classeChoisieComplete = $classeModel->getDetailsWithNiveau($classeIdChoisie);
                    if ($classePrecedente && $classeChoisieComplete && $classeChoisieComplete['niveau_ordre'] < $classePrecedente['niveau_ordre']) {
                        $_SESSION['error'] = "❌ Rétrogradation interdite !";
                        $this->redirect('inscriptions/nouveau?etape=3');
                        return;
                    }
                }
            }
            
            $_SESSION['inscription_data']['classe_id'] = $classeIdChoisie;
            $prochaineEtape = ($typeInscription === 'reinscription') ? 5 : 4;
            $this->redirect('inscriptions/nouveau?etape=' . $prochaineEtape);
            return;
        }
        
        $classeModel = new Classe();
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : 0;
        $classes = $classeModel->getSortedByLevel($anneeId);
        
        $classeSuggeree = null;
        $classePrecedente = null;
        if ($typeInscription === 'reinscription' && !empty($_SESSION['inscription_data']['eleve_id'])) {
            $eleveId = $_SESSION['inscription_data']['eleve_id'];
            if ($anneeActive) {
                $classePrecedente = $classeModel->getPreviousByEleve($eleveId, $anneeActive['id']);
                if ($classePrecedente) {
                    $classeSuggeree = $classeModel->getSuggestedByNiveauOrder($classePrecedente['niveau_ordre'] + 1);
                }
            }
        }
        
        $this->view('inscriptions/etape3_classe', [
            'classes' => $classes,
            'classeSuggeree' => $classeSuggeree,
            'classePrecedente' => $classePrecedente,
            'savedClasseId' => $_SESSION['inscription_data']['classe_id'] ?? null,
            'typeInscription' => $typeInscription
        ]);
    }

    protected function etape4Documents() {
        if (!isset($_SESSION['inscription_data'])) {
            $this->redirect('inscriptions/nouveau?etape=1');
            return;
        }
        
        $inscriptionId = $_SESSION['inscription_data']['inscription_id'] ?? null;
        $eleveId = $_SESSION['inscription_data']['eleve_id'] ?? null;
        
        // Valider que eleve_id existe en session
        if (!$eleveId) {
            $_SESSION['error'] = "Informations de l'élève manquantes. Veuillez recommencer depuis le début.";
            $this->redirect('inscriptions/nouveau?etape=1');
            return;
        }
        
        $model = new Inscription();
        $docModel = new DocumentsInscription();
        $inscription = $inscriptionId ? $model->getDetails($inscriptionId) : null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'upload') {
                $this->uploadDocument($inscriptionId, ['eleve_id' => $eleveId], 'inscriptions/nouveau?etape=4');
                return;
            } else if ($action === 'delete') {
                $docModel->deleteDocument($_POST['document_id'] ?? 0);
                $this->redirect('inscriptions/nouveau?etape=4');
                return;
            }
        }
        
        if (($_SESSION['inscription_data']['type_inscription'] ?? '') === 'reinscription') {
            $this->redirect('inscriptions/nouveau?etape=5');
            return;
        }

        $documents = $inscriptionId ? $docModel->getByInscription($inscriptionId) : ($eleveId ? $docModel->getByEleve($eleveId) : []);
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $classeModel = new Classe();
        $classe = ($_SESSION['inscription_data']['classe_id'] ?? null) ? $classeModel->getDetails($_SESSION['inscription_data']['classe_id']) : null;
        
        $typeInscription = $_SESSION['inscription_data']['type_inscription'] ?? 'nouveau';
        $exigences = ($anneeActive) ? $docModel->getExigences($anneeActive['id'], $typeInscription) : [];
        $stats = $docModel->getStats($inscriptionId);
        
        $this->view('inscriptions/etape4_documents', [
            'inscription' => $inscription,
            'documents' => $documents,
            'exigences' => $exigences,
            'stats' => $stats,
            'cycle' => $classe['cycle_nom'] ?? ''
        ]);
    }

    protected function etape5ArticlesOptionnels() {
        if (!isset($_SESSION['inscription_data'])) {
            $this->redirect('inscriptions/nouveau?etape=1');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['inscription_data']['articles_optionnels'] = $_POST['articles'] ?? [];
            $this->redirect('inscriptions/nouveau?etape=6');
            return;
        }
        
        $classeId = $_SESSION['inscription_data']['classe_id'] ?? null;
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        
        if (!$anneeActive || !$classeId) {
            $this->redirect('inscriptions/nouveau?etape=3');
            return;
        }
        
        $articleModel = new Article();
        $articles = $articleModel->getAllWithTarifs($anneeActive['id']);
        $articlesDisponibles = array_filter($articles, fn($a) => $a['actif'] && !empty($a['prix_unitaire']));
        
        $this->view('inscriptions/etape5_articles', [
            'articles' => $articlesDisponibles,
            'articlesSelectionnes' => $_SESSION['inscription_data']['articles_optionnels'] ?? [],
            'classe' => (new Classe())->find($classeId)
        ]);
    }

    protected function etape6FraisEtPaiement() {
        if (!isset($_SESSION['inscription_data'])) {
            $this->redirect('inscriptions/nouveau?etape=1');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pInsc = floatval($_POST['paiement_droit_inscription'] ?? 0);
            $pEcol = floatval($_POST['paiement_premier_mois'] ?? 0);
            $mArt = floatval($_POST['montant_articles_total'] ?? 0);
            
            $_SESSION['inscription_data']['frais_inscription_montant'] = floatval($_POST['frais_inscription_montant']);
            $_SESSION['inscription_data']['premier_mois_ecolage_montant'] = floatval($_POST['premier_mois_ecolage_montant']);
            $_SESSION['inscription_data']['montant_articles_total'] = $mArt;
            $_SESSION['inscription_data']['option_ecolage'] = $_POST['option_ecolage'] ?? 1;
            $_SESSION['inscription_data']['nombre_mois'] = intval($_POST['nombre_mois'] ?? 1);
            $_SESSION['inscription_data']['paiement_initial'] = [
                'paiement_droit_inscription' => $pInsc,
                'paiement_premier_mois' => $pEcol,
                'paiement_articles' => $mArt,
                'montant' => $pInsc + $pEcol + $mArt,
                'mode_paiement' => $_POST['mode_paiement'],
                'reference_externe' => $_POST['reference_externe'] ?? null,
                'commentaire' => $_POST['commentaire_paiement'] ?? null
            ];
            $_SESSION['inscription_data']['commentaire'] = $_POST['commentaire'] ?? null;
            
            return $this->enregistrer(true);
        }
        
        $inscriptionId = $_SESSION['inscription_data']['inscription_id'] ?? null;
        $classeId = $_SESSION['inscription_data']['classe_id'] ?? null;
        $anneeActive = (new AnneeScolaire())->getActive();
        $classe = (new Classe())->find($classeId);
        
        if (!$anneeActive || !$classe) {
            $this->redirect('inscriptions/nouveau?etape=1');
            return;
        }
        
        $tarif = (new TarifInscription())->getByAnneeAndNiveau($anneeActive['id'], $classe['niveau_id']);
        if (!$tarif) {
            $_SESSION['error'] = "Tarif manquant.";
            $this->redirect('inscriptions/nouveau?etape=5');
            return;
        }

        $articlesChoisis = [];
        $totalArt = 0;
        foreach ($_SESSION['inscription_data']['articles_optionnels'] ?? [] as $artId) {
            $art = (new Article())->getWithTarif($artId, $anneeActive['id']);
            if ($art) { $articlesChoisis[] = $art; $totalArt += $art['prix_unitaire']; }
        }

        $this->view('inscriptions/etape5_frais', [
            'tarifDroit' => ['montant' => $tarif['frais_inscription'], 'annee_scolaire' => $anneeActive['libelle']],
            'tarifEcolage' => ['montant' => $tarif['ecolage_mensuel'], 'mois_debut' => $tarif['mois_debut_annee'] ?? 9],
            'classe' => $classe,
            'modesPaiement' => (new ModePaiement())->all(['actif' => 1]),
            'articlesChoisis' => $articlesChoisis,
            'montantTotalArticles' => $totalArt
        ]);
    }

    /**
     * Upload d'un document pendant le processus d'inscription
     */
    protected function uploadDocument($inscriptionId, $additionalData = [], $redirectUrl = null) {
        $docModel = new DocumentsInscription();
        
        if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['error'] = "Veuillez sélectionner un fichier.";
            $this->redirect($redirectUrl ?? 'inscriptions/nouveau?etape=4');
            return;
        }

        $file = $_FILES['fichier'];
        $typeDocument = $_POST['type_document'] ?? 'autre';

        // Validation du type de fichier
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = "Type de fichier non autorisé. PDF, JPG et PNG uniquement.";
            $this->redirect($redirectUrl ?? 'inscriptions/nouveau?etape=4');
            return;
        }

        // Création du dossier si inexistant
        $uploadDir = PUBLIC_PATH . '/uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'doc_' . ($inscriptionId ?? 'temp') . '_' . time() . '_' . uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $data = [
                'inscription_id' => $inscriptionId,
                'eleve_id' => $additionalData['eleve_id'] ?? null,
                'type_document' => $typeDocument,
                'nom_fichier' => $filename,
                'chemin_fichier' => 'uploads/documents/' . $filename,
                'type_mime' => $file['type'],
                'taille_fichier' => $file['size']
            ];
            
            $docModel->create($data);
            $_SESSION['success'] = "Document ajouté avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de l'enregistrement du fichier.";
        }
        
        $this->redirect($redirectUrl ?? 'inscriptions/nouveau?etape=4');
    }

    protected function etape7Confirmation() {
        $id = $_GET['id'] ?? null;
        $inscription = (new Inscription())->getDetails($id);
        if (!$inscription) { $this->redirect('inscriptions/liste'); return; }

        $this->view('inscriptions/etape6_confirmation', [
            'inscription' => $inscription,
            'paiement' => (new Paiement())->getLastByFacture($inscription['facture_inscription_id']),
            'lignes' => (new Facture())->getLignes($inscription['facture_inscription_id'])
        ]);
    }
}
