<?php
/**
 * Contrôleur PaiementMensuelController
 * Gère la saisie des paiements mensuels d'écolage
 */

require_once __DIR__ . '/../Models/Eleve.php';
require_once __DIR__ . '/../Models/Classe.php';
require_once __DIR__ . '/../Models/AnneeScolaire.php';
require_once __DIR__ . '/../Models/EcheancierEcolage.php';
require_once __DIR__ . '/../Models/ModePaiement.php';
require_once __DIR__ . '/../Models/Paiement.php';
require_once __DIR__ . '/../Models/Facture.php';
require_once __DIR__ . '/../Services/EcheancierService.php';

class PaiementMensuelController {
    
    private $eleveModel;
    private $classeModel;
    private $anneeScolaireModel;
    private $echeancierModel;
    private $modePaiementModel;
    private $paiementModel;
    private $factureModel;
    private $echeancierService;
    
    public function __construct() {
        $this->eleveModel = new Eleve();
        $this->classeModel = new Classe();
        $this->anneeScolaireModel = new AnneeScolaire();
        $this->echeancierModel = new EcheancierEcolage();
        $this->modePaiementModel = new ModePaiement();
        $this->paiementModel = new Paiement();
        $this->factureModel = new Facture();
        $this->echeancierService = new EcheancierService();
    }
    
    /**
     * Page de recherche d'élève
     */
    public function index() {
        // Récupérer les classes groupées par cycle et années scolaires
        $classes = $this->classeModel->query("
            SELECT c.id, c.nom, c.code, cy.libelle as cycle_nom 
            FROM classes c 
            JOIN niveaux n ON c.niveau_id = n.id
            JOIN cycles cy ON n.cycle_id = cy.id
            WHERE c.statut = 'actif' AND c.deleted_at IS NULL
            ORDER BY cy.ordre ASC, cy.libelle ASC, n.ordre ASC, c.nom ASC
        ");
        $anneesScolaires = $this->anneeScolaireModel->getAll();
        
        $eleves = null;
        
        // Si recherche
        if (isset($_GET['search']) || isset($_GET['classe_id'])) {
            $search = $_GET['search'] ?? '';
            $classeId = $_GET['classe_id'] ?? null;
            $anneeScolaireId = $_GET['annee_scolaire_id'] ?? $this->anneeScolaireModel->getActive()['id'];
            
            // Construire la requête
            $sql = "SELECT DISTINCT
                        e.id,
                        e.matricule,
                        e.nom,
                        e.prenom,
                        c.nom as classe_nom,
                        COUNT(CASE WHEN ee.montant_restant > 0 THEN 1 END) as nb_impayees,
                        SUM(CASE WHEN ee.montant_restant > 0 THEN ee.montant_restant ELSE 0 END) as total_impaye
                    FROM eleves e
                    INNER JOIN inscriptions i ON e.id = i.eleve_id
                    INNER JOIN classes c ON i.classe_id = c.id
                    LEFT JOIN echeanciers_ecolages ee ON (e.id = ee.eleve_id AND i.annee_scolaire_id = ee.annee_scolaire_id)
                    WHERE i.annee_scolaire_id = ? AND i.statut = 'validee'";
            
            $params = [$anneeScolaireId];
            
            if ($search) {
                $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)";
                $searchParam = "%{$search}%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            if ($classeId) {
                $sql .= " AND c.id = ?";
                $params[] = $classeId;
            }
            
            $sql .= " GROUP BY e.id, e.matricule, e.nom, e.prenom, c.nom
                      ORDER BY e.nom ASC, e.prenom ASC";
            
            $eleves = $this->eleveModel->query($sql, $params);
        }
        
        require_once __DIR__ . '/../Views/finance/paiement_mensuel.php';
    }
    
    /**
     * Formulaire de saisie de paiement pour un élève
     */
    public function saisir() {
        $eleveId = $_GET['eleve_id'] ?? null;
        $anneeScolaireId = $_GET['annee_scolaire_id'] ?? null;
        
        if (!$eleveId || !$anneeScolaireId) {
            $_SESSION['error'] = "Paramètres manquants";
            header('Location: /ROSSIGNOLES/finance/paiement-mensuel');
            exit;
        }
        
        // Récupérer l'élève
        $eleve = $this->eleveModel->queryOne(
            "SELECT e.*, c.nom as classe_nom
             FROM eleves e
             INNER JOIN inscriptions i ON e.id = i.eleve_id
             INNER JOIN classes c ON i.classe_id = c.id
             WHERE e.id = ? AND i.annee_scolaire_id = ? AND i.statut = 'validee'
             LIMIT 1",
            [$eleveId, $anneeScolaireId]
        );
        
        if (!$eleve) {
            $_SESSION['error'] = "Élève non trouvé ou inscription non validée";
            header('Location: /ROSSIGNOLES/finance/paiement-mensuel');
            exit;
        }
        
        // Récupérer l'année scolaire
        $anneeScolaire = $this->anneeScolaireModel->findById($anneeScolaireId);
        
        // Récupérer les échéances
        $echeances = $this->echeancierModel->getEcheancierEleve($eleveId, $anneeScolaireId);
        
        // Mettre à jour les statuts
        foreach ($echeances as &$echeance) {
            $this->echeancierModel->updateStatut($echeance['id']);
        }
        
        // Récupérer à nouveau après mise à jour
        $echeances = $this->echeancierModel->getEcheancierEleve($eleveId, $anneeScolaireId);
        
        // Récupérer les modes de paiement
        $modesPaiement = $this->modePaiementModel->getAll();
        
        require_once __DIR__ . '/../Views/finance/paiement_mensuel_saisir.php';
    }
    
    /**
     * Enregistrement du paiement
     */
    public function enregistrer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /ROSSIGNOLES/finance/paiement-mensuel');
            exit;
        }
        
        try {
            $eleveId = $_POST['eleve_id'] ?? null;
            $anneeScolaireId = $_POST['annee_scolaire_id'] ?? null;
            $echeancesIds = $_POST['echeances'] ?? [];
            $montantPaye = $_POST['montant_paye'] ?? 0;
            $modePaiementId = $_POST['mode_paiement_id'] ?? null;
            $datePaiement = $_POST['date_paiement'] ?? date('Y-m-d');
            $reference = $_POST['reference'] ?? null;
            $remarque = $_POST['remarque'] ?? null;
            
            if (!$eleveId || !$anneeScolaireId || empty($echeancesIds) || !$montantPaye) {
                throw new Exception("Données incomplètes");
            }
            
            // Démarrer une transaction
            $this->echeancierModel->beginTransaction();
            
            // Créer une facture pour ce paiement
            require_once __DIR__ . '/../Models/TypeFacture.php';
            $typeFactureModel = new TypeFacture();
            $typeFacture = $typeFactureModel->queryOne("SELECT id FROM types_facture WHERE code = 'ECOLAGE' LIMIT 1");
            
            if (!$typeFacture) {
                $typeFactureId = $typeFactureModel->create([
                    'code' => 'ECOLAGE',
                    'libelle' => 'Écolage mensuel',
                    'description' => 'Paiement des frais de scolarité mensuels',
                    'prefixe_numero' => 'Eco',
                    'actif' => 1
                ]);
            } else {
                $typeFactureId = $typeFacture['id'];
            }
            
            // Calculer le montant total des échéances
            $montantTotal = 0;
            foreach ($echeancesIds as $echeanceId) {
                $echeance = $this->echeancierModel->findById($echeanceId);
                if ($echeance) {
                    $montantTotal += $echeance['montant_restant'];
                }
            }
            
            // Créer la facture
            $factureId = $this->factureModel->create([
                'numero_facture' => $this->factureModel->generateNextNumber('numero_facture', 'Eco'),
                'eleve_id' => $eleveId,
                'annee_scolaire_id' => $anneeScolaireId,
                'type_facture_id' => $typeFactureId,
                'date_facture' => $datePaiement,
                'montant_total' => $montantTotal,
                'montant_paye' => min($montantPaye, $montantTotal),
                'montant_restant' => max(0, $montantTotal - $montantPaye),
                'statut' => $montantPaye >= $montantTotal ? 'payee' : 'partiellement_payee',
                'description' => 'Paiement écolage - ' . count($echeancesIds) . ' mois'
            ]);
            
            // Enregistrer le paiement
            $paiementId = $this->paiementModel->create([
                'numero_paiement' => $this->paiementModel->generateNextNumber('numero_paiement', 'Pay'),
                'facture_id' => $factureId,
                'date_paiement' => $datePaiement,
                'montant' => $montantPaye,
                'mode_paiement_id' => $modePaiementId,
                'reference_paiement' => $reference,
                'remarque' => $remarque
            ]);
            
            // Répartir le paiement sur les échéances
            $montantRestant = $montantPaye;
            
            foreach ($echeancesIds as $echeanceId) {
                if ($montantRestant <= 0) break;
                
                $echeance = $this->echeancierModel->findById($echeanceId);
                if (!$echeance) continue;
                
                $montantAAppliquer = min($montantRestant, $echeance['montant_restant']);
                
                $nouveauMontantPaye = $echeance['montant_paye'] + $montantAAppliquer;
                $nouveauMontantRestant = $echeance['montant_du'] - $nouveauMontantPaye;
                
                $updateData = [
                    'montant_paye' => $nouveauMontantPaye,
                    'montant_restant' => $nouveauMontantRestant,
                    'nombre_paiements' => $echeance['nombre_paiements'] + 1,
                    'derniere_facture_id' => $factureId
                ];
                
                if ($nouveauMontantRestant <= 0) {
                    $updateData['statut'] = 'paye';
                    $updateData['date_paiement_complet'] = $datePaiement;
                    $updateData['jours_retard'] = 0;
                }
                
                $this->echeancierModel->update($echeanceId, $updateData);
                
                $montantRestant -= $montantAAppliquer;
            }
            
            // Vérifier si l'élève peut être débloqué
            require_once __DIR__ . '/../Services/EcolageService.php';
            $ecolageService = new EcolageService();
            
            $inscription = $this->eleveModel->queryOne(
                "SELECT i.id FROM inscriptions i WHERE i.eleve_id = ? AND i.annee_scolaire_id = ? LIMIT 1",
                [$eleveId, $anneeScolaireId]
            );
            
            if ($inscription) {
                $ecolageService->debloquerEleve($eleveId, $inscription['id']);
            }
            
            $this->echeancierModel->commit();
            
            $_SESSION['success'] = "Paiement enregistré avec succès";
            header('Location: /ROSSIGNOLES/finance/recus?paiement_id=' . $paiementId);
            exit;
            
        } catch (Exception $e) {
            $this->echeancierModel->rollback();
            $_SESSION['error'] = "Erreur lors de l'enregistrement : " . $e->getMessage();
            header('Location: /ROSSIGNOLES/finance/paiement-mensuel');
            exit;
        }
    }
    /**
     * Génère l'échéancier pour un élève (Action forcée)
     */
    public function generer() {
        $eleveId = $_GET['eleve_id'] ?? null;
        $anneeScolaireId = $_GET['annee_scolaire_id'] ?? null;
        
        if (!$eleveId || !$anneeScolaireId) {
            $_SESSION['error'] = "Paramètres manquants";
            header('Location: /ROSSIGNOLES/finance/paiement-mensuel');
            exit;
        }

        try {
            // Récupérer l'inscription pour trouver l'ID
            $inscription = $this->eleveModel->queryOne(
                "SELECT id FROM inscriptions WHERE eleve_id = ? AND annee_scolaire_id = ? AND statut = 'validee' LIMIT 1",
                [$eleveId, $anneeScolaireId]
            );

            if (!$inscription) {
                throw new Exception("Inscription validée introuvable pour cet élève.");
            }

            $result = $this->echeancierService->genererEcheancierInscription($inscription['id'], $_SESSION['user_id'] ?? null);

            if ($result['success']) {
                $_SESSION['success'] = "Échéancier généré avec succès.";
            } else {
                $_SESSION['error'] = "Erreur : " . $result['message'];
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de la génération : " . $e->getMessage();
        }

        header("Location: /ROSSIGNOLES/finance/paiement-mensuel/saisir?eleve_id=$eleveId&annee_scolaire_id=$anneeScolaireId");
        exit;
    }
}
