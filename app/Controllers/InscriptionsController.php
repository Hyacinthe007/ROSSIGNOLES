<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Facture;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\TarifInscription;
use App\Models\LogActivite;
use App\Models\AnneeScolaire;
use App\Models\ParentModel;
use App\Models\Article;
use App\Models\TarifArticle;
use App\Models\ModePaiement;
use App\Models\DocumentsInscription;
use App\Controllers\Traits\InscriptionStepsTrait;

/**
 * Contrôleur InscriptionsController
 * Gère le processus unifié d'inscription/réinscription avec paiement
 */
class InscriptionsController extends BaseController {
    use InscriptionStepsTrait;
    
    /**
     * Liste des inscriptions
     */
    public function liste() {
        $model = new Inscription();
        
        // Obtenir l'année scolaire active

        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;

        $filters = [];
        if ($anneeId) {
            $filters['annee_scolaire_id'] = $anneeId;
        }

        // Récupérer les filtres depuis la requête GET
        if (isset($_GET['type']) && !empty($_GET['type'])) $filters['type_inscription'] = $_GET['type'];
        if (isset($_GET['statut']) && !empty($_GET['statut'])) $filters['statut'] = $_GET['statut'];
        if (isset($_GET['classe_id']) && !empty($_GET['classe_id'])) $filters['classe_id'] = $_GET['classe_id'];
        if (isset($_GET['q']) && !empty($_GET['q'])) $filters['q'] = $_GET['q'];

        // Obtenir les inscriptions avec les filtres appliqués directement dans la requête
        // Tri par matricule des élèves en ordre croissant
        $inscriptions = $model->getAllWithDetails($filters, 'e.matricule ASC');
        
        // Obtenir les classes pour le filtre
        $classeModel = new Classe();
        $classes = $classeModel->getSortedByLevel($anneeId);
        
        $this->view('inscriptions/liste', [
            'inscriptions' => $inscriptions,
            'filters' => $filters, // Passer les filtres à la vue
            'statistiques' => null, // Initialisé à null pour éviter le warning
            'classes' => $classes,
            'filters' => $filters,
            'anneeActive' => $anneeActive
        ]);
    }
    
    /**
     * Détails d'une inscription
     */
    public function details($id) {
        $model = new Inscription();
        $paiementModel = new Paiement();
        
        $inscription = $model->getDetails($id);
        if (!$inscription) {
            $_SESSION['error'] = "Inscription non trouvée";
            $this->redirect('/inscriptions/liste');
            return;
        }
        
        // Récupérer tous les paiements liés à cet élève pour l'année scolaire en cours
        $paiements = $paiementModel->getByEleve($inscription['eleve_id'], $inscription['annee_scolaire_id']);
        
        // Récupérer toutes les lignes de factures liées à cet élève pour cette année scolaire
        // (Inscription + Ecolages mensuels + Articles)
        $factureModel = new Facture();
        $lignesFacture = $factureModel->query(
            "SELECT lf.*, tf.libelle as type_frais 
             FROM lignes_facture lf
             INNER JOIN factures f ON lf.facture_id = f.id
             LEFT JOIN types_frais tf ON lf.type_frais_id = tf.id
             WHERE f.eleve_id = ? AND f.annee_scolaire_id = ? AND f.statut != 'annulee'",
            [$inscription['eleve_id'], $inscription['annee_scolaire_id']]
        );
        
        // Récupérer les autres élèves de la même classe
        $elevesMemeClasse = $model->query(
            "SELECT e.*, i.date_inscription, i.type_inscription, i.id as inscription_id
             FROM inscriptions i
             JOIN eleves e ON i.eleve_id = e.id
             WHERE i.classe_id = ? AND i.annee_scolaire_id = ? AND i.statut = 'validee' AND e.id != ?
             ORDER BY e.nom ASC, e.prenom ASC",
            [$inscription['classe_id'], $inscription['annee_scolaire_id'], $inscription['eleve_id']]
        );
        
        $this->view('inscriptions/details', [
            'inscription' => $inscription,
            'paiements' => $paiements,
            'lignesFacture' => $lignesFacture,
            'elevesMemeClasse' => $elevesMemeClasse
        ]);
    }
    
    /**
     * Affiche le reçu de paiement imprimable
     */
    public function recuPaiement($id) {
        $model = new Inscription();
        $paiementModel = new Paiement();
        
        $inscription = $model->getDetails($id);
        if (!$inscription) {
            $_SESSION['error'] = "Inscription non trouvée";
            $this->redirect('/inscriptions/liste');
            return;
        }
        
        $paiements = [];
        if (!empty($inscription['facture_inscription_id'])) {
             $paiements = $paiementModel->getByFactureWithDetails($inscription['facture_inscription_id']);
        }
        
        require_once __DIR__ . '/../Views/inscriptions/recu_paiement.php';
    }
    
    /**
     * Formulaire de nouvelle inscription/réinscription (étape 1)
     */
    /**
     * Démarre une inscription pour un parent existant
     */
    public function parParent($parentId) {

        $parentModel = new ParentModel();
        $parent = $parentModel->find($parentId);
        
        if (!$parent) {
            $_SESSION['error'] = "Parent non trouvé";
            $this->redirect('/parents/list');
            return;
        }
        
        // Mapper le type_parent de la base vers le libellé utilisé dans le formulaire
        $mappingLiens = [
            'pere' => 'Père',
            'père' => 'Père',
            'mere' => 'Mère',
            'mère' => 'Mère',
            'tuteur' => 'Tuteur',
            'tutrice' => 'Tutrice',
            'autre' => 'Autre'
        ];
        
        $lienParente = $parent['type_parent'] ?? 'Tuteur';
        $normalizedLien = mb_strtolower((string)$lienParente, 'UTF-8');
        if (isset($mappingLiens[$normalizedLien])) {
            $lienParente = $mappingLiens[$normalizedLien];
        } else {
            // Si pas dans le mapping, on met la première lettre en majuscule pour essayer de correspondre
            $lienParente = mb_convert_case($lienParente, MB_CASE_TITLE, "UTF-8");
        }

        // Initialiser la session d'inscription
        $_SESSION['inscription_data'] = [
            'type_inscription' => 'nouvelle',
            'parent_data' => [
                'nom' => $parent['nom'],
                'prenom' => $parent['prenom'],
                'telephone' => $parent['telephone'],
                'email' => $parent['email'],
                'adresse' => $parent['adresse'],
                'profession' => $parent['profession'] ?? '',
                'sexe' => $parent['sexe'] ?? '',
                'lien_parente' => $lienParente
            ]
        ];
        
        // Rediriger directement vers l'étape 2 car les données du parent sont déjà en session
        $_SESSION['success'] = "Parent sélectionné : " . $parent['nom'] . " " . $parent['prenom'] . ". Veuillez continuer l'inscription.";
        $this->redirect('/inscriptions/nouveau?etape=2');
    }
    
    public function nouveau() {
        // Réinitialiser les données si on commence au début (pas d'étape ou étape 1)
        if (!isset($_GET['etape']) || $_GET['etape'] == 1) {
            unset($_SESSION['inscription_data']);
            // Nettoyer aussi les messages de session pour l'étape 1
            unset($_SESSION['success']);
            unset($_SESSION['error']);
        }
        
        $etape = $_GET['etape'] ?? 1;
        
        switch ($etape) {
            case 1: $this->etape1ChoixType(); break;
            case 2: $this->etape2SelectionEleve(); break;
            case 3: $this->etape3ChoixClasse(); break;
            case 4: $this->etape4Documents(); break;
            case '4.5':
            case 5: $this->etape5ArticlesOptionnels(); break;
            case 6: $this->etape6FraisEtPaiement(); break;
            case 7: $this->etape7Confirmation(); break;
            default: $this->redirect('/inscriptions/nouveau?etape=1');
        }
    }
    
    /**
     * Annule une inscription en brouillon et nettoie tous les données associées
     */
    public function annulerInscription() {
        if (!isset($_SESSION['inscription_data'])) {
            $_SESSION['error'] = "Aucune inscription en cours à annuler.";
            $this->redirect('/inscriptions/nouveau?etape=1');
            return;
        }
        
        $eleveId = $_SESSION['inscription_data']['eleve_id'] ?? null;
        $inscriptionId = $_SESSION['inscription_data']['inscription_id'] ?? null;
        
        if ($eleveId) {
            try {
                $eleveModel = new Eleve();
                $eleve = $eleveModel->find($eleveId);
                
                // Vérifier que l'élève est bien en statut 'brouillon' (non finalisé)
                if ($eleve && $eleve['statut'] === 'brouillon') {
                    // Utiliser une transaction pour garantir la cohérence
                    $eleveModel->beginTransaction();
                    
                    try {
                        // Supprimer l'inscription si elle existe
                        if ($inscriptionId) {
                            $inscriptionModel = new Inscription();
                            $inscriptionModel->delete($inscriptionId);
                        }
                        
                        // Supprimer les documents uploadés
                        $docModel = new DocumentsInscription();
                        $documents = $docModel->getByEleve($eleveId);
                        foreach ($documents as $doc) {
                            // Supprimer le fichier physique
                            $filePath = PUBLIC_PATH . '/' . $doc['chemin_fichier'];
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                            // Supprimer l'entrée en BDD
                            $docModel->delete($doc['id']);
                        }
                        
                        // Supprimer les relations élève-parent
                        $parentModel = new ParentModel();
                        $parentModel->execute(
                            "DELETE FROM eleves_parents WHERE eleve_id = ?",
                            [$eleveId]
                        );
                        
                        // Supprimer l'élève
                        $eleveModel->delete($eleveId);
                        
                        $eleveModel->commit();
                        $_SESSION['success'] = "Inscription annulée avec succès. Toutes les données ont été supprimées.";
                    } catch (\Exception $e) {
                        $eleveModel->rollback();
                        throw $e;
                    }
                } else {
                    // L'élève n'est pas en brouillon (réinscription ou déjà finalisé)
                    // Supprimer seulement l'inscription brouillon si elle existe, sans toucher à l'élève
                    if ($inscriptionId) {
                        $inscriptionModel = new Inscription();
                        $inscription = $inscriptionModel->find($inscriptionId);
                        if ($inscription && ($inscription['statut'] ?? '') !== 'validee') {
                            $inscriptionModel->delete($inscriptionId);
                        }
                    }
                    $_SESSION['success'] = "Inscription annulée.";
                }
            } catch (\Exception $e) {
                error_log("Erreur lors de l'annulation d'inscription: " . $e->getMessage());
                $_SESSION['error'] = "Erreur lors de l'annulation : " . $e->getMessage();
            }
        }
        
        // Nettoyer la session
        unset($_SESSION['inscription_data']);
        
        // Rediriger vers la page d'accueil des inscriptions
        $this->redirect('/inscriptions/nouveau?etape=1');
    }
    
    // La méthode etape1ChoixType est gérée par le trait
    
    
    // Les méthodes d'étapes (2 à 4) sont gérées par le trait InscriptionStepsTrait
    

    // Méthodes d'étapes gérées par le trait
    
    
    /**
     * Sauvegarde initiale pour avoir un ID (utilisé avant l'upload de documents)
     */
    private function sauvegarderBrouillon() {
        if (!isset($_SESSION['inscription_data'])) return null;
        
        // Si déjà sauvegardé, on vérifie si la classe est identique
        if (isset($_SESSION['inscription_data']['inscription_id'])) {
            $inscriptionId = $_SESSION['inscription_data']['inscription_id'];
            $model = new Inscription();
            $inscriptionCense = $model->find($inscriptionId);
            
            // Si l'inscription en base a la même classe, on ne touche à rien
            if ($inscriptionCense && $inscriptionCense['classe_id'] == $_SESSION['inscription_data']['classe_id']) {
                return $inscriptionId;
            }
            
            // Sinon, si la classe a changé, on devra mettre à jour (ou continuer le processus pour recréer/mettre à jour)
            unset($_SESSION['inscription_data']['inscription_id']); // On force la recréation ou mise à jour
        }

        $data = $_SESSION['inscription_data'];
        
        // Extraire ou calculer les tarifs pour créer la facture
        if (empty($data['classe_id'])) {
            error_log("=== ERREUR: classe_id manquant dans sauvegarderBrouillon ===");
            error_log("Data: " . json_encode($data));
            $_SESSION['error'] = "Veuillez d'abord sélectionner une classe.";
            $this->redirect('/inscriptions/nouveau?etape=3');
            return;
        }
        
        $classeId = $data['classe_id'];
        error_log("=== sauvegarderBrouillon - Recherche classe ===");
        error_log("Classe ID recherché: " . $classeId);
        
        $classeModel = new Classe();
        $classe = $classeModel->find($classeId);
        
        error_log("Classe trouvée: " . ($classe ? json_encode($classe) : 'FALSE'));
        
        if (!$classe) {
            error_log("=== ERREUR: Classe introuvable ===");
            error_log("ID recherché: " . $classeId);
            $_SESSION['error'] = "Classe introuvable.";
            $this->redirect('/inscriptions/nouveau?etape=3');
            return;
        }
        

        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        
        if (!$anneeActive) {
            $_SESSION['error'] = "Aucune année scolaire active.";
            $this->redirect('/inscriptions/nouveau?etape=1');
            return;
        }
        
        $tarifModel = new TarifInscription();
        $tarif = $tarifModel->getByAnneeAndNiveau($anneeActive['id'], $classe['niveau_id']);
        
        if (!$tarif) {
            throw new Exception("Aucun tarif configuré pour ce niveau.");
        }
        
        $_SESSION['inscription_data']['frais_inscription_montant'] = $tarif['frais_inscription'] ?? 0;
        $_SESSION['inscription_data']['premier_mois_ecolage_montant'] = $tarif['ecolage_mensuel'] ?? 0;
        $_SESSION['inscription_data']['annee_scolaire_id'] = $anneeActive['id'];
        
        // Création du parent et de l'élève si nouveau
        if ($data['eleve_nouveau'] ?? false) {
             $eleveModel = new Eleve();
             
             // CORRECTIF : Si l'élève a déjà été créé lors d'un appel précédent, on ne le recrée pas !
             if (!empty($_SESSION['inscription_data']['eleve_id'])) {
                 error_log("Réutilisation de l'élève existant (ID: " . $_SESSION['inscription_data']['eleve_id'] . ")");
                 // L'élève existe déjà, on ne fait rien
             } else {
                 // Générer le matricule si nécessaire
                 if (empty($data['eleve_data']['matricule'])) {
                     if (!function_exists('generateMatricule')) require_once APP_PATH . '/Helpers/functions.php';
                     $data['eleve_data']['matricule'] = generateMatricule('eleve', 'eleves');
                     $_SESSION['inscription_data']['eleve_data']['matricule'] = $data['eleve_data']['matricule'];
                 }
                 
                 // Créer l'élève
                 $_SESSION['inscription_data']['eleve_id'] = $eleveModel->create($data['eleve_data']);
                 error_log("Nouvel élève créé (ID: " . $_SESSION['inscription_data']['eleve_id'] . ")");
             }
             
             // Créer le parent et la relation si nécessaire
             if (!empty($data['parent_data']) && empty($_SESSION['inscription_data']['parent_created'])) {
         
                 $parentModel = new ParentModel();
                 
                 // Vérifier si un parent avec le même téléphone existe déjà
                 $parentExistant = null;
                 if (!empty($data['parent_data']['telephone'])) {
                     $parentExistant = $parentModel->getByTelephone($data['parent_data']['telephone']);
                 }
                 
                 // Créer le parent s'il n'existe pas
                 if ($parentExistant) {
                     $parentId = $parentExistant['id'];
                     error_log("Parent existant trouvé (ID: $parentId)");
                 } else {
                     $parentId = $parentModel->create([
                         'nom' => $data['parent_data']['nom'],
                         'prenom' => $data['parent_data']['prenom'],
                         'telephone' => $data['parent_data']['telephone'],
                         'email' => $data['parent_data']['email'] ?? null,
                         'adresse' => $data['parent_data']['adresse'] ?? null
                     ]);
                     error_log("Nouveau parent créé (ID: $parentId)");
                 }
                 
                 $parentModel->linkToEleve($parentId, $_SESSION['inscription_data']['eleve_id'], $data['parent_data']['lien_parente'] ?? 'pere');
                 $_SESSION['inscription_data']['parent_created'] = true;
             }
        } else {
            // Élève existant (réinscription) - vérifier que l'eleve_id est bien défini dans la session
            if (empty($_SESSION['inscription_data']['eleve_id'])) {
                error_log("=== ERREUR: eleve_id manquant pour réinscription ===");
                error_log("Session: " . json_encode($_SESSION['inscription_data']));
                error_log("Data: " . json_encode($data));
                throw new Exception("ID de l'élève manquant pour la réinscription.");
            }
            // L'eleve_id est déjà dans la session, pas besoin de le réassigner
            error_log("Réinscription - eleve_id trouvé: " . $_SESSION['inscription_data']['eleve_id']);
        }
        
        // Vérification finale : s'assurer que l'eleve_id est bien défini
        if (empty($_SESSION['inscription_data']['eleve_id'])) {
            throw new Exception("Impossible de créer l'inscription : ID de l'élève manquant.");
        }
        
        // NE PAS créer l'inscription à ce stade (étape 3)
        // L'inscription sera créée uniquement à l'étape 6 après le paiement
        // On stocke seulement les données dans la session
        error_log("Brouillon sauvegardé - Données stockées en session (pas encore en BDD)");
        
        return null; // Pas d'ID d'inscription pour le moment
    }
    
    public function enregistrer($goToReceipt = false) {
        if (!isset($_SESSION['inscription_data'])) {
            $this->redirect('/inscriptions/nouveau');
            return;
        }
        
        $model = new Inscription();
        try {
            // Création élève si nouveau
             $data = $_SESSION['inscription_data'];
             if ($data['eleve_nouveau'] ?? false) {
                 $eleveModel = new Eleve();

                 // CORRECTIF : Si l'élève a déjà été créé lors du brouillon, on ne le recrée pas !
                 if (!empty($data['eleve_id'])) {
                     error_log("Réutilisation de l'élève existant (ID: " . $data['eleve_id'] . ")");
                     // On met quand même à jour les données au cas où elles auraient changé entre temps
                     $eleveModel->update($data['eleve_id'], $data['eleve_data']);
                 } else {
                     // S'assurer qu'on a bien un matricule non vide
                     if (empty($data['eleve_data']['matricule'] ?? null)) {
                         if (!function_exists('generateMatricule')) {
                             require_once APP_PATH . '/Helpers/functions.php';
                         }
                         $data['eleve_data']['matricule'] = generateMatricule('eleve', 'eleves');
                     }

                 // Tentative de création avec gestion des doublons de matricule
                 $maxAttempts = 3;
                 $attempt = 0;
                 while ($attempt < $maxAttempts) {
                     try {
                         $data['eleve_id'] = $eleveModel->create($data['eleve_data']);
                         break; // succès, on sort de la boucle
                     } catch (PDOException $e) {
                         // Doublon sur le matricule : on régénère et on réessaie
                         if ($e->getCode() === '23000' && strpos($e->getMessage(), 'matricule') !== false) {
                             if (!function_exists('generateMatricule')) {
                                 require_once APP_PATH . '/Helpers/functions.php';
                             }
                             $data['eleve_data']['matricule'] = generateMatricule('eleve', 'eleves');
                             $attempt++;
                             continue;
                         }
                         // Autre erreur : on relance
                         throw $e;
                     }
                 }

                 if (empty($data['eleve_id'])) {
                     throw new Exception("Impossible de créer l'élève après plusieurs tentatives (matricule en doublon).");
                 }

                 // Création du parent et de la relation eleves_parents
                 if (!empty($data['parent_data'])) {
             
                     $parentModel = new ParentModel();
                     
                     // Vérifier si un parent avec le même téléphone existe déjà
                     $parentExistant = null;
                     if (!empty($data['parent_data']['telephone'])) {
                         $parentExistant = $parentModel->getByTelephone($data['parent_data']['telephone']);
                     }
                     
                     // Créer le parent s'il n'existe pas
                     if ($parentExistant) {
                         $parentId = $parentExistant['id'];
                         error_log("Parent existant trouvé (ID: $parentId) pour le téléphone: " . $data['parent_data']['telephone']);
                     } else {
                         $parentId = $parentModel->create([
                             'nom' => $data['parent_data']['nom'],
                             'prenom' => $data['parent_data']['prenom'],
                             'telephone' => $data['parent_data']['telephone'],
                             'email' => $data['parent_data']['email'] ?? null,
                             'adresse' => $data['parent_data']['adresse'] ?? null
                         ]);
                         error_log("Nouveau parent créé (ID: $parentId)");
                     }
                     
                     // Créer la relation eleves_parents
                     $parentModel->linkToEleve($parentId, $data['eleve_id'], $data['parent_data']['lien_parente'] ?? 'pere');
                     error_log("Relation eleves_parents créée pour élève ID: " . $data['eleve_id'] . " et parent ID: $parentId");
                }
            } // Fin du ELSE (si l'élève n'existait pas encore)
        } // Fin du IF (eleve_nouveau)
             
             // Récupérer année active
     
             $anneeModel = new AnneeScolaire();
             $anneeActive = $anneeModel->getActive();
             
             if (!$anneeActive) {
                 throw new Exception("Aucune année scolaire active trouvée. Veuillez activer une année scolaire.");
             }
             
             $data['annee_scolaire_id'] = $anneeActive['id'];
             
             // Appel modèle
              $inscriptionId = $data['inscription_id'] ?? null;
              if (empty($inscriptionId)) {
                  $inscriptionId = $model->creerInscription($data, null);
                  $_SESSION['inscription_data']['inscription_id'] = $inscriptionId;
                  
                  // Rattacher les documents orphelins (uploadés à l'étape 4 sans ID d'inscription)
                  $docModel = new DocumentsInscription();
                  $docModel->execute(
                      "UPDATE documents_inscription SET inscription_id = ? WHERE eleve_id = ? AND inscription_id IS NULL",
                      [$inscriptionId, $data['eleve_id']]
                  );
              }
              $model->finaliserInscription($inscriptionId, $data, $data['paiement_initial'] ?? null);
              
              // Passer l'élève du statut 'brouillon' à 'actif' maintenant que l'inscription est finalisée
              $eleveModel = new Eleve();
              $eleveModel->update($data['eleve_id'], ['statut' => 'actif', 'date_inscription' => date('Y-m-d')]);
              
              LogActivite::log(
                  'Finalisation Inscription', 
                  'Scolarité', 
                  "Validation et paiement pour l'inscription ID: $inscriptionId",
                  'inscriptions',
                  $inscriptionId
              );
                            $data = $_SESSION['inscription_data'];
               unset($_SESSION['inscription_data']);
               
               if ($goToReceipt) {
                   $this->redirect('/inscriptions/nouveau?etape=7&id=' . $inscriptionId);
               } else {
                   $_SESSION['success'] = "Inscription finalisée avec succès.";
                   $this->redirect('/inscriptions/liste');
               }
              
        } catch (Exception $e) {
            // Logger l'erreur complète
            error_log("Erreur lors de l'enregistrement de l'inscription: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $_SESSION['error'] = "Erreur lors de l'enregistrement : " . $e->getMessage();
            $this->redirect('/inscriptions/nouveau?etape=5');
        }
    }
    
    // ... Autres méthodes (ajouterPaiement, terminer, modifier) adaptées ...
    // Je simplifie pour l'instant pour rentrer dans le fichier
    
     public function ajouterPaiement($id) {
        $model = new Inscription();
        $inscription = $model->getDetails($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             // Utiliser Paiement model sur la facture de l'inscription
             if ($inscription['facture_inscription_id']) {
                 $paiementModel = new Paiement();
                 $paiementModel->create([
                     'facture_id' => $inscription['facture_inscription_id'],
                     'montant' => $_POST['montant'],
                     'date_paiement' => date('Y-m-d'),
                     'mode_paiement_id' => $_POST['mode_paiement'],
                     'reference_paiement' => $_POST['reference_externe'] ?? ''
                 ]);
                 // Mettre à jour facture montant_paye/statut (supposé géré par trigger ou manuel)
                 $factureModel = new Facture();
                 $facture = $factureModel->find($inscription['facture_inscription_id']);
                 $newPaye = $facture['montant_paye'] + $_POST['montant'];
                 $statut = ($newPaye >= $facture['montant_total']) ? 'payee' : 'partiellement_payee';
                 $factureModel->update($inscription['facture_inscription_id'], ['montant_paye' => $newPaye, 'statut' => $statut]);
                 
                 $_SESSION['success'] = "Paiement ajouté.";
                 
                 // Logging
                 LogActivite::log(
                     'Nouveau Paiement', 
                     'Finance', 
                     "Paiement de {$_POST['montant']} pour l'inscription ID: $id",
                     'inscriptions',
                     $id
                 );
                 
                 $this->redirect('/inscriptions/details/'.$id);
             }
        }
        

        $modeModel = new ModePaiement();
        $modesPaiement = $modeModel->all(['actif' => 1]);
        
        $this->view('inscriptions/ajouter_paiement', ['inscription' => $inscription, 'modesPaiement' => $modesPaiement]);
    }
    
    // La gestion des documents est déportée dans InscriptionDocumentController
    
}
