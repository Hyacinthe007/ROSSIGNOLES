<?php
/**
 * Contrôleur Pédagogie
 * Gère les niveaux, cycles, séries, enseignements et emplois du temps
 */

require_once __DIR__ . '/BaseController.php';

class PedagogieController extends BaseController {
    
    public function __construct() {
        $this->requireAuth();
    }
    
    /**
     * Liste des niveaux
     */
    public function niveaux() {
        require_once APP_PATH . '/Models/Niveau.php';
        $model = new Niveau();
        
        try {
            $niveaux = $model->getAllWithCycle();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des niveaux: " . $e->getMessage());
            $niveaux = [];
        }
        
        $this->view('pedagogie/niveaux', ['niveaux' => $niveaux]);
    }
    
    /**
     * Liste des cycles
     */
    public function cycles() {
        require_once APP_PATH . '/Models/Cycle.php';
        $model = new Cycle();
        
        try {
            $cycles = $model->getAllActifs();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des cycles: " . $e->getMessage());
            $cycles = [];
        }
        
        $this->view('pedagogie/cycles', ['cycles' => $cycles]);
    }
    
    /**
     * Liste des séries avec leurs niveaux associés
     */
    public function series() {
        require_once APP_PATH . '/Models/Serie.php';
        require_once APP_PATH . '/Models/Niveau.php';
        
        $serieModel = new Serie();
        $niveauModel = new Niveau();
        
        try {
            // Récupération des séries avec leurs niveaux associés
            $series = $serieModel->getAllWithNiveau();
            
            // Récupérer les niveaux pour le formulaire d'ajout/modification
            $niveaux = $niveauModel->all(['actif' => 1], 'ordre ASC');
            
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des séries: " . $e->getMessage());
            $series = [];
            $niveaux = [];
        }
        
        $this->view('pedagogie/series', [
            'series' => $series,
            'niveaux' => $niveaux
        ]);
    }
    
    /**
     * Liste des cours (Associations Enseignants-Classes-Matières)
     */
    public function enseignements() {
        require_once APP_PATH . '/Models/EnseignantsClasses.php';
        $model = new EnseignantsClasses();
        
        try {
            // Utilisation du modèle EnseignantsClasses
            $enseignements = $model->getAllAssignments();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des enseignements: " . $e->getMessage());
            $enseignements = [];
        }
        
        $this->view('pedagogie/enseignements', ['enseignements' => $enseignements]);
    }

    /**
     * Ajouter une attribution d'enseignement
     */
    public function addEnseignement() {
        require_once APP_PATH . '/Models/EnseignantsClasses.php';
        require_once APP_PATH . '/Models/Classe.php';
        require_once APP_PATH . '/Models/Matiere.php';
        require_once APP_PATH . '/Models/Personnel.php';
        require_once APP_PATH . '/Models/AnneeScolaire.php';

        $ecModel = new EnseignantsClasses();
        $classeModel = new Classe();
        $matiereModel = new Matiere();
        $personnelModel = new Personnel();
        $anneeModel = new AnneeScolaire();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'classe_id' => $_POST['classe_id'],
                    'matiere_id' => $_POST['matiere_id'],
                    'personnel_id' => $_POST['personnel_id'],
                    'annee_scolaire_id' => $_POST['annee_scolaire_id'],
                    'volume_horaire' => !empty($_POST['volume_horaire']) ? $_POST['volume_horaire'] : null,
                    'actif' => isset($_POST['actif']) ? 1 : 0
                ];

                $ecModel->create($data);
                
                $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
                $this->redirect('pedagogie/enseignements' . $iframeParam);

            } catch (PDOException $e) {
                error_log("Erreur addEnseignement: " . $e->getMessage());
                die("Erreur lors de l'enregistrement de l'attribution : " . $e->getMessage());
            }
        }

        // Chargement des données pour le formulaire
        $anneeActive = $anneeModel->getActive();
        $classes = $classeModel->query("SELECT * FROM classes WHERE statut = 'actif' AND deleted_at IS NULL ORDER BY nom ASC");
        $matieres = $matiereModel->all(['actif' => 1], 'nom ASC');
        $enseignants = $personnelModel->query("SELECT * FROM personnels WHERE type_personnel = 'enseignant' AND statut = 'actif' ORDER BY nom ASC");

        $this->view('pedagogie/enseignements_form', [
            'anneeActive' => $anneeActive,
            'classes' => $classes,
            'matieres' => $matieres,
            'enseignants' => $enseignants
        ]);
    }

    /**
     * Modifier une attribution d'enseignement
     */
    public function editEnseignement($id) {
        require_once APP_PATH . '/Models/EnseignantsClasses.php';
        require_once APP_PATH . '/Models/Classe.php';
        require_once APP_PATH . '/Models/Matiere.php';
        require_once APP_PATH . '/Models/Personnel.php';
        require_once APP_PATH . '/Models/AnneeScolaire.php';

        $ecModel = new EnseignantsClasses();
        $classeModel = new Classe();
        $matiereModel = new Matiere();
        $personnelModel = new Personnel();
        $anneeModel = new AnneeScolaire();

        $enseignement = $ecModel->find($id);
        if (!$enseignement) die("Attribution non trouvée");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'classe_id' => $_POST['classe_id'],
                    'matiere_id' => $_POST['matiere_id'],
                    'personnel_id' => $_POST['personnel_id'],
                    'annee_scolaire_id' => $_POST['annee_scolaire_id'],
                    'volume_horaire' => !empty($_POST['volume_horaire']) ? $_POST['volume_horaire'] : null,
                    'actif' => isset($_POST['actif']) ? 1 : 0
                ];

                $ecModel->update($id, $data);
                
                $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
                $this->redirect('pedagogie/enseignements' . $iframeParam);

            } catch (PDOException $e) {
                error_log("Erreur editEnseignement: " . $e->getMessage());
                die("Erreur lors de la mise à jour de l'attribution : " . $e->getMessage());
            }
        }

        // Chargement des données pour le formulaire
        $anneeActive = $anneeModel->getActive();
        $classes = $classeModel->query("SELECT * FROM classes WHERE statut = 'actif' AND deleted_at IS NULL ORDER BY nom ASC");
        $matieres = $matiereModel->all(['actif' => 1], 'nom ASC');
        $enseignants = $personnelModel->query("SELECT * FROM personnels WHERE type_personnel = 'enseignant' AND statut = 'actif' ORDER BY nom ASC");

        $this->view('pedagogie/enseignements_form', [
            'enseignement' => $enseignement,
            'anneeActive' => $anneeActive,
            'classes' => $classes,
            'matieres' => $matieres,
            'enseignants' => $enseignants
        ]);
    }

    /**
     * Supprimer une attribution d'enseignement
     */
    public function deleteEnseignement($id) {
        require_once APP_PATH . '/Models/EnseignantsClasses.php';
        $ecModel = new EnseignantsClasses();
        
        try {
            $ecModel->delete($id);
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
            $this->redirect('pedagogie/enseignements' . $iframeParam);
        } catch (PDOException $e) {
            error_log("Erreur deleteEnseignement: " . $e->getMessage());
            die("Erreur lors de la suppression");
        }
    }
    
    /**
     * Emplois du temps
     */
    public function emploisTemps() {
        require_once APP_PATH . '/Models/EmploisTemps.php';
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        
        $model = new EmploisTemps();
        $anneeModel = new AnneeScolaire();
        
        $classeId = $_GET['classe_id'] ?? null;
        $personnelId = $_GET['personnel_id'] ?? null;
        
        // Gestion de la semaine sélectionnée (format 'YYYY-Www')
        $selectedWeek = $_GET['semaine'] ?? date('Y-\WW'); 
        $timestamp = strtotime($selectedWeek);
        if (!$timestamp) {
            $selectedWeek = date('Y-\WW');
            $timestamp = strtotime($selectedWeek);
        }
        
        // Calcul des dates de début et fin de semaine
        $monday = date('Y-m-d', strtotime('monday this week', $timestamp));
        $friday = date('Y-m-d', strtotime('friday this week', $timestamp));
        $saturday = date('Y-m-d', strtotime('saturday this week', $timestamp));
        $weekLabel = "Semaine du " . date('d', strtotime($monday)) . " au " . date('d F Y', strtotime($friday));
        
        // Traduction des mois en français si possible
        $months_en = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $months_fr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
        $weekLabel = str_ireplace($months_en, $months_fr, $weekLabel);

        // Dates journalières pour vérification vacances
        $weekDates = [];
        for ($i = 0; $i < 6; $i++) {
            $dayName = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'][$i];
            $weekDates[$dayName] = date('Y-m-d', strtotime("+$i days", strtotime($monday)));
        }

        // Récupérer l'année scolaire active pour filtrer
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;
        
        // Récupération des filtres
        $classes = [];
        $enseignants = [];
        try {
            $classes = $model->query("
                SELECT c.id, c.code as libelle, cy.libelle as cycle_nom 
                FROM classes c 
                JOIN niveaux n ON c.niveau_id = n.id
                JOIN cycles cy ON n.cycle_id = cy.id
                WHERE c.statut = 'actif' AND c.deleted_at IS NULL 
                ORDER BY cy.ordre ASC, cy.libelle ASC, c.code ASC
            ");
            
            $enseignants = $model->query("SELECT id, nom, prenom FROM personnels WHERE type_personnel = 'enseignant' AND statut = 'actif' ORDER BY nom ASC, prenom ASC");
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des filtres: " . $e->getMessage());
        }
        
        $emploisTemps = [];
        $vacances = [];
        if ($anneeId) {
            try {
                // Dans le système actuel, l'emploi du temps est fixe par semaine type.
                // On peut le filtrer plus tard par date si une table de cours spécifiques existe.
                $emploisTemps = $model->getEmploiTempsMatriciel($anneeId, $classeId, $personnelId);

                // Récupération des vacances/fériés pour cette semaine
                require_once APP_PATH . '/Models/CalendrierScolaire.php';
                $calModel = new CalendrierScolaire();
                $allVacances = $calModel->query("SELECT * FROM calendrier_scolaire WHERE annee_scolaire_id = ? AND bloque_cours = 1 AND (date_debut <= ? AND date_fin >= ?)", [$anneeId, $saturday, $monday]);
                
                // Mapper par jour
                foreach ($weekDates as $dayName => $date) {
                    foreach ($allVacances as $v) {
                        if ($date >= $v['date_debut'] && $date <= $v['date_fin']) {
                            $vacances[$dayName] = $v;
                            break;
                        }
                    }
                }

            } catch (PDOException $e) {
                error_log("Erreur lors de la récupération des emplois du temps: " . $e->getMessage());
            }
        }
        
        $this->view('pedagogie/emplois_temps', [
            'classes' => $classes,
            'enseignants' => $enseignants,
            'emploisTemps' => $emploisTemps,
            'vacances' => $vacances,
            'weekDates' => $weekDates,
            'classeId' => $classeId,
            'personnelId' => $personnelId,
            'selectedWeek' => $selectedWeek,
            'weekLabel' => $weekLabel
        ]);
    }

    /**
     * Formulaire d'ajout d'un créneau dans l'emploi du temps
     */
    public function addEmploiTemps() {
        require_once APP_PATH . '/Models/EmploisTemps.php';
        require_once APP_PATH . '/Models/EnseignantsClasses.php';
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        
        $model = new EmploisTemps();
        $ecModel = new EnseignantsClasses();
        $anneeModel = new AnneeScolaire();
        
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Récupérer les données de l'enseignement sélectionné
                $ecId = $_POST['enseignement_id'];
                $enseignement = $ecModel->find($ecId);
                
                if (!$enseignement) die("Enseignement non trouvé");

                $jour = $_POST['jour_semaine'];
                $hDebut = $_POST['heure_debut'];
                $hFin = $_POST['heure_fin'];

                // 1. Vérifier conflit pour la classe
                if ($model->hasConflitClasse($enseignement['classe_id'], $jour, $hDebut, $hFin, $anneeId)) {
                    $_SESSION['error'] = "Conflit : La classe est déjà occupée sur ce créneau ($jour $hDebut-$hFin).";
                    $this->redirect('pedagogie/emplois-temps/add');
                    return;
                }

                // 2. Vérifier conflit pour l'enseignant
                if ($model->hasConflitEnseignant($enseignement['personnel_id'], $jour, $hDebut, $hFin, $anneeId)) {
                    $_SESSION['error'] = "Conflit : L'enseignant est déjà occupé ailleurs sur ce créneau ($jour $hDebut-$hFin).";
                    $this->redirect('pedagogie/emplois-temps/add');
                    return;
                }

                $data = [
                    'classe_id' => $enseignement['classe_id'],
                    'matiere_id' => $enseignement['matiere_id'],
                    'personnel_id' => $enseignement['personnel_id'],
                    'annee_scolaire_id' => $anneeId,
                    'jour_semaine' => $jour,
                    'heure_debut' => $hDebut,
                    'heure_fin' => $hFin,
                    'remarque' => $_POST['remarque'] ?? '',
                    'actif' => 1
                ];

                $model->create($data);
                $_SESSION['success'] = "Créneau ajouté avec succès.";
                $this->redirect('pedagogie/emplois-temps?classe_id=' . $enseignement['classe_id']);
                return;
            } catch (Exception $e) {
                error_log("Erreur ajout emploi du temps: " . $e->getMessage());
                $_SESSION['error'] = "Erreur lors de l'enregistrement : " . $e->getMessage();
                $this->redirect('pedagogie/emplois-temps/add');
                return;
            }
        }
        
        // Récupérer les enseignements disponibles (JOINS)
        $enseignements = $ecModel->getAllAssignments($anneeId);
        
        // Pré-remplissage via GET (bouton Plus)
        $prefilled = [
            'jour' => $_GET['jour'] ?? null,
            'h_debut' => $_GET['h_debut'] ?? null,
            'h_fin' => $_GET['h_fin'] ?? null,
            'classe_id' => $_GET['classe_id'] ?? null
        ];
        
        $this->view('pedagogie/emplois_temps_form', [
            'enseignements' => $enseignements,
            'prefilled' => $prefilled
        ]);
    }

    public function editEmploiTemps($id) {
        require_once APP_PATH . '/Models/EmploisTemps.php';
        require_once APP_PATH . '/Models/EnseignantsClasses.php';
        require_once APP_PATH . '/Models/AnneeScolaire.php';
        
        $model = new EmploisTemps();
        $ecModel = new EnseignantsClasses();
        $anneeModel = new AnneeScolaire();
        
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;

        $emploiTemps = $model->find($id);
        if (!$emploiTemps) die("Créneau non trouvé");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $ecId = $_POST['enseignement_id'];
                $enseignement = $ecModel->find($ecId);
                
                if (!$enseignement) die("Enseignement non trouvé");

                $jour = $_POST['jour_semaine'];
                $hDebut = $_POST['heure_debut'];
                $hFin = $_POST['heure_fin'];

                // 1. Vérifier conflit pour la classe (en excluant le créneau actuel via $id)
                if ($model->hasConflitClasse($enseignement['classe_id'], $jour, $hDebut, $hFin, $anneeId, $id)) {
                    $_SESSION['error'] = "Conflit : La classe est déjà occupée sur ce créneau ($jour $hDebut-$hFin).";
                    $this->redirect('pedagogie/emplois-temps/edit/' . $id);
                    return;
                }

                // 2. Vérifier conflit pour l'enseignant (en excluant le créneau actuel via $id)
                if ($model->hasConflitEnseignant($enseignement['personnel_id'], $jour, $hDebut, $hFin, $anneeId, $id)) {
                    $_SESSION['error'] = "Conflit : L'enseignant est déjà occupé ailleurs sur ce créneau ($jour $hDebut-$hFin).";
                    $this->redirect('pedagogie/emplois-temps/edit/' . $id);
                    return;
                }

                $data = [
                    'classe_id' => $enseignement['classe_id'],
                    'matiere_id' => $enseignement['matiere_id'],
                    'personnel_id' => $enseignement['personnel_id'],
                    'annee_scolaire_id' => $anneeId,
                    'jour_semaine' => $jour,
                    'heure_debut' => $hDebut,
                    'heure_fin' => $hFin,
                    'remarque' => $_POST['remarque'] ?? '',
                ];

                $model->update($id, $data);
                $_SESSION['success'] = "Créneau mis à jour avec succès.";
                $this->redirect('pedagogie/emplois-temps?classe_id=' . $enseignement['classe_id']);
                return;

            } catch (Exception $e) {
                error_log("Erreur editEmploiTemps: " . $e->getMessage());
                $_SESSION['error'] = "Erreur lors de la mise à jour : " . $e->getMessage();
                $this->redirect('pedagogie/emplois-temps/edit/' . $id);
                return;
            }
        }

        // Liste des enseignements possibles
        $enseignements = $ecModel->getAllAssignments($anneeId);

        // Trouver l'ID de l'enseignement correspondant pour pré-sélectionner
        $currentEcId = null;
        foreach ($enseignements as $e) {
            if ($e['classe_id'] == $emploiTemps['classe_id'] && 
                $e['matiere_id'] == $emploiTemps['matiere_id'] && 
                $e['personnel_id'] == $emploiTemps['personnel_id']) {
                $currentEcId = $e['id'];
                break;
            }
        }

        $this->view('pedagogie/emplois_temps_form', [
            'emploiTemps' => $emploiTemps,
            'enseignements' => $enseignements,
            'currentEcId' => $currentEcId
        ]);
    }
    /**
     * Gestion des coefficients pour une série
     */
    public function coefficients($serieId) {
        require_once APP_PATH . '/Models/BaseModel.php';
        require_once APP_PATH . '/Models/MatieresSeries.php';
        require_once APP_PATH . '/Models/Matiere.php';
        
        $model = new BaseModel();
        $msModel = new MatieresSeries();
        $matiereModel = new Matiere();
        
        try {
            // Récupérer les informations de la série
            $serie = $model->queryOne("
                SELECT s.*, n.libelle as niveau_nom 
                FROM series s 
                JOIN niveaux n ON s.niveau_id = n.id 
                WHERE s.id = ?", 
                [$serieId]
            );
            
            if (!$serie) {
                die("Série non trouvée");
            }
            
            // Récupérer les matières associées à cette série avec leurs coefficients
            $matieresAssociees = $msModel->getMatieresParSerie($serieId);
            
            // Récupérer TOUTES les matières actives pour pouvoir en ajouter de nouvelles
            $toutesMatieres = $matiereModel->all(['actif' => 1], 'nom ASC');
            
        } catch (PDOException $e) {
            error_log("Erreur coefficients: " . $e->getMessage());
            die("Erreur lors de la récupération des données");
        }
        
        $this->view('pedagogie/coefficients', [
            'serie' => $serie,
            'matieresAssociees' => $matieresAssociees,
            'toutesMatieres' => $toutesMatieres
        ]);
    }
    
    /**
     * Mise à jour des coefficients via POST
     */
    public function updateCoefficients() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pedagogie/series');
        }
        
        require_once APP_PATH . '/Models/MatieresSeries.php';
        $msModel = new MatieresSeries();
        
        $serieId = $_POST['serie_id'];
        $matieres = $_POST['matieres'] ?? []; // format: [matiere_id => [coefficient, obligatoire, heures_semaine]]
        
        try {
            // 1. On commence par mettre à jour toutes les associations existantes à actif = 0
            // Cela permet de gérer les suppressions (matières qui étaient là mais ne sont plus dans le formulaire)
            $msModel->query("UPDATE matieres_series SET actif = 0 WHERE serie_id = ?", [$serieId]);
            
            // 2. Pour chaque matière soumise, on crée ou on met à jour l'association
            foreach ($matieres as $matiereId => $data) {
                $coef = $data['coefficient'] ?? 1;
                $obligatoire = isset($data['obligatoire']) ? 1 : 0;
                $heures = (!isset($data['heures_semaine']) || $data['heures_semaine'] === '') ? null : $data['heures_semaine'];
                
                // Vérifier si l'association existe (même inactive)
                $existing = $msModel->queryOne(
                    "SELECT id FROM matieres_series WHERE serie_id = ? AND matiere_id = ?",
                    [$serieId, $matiereId]
                );
                
                if ($existing) {
                    // Update de l'existant et réactivation
                    $msModel->update($existing['id'], [
                        'coefficient' => $coef,
                        'obligatoire' => $obligatoire,
                        'heures_semaine' => $heures,
                        'actif' => 1
                    ]);
                } else {
                    // Création d'une nouvelle association
                    $msModel->create([
                        'serie_id' => $serieId,
                        'matiere_id' => $matiereId,
                        'coefficient' => $coef,
                        'obligatoire' => $obligatoire,
                        'heures_semaine' => $heures,
                        'actif' => 1
                    ]);
                }
            }
            
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
            $this->redirect('pedagogie/series/coefficients/' . $serieId . $iframeParam);
            
        } catch (PDOException $e) {
            error_log("Erreur updateCoefficients: " . $e->getMessage());
            die("Erreur lors de la mise à jour des coefficients");
        }
    }

    /**
     * Gestion des coefficients pour un niveau
     */
    public function coefficientsNiveau($niveauId) {
        require_once APP_PATH . '/Models/BaseModel.php';
        require_once APP_PATH . '/Models/MatieresNiveaux.php';
        require_once APP_PATH . '/Models/Matiere.php';
        
        $model = new BaseModel();
        $mnModel = new MatieresNiveaux();
        $matiereModel = new Matiere();
        
        try {
            // Récupérer les informations du niveau
            $niveau = $model->queryOne("SELECT * FROM niveaux WHERE id = ?", [$niveauId]);
            
            if (!$niveau) {
                die("Niveau non trouvé");
            }
            
            // Récupérer les matières associées à ce niveau
            $matieresAssociees = $mnModel->getMatieresParNiveau($niveauId);
            
            // Récupérer TOUTES les matières actives
            $toutesMatieres = $matiereModel->all(['actif' => 1], 'nom ASC');
            
        } catch (PDOException $e) {
            error_log("Erreur coefficientsNiveau: " . $e->getMessage());
            die("Erreur lors de la récupération des données");
        }
        
        $this->view('pedagogie/coefficients_niveau', [
            'niveau' => $niveau,
            'matieresAssociees' => $matieresAssociees,
            'toutesMatieres' => $toutesMatieres
        ]);
    }

    /**
     * Mise à jour des coefficients de niveau via POST
     */
    public function updateCoefficientsNiveau() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pedagogie/niveaux');
        }
        
        require_once APP_PATH . '/Models/MatieresNiveaux.php';
        $mnModel = new MatieresNiveaux();
        
        $niveauId = $_POST['niveau_id'];
        $matieres = $_POST['matieres'] ?? [];
        
        try {
            $mnModel->query("UPDATE matieres_niveaux SET actif = 0 WHERE niveau_id = ?", [$niveauId]);
            
            foreach ($matieres as $matiereId => $data) {
                $coef = $data['coefficient'] ?? 1;
                $obligatoire = isset($data['obligatoire']) ? 1 : 0;
                $heures = (!isset($data['heures_semaine']) || $data['heures_semaine'] === '') ? null : $data['heures_semaine'];
                
                $existing = $mnModel->queryOne(
                    "SELECT id FROM matieres_niveaux WHERE niveau_id = ? AND matiere_id = ?",
                    [$niveauId, $matiereId]
                );
                
                if ($existing) {
                    $mnModel->update($existing['id'], [
                        'coefficient' => $coef,
                        'obligatoire' => $obligatoire,
                        'heures_semaine' => $heures,
                        'actif' => 1
                    ]);
                } else {
                    $mnModel->create([
                        'niveau_id' => $niveauId,
                        'matiere_id' => $matiereId,
                        'coefficient' => $coef,
                        'obligatoire' => $obligatoire,
                        'heures_semaine' => $heures,
                        'actif' => 1
                    ]);
                }
            }
            
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
            $this->redirect('pedagogie/niveaux/coefficients/' . $niveauId . $iframeParam);
            
        } catch (PDOException $e) {
            error_log("Erreur updateCoefficientsNiveau: " . $e->getMessage());
            die("Erreur lors de la mise à jour des coefficients");
        }
    }

    /**
     * Ajouter une série
     */
    public function addSerie() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pedagogie/series');
        }

        require_once APP_PATH . '/Models/Serie.php';
        $model = new Serie();

        try {
            $data = [
                'code' => $_POST['code'],
                'libelle' => $_POST['libelle'],
                'niveau_id' => $_POST['niveau_id'],
                'description' => $_POST['description'] ?? '',
                'actif' => isset($_POST['actif']) ? 1 : 0
            ];

            $model->create($data);
            $this->redirect('pedagogie/series');
        } catch (PDOException $e) {
            error_log("Erreur addSerie: " . $e->getMessage());
            die("Erreur lors de l'ajout de la série");
        }
    }

    /**
     * Modifier une série
     */
    public function editSerie($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('pedagogie/series');
        }

        require_once APP_PATH . '/Models/Serie.php';
        $model = new Serie();

        try {
            $data = [
                'code' => $_POST['code'],
                'libelle' => $_POST['libelle'],
                'niveau_id' => $_POST['niveau_id'],
                'description' => $_POST['description'] ?? '',
                'actif' => isset($_POST['actif']) ? 1 : 0
            ];

            $model->update($id, $data);
            $this->redirect('pedagogie/series');
        } catch (PDOException $e) {
            error_log("Erreur editSerie: " . $e->getMessage());
            die("Erreur lors de la modification de la série");
        }
    }

    /**
     * Supprimer une série
     */
    public function deleteSerie($id) {
        require_once APP_PATH . '/Models/Serie.php';
        $model = new Serie();

        try {
            // Vérifier si la série est utilisée dans des classes
            $count = $model->queryOne("SELECT COUNT(*) as count FROM classes WHERE serie_id = ?", [$id]);
            if ($count['count'] > 0) {
                // On ne peut pas supprimer, on désactive
                $model->update($id, ['actif' => 0]);
            } else {
                $model->delete($id);
            }
            $this->redirect('pedagogie/series');
        } catch (PDOException $e) {
            error_log("Erreur deleteSerie: " . $e->getMessage());
            die("Erreur lors de la suppression de la série");
        }
    }

    /**
     * Basculer le statut d'une série
     */
    public function toggleSerie($id) {
        require_once APP_PATH . '/Models/BaseModel.php'; // Added this line
        $model = new BaseModel(); // Changed from Serie() to BaseModel()

        try {
            $serie = $model->queryOne("SELECT id, actif FROM series WHERE id = ?", [$id]); // Changed from find() to queryOne()
            if ($serie) {
                $newStatut = $serie['actif'] ? 0 : 1; // New logic for status
                $model->update($id, ['actif' => $newStatut], 'series'); // Added table name
            }
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : ''; // Added iframe param
            $this->redirect('pedagogie/series' . $iframeParam); // Updated redirect
        } catch (PDOException $e) {
            error_log("Erreur toggleSerie: " . $e->getMessage());
            die("Erreur lors du basculement du statut");
        }
    }

    /**
     * Gestion des coefficients pour une classe spécifique (Override)
     */
    public function coefficientsClasse($classeId) {
        require_once APP_PATH . '/Models/MatieresClasses.php';
        require_once APP_PATH . '/Models/Matiere.php';
        require_once APP_PATH . '/Models/Classe.php';
        
        $mcModel = new MatieresClasses();
        $matiereModel = new Matiere();
        $classeModel = new Classe();
        
        try {
            // Récupérer les informations de la classe
            $classe = $classeModel->getDetails($classeId);
            if (!$classe) {
                die("Classe non trouvée");
            }
            
            // Récupérer les matières associées à cette classe
            $matieresAssociees = $mcModel->getMatieresParClasse($classeId, $classe['annee_scolaire_id']);
            
            // Récupérer TOUTES les matières actives
            $toutesMatieres = $matiereModel->all(['actif' => 1], 'nom ASC');
            
        } catch (PDOException $e) {
            error_log("Erreur coefficientsClasse: " . $e->getMessage());
            die("Erreur lors de la récupération des données");
        }
        
        $this->view('pedagogie/coefficients_classe', [
            'classe' => $classe,
            'matieresAssociees' => $matieresAssociees,
            'toutesMatieres' => $toutesMatieres
        ]);
    }

    /**
     * Mise à jour des coefficients de classe via POST
     */
    public function updateCoefficientsClasse() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Méthode non autorisée");
        }
        
        require_once APP_PATH . '/Models/MatieresClasses.php';
        $mcModel = new MatieresClasses();
        
        $classeId = $_POST['classe_id'];
        $anneeId = $_POST['annee_scolaire_id'];
        $matieres = $_POST['matieres'] ?? [];
        
        try {
            // 1. Supprimer les anciennes configurations pour cette classe/année
            // On peut choisir de désactiver ou supprimer. Ici on supprime pour éviter l'accumulation
            $mcModel->query("DELETE FROM matieres_classes WHERE classe_id = ? AND annee_scolaire_id = ?", [$classeId, $anneeId]);
            
            // 2. Créer les nouvelles configurations
            foreach ($matieres as $matiereId => $data) {
                $coef = $data['coefficient'] ?? 1.00;
                $obligatoire = isset($data['obligatoire']) ? 1 : 0;
                $heures = (!isset($data['heures_semaine']) || $data['heures_semaine'] === '') ? null : $data['heures_semaine'];
                
                $mcModel->create([
                    'classe_id' => $classeId,
                    'matiere_id' => $matiereId,
                    'annee_scolaire_id' => $anneeId,
                    'coefficient' => $coef,
                    'obligatoire' => $obligatoire,
                    'heures_semaine' => $heures
                ]);
            }
            
            $iframeParam = isset($_GET['iframe']) ? '?iframe=1' : '';
            $this->redirect('pedagogie/classes/coefficients/' . $classeId . $iframeParam);
            
        } catch (PDOException $e) {
            error_log("Erreur updateCoefficientsClasse: " . $e->getMessage());
            die("Erreur lors de la mise à jour des coefficients");
        }
    }
}
