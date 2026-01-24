<?php
/**
 * Contrôleur InscriptionsController
 * Gère le processus unifié d'inscription/réinscription avec paiement
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Inscription.php';
require_once APP_PATH . '/Models/Paiement.php';
require_once APP_PATH . '/Models/Facture.php'; // Remplace Frais.php
require_once APP_PATH . '/Models/Eleve.php';
require_once APP_PATH . '/Models/Classe.php';
require_once APP_PATH . '/Models/TarifInscription.php';
require_once APP_PATH . '/Models/LogActivite.php';

class InscriptionsController extends BaseController {
    
    /**
     * Liste des inscriptions
     */
    public function liste() {
        $model = new Inscription();
        
        // Obtenir l'année scolaire active
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;

        $filters = [];
        if ($anneeId) {
            $filters['annee_scolaire_id'] = $anneeId;
        }

        // Filtre spécifique demandé : Uniquement les élèves ayant payé
        // On va modifier la requête dans getAllWithDetails ou filtrer ici
        $allInscriptions = $model->getAllWithDetails($filters);
        
        // Filtrer pour ne garder que ceux ayant payé (facture_statut = 'payee')
        $inscriptions = array_filter($allInscriptions, function($i) {
            return ($i['facture_statut'] === 'payee' || $i['statut'] === 'validee');
        });
        
        // Obtenir les classes pour le filtre (si encore pertinent)
        $classeModel = new Classe();
        $classes = $classeModel->all();
        
        $this->view('inscriptions/liste', [
            'inscriptions' => $inscriptions,
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
        
        // Récupérer les paiements liés à cette inscription (via Facture)
        $paiements = [];
        if (!empty($inscription['facture_inscription_id'])) {
             // Si on a l'ID de facture, on cherche les paiements de cette facture
             // Ou on garde la méthode générique par élève/année
             $paiements = $paiementModel->query(
                 "SELECT * FROM paiements WHERE facture_id = ? ORDER BY date_paiement DESC", 
                 [$inscription['facture_inscription_id']]
             );
        }
        
        $this->view('inscriptions/details', [
            'inscription' => $inscription,
            'paiements' => $paiements
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
             $paiements = $paiementModel->query(
                 "SELECT p.*, lf.designation as type_frais 
                  FROM paiements p 
                  LEFT JOIN lignes_facture lf ON p.remarque = lf.designation AND lf.facture_id = p.facture_id
                  WHERE p.facture_id = ? 
                  ORDER BY p.date_paiement DESC", 
                 [$inscription['facture_inscription_id']]
             );
             
             // Si aucun détail n'est trouvé via la remarque, on garde la description de la facture
             if (empty($paiements)) {
                 $paiements = $paiementModel->query(
                     "SELECT p.*, f.description as type_frais 
                      FROM paiements p 
                      INNER JOIN factures f ON p.facture_id = f.id 
                      WHERE p.facture_id = ? 
                      ORDER BY p.date_paiement DESC", 
                     [$inscription['facture_inscription_id']]
                 );
             }
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
        require_once APP_PATH . '/Models/Parent.php';
        $parentModel = new ParentModel();
        $parent = $parentModel->find($parentId);
        
        if (!$parent) {
            $_SESSION['error'] = "Parent non trouvé";
            $this->redirect('/parents/list');
            return;
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
                'lien_parente' => $parent['type_parent'] ?? 'tuteur'
            ]
        ];
        
        $this->redirect('/inscriptions/nouveau?etape=2');
    }
    
    public function nouveau() {
        // Réinitialiser les données si on commence au début (pas d'étape ou étape 1)
        if (!isset($_GET['etape']) || $_GET['etape'] == 1) {
            unset($_SESSION['inscription_data']);
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
    
    private function etape1ChoixType() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['inscription_data']['type_inscription'] = $_POST['type_inscription'];
            $this->redirect('/inscriptions/nouveau?etape=2');
            return;
        }
        $this->view('inscriptions/etape1_type');
    }
    
    private function etape2SelectionEleve() {
        if (!isset($_SESSION['inscription_data'])) {
            $_SESSION['error'] = "Votre session a expiré. Veuillez recommencer.";
            $this->redirect('/inscriptions/nouveau?etape=1');
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
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $fileName = 'eleve_' . uniqid() . '.' . $extension;
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                        $photoPath = 'uploads/eleves/photos/' . $fileName;
                    }
                }

                // Générer un matricule pour le nouvel élève
                if (!function_exists('generateMatricule')) {
                    require_once APP_PATH . '/Helpers/functions.php';
                }
                $matricule = generateMatricule('eleve', 'eleves');

                // Sauvegarder les données élève nécessaires pour la création en base
                $_SESSION['inscription_data']['eleve_data'] = [
                    'matricule'        => $matricule,
                    'nom'              => mb_strtoupper($_POST['nom'] ?? ''),
                    'prenom'           => mb_convert_case($_POST['prenom'] ?? '', MB_CASE_TITLE, "UTF-8"),
                    'sexe'             => $_POST['sexe'],
                    'date_naissance'   => $_POST['date_naissance'],
                    'lieu_naissance'   => $_POST['lieu_naissance'] ?? null,
                    'photo'            => $photoPath,
                    'statut'           => 'actif',
                    'date_inscription' => date('Y-m-d'),
                ];
                
                $telephone = preg_replace('/[^0-9]/', '', $_POST['parent_telephone']);
                $_SESSION['inscription_data']['parent_data'] = [
                    'nom'          => mb_strtoupper($_POST['parent_nom'] ?? ''),
                    'prenom'       => mb_convert_case($_POST['parent_prenom'] ?? '', MB_CASE_TITLE, "UTF-8"),
                    'lien_parente' => $_POST['parent_lien'],
                    'telephone'    => $telephone,
                    'email'        => $_POST['parent_email'] ?? null,
                    'profession'   => $_POST['parent_profession'] ?? null,
                    'adresse'      => $_POST['parent_adresse'] ?? null,
                ];
                $_SESSION['inscription_data']['eleve_nouveau'] = true;
            } else {
                $eleveId = $_POST['eleve_id'];
                $inscriptionModel = new Inscription();
                require_once __DIR__ . '/../Models/AnneeScolaire.php';
                $anneeModel = new AnneeScolaire();
                $anneeActive = $anneeModel->getActive();
                
                if ($anneeActive) {
                    $validationReinscription = $inscriptionModel->validateReinscription($eleveId, $anneeActive['id']);
                    if (!$validationReinscription['valid']) {
                        $_SESSION['error'] = $validationReinscription['message'];
                        $this->redirect('/inscriptions/nouveau?etape=2');
                        return;
                    }
                }
                $_SESSION['inscription_data']['eleve_id'] = $eleveId;
                $_SESSION['inscription_data']['eleve_nouveau'] = false;
            }
            $this->redirect('/inscriptions/nouveau?etape=3');
            return;
        }
        
        $eleveModel = new Eleve();
        $eleves = [];
        $nextMatricule = null;
        
        if ($type === 'reinscription') {
            // Récupérer l'année scolaire active
            require_once APP_PATH . '/Models/AnneeScolaire.php';
            $anneeModel = new AnneeScolaire();
            $anneeActive = $anneeModel->getActive();
            
            // DEBUG TEMPORAIRE
            error_log("=== DEBUG REINSCRIPTION ===");
            error_log("Année active: " . ($anneeActive ? json_encode($anneeActive) : "AUCUNE"));
            
            if ($anneeActive) {
                // Utiliser la méthode du modèle pour récupérer les élèves éligibles
                $eleves = $eleveModel->getElevesEligiblesReinscription($anneeActive['id']);
                
                // DEBUG TEMPORAIRE
                error_log("Nombre d'élèves trouvés: " . count($eleves));
                if (count($eleves) > 0) {
                    error_log("Premier élève: " . json_encode($eleves[0]));
                } else {
                    // Vérifier les statuts des élèves
                    $debugSql3 = "SELECT statut, COUNT(*) as total FROM eleves GROUP BY statut";
                    $debugResult3 = $eleveModel->query($debugSql3);
                    error_log("Statuts des élèves: " . json_encode($debugResult3));
                    
                    // Vérifier les élèves qui ont des inscriptions
                    $debugSql4 = "SELECT COUNT(DISTINCT eleve_id) as total FROM inscriptions WHERE annee_scolaire_id < ?";
                    $debugResult4 = $eleveModel->query($debugSql4, [$anneeActive['id']]);
                    error_log("Élèves avec inscriptions année précédente: " . json_encode($debugResult4));
                }
                
                // Vérifier aussi combien d'élèves sont dans l'année précédente
                $debugSql1 = "SELECT COUNT(*) as total FROM inscriptions WHERE annee_scolaire_id < ?";
                $debugResult1 = $eleveModel->query($debugSql1, [$anneeActive['id']]);
                error_log("Inscriptions année précédente: " . json_encode($debugResult1));
                
                // Vérifier combien sont déjà réinscrits
                $debugSql2 = "SELECT COUNT(*) as total FROM inscriptions WHERE annee_scolaire_id = ?";
                $debugResult2 = $eleveModel->query($debugSql2, [$anneeActive['id']]);
                error_log("Inscriptions année active: " . json_encode($debugResult2));
            }
        } else {
            // Prévisualisation du prochain matricule élève
            if (!function_exists('generateMatricule')) {
                require_once APP_PATH . '/Helpers/functions.php';
            }
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
    
    
    private function etape3ChoixClasse() {
        // Vérifier que les étapes précédentes sont complétées
        if (!isset($_SESSION['inscription_data'])) {
            $_SESSION['error'] = "Veuillez d'abord compléter les étapes précédentes.";
            $this->redirect('/inscriptions/nouveau?etape=1');
            return;
        }
        
        // Pour une nouvelle inscription, vérifier que les données élève existent
        // Pour une réinscription, vérifier que l'eleve_id existe
        $typeInscription = $_SESSION['inscription_data']['type_inscription'] ?? 'nouvelle';
        
        if ($typeInscription === 'nouvelle') {
            // Nouvelle inscription : vérifier que les données élève sont présentes
            if (empty($_SESSION['inscription_data']['eleve_data'])) {
                $_SESSION['error'] = "Veuillez d'abord renseigner les informations de l'élève.";
                $this->redirect('/inscriptions/nouveau?etape=2');
                return;
            }
        } else {
            // Réinscription : vérifier que l'élève a été sélectionné
            if (empty($_SESSION['inscription_data']['eleve_id'])) {
                $_SESSION['error'] = "Veuillez d'abord sélectionner un élève.";
                $this->redirect('/inscriptions/nouveau?etape=2');
                return;
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation : vérifier que classe_id est fourni
            if (empty($_POST['classe_id'])) {
                $_SESSION['error'] = "Veuillez sélectionner une classe.";
                $this->redirect('/inscriptions/nouveau?etape=3');
                return;
            }
            
            $classeIdChoisie = $_POST['classe_id'];
            
            // Vérifier que la classe existe
            $classeModel = new Classe();
            $classeChoisie = $classeModel->find($classeIdChoisie);
            
            if (!$classeChoisie) {
                $_SESSION['error'] = "La classe sélectionnée n'existe pas.";
                $this->redirect('/inscriptions/nouveau?etape=3');
                return;
            }
            
            $typeInscription = $_SESSION['inscription_data']['type_inscription'] ?? 'nouvelle';
            
            // VALIDATION ANTI-RÉTROGRADATION pour les réinscriptions
            if ($typeInscription === 'reinscription' && !empty($_SESSION['inscription_data']['eleve_id'])) {
                $eleveId = $_SESSION['inscription_data']['eleve_id'];
                
                // Récupérer la classe de l'année précédente
                require_once APP_PATH . '/Models/AnneeScolaire.php';
                $anneeModel = new AnneeScolaire();
                $anneeActive = $anneeModel->getActive();
                
                if ($anneeActive) {
                    // Classe précédente de l'élève
                    $classePrecedente = $classeModel->queryOne(
                        "SELECT c.*, n.ordre as niveau_ordre, n.libelle as niveau_nom
                         FROM classes c
                         INNER JOIN inscriptions i ON c.id = i.classe_id
                         INNER JOIN niveaux n ON c.niveau_id = n.id
                         WHERE i.eleve_id = ? 
                         AND i.annee_scolaire_id < ?
                         ORDER BY i.annee_scolaire_id DESC
                         LIMIT 1",
                        [$eleveId, $anneeActive['id']]
                    );
                    
                    // Récupérer les infos complètes de la classe choisie avec niveau
                    $classeChoisieComplete = $classeModel->queryOne(
                        "SELECT c.*, n.ordre as niveau_ordre, n.libelle as niveau_nom
                         FROM classes c
                         INNER JOIN niveaux n ON c.niveau_id = n.id
                         WHERE c.id = ?",
                        [$classeIdChoisie]
                    );
                    
                    // Vérifier que le niveau n'est pas inférieur
                    if ($classePrecedente && $classeChoisieComplete) {
                        if ($classeChoisieComplete['niveau_ordre'] < $classePrecedente['niveau_ordre']) {
                            $_SESSION['error'] = "❌ Rétrogradation interdite ! L'élève était en {$classePrecedente['niveau_nom']} ({$classePrecedente['nom']}) l'année dernière. "
                                               . "Il ne peut pas être inscrit en {$classeChoisieComplete['niveau_nom']} ({$classeChoisieComplete['nom']}) qui est un niveau inférieur.";
                            $this->redirect('/inscriptions/nouveau?etape=3');
                            return;
                        }
                    }
                }
            }
            
            $_SESSION['inscription_data']['classe_id'] = $classeIdChoisie;
            
            // Déterminer la prochaine étape selon le type d'inscription
            $typeInscription = $_SESSION['inscription_data']['type_inscription'] ?? 'nouvelle';
            $isReinscription = ($typeInscription === 'reinscription');
            
            // Pour les réinscriptions, pas besoin de documents, on va directement aux articles (étape 5)
            // Pour les nouvelles inscriptions, on passe par les documents (étape 4)
            $prochaineEtape = $isReinscription ? 5 : 4;
            
            // Sauvegarder le brouillon pour avoir un ID d'inscription pour les documents
            try {
                $inscriptionId = $this->sauvegarderBrouillon();
                if ($inscriptionId) {
                    $_SESSION['inscription_data']['inscription_id'] = $inscriptionId;
                    $this->redirect('/inscriptions/nouveau?etape=' . $prochaineEtape);
                    return;
                }
            } catch (Exception $e) {
                error_log("Erreur sauvegarderBrouillon: " . $e->getMessage());
                error_log("Trace: " . $e->getTraceAsString());
                $_SESSION['error'] = "Erreur lors de la préparation de l'inscription : " . $e->getMessage();
                $this->redirect('/inscriptions/nouveau?etape=3');
                return;
            }
            
            $this->redirect('/inscriptions/nouveau?etape=' . $prochaineEtape);
            return;
        }
        
        $classeModel = new Classe();
        
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : 0;

        // Récupérer toutes les classes actives avec leurs niveaux et le nombre d'élèves
        $classes = $classeModel->getAllWithNiveauAndCount($anneeId);
        
        $classeSuggeree = null;
        $classePrecedente = null;
        $savedClasseId = $_SESSION['inscription_data']['classe_id'] ?? null;
        
        // Pour les réinscriptions, récupérer la classe précédente et suggérer la suivante
        $typeInscription = $_SESSION['inscription_data']['type_inscription'] ?? 'nouvelle';
        if ($typeInscription === 'reinscription' && !empty($_SESSION['inscription_data']['eleve_id'])) {
            $eleveId = $_SESSION['inscription_data']['eleve_id'];
            
            // require_once APP_PATH . '/Models/AnneeScolaire.php';
            // $anneeModel = new AnneeScolaire();
            // $anneeActive = $anneeModel->getActive();
            
            if ($anneeActive) {
                // Récupérer la classe de l'année précédente
                $classePrecedente = $classeModel->queryOne(
                    "SELECT c.*, n.libelle as niveau_nom, n.ordre as niveau_ordre
                     FROM classes c
                     INNER JOIN inscriptions i ON c.id = i.classe_id
                     INNER JOIN niveaux n ON c.niveau_id = n.id
                     WHERE i.eleve_id = ? 
                     AND i.annee_scolaire_id < ?
                     ORDER BY i.annee_scolaire_id DESC
                     LIMIT 1",
                    [$eleveId, $anneeActive['id']]
                );
                
                // Suggérer la classe du niveau suivant (même série si possible)
                if ($classePrecedente) {
                    $classeSuggeree = $classeModel->queryOne(
                        "SELECT c.*, n.libelle as niveau_nom, n.ordre as niveau_ordre
                         FROM classes c
                         INNER JOIN niveaux n ON c.niveau_id = n.id
                         WHERE n.ordre = ? 
                         AND c.statut = 'actif' 
                         AND c.deleted_at IS NULL
                         ORDER BY c.nom ASC
                         LIMIT 1",
                        [$classePrecedente['niveau_ordre'] + 1]
                    );
                }
            }
        }
        
        $this->view('inscriptions/etape3_classe', [
            'classes' => $classes,
            'classeSuggeree' => $classeSuggeree,
            'classePrecedente' => $classePrecedente,
            'savedClasseId' => $savedClasseId,
            'typeInscription' => $typeInscription
        ]);
    }
    
    private function etape4Documents() {
        if (!isset($_SESSION['inscription_data'])) {
            $_SESSION['error'] = "Votre session a expiré. Veuillez recommencer.";
            $this->redirect('/inscriptions/nouveau?etape=1');
            return;
        }
        
        $inscriptionId = $_SESSION['inscription_data']['inscription_id'] ?? null;
        if (!$inscriptionId) {
            $this->redirect('/inscriptions/nouveau?etape=3');
            return;
        }

        require_once APP_PATH . '/Models/DocumentsInscription.php';
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        
        $model = new Inscription();
        $docModel = new DocumentsInscription();
        
        $inscription = $model->getDetails($inscriptionId);
        if (!$inscription) {
            $_SESSION['error'] = "Inscription non trouvée";
            $this->redirect('/inscriptions/nouveau?etape=3');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'upload') {
                $this->uploadDocument($inscriptionId, $inscription, '/inscriptions/nouveau?etape=4');
                return;
            } else if ($action === 'delete') {
                $docId = $_POST['document_id'] ?? 0;
                $docModel->deleteDocument($docId);
                $this->redirect('/inscriptions/nouveau?etape=4');
                return;
            }
        }
        
        // Si c'est une réinscription, on passe directement à l'étape 5 (articles optionnels)
        if (($_SESSION['inscription_data']['type_inscription'] ?? '') === 'reinscription') {
            $this->redirect('/inscriptions/nouveau?etape=5');
            return;
        }

        $documents = $docModel->getByInscription($inscriptionId);
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        
        // Récupérer le cycle pour les exigences conditionnelles
        $classeModel = new Classe();
        $classe = $classeModel->getDetails($inscription['classe_id']);
        $cycle = $classe['cycle_nom'] ?? '';
        $niveau = $classe['niveau_nom'] ?? '';

        $exigences = $anneeActive ? $docModel->getExigences($anneeActive['id'], $inscription['type_inscription']) : [];
        
        // Si c'est une nouvelle inscription, on s'assure que les documents de base sont là
        // Si la base de données ne les fournit pas, on les injecte ou on les force
        if ($inscription['type_inscription'] === 'nouvelle') {
            // Acte de naissance toujours obligatoire
            $hasActe = false;
            foreach ($exigences as $ex) if ($ex['type_document'] === 'acte_naissance') $hasActe = true;
            if (!$hasActe) {
                $exigences[] = ['type_document' => 'acte_naissance', 'libelle' => 'Acte de naissance', 'obligatoire' => 1];
            }

            // Certificat ou Bulletin pour Secondaire / Lycée
            $isSecondaireOuLycee = (strpos(strtolower($cycle), 'secondaire') !== false || strpos(strtolower($cycle), 'lycée') !== false || strpos(strtolower($niveau), 'seconde') !== false || strpos(strtolower($niveau), 'première') !== false || strpos(strtolower($niveau), 'terminale') !== false);
            
            if ($isSecondaireOuLycee) {
                $hasScolarite = false;
                foreach ($exigences as $ex) if ($ex['type_document'] === 'certificat_scolarite' || $ex['type_document'] === 'bulletin_notes') $hasScolarite = true;
                if (!$hasScolarite) {
                    $exigences[] = ['type_document' => 'certificat_scolarite', 'libelle' => 'Certificat de scolarité ou Bulletin scolaire', 'obligatoire' => 1];
                }
            }
        }

        $stats = $docModel->getStats($inscriptionId);
        
        $this->view('inscriptions/etape4_documents', [
            'inscription' => $inscription,
            'documents' => $documents,
            'exigences' => $exigences,
            'stats' => $stats,
            'cycle' => $cycle
        ]);
    }

    private function etape5ArticlesOptionnels() {
        if (!isset($_SESSION['inscription_data'])) {
            $_SESSION['error'] = "Votre session a expiré. Veuillez recommencer.";
            $this->redirect('/inscriptions/nouveau?etape=1');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sauvegarder les articles sélectionnés
            $articlesSelectionnes = $_POST['articles'] ?? [];
            $_SESSION['inscription_data']['articles_optionnels'] = $articlesSelectionnes;
            
            // Passer à l'étape suivante (Frais et Paiement)
            $this->redirect('/inscriptions/nouveau?etape=6');
            return;
        }
        
        $inscriptionId = $_SESSION['inscription_data']['inscription_id'] ?? null;
        $classeId = $_SESSION['inscription_data']['classe_id'] ?? null;
        
        if (!$inscriptionId || !$classeId) {
            $_SESSION['error'] = "Veuillez d'abord compléter les étapes précédentes";
            $this->redirect('/inscriptions/nouveau?etape=4');
            return;
        }
        
        // Récupérer la classe et le niveau
        $classeModel = new Classe();
        $classe = $classeModel->find($classeId);
        
        // Récupérer l'année active
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        
        if (!$anneeActive) {
            $_SESSION['error'] = "Aucune année scolaire active";
            $this->redirect('/inscriptions/nouveau?etape=4');
            return;
        }
        
        // Récupérer les articles disponibles pour ce niveau
        require_once APP_PATH . '/Models/Article.php';
        require_once APP_PATH . '/Models/TarifArticle.php';
        
        $articleModel = new Article();
        $articles = $articleModel->getAllWithTarifs($anneeActive['id']);
        
        // Filtrer uniquement les articles actifs avec un prix défini
        $articlesDisponibles = array_filter($articles, function($article) {
            return $article['actif'] && !empty($article['prix_unitaire']);
        });
        
        // Récupérer les articles déjà sélectionnés (si retour en arrière)
        $articlesSelectionnes = $_SESSION['inscription_data']['articles_optionnels'] ?? [];
        
        $this->view('inscriptions/etape5_articles', [
            'articles' => $articlesDisponibles,
            'articlesSelectionnes' => $articlesSelectionnes,
            'classe' => $classe
        ]);
    }

    private function etape6FraisEtPaiement() {
        if (!isset($_SESSION['inscription_data'])) {
            $_SESSION['error'] = "Votre session a expiré. Veuillez recommencer.";
            $this->redirect('/inscriptions/nouveau?etape=1');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paiementDroitInscription = floatval($_POST['paiement_droit_inscription'] ?? 0);
            $paiementEcolage = floatval($_POST['paiement_premier_mois'] ?? 0);
            $montantArticles = floatval($_POST['montant_articles_total'] ?? 0);
            
            $montantTotal = $paiementDroitInscription + $paiementEcolage + $montantArticles;
            
            $droitInscriptionDu = floatval($_POST['frais_inscription_montant']);
            $ecolageDu = floatval($_POST['premier_mois_ecolage_montant']);

            $_SESSION['inscription_data']['frais_inscription_montant'] = $droitInscriptionDu;
            $_SESSION['inscription_data']['premier_mois_ecolage_montant'] = $ecolageDu;
            $_SESSION['inscription_data']['montant_articles_total'] = $montantArticles;
            $_SESSION['inscription_data']['option_ecolage'] = $_POST['option_ecolage'] ?? 1;
            $_SESSION['inscription_data']['nombre_mois'] = intval($_POST['nombre_mois'] ?? 1);

            $_SESSION['inscription_data']['paiement_initial'] = [
                'paiement_droit_inscription' => $paiementDroitInscription,
                'paiement_premier_mois' => $paiementEcolage,
                'paiement_articles' => $montantArticles,
                'montant' => $montantTotal,
                'mode_paiement' => $_POST['mode_paiement'], // ID
                'reference_externe' => $_POST['reference_externe'] ?? null,
                'commentaire' => $_POST['commentaire_paiement'] ?? null
            ];
            $_SESSION['inscription_data']['commentaire'] = $_POST['commentaire'] ?? null;
            
            // VALIDATION DÉFINITIVE ICI
            return $this->enregistrer(true); // Passer true pour dire qu'on veut aller au reçu
        }
        
        $inscriptionId = $_SESSION['inscription_data']['inscription_id'] ?? null;
        $classeId = $_SESSION['inscription_data']['classe_id'] ?? null;
        
        if (!$inscriptionId) {
            // Si on n'a pas d'ID, on essaie de sauvegarder à nouveau
            try {
                $inscriptionId = $this->sauvegarderBrouillon();
                $_SESSION['inscription_data']['inscription_id'] = $inscriptionId;
            } catch (Exception $e) {
                $_SESSION['error'] = "Veuillez d'abord compléter les étapes précédentes";
                $this->redirect('/inscriptions/nouveau?etape=3');
                return;
            }
        }
        
        $model = new Inscription();
        $inscription = $model->getDetails($inscriptionId);
        
        $classeModel = new Classe();
        $classe = $classeModel->find($classeId);
        
        if (!$classe) {
            $_SESSION['error'] = "Classe introuvable.";
            $this->redirect('/inscriptions/nouveau?etape=3');
            return;
        }
        
        // Année
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        
        if (!$anneeActive) {
            $_SESSION['error'] = "Aucune année scolaire active.";
            $this->redirect('/inscriptions/nouveau?etape=1');
            return;
        }
        
        require_once APP_PATH . '/Models/TarifInscription.php';
        $tarifModel = new TarifInscription();
        $tarif = $tarifModel->getByAnneeAndNiveau($anneeActive['id'], $classe['niveau_id']);
        
        if (!$tarif) {
            $_SESSION['error'] = "Aucun tarif configuré pour ce niveau et cette année scolaire.";
            $this->redirect('/inscriptions/nouveau?etape=5');
            return;
        }

        $tarifDroit = [
            'montant' => $tarif['frais_inscription'],
            'annee_scolaire' => $anneeActive['libelle']
        ];
        
        $tarifEcolage = [
            'montant' => $tarif['ecolage_mensuel'],
            'mois_debut' => $tarif['mois_debut_annee'] ?? 9
        ];
        
        require_once APP_PATH . '/Models/ModePaiement.php';
        $modeModel = new ModePaiement();
        $modesPaiement = $modeModel->all(['actif' => 1]);

        $savedFraisData = $_SESSION['inscription_data']['frais_data'] ?? [];
        
        // Récupérer les articles sélectionnés pour les afficher
        $articlesChoisis = [];
        $montantTotalArticles = 0;
        $articlesSession = $_SESSION['inscription_data']['articles_optionnels'] ?? [];
        
        if (!empty($articlesSession)) {
            require_once APP_PATH . '/Models/Article.php';
            $articleModel = new Article();
            foreach ($articlesSession as $articleId) {
                $art = $articleModel->getWithTarif($articleId, $anneeActive['id']);
                if ($art) {
                    $articlesChoisis[] = $art;
                    $montantTotalArticles += $art['prix_unitaire'];
                }
            }
        }

        $this->view('inscriptions/etape5_frais', [
            'tarifDroit' => $tarifDroit,
            'tarifEcolage' => $tarifEcolage,
            'classe' => $classe,
            'savedFraisData' => $savedFraisData,
            'modesPaiement' => $modesPaiement,
            'inscription' => $inscription,
            'articlesChoisis' => $articlesChoisis,
            'montantTotalArticles' => $montantTotalArticles
        ]);
    }
    
    private function etape7Confirmation() {
        $inscriptionId = $_GET['id'] ?? null;
        if (!$inscriptionId) {
            $this->redirect('/inscriptions/nouveau?etape=5');
            return;
        }

        $model = new Inscription();
        $inscription = $model->getDetails($inscriptionId);
        
        if (!$inscription) {
            $_SESSION['error'] = "Inscription introuvable.";
            $this->redirect('/inscriptions/liste');
            return;
        }

        // Récupérer le dernier paiement lié à cette inscription
        $paiement = $model->queryOne(
            "SELECT p.*, mp.libelle as mode_paiement_libelle, a.libelle as annee_scolaire 
             FROM paiements p 
             LEFT JOIN factures f ON p.facture_id = f.id 
             LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
             LEFT JOIN annees_scolaires a ON f.annee_scolaire_id = a.id
             WHERE f.id = ? ORDER BY p.id DESC LIMIT 1",
            [$inscription['facture_inscription_id']]
        );

        // Récupérer les lignes de la facture pour le détail du reçu
        $lignes = $model->query(
            "SELECT * FROM lignes_facture WHERE facture_id = ?",
            [$inscription['facture_inscription_id']]
        );

        $this->view('inscriptions/etape6_confirmation', [
            'inscription' => $inscription,
            'paiement' => $paiement,
            'lignes' => $lignes
        ]);
    }
    
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
        
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        
        if (!$anneeActive) {
            $_SESSION['error'] = "Aucune année scolaire active.";
            $this->redirect('/inscriptions/nouveau?etape=1');
            return;
        }
        
        $tarifModel = new TarifInscription();
        $tarif = $tarifModel->queryOne(
            "SELECT * FROM tarifs_inscription WHERE niveau_id = ? AND annee_scolaire_id = ? AND actif = 1 LIMIT 1",
            [$classe['niveau_id'], $anneeActive['id']]
        );
        
        if (!$tarif) {
            throw new Exception("Aucun tarif configuré pour ce niveau.");
        }
        
        $_SESSION['inscription_data']['frais_inscription_montant'] = $tarif['frais_inscription'] ?? 0;
        $_SESSION['inscription_data']['premier_mois_ecolage_montant'] = $tarif['ecolage_mensuel'] ?? 0;
        $_SESSION['inscription_data']['annee_scolaire_id'] = $anneeActive['id'];
        
        // Création du parent et de l'élève si nouveau
        if ($data['eleve_nouveau'] ?? false) {
             $eleveModel = new Eleve();
             if (empty($data['eleve_data']['matricule'])) {
                 if (!function_exists('generateMatricule')) require_once APP_PATH . '/Helpers/functions.php';
                 $data['eleve_data']['matricule'] = generateMatricule('eleve', 'eleves');
             }
             $_SESSION['inscription_data']['eleve_id'] = $eleveModel->create($data['eleve_data']);
             
             if (!empty($data['parent_data'])) {
                 require_once APP_PATH . '/Models/Parent.php';
                 $parentModel = new ParentModel();
                 $parentId = $parentModel->create([
                     'nom' => $data['parent_data']['nom'],
                     'prenom' => $data['parent_data']['prenom'],
                     'telephone' => $data['parent_data']['telephone'],
                     'adresse' => $data['parent_data']['adresse'] ?? null
                 ]);
                 $parentModel->query("INSERT INTO eleves_parents (eleve_id, parent_id, lien_parente) VALUES (?, ?, ?)", [$_SESSION['inscription_data']['eleve_id'], $parentId, $data['parent_data']['lien_parente'] ?? 'pere']);
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
        
        // Créer l'inscription via le modèle (avec paiement à null)
        $inscriptionModel = new Inscription();
        $inscriptionId = $inscriptionModel->creerInscription($_SESSION['inscription_data'], null);
        
        return $inscriptionId;
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
                     require_once APP_PATH . '/Models/Parent.php';
                     $parentModel = new ParentModel();
                     
                     // Vérifier si un parent avec le même téléphone existe déjà
                     $parentExistant = null;
                     if (!empty($data['parent_data']['telephone'])) {
                         $parentExistant = $parentModel->queryOne(
                             "SELECT id FROM parents WHERE telephone = ? LIMIT 1",
                             [$data['parent_data']['telephone']]
                         );
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
                     $parentModel->query(
                         "INSERT INTO eleves_parents (eleve_id, parent_id, lien_parente) 
                          VALUES (?, ?, ?)",
                         [
                             $data['eleve_id'],
                             $parentId,
                             $data['parent_data']['lien_parente'] ?? 'pere'
                         ]
                     );
                     error_log("Relation eleves_parents créée pour élève ID: " . $data['eleve_id'] . " et parent ID: $parentId");
                }
            } // Fin du ELSE (si l'élève n'existait pas encore)
        } // Fin du IF (eleve_nouveau)
             
             // Récupérer année active
             require_once APP_PATH . '/Models/AnneeScolaire.php';
             $anneeModel = new AnneeScolaire();
             $anneeActive = $anneeModel->getActive();
             
             if (!$anneeActive) {
                 throw new Exception("Aucune année scolaire active trouvée. Veuillez activer une année scolaire.");
             }
             
             $data['annee_scolaire_id'] = $anneeActive['id'];
             
             // Appel modèle
              $inscriptionId = $data['inscription_id']; // L'ID de l'inscription brouillon est déjà en session
              $model->finaliserInscription($inscriptionId, $data, $data['paiement_initial'] ?? null);
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
        
        require_once APP_PATH . '/Models/ModePaiement.php';
        $modeModel = new ModePaiement();
        $modesPaiement = $modeModel->all(['actif' => 1]);
        
        $this->view('inscriptions/ajouter_paiement', ['inscription' => $inscription, 'modesPaiement' => $modesPaiement]);
    }
    
    /**
     * Gestion des documents d'inscription
     */
    public function documents($id) {
        require_once APP_PATH . '/Models/DocumentsInscription.php';
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        
        $model = new Inscription();
        $docModel = new DocumentsInscription();
        
        $inscription = $model->getDetails($id);
        if (!$inscription) {
            $_SESSION['error'] = "Inscription non trouvée";
            $this->redirect('/inscriptions/liste');
            return;
        }
        
        // Traitement des actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'upload':
                    $this->uploadDocument($id, $inscription);
                    break;
                    
                case 'valider':
                    $docId = $_POST['document_id'] ?? 0;
                    if ($docModel->valider($docId, $_SESSION['user_id'])) {
                        $_SESSION['success'] = "Document validé avec succès";
                    } else {
                        $_SESSION['error'] = "Erreur lors de la validation du document";
                    }
                    $this->redirect('/inscriptions/documents/' . $id);
                    return;
                    
                case 'refuser':
                    $docId = $_POST['document_id'] ?? 0;
                    $motif = $_POST['motif_refus'] ?? '';
                    if ($docModel->refuser($docId, $motif, $_SESSION['user_id'])) {
                        $_SESSION['success'] = "Document refusé";
                    } else {
                        $_SESSION['error'] = "Erreur lors du refus du document";
                    }
                    $this->redirect('/inscriptions/documents/' . $id);
                    return;
                    
                case 'supprimer':
                    $docId = $_POST['document_id'] ?? 0;
                    if ($docModel->deleteDocument($docId)) {
                        $_SESSION['success'] = "Document supprimé avec succès";
                    } else {
                        $_SESSION['error'] = "Erreur lors de la suppression du document";
                    }
                    $this->redirect('/inscriptions/documents/' . $id);
                    return;
            }
        }
        
        // Récupérer les documents existants
        $documents = $docModel->getByInscription($id);
        
        // Récupérer les exigences de documents
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $exigences = [];
        
        if ($anneeActive) {
            $exigences = $docModel->getExigences(
                $anneeActive['id'], 
                $inscription['type_inscription']
            );
        }
        
        // Statistiques
        $stats = $docModel->getStats($id);
        
        $this->view('inscriptions/documents', [
            'inscription' => $inscription,
            'documents' => $documents,
            'exigences' => $exigences,
            'stats' => $stats
        ]);
    }
    
    /**
     * Upload d'un document
     */
    private function uploadDocument($inscriptionId, $inscription, $redirectUrl = null) {
        if (!$redirectUrl) {
            $redirectUrl = '/inscriptions/documents/' . $inscriptionId;
        }
        require_once APP_PATH . '/Models/DocumentsInscription.php';
        
        if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Erreur lors de l'upload du fichier";
            $this->redirect($redirectUrl);
            return;
        }
        
        $file = $_FILES['fichier'];
        $typeDocument = $_POST['type_document'] ?? '';
        $obligatoire = isset($_POST['obligatoire_pour_validation']) ? 1 : 0;
        
        // Validation du type de fichier
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = "Type de fichier non autorisé. Formats acceptés: PDF, JPG, PNG";
            $this->redirect($redirectUrl);
            return;
        }
        
        // Validation de la taille (5 Mo max)
        $maxSize = 5 * 1024 * 1024; // 5 Mo
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = "Le fichier est trop volumineux (max 5 Mo)";
            $this->redirect($redirectUrl);
            return;
        }
        
        // Créer le répertoire de stockage si nécessaire
        $uploadDir = STORAGE_PATH . '/documents/inscriptions/' . $inscriptionId;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $typeDocument . '_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;
        
        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $_SESSION['error'] = "Erreur lors de l'enregistrement du fichier";
            $this->redirect($redirectUrl);
            return;
        }
        
        // Enregistrer en base de données
        $docModel = new DocumentsInscription();
        $data = [
            'inscription_id' => $inscriptionId,
            'eleve_id' => $inscription['eleve_id'],
            'type_document' => $typeDocument,
            'nom_fichier' => $file['name'],
            'chemin_fichier' => $filepath,
            'taille_fichier' => $file['size'],
            'type_mime' => $file['type'],
            'statut' => 'en_attente',
            'obligatoire_pour_validation' => $obligatoire,
            'numero_document' => $_POST['numero_document'] ?? null,
            'date_emission' => $_POST['date_emission'] ?? null,
            'date_expiration' => $_POST['date_expiration'] ?? null,
            'lieu_emission' => $_POST['lieu_emission'] ?? null,
            'remarques' => $_POST['remarques'] ?? null,
            'telecharge_par' => $_SESSION['user_id']
        ];
        
        if ($docModel->create($data)) {
            $_SESSION['success'] = "Document uploadé avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de l'enregistrement du document";
        }
        
        $this->redirect($redirectUrl);
    }
}
