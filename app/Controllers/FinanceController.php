<?php
/**
 * Contrôleur des finances
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Services/FinanceService.php';



class FinanceController extends BaseController {
    private $financeService;
    private $fraisModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->financeService = new FinanceService();
//        $this->fraisModel = new Frais(); // Modèle inexistant et inutilisé
    }
    
    public function dashboard() {
        $stats = $this->financeService->getStats();
        $this->view('finance/dashboard', ['stats' => $stats]);
    }
    
    /**
     * Liste des échéances d'écolage avec filtres
     */
    public function listeEcolage() {
        // Récupérer les filtres
        $mois = isset($_GET['mois']) ? (int)$_GET['mois'] : date('n');
        $annee = isset($_GET['annee']) ? (int)$_GET['annee'] : date('Y');
        $statut = $_GET['statut'] ?? null;
        $classeId = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : null;
        
        // Récupérer la liste
        $echeances = $this->financeService->getListeEcolage($mois, $annee, $statut, $classeId);
        
        // Récupérer les classes pour le filtre
        require_once APP_PATH . '/Models/Classe.php';
        $classeModel = new Classe();
        $classes = $classeModel->query("SELECT * FROM classes WHERE statut = 'actif' AND deleted_at IS NULL ORDER BY nom ASC");
        
        // Noms des mois en français
        $moisNoms = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        
        $this->view('finance/liste_ecolage', [
            'echeances' => $echeances,
            'classes' => $classes,
            'filters' => [
                'mois' => $mois,
                'annee' => $annee,
                'statut' => $statut,
                'classe_id' => $classeId
            ],
            'moisNoms' => $moisNoms
        ]);
    }
    
    /**
     * Enregistrer un paiement d'écolage
     */
    public function payerEcolage($id) {
        require_once APP_PATH . '/Models/EcheancierEcolage.php';
        require_once APP_PATH . '/Models/Paiement.php';
        
        $echeancierModel = new EcheancierEcolage();
        $paiementModel = new Paiement();
        
        // Récupérer les détails de l'échéance avec nouveau schéma
        $echeance = $echeancierModel->queryOne(
            "SELECT e.*, el.nom as eleve_nom, el.prenom as eleve_prenom, el.matricule, 
                    c.nom as classe_nom,
                    p.telephone as parent_telephone, p.nom as parent_nom
             FROM echeanciers_ecolages e
             INNER JOIN eleves el ON e.eleve_id = el.id
             INNER JOIN inscriptions i ON e.eleve_id = i.eleve_id AND e.annee_scolaire_id = i.annee_scolaire_id
             INNER JOIN classes c ON i.classe_id = c.id
             LEFT JOIN eleves_parents ep ON el.id = ep.eleve_id
             LEFT JOIN parents p ON ep.parent_id = p.id
             WHERE e.id = ?",
            [$id]
        );
        
        if (!$echeance) {
            http_response_code(404);
            die("Échéance non trouvée");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $montant = str_replace([' ', ','], ['', '.'], $_POST['montant'] ?? '0');
            $montant = (float)$montant;
            $modePaiement = $_POST['mode_paiement'] ?? 1; // ID du mode
            $reference = $_POST['reference'] ?? null;
            
            if ($montant <= 0) {
                $_SESSION['error_message'] = "Le montant doit être valide.";
            } else {
                // Créer le paiement (lié à l'échéance via?? Pas de facture ici?)
                // Idéalement on crée une FACTURE pour ce paiement d'écolage ou on lie directement si le modèle le permet.
                // Le modèle Paiement a 'facture_id'. 
                // Pour l'instant, on suppose qu'on crée une facture à la volée ou qu'on adapte le modèle.
                
                // CRÉATION FACTURE POUR LE PAIEMENT (Si nécessaire pour la cohérence)
                require_once APP_PATH . '/Models/Facture.php';
                $factureModel = new Facture();
                
                // Récupérer type facture Ecolage
                $typeFactureEcolage = $factureModel->queryOne("SELECT id FROM types_facture WHERE code = 'ECOLAGE' OR libelle LIKE '%Ecolage%' LIMIT 1");
                $typeFactureId = $typeFactureEcolage ? $typeFactureEcolage['id'] : 1; // Fallback
                
                $factureId = $factureModel->create([
                    'numero_facture' => $factureModel->generateNextNumber('numero_facture', 'Eco'),
                    'eleve_id' => $echeance['eleve_id'],
                    'annee_scolaire_id' => $echeance['annee_scolaire_id'],
                    'type_facture_id' => $typeFactureId,
                    'date_facture' => date('Y-m-d'),
                    'montant_total' => $montant,
                    'montant_paye' => $montant,
                    'montant_restant' => 0,
                    'statut' => 'payee',
                    'description' => "Paiement écolage " . $echeance['mois_libelle']
                ]);
                
                // CRÉATION DE LA LIGNE DE FACTURE (Important pour le reçu détaillé)
                require_once APP_PATH . '/Models/LigneFacture.php';
                require_once APP_PATH . '/Models/TypeFrais.php';
                $ligneFactureModel = new LigneFacture();
                $typeFraisModel = new TypeFrais();
                
                // Chercher type frais écolage
                $typeFraisEcolage = $typeFraisModel->queryOne("SELECT id FROM types_frais WHERE categorie = 'scolarite' LIMIT 1");
                $typeFraisId = $typeFraisEcolage ? $typeFraisEcolage['id'] : 1; // Fallback
                
                $ligneFactureModel->create([
                    'facture_id' => $factureId,
                    'type_frais_id' => $typeFraisId,
                    'designation' => "Écolage mois de " . $echeance['mois_libelle'], // Ex: Écolage mois de Septembre 2024
                    'quantite' => 1,
                    'prix_unitaire' => $montant,
                    'montant' => $montant
                ]);
                
                $paiementData = [
                    'numero_paiement' => $paiementModel->generateNextNumber('numero_paiement', 'Pay'),
                    'facture_id' => $factureId,
                    'date_paiement' => date('Y-m-d'),
                    'montant' => $montant,
                    'mode_paiement_id' => $modePaiement,
                    'reference_paiement' => $reference,
                    'remarque' => "Paiement écolage " . $echeance['mois_libelle']
                ];
                
                $paiementModel->create($paiementData);
                
                // Mettre à jour l'échéancier
                $nouveauMontantPaye = $echeance['montant_paye'] + $montant;
                $nouveauStatut = ($nouveauMontantPaye >= $echeance['montant_du']) ? 'paye' : 'partiel';
                
                $echeancierModel->update($id, [
                    'montant_paye' => $nouveauMontantPaye,
                    'statut' => $nouveauStatut,
                    'date_paiement_complet' => ($nouveauStatut == 'paye') ? date('Y-m-d') : null,
                    'derniere_facture_id' => $factureId
                ]);
                
                $_SESSION['success_message'] = "Paiement de " . number_format($montant, 0, ',', ' ') . " Ar enregistré avec succès.";
                $this->redirect('finance/liste-ecolage');
            }
        }
        
        // Charger les modes de paiement
        require_once APP_PATH . '/Models/ModePaiement.php';
        $modePaiementModel = new ModePaiement();
        $modesPaiement = $modePaiementModel->all(['actif' => 1]);
        
        $this->view('finance/payer_ecolage', ['echeance' => $echeance, 'modesPaiement' => $modesPaiement]);
    }
    
    public function list() {
        require_once APP_PATH . '/Models/Facture.php';
        $factureModel = new Facture();
        
        try {
            // Récupérer les factures
            $factures = $factureModel->query(
                "SELECT f.*, 
                        e.nom as eleve_nom, e.prenom as eleve_prenom, e.matricule,
                        tf.libelle as type_facture_libelle,
                        an.libelle as annee_scolaire_libelle
                 FROM factures f
                 LEFT JOIN eleves e ON f.eleve_id = e.id
                 LEFT JOIN types_facture tf ON f.type_facture_id = tf.id
                 LEFT JOIN annees_scolaires an ON f.annee_scolaire_id = an.id
                 ORDER BY f.date_facture DESC"
            );
        } catch (PDOException $e) {
            $factures = [];
        }
        
        $stats = $this->financeService->getStats();
        $this->view('finance/list', ['factures' => $factures, 'stats' => $stats]);
    }
    
    public function add() {
        require_once APP_PATH . '/Models/Facture.php';
        require_once APP_PATH . '/Models/LigneFacture.php';
        $factureModel = new Facture();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Logique création facture
            // Pour simplifier, on crée une facture simple
             $data = [
                'numero_facture' => $factureModel->generateNextNumber('numero_facture', 'Fact'),
                'eleve_id' => $_POST['eleve_id'] ?? '',
                'type_facture_id' => $_POST['type_facture_id'] ?? '',
                'annee_scolaire_id' => $_POST['annee_scolaire_id'] ?? '',
                'date_facture' => date('Y-m-d'),
                'montant_total' => $_POST['montant'] ?? 0, // Simplifié
                'statut' => 'impayee',
                'description' => $_POST['description'] ?? ''
            ];
            
            $id = $factureModel->create($data);
            
            // Créer une ligne par défaut
            $ligneModel = new LigneFacture();
            $ligneModel->create([
                'facture_id' => $id,
                'libelle' => 'Frais divers',
                'montant' => $_POST['montant'] ?? 0,
                'quantite' => 1,
                'total' => $_POST['montant'] ?? 0
            ]);

            $_SESSION['success_message'] = 'Facture créée avec succès !';
            $this->redirect('finance/details/' . $id);
        } else {
            // Données pour formulaire
             // ... Récupération eleves, types, annees ...
             // Similaire à avant mais pointant vers nouvelles tables
             // Je simplifie l'implementation ici pour brevity du diff, mais normalement on charge eleves/types_facture/annees
             
             require_once APP_PATH . '/Models/Eleve.php';
             require_once APP_PATH . '/Models/TypeFacture.php';
             require_once APP_PATH . '/Models/AnneeScolaire.php';
             
             $eleveModel = new Eleve();
             $typeFactureModel = new TypeFacture();
             $anneeModel = new AnneeScolaire();
             
             $eleves = $eleveModel->query("SELECT * FROM eleves WHERE statut = 'actif' ORDER BY nom ASC");
             $typesFacture = $typeFactureModel->all(['actif' => 1], 'libelle ASC');
             $anneesScolaires = $anneeModel->all([], 'date_debut DESC');
             
            $this->view('finance/add', [
                'eleves' => $eleves,
                'typesFacture' => $typesFacture, // Changé de typesFrais
                'anneesScolaires' => $anneesScolaires
            ]);
        }
    }
    
    public function details($id) {
        require_once APP_PATH . '/Models/Facture.php';
        $factureModel = new Facture();
        
        $facture = $factureModel->queryOne(
            "SELECT f.*, 
                    e.nom as eleve_nom, e.prenom as eleve_prenom, e.matricule,
                    tf.libelle as type_facture_libelle,
                    an.libelle as annee_scolaire_libelle
             FROM factures f
             LEFT JOIN eleves e ON f.eleve_id = e.id
             LEFT JOIN types_facture tf ON f.type_facture_id = tf.id
             LEFT JOIN annees_scolaires an ON f.annee_scolaire_id = an.id
             WHERE f.id = ?",
            [$id]
        );
        
         if (!$facture) {
            http_response_code(404);
            die("Facture non trouvée");
        }
        
        $lignes = $factureModel->getLignes($id);
        $paiements = $factureModel->getPaiements($id);

        $this->view('finance/details', [
            'facture' => $facture, 
            'lignes' => $lignes,
            'paiements' => $paiements
        ]);
    }

    // ... (Reste des méthodes TypesFrais, Recus, Bourses inchangées ou à adapter si besoin)
    // Pour l'instant on garde le reste comme placeholder ou legacy compatible
    
    /**
     * Gestion des types de frais (Adapter pour TypesFacture ou garder TypesFrais pour références)
     */
    /**
     * Gestion des types de frais (sera géré par un contrôleur dédié ou ici)
     */
    public function typesFrais() {
        require_once APP_PATH . '/Models/TypeFacture.php';
        $model = new TypeFacture();
        $types = $model->all();

        $this->view('finance/types-frais', ['typesFrais' => $types]);
    }

    public function addTypeFrais() {
        require_once APP_PATH . '/Models/TypeFacture.php';
        $model = new TypeFacture();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $libelle = $_POST['libelle'] ?? '';
            $description = $_POST['description'] ?? '';
            
            // Génération d'un code unique basique basé sur le libellé
            $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $libelle));
            $code = substr($code, 0, 20); // Limite de taille probable
            if (empty($code)) {
                $code = 'TYPE_' . time();
            }

            $data = [
                'libelle' => $libelle,
                'description' => $description,
                'code' => $code,
                'actif' => 1
            ];
            
            try {
                $model->create($data);
                $_SESSION['success_message'] = 'Type de frais ajouté avec succès.';
                $this->redirect('finance/types-frais');
            } catch (Exception $e) {
                $_SESSION['error_message'] = "Erreur lors de l'ajout : " . $e->getMessage();
                $this->view('finance/types-frais-add');
            }
        } else {
            $this->view('finance/types-frais-add');
        }
    }

    public function editTypeFrais($id) {
        require_once APP_PATH . '/Models/TypeFacture.php';
        $model = new TypeFacture();
        
        $typeFrais = $model->find($id);
        if (!$typeFrais) {
            $_SESSION['error_message'] = "Type de frais introuvable.";
            $this->redirect('finance/types-frais');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'libelle' => $_POST['libelle'] ?? $typeFrais['libelle'],
                'description' => $_POST['description'] ?? $typeFrais['description']
            ];
            
            try {
                $model->update($id, $data);
                $_SESSION['success_message'] = 'Type de frais modifié avec succès.';
                $this->redirect('finance/types-frais');
            } catch (Exception $e) {
                $_SESSION['error_message'] = "Erreur lors de la modification : " . $e->getMessage();
                $this->view('finance/types-frais-edit', ['typeFrais' => $typeFrais]);
            }
        } else {
            $this->view('finance/types-frais-edit', ['typeFrais' => $typeFrais]);
        }
    }

    public function deleteTypeFrais($id) {
         require_once APP_PATH . '/Models/TypeFacture.php';
         $model = new TypeFacture();
         
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             try {
                $model->delete($id);
                $_SESSION['success_message'] = 'Type de frais supprimé.';
             } catch (Exception $e) {
                $_SESSION['error_message'] = "Erreur lors de la suppression : " . $e->getMessage();
             }
             $this->redirect('finance/types-frais');
         }
    }

    /**
     * Vue détaillée de l'échéancier par élève avec historique paiements
     */
    public function echeancierEleve($eleveId) {
        require_once APP_PATH . '/Models/EcheancierEcolage.php';
        require_once APP_PATH . '/Models/Eleve.php';
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        require_once APP_PATH . '/Models/Paiement.php';

        $echeancierModel = new EcheancierEcolage();
        $eleveModel = new Eleve();
        $anneeModel = new AnneeScolaire();
        $paiementModel = new Paiement();

        // 1. Infos Élève
        $eleve = $eleveModel->queryOne(
            "SELECT e.*, c.nom as classe_nom 
             FROM eleves e
             LEFT JOIN inscriptions i ON e.id = i.eleve_id
             LEFT JOIN classes c ON i.classe_id = c.id
             WHERE e.id = ?
             ORDER BY i.date_inscription DESC LIMIT 1",
            [$eleveId]
        );

        if (!$eleve) {
            $_SESSION['error'] = "Élève introuvable.";
            $this->redirect('finance/liste-ecolage');
            return;
        }

        // 2. Année Scolaire Active
        $annee = $anneeModel->getActive();
        if (!$annee) {
            $_SESSION['error'] = "Aucune année scolaire active.";
            $this->redirect('dashboard');
            return;
        }

        // 3. Échéancier (10 mois)
        // On récupère l'échéancier existant
        $echeancier = $echeancierModel->getEcheancierEleve($eleveId, $annee['id']);

        // Si pas d'échéancier, on pourrait le générer, mais pour l'instant on affiche vide ou erreur
        // Idéalement, l'échéancier est créé à l'inscription.

        // Mise à jour des statuts 'live' pour l'affichage correct (Retard, etc.)
        foreach ($echeancier as &$ech) {
            $echeancierModel->updateStatut($ech['id']);
            // Re-fetch pour avoir le statut à jour si besoin, ou juste faire confiance à la modif en base
            // Pour optimiser, on pourrait ne pas update à chaque vue mais bon.
        }
        // Re-lire l'échéancier fraudriat une nouvelle requête, on suppose que l'affichage gérera les dates si on veut du pur temps réel sans update DB,
        // MAIS la demande implique de voir les statuts "Retard/Exclu" qui sont stockés en base. 
        // Donc on re-charge pour être sûr d'avoir les données fraîches après l'updateStatut.
        $echeancier = $echeancierModel->getEcheancierEleve($eleveId, $annee['id']);


        // 4. Historique des Paiements
        // On cherche tous les paiements liés à cet élève via les factures
        $paiements = $paiementModel->query(
            "SELECT p.*, f.numero_facture, mp.libelle as mode_paiement, lf.designation as designation
             FROM paiements p
             INNER JOIN factures f ON p.facture_id = f.id
             LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
             LEFT JOIN lignes_facture lf ON f.id = lf.facture_id 
             WHERE f.eleve_id = ? AND f.annee_scolaire_id = ?
             GROUP BY p.id
             ORDER BY p.date_paiement DESC",
            [$eleveId, $annee['id']]
        );

        $this->view('finance/echeancier_eleve', [
            'eleve' => $eleve,
            'annee' => $annee,
            'echeancier' => $echeancier,
            'paiements' => $paiements
        ]);
    }

    /**
     * Liste et recherche des reçus de paiement
     */
    public function recus() {
        require_once APP_PATH . '/Models/Paiement.php';
        require_once APP_PATH . '/Models/Facture.php';
        
        $paiementModel = new Paiement();
        $search = $_GET['search'] ?? '';
        $paiementId = $_GET['id'] ?? null;
        
        // Si un ID spécifique est demandé, afficher uniquement ce reçu
        if ($paiementId) {
            $paiement = $paiementModel->queryOne(
                "SELECT p.*, 
                        f.id as facture_id, f.numero_facture, f.description as facture_description,
                        e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom, e.date_inscription,
                        mp.libelle as mode_paiement_libelle,
                        a.libelle as annee_scolaire
                 FROM paiements p
                 LEFT JOIN factures f ON p.facture_id = f.id
                 LEFT JOIN eleves e ON f.eleve_id = e.id
                 LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
                 LEFT JOIN annees_scolaires a ON f.annee_scolaire_id = a.id
                 WHERE p.id = ?",
                [$paiementId]
            );
            
            if (!$paiement) {
                $_SESSION['error'] = "Reçu non trouvé";
                $this->redirect('/finance/recus');
                return;
            }
            
            // Récupérer les lignes de la facture pour le détail
            $lignes = [];
            if (!empty($paiement['facture_id'])) {
                require_once APP_PATH . '/Models/LigneFacture.php';
                $ligneModel = new LigneFacture();
                $lignes = $ligneModel->query(
                    "SELECT * FROM lignes_facture WHERE facture_id = ?", 
                    [$paiement['facture_id']]
                );
            }
            
            // Afficher le reçu unique (pour impression)
            $this->view('finance/recu_detail', [
                'paiement' => $paiement,
                'lignes' => $lignes
            ]);
            return;
        }
        
        // Sinon, afficher la liste des reçus avec recherche
        if (!empty($search)) {
            $paiements = $paiementModel->query(
                "SELECT p.*, 
                        f.numero_facture, f.description as facture_description,
                        e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                        c.code as classe_code,
                        mp.libelle as mode_paiement_libelle,
                        (SELECT GROUP_CONCAT(ee.mois_libelle SEPARATOR ', ') 
                         FROM echeanciers_ecolages ee 
                         WHERE ee.derniere_facture_id = f.id) as mois_ecolage
                 FROM paiements p
                 LEFT JOIN factures f ON p.facture_id = f.id
                 LEFT JOIN eleves e ON f.eleve_id = e.id
                 LEFT JOIN inscriptions i ON (f.eleve_id = i.eleve_id AND f.annee_scolaire_id = i.annee_scolaire_id)
                 LEFT JOIN classes c ON i.classe_id = c.id
                 LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
                 WHERE e.nom LIKE ? 
                    OR e.prenom LIKE ?
                    OR e.matricule LIKE ?
                    OR f.numero_facture LIKE ?
                    OR p.numero_paiement LIKE ?
                    OR c.code LIKE ?
                 ORDER BY p.date_paiement DESC, p.id DESC",
                ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]
            );
        } else {
            $paiements = $paiementModel->query(
                "SELECT p.*, 
                        f.numero_facture, f.description as facture_description,
                        e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                        c.code as classe_code,
                        mp.libelle as mode_paiement_libelle,
                        (SELECT GROUP_CONCAT(ee.mois_libelle SEPARATOR ', ') 
                         FROM echeanciers_ecolages ee 
                         WHERE ee.derniere_facture_id = f.id) as mois_ecolage
                 FROM paiements p
                 LEFT JOIN factures f ON p.facture_id = f.id
                 LEFT JOIN eleves e ON f.eleve_id = e.id
                 LEFT JOIN inscriptions i ON (f.eleve_id = i.eleve_id AND f.annee_scolaire_id = i.annee_scolaire_id)
                 LEFT JOIN classes c ON i.classe_id = c.id
                 LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
                 ORDER BY p.date_paiement DESC, p.id DESC
                 LIMIT 100"
            );
        }
        
        $this->view('finance/recus', [
            'paiements' => $paiements,
            'search' => $search
        ]);
    }

    /**
     * Exporte la liste des reçus en Excel (CSV)
     */
    public function exportRecusExcel() {
        if (!hasPermission('finance_recus_export')) {
            $_SESSION['error'] = "Vous n'avez pas la permission d'exporter les reçus.";
            $this->redirect('finance/recus');
            return;
        }

        require_once APP_PATH . '/Models/Paiement.php';
        $paiementModel = new Paiement();
        $search = $_GET['search'] ?? '';

        if (!empty($search)) {
            $paiements = $paiementModel->query(
                "SELECT p.*, 
                        f.numero_facture,
                        e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                        c.code as classe_code,
                        mp.libelle as mode_paiement_libelle,
                        (SELECT GROUP_CONCAT(ee.mois_libelle SEPARATOR ', ') 
                         FROM echeanciers_ecolages ee 
                         WHERE ee.derniere_facture_id = f.id) as mois_ecolage
                 FROM paiements p
                 LEFT JOIN factures f ON p.facture_id = f.id
                 LEFT JOIN eleves e ON f.eleve_id = e.id
                 LEFT JOIN inscriptions i ON (f.eleve_id = i.eleve_id AND f.annee_scolaire_id = i.annee_scolaire_id)
                 LEFT JOIN classes c ON i.classe_id = c.id
                 LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
                 WHERE e.nom LIKE ? 
                    OR e.prenom LIKE ?
                    OR e.matricule LIKE ?
                    OR f.numero_facture LIKE ?
                    OR p.numero_paiement LIKE ?
                    OR c.code LIKE ?
                 ORDER BY p.date_paiement DESC, p.id DESC",
                ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]
            );
        } else {
            $paiements = $paiementModel->query(
                "SELECT p.*, 
                        f.numero_facture,
                        e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                        c.code as classe_code,
                        mp.libelle as mode_paiement_libelle,
                        (SELECT GROUP_CONCAT(ee.mois_libelle SEPARATOR ', ') 
                         FROM echeanciers_ecolages ee 
                         WHERE ee.derniere_facture_id = f.id) as mois_ecolage
                 FROM paiements p
                 LEFT JOIN factures f ON p.facture_id = f.id
                 LEFT JOIN eleves e ON f.eleve_id = e.id
                 LEFT JOIN inscriptions i ON (f.eleve_id = i.eleve_id AND f.annee_scolaire_id = i.annee_scolaire_id)
                 LEFT JOIN classes c ON i.classe_id = c.id
                 LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
                 ORDER BY p.date_paiement DESC, p.id DESC"
            );
        }

        $filename = "recus_paiement_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel
        
        fputcsv($output, ['Date', 'Matricule', 'Élève', 'Classe', 'Mois Écolage', 'N° Facture', 'Mode', 'Montant'], ';');
        
        foreach ($paiements as $p) {
            $mois = !empty($p['mois_ecolage']) ? explode(', ', $p['mois_ecolage']) : [null];
            $nombreMois = count($mois);
            $montantParMois = $nombreMois > 0 ? $p['montant'] / $nombreMois : $p['montant'];
            
            foreach ($mois as $index => $m) {
                fputcsv($output, [
                    date('d/m/Y', strtotime($p['date_paiement'])),
                    $p['matricule'],
                    $p['eleve_nom'] . ' ' . $p['eleve_prenom'],
                    $p['classe_code'] ?? 'N/A',
                    $m ?? '-',
                    $p['numero_facture'],
                    $p['mode_paiement_libelle'],
                    number_format($montantParMois, 0, ',', ' ')
                ], ';');
            }
        }
        
        fclose($output);
        exit;
    }
    
    // Placeholder pour la suppression, adapter Facture
    public function delete($id) {
         require_once APP_PATH . '/Models/Facture.php';
         $factureModel = new Facture();
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $factureModel->delete($id);
            $_SESSION['success_message'] = 'Facture supprimée.';
            $this->redirect('finance/list');
         }
    }

    /**
     * Exporte un reçu en PDF
     */
    public function exportRecuPdf($id) {
        require_once APP_PATH . '/Models/Paiement.php';
        $paiementModel = new Paiement();
        
        $paiement = $paiementModel->queryOne(
            "SELECT p.*, 
                    f.id as facture_id, f.numero_facture, f.description as facture_description,
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom, e.date_inscription,
                    mp.libelle as mode_paiement_libelle,
                    a.libelle as annee_scolaire
             FROM paiements p
             LEFT JOIN factures f ON p.facture_id = f.id
             LEFT JOIN eleves e ON f.eleve_id = e.id
             LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
             LEFT JOIN annees_scolaires a ON f.annee_scolaire_id = a.id
             WHERE p.id = ?",
            [$id]
        );
        
        if (!$paiement) {
            $_SESSION['error'] = "Reçu non trouvé";
            $this->redirect('finance/recus');
            return;
        }

        // Récupérer les lignes
        require_once APP_PATH . '/Models/LigneFacture.php';
        $ligneModel = new LigneFacture();
        $lignes = $ligneModel->query(
            "SELECT * FROM lignes_facture WHERE facture_id = ?", 
            [$paiement['facture_id'] ?? 0]
        );

        $html = $this->renderRecuHtml($paiement, $lignes);
        
        require_once APP_PATH . '/Services/PdfService.php';
        $pdfService = new PdfService();
        $pdfService->generateRecu($html, "Recu_Paiement_{$paiement['numero_paiement']}.pdf");
    }

    /**
     * Rendu HTML du reçu pour le PDF
     */
    private function renderRecuHtml($paiement, $lignes) {
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
                .container { padding: 40px; border: 1px solid #e5e7eb; border-radius: 8px; }
                .header { text-align: center; border-bottom: 2px solid #059669; padding-bottom: 20px; margin-bottom: 30px; }
                .school-name { font-size: 18pt; font-weight: bold; color: #059669; text-transform: uppercase; }
                .document-title { font-size: 16pt; font-weight: bold; margin: 20px 0; color: #374151; }
                
                .info-table { width: 100%; margin-bottom: 30px; border: none; }
                .info-box { background: #f9fafb; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb; }
                
                table.details-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
                table.details-table th { background: #059669; color: white; padding: 12px 10px; text-align: left; font-size: 9pt; text-transform: uppercase; }
                table.details-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 10pt; }
                .right { text-align: right; }
                
                .total-section { margin-top: 20px; border-top: 2px solid #059669; padding-top: 10px; }
                .total-amount { font-size: 18pt; font-weight: bold; color: #059669; }
                .in-words { margin-top: 20px; padding: 15px; background: #ecfdf5; border-left: 5px solid #059669; font-style: italic; font-size: 9pt; }
                
                .signature-section { margin-top: 50px; text-align: right; }
                .signature-box { display: inline-block; width: 200px; text-align: center; }
                .signature-line { height: 60px; border-bottom: 1px solid #374151; margin-bottom: 10px; }
                
                .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 8pt; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 10px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="school-name">École ROSSIGNOLES</div>
                    <div style="font-size: 9pt; color: #6b7280;">Enseignement Général - Maternelle, Primaire, Secondaire</div>
                    <div style="font-size: 9pt; color: #6b7280;">Antananarivo, Madagascar</div>
                </div>

                <div class="document-title" style="text-align: center;">REÇU DE PAIEMENT</div>

                <table class="info-table">
                    <tr>
                        <td width="60%">
                            <div class="info-box">
                                <strong>Élève :</strong> <span style="font-size: 11pt;"><?= htmlspecialchars(($paiement['eleve_nom'] ?? '') . ' ' . ($paiement['eleve_prenom'] ?? '')) ?></span><br>
                                <strong>Matricule :</strong> <?= htmlspecialchars($paiement['matricule'] ?? '-') ?><br>
                                <strong>Année scolaire :</strong> <?= htmlspecialchars($paiement['annee_scolaire'] ?? 'N/A') ?>
                            </div>
                        </td>
                        <td width="40%" style="text-align: right; vertical-align: top;">
                            <strong>N° Reçu :</strong> <span style="color: #059669; font-weight: bold;"><?= htmlspecialchars($paiement['numero_paiement'] ?? 'PAY-'.$paiement['id']) ?></span><br>
                            <strong>Date :</strong> <?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?><br>
                            <strong>Mode :</strong> <?= htmlspecialchars($paiement['mode_paiement_libelle'] ?? 'Espèces') ?>
                        </td>
                    </tr>
                </table>

                <table class="details-table">
                    <thead>
                        <tr>
                            <th width="75%">Désignation / Description</th>
                            <th width="25%" class="right">Montant (Ar)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($lignes)): ?>
                            <?php foreach ($lignes as $ligne): ?>
                            <tr>
                                <td><?= htmlspecialchars($ligne['designation'] ?? $ligne['libelle']) ?></td>
                                <td class="right"><?= number_format($ligne['montant'], 0, ',', ' ') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td><?= htmlspecialchars($paiement['facture_description'] ?? 'Paiement scolarité') ?></td>
                                <td class="right"><?= number_format($paiement['montant'], 0, ',', ' ') ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="right" style="font-weight: bold;">TOTAL PAYÉ</td>
                            <td class="right total-amount"><?= number_format($paiement['montant'], 0, ',', ' ') ?> Ar</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="in-words">
                    Somme arrêtée à la somme de : <strong><?= numberToWords($paiement['montant']) ?> Ariary</strong>
                </div>

                <div class="signature-section">
                    <div class="signature-box">
                        <div style="font-weight: bold; text-decoration: underline;">La Caisse / Responsable</div>
                        <div class="signature-line"></div>
                        <div style="font-size: 8pt; color: #6b7280;">Fait à Antananarivo, le <?= date('d/m/Y') ?></div>
                    </div>
                </div>

                <div class="footer">
                    École ROSSIGNOLES - Reçu généré par le système ERP le <?= date('d/m/Y à H:i') ?>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Échéancier de recouvrement (Impayés après date limite)
     */
    public function echeanciers() {
        // Mettre à jour les statuts d'abord pour être sûr
        require_once APP_PATH . '/Models/EcheancierEcolage.php';
        $echeancierModel = new EcheancierEcolage();
        
        // On récupère toutes les échéances non soldées qui ne sont pas exonérées
        // On inclut explicitement les statuts NULL ou vides
        $echeancesAChecker = $echeancierModel->query(
            "SELECT id FROM echeanciers_ecolages 
             WHERE (statut NOT IN ('paye', 'exonere') OR statut IS NULL OR statut = '') 
             AND montant_restant > 0"
        );
        
        foreach ($echeancesAChecker as $ech) {
             $echeancierModel->updateStatut($ech['id']);
        }

        $statutFilter = $_GET['statut'] ?? 'retard'; // Par défaut, on montre les retards (Recouvrement)
        
        // Si on accède via retard_10, on le traite comme retard (Recouvrement)
        if ($statutFilter === 'retard_10') {
            $statutFilter = 'retard';
        }
        
        if ($statutFilter === 'exclusion') {
            $pageTitle = "Liste des exclus";
            $pageSubtitle = "Élèves suspendus pour défaut de paiement";
            $alertMessage = "Ces élèves sont automatiquement marqués comme 'Inactifs' dans le système. Ils ne peuvent plus être marqués présents en classe.";
            $alertClass = "bg-red-50 border-red-400 text-red-700";
            $iconClass = "fa-user-slash text-red-600";
        } else {
            // Recouvrement
            $pageTitle = "Recouvrement";
            $pageSubtitle = "Suivi des impayés et recouvrement";
            $alertMessage = "Les élèves dans cette liste sont en période de recouvrement. Ils ont dépassé la date limite du 10 du mois M mais ne sont pas encore suspendus.";
            $alertClass = "bg-orange-50 border-orange-400 text-orange-700";
            $iconClass = "fa-hand-holding-usd text-orange-600";
            $statutFilter = 'retard'; // 'retard' dans le service couvre maintenant 'impayee' + 'retard'
        }

        $echeances = $this->financeService->getEcheancierRecouvrement(null, $statutFilter);
        
        $this->view('finance/echeanciers', [
            'echeances' => $echeances,
            'statutFilter' => $statutFilter,
            'pageTitle' => $pageTitle,
            'pageSubtitle' => $pageSubtitle,
            'alertMessage' => $alertMessage,
            'alertClass' => $alertClass,
            'iconClass' => $iconClass
        ]);
    }

    /**
     * Envoie un SMS de relance individuel
     */
    public function envoyerSmsRelance($id) {
        // Permission check (facultatif si finance_view suffit)
        // $this->requirePermission('finance_sms');
        
        require_once APP_PATH . '/Models/EcheancierEcolage.php';
        require_once APP_PATH . '/Models/BaseModel.php';
        require_once APP_PATH . '/Services/SmsService.php';
        
        $echeancierModel = new EcheancierEcolage();
        $echeance = $echeancierModel->findById($id);
        
        if (!$echeance) {
            $_SESSION['error_message'] = "Échéance introuvable.";
            $this->redirect('finance/echeanciers');
            return;
        }
        
        $db = BaseModel::getDBConnection();
        $eleve = $db->query("
            SELECT el.*, 
                   (SELECT p.telephone 
                    FROM eleves_parents ep 
                    INNER JOIN parents p ON ep.parent_id = p.id 
                    WHERE ep.eleve_id = el.id 
                    LIMIT 1) as parent_telephone 
            FROM eleves el 
            WHERE el.id = " . (int)$echeance['eleve_id']
        )->fetch();
        
        if (!$eleve || empty($eleve['parent_telephone'])) {
            $_SESSION['error_message'] = "Numéro de téléphone du parent introuvable.";
            $this->redirect('finance/echeanciers');
            return;
        }
        
        $smsService = new SmsService();
        $message = "ROSSIGNOLES: Rappel paiement écolage {$echeance['mois_libelle']} pour l'élève {$eleve['nom']}. Reste à payer: " . number_format($echeance['montant_restant'], 0, ',', ' ') . " Ar. Merci de régulariser.";
        
        if ($smsService->send($eleve['parent_telephone'], $message)) {
            $_SESSION['success_message'] = "SMS envoyé avec succès au parent ({$eleve['parent_telephone']}).";
        } else {
            $_SESSION['error_message'] = "Erreur lors de l'envoi du SMS.";
        }
        
        $this->redirect('finance/echeanciers');
    }

    /**
     * Envoie un SMS de relance à tous les élèves de la liste actuelle
     */
    public function envoyerSmsRelanceTous() {
        $statut = $_POST['statut'] ?? 'retard';
        $echeances = $this->financeService->getEcheancierRecouvrement(null, $statut);
        
        if (empty($echeances)) {
            $_SESSION['error_message'] = "Aucun élève dans la liste à relancer.";
            $this->redirect('finance/echeanciers?statut=' . $statut);
            return;
        }
        
        require_once APP_PATH . '/Services/SmsService.php';
        $smsService = new SmsService();
        $count = 0;
        
        foreach ($echeances as $e) {
            if (!empty($e['parent_telephone'])) {
                $message = "ROSSIGNOLES: Rappel paiement écolage {$e['mois_libelle']} pour l'élève {$e['eleve_nom']}. Reste: " . number_format($e['montant_restant'], 0, ',', ' ') . " Ar. Merci de régulariser.";
                if ($smsService->send($e['parent_telephone'], $message)) {
                    $count++;
                }
            }
        }
        
        $_SESSION['success_message'] = "$count SMS(s) envoyé(s) avec succès.";
        $this->redirect('finance/echeanciers?statut=' . $statut);
    }
}


