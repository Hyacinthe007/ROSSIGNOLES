<?php
/**
 * Contrôleur du personnel
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Personnel.php';
require_once APP_PATH . '/Models/PersonnelEnseignant.php';
require_once APP_PATH . '/Models/PersonnelAdministratif.php';

class PersonnelController extends BaseController {
    
    /**
     * Liste du personnel
     */
    public function list() {
        $personnelModel = new Personnel();
        $personnels = $personnelModel->query(
            "SELECT p.*, 
                    pe.specialite, pe.grade,
                    pa.departement, pa.poste_id
             FROM personnels p 
             LEFT JOIN personnels_enseignants pe ON p.id = pe.personnel_id 
             LEFT JOIN personnels_administratifs pa ON p.id = pa.personnel_id 
             WHERE p.statut = 'actif' AND p.deleted_at IS NULL
             ORDER BY p.nom ASC"
        );
        $this->view('personnel/list', ['personnel' => $personnels]);
    }

    /**
     * Wizard de création (remplace l'ancien add)
     */
    public function add() {
        $this->redirect('personnel/nouveau');
    }

    /**
     * Nouveau personnel (Wizard)
     */
    public function nouveau() {
        // Réinitialiser la session si nouvelle saisie
        if (!isset($_GET['etape'])) {
            unset($_SESSION['personnel_data']);
        }
        
        $etape = $_GET['etape'] ?? 1;
        
        switch ($etape) {
            case 1:
                $this->etape1ChoixType();
                break;
            case 2:
                $this->etape2Formulaire();
                break;
            default:
                $this->redirect('personnel/nouveau?etape=1');
        }
    }
    
    /**
     * Étape 1: Choix du type de personnel
     */
    private function etape1ChoixType() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['personnel_data']['type_personnel'] = $_POST['type_personnel'];
            $this->redirect('personnel/nouveau?etape=2');
            return;
        }
        
        $this->view('personnel_consolide/etape1_type');
    }
    
    /**
     * Étape 2: Formulaire selon le type choisi
     */
    private function etape2Formulaire() {
        $type = $_SESSION['personnel_data']['type_personnel'] ?? 'administratif';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->enregistrer();
            return;
        }
        
        $matricule = $this->genererMatricule($type);
        
        $this->view('personnel_consolide/etape2_formulaire', [
            'type' => $type,
            'matricule' => $matricule
        ]);
    }
    
    /**
     * Enregistrement final
     */
    public function enregistrer() {
        // Logging pour diagnostic
        error_log("=== DÉBUT ENREGISTREMENT PERSONNEL ===");
        error_log("SESSION: " . print_r($_SESSION, true));
        error_log("POST: " . print_r($_POST, true));
        
        // Vérifier que la session existe
        if (!isset($_SESSION['personnel_data']['type_personnel'])) {
            error_log("ERREUR: type_personnel non défini dans la session");
            $_SESSION['error'] = "Session expirée. Veuillez recommencer le processus d'inscription.";
            $this->redirect('personnel/nouveau?etape=1');
            return;
        }
        
        $type = $_SESSION['personnel_data']['type_personnel'];
        error_log("Type de personnel: " . $type);
        
        try {
            // Données de base
            $personnelData = [
                'matricule' => $_POST['matricule'],
                'nom' => isset($_POST['nom']) ? mb_strtoupper($_POST['nom']) : '',
                'prenom' => isset($_POST['prenom']) ? mb_convert_case($_POST['prenom'], MB_CASE_TITLE, "UTF-8") : '',
                'sexe' => $_POST['sexe'] ?? 'M',
                'date_naissance' => $_POST['date_naissance'] ?? null,
                'lieu_naissance' => $_POST['lieu_naissance'] ?? null,
                'cin' => !empty($_POST['cin']) ? $_POST['cin'] : null,
                'numero_cnaps' => $_POST['numero_cnaps'] ?? null,
                'iban' => $_POST['iban'] ?? null,
                'situation_matrimoniale' => $_POST['situation_matrimoniale'] ?? 'celibataire',
                'nb_enfants' => $_POST['nb_enfants'] ?? 0,
                'adresse' => $_POST['adresse'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'email' => !empty($_POST['email']) ? $_POST['email'] : null,
                'date_embauche' => $_POST['date_embauche'] ?? date('Y-m-d'),
                'date_fin_contrat' => !empty($_POST['date_fin_contrat']) ? $_POST['date_fin_contrat'] : null,
                'type_contrat' => $_POST['type_contrat'] ?? 'cdi',
                'urgence_nom' => $_POST['urgence_nom'] ?? null,
                'urgence_telephone' => $_POST['urgence_telephone'] ?? null,
                'urgence_lien' => $_POST['urgence_lien'] ?? null,
                'diplome' => $_POST['diplome'] ?? null,
                'annee_obtention_diplome' => $_POST['annee_obtention_diplome'] ?? null,
                'statut' => 'actif',
                'type_personnel' => $type
            ];
            
            // Photo
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = PUBLIC_PATH . '/uploads/photos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = 'pers_' . uniqid() . '.' . strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $fileName)) {
                    $personnelData['photo'] = 'uploads/photos/' . $fileName;
                }
            }
            
            $personnelModel = new Personnel();
            $personnelId = $personnelModel->create($personnelData);
            
            if ($type === 'enseignant' && $personnelId) {
                $enseignantData = [
                    'personnel_id' => $personnelId,
                    'specialite' => $_POST['specialite'] ?? '',
                    'diplome' => $_POST['diplome'] ?? '',
                    'grade' => $_POST['grade'] ?? 'vacataire',
                    'anciennete_annees' => 0
                ];
                (new PersonnelEnseignant())->create($enseignantData);
                
            } elseif ($type === 'administratif' && $personnelId) {
                // Ne créer l'entrée que si un poste est sélectionné et qu'il est numérique (un ID)
                $posteId = $_POST['poste_id'] ?? null;
                if (!empty($posteId) && is_numeric($posteId)) {
                    $adminData = [
                        'personnel_id' => $personnelId,
                        'poste_id' => $posteId,
                        'departement' => $_POST['departement'] ?? null,
                        'niveau_acces' => $_POST['niveau_acces'] ?? 1
                    ];
                    (new PersonnelAdministratif())->create($adminData);
                } else {
                    error_log("⚠️ Poste ignoré (car non numérique ou vide) : " . $posteId);
                }
            }
            
            error_log("✅ Personnel créé avec succès - ID: " . $personnelId);
            unset($_SESSION['personnel_data']);
            $_SESSION['success'] = "Personnel enregistré avec succès";
            error_log("Redirection vers personnel/details/" . $personnelId);
            $this->redirect('personnel/details/' . $personnelId); // Redirection vers details
            
        } catch (Exception $e) {
            error_log("❌ ERREUR lors de l'enregistrement: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
            $this->redirect('personnel/nouveau?etape=2');
        }
    }

    private function genererMatricule($type) {
        $prefix = $type === 'enseignant' ? 'ENS' : 'PER';
        $personnelModel = new Personnel();
        
        // On cherche les matricules existants pour ce préfixe
        $results = $personnelModel->query(
            "SELECT matricule FROM personnels WHERE matricule LIKE ?",
            [$prefix . '-%']
        );
        
        $maxNum = 0;
        foreach ($results as $row) {
            $parts = explode('-', $row['matricule']);
            if (count($parts) >= 2) {
                $num = (int)$parts[count($parts) - 1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        
        $next = $maxNum + 1;
        
        // Boucle de sécurité pour s'assurer que le matricule n'existe vraiment pas
        // (utile en cas de suppression physique ou de trous dans la numérotation)
        while (true) {
            $matricule = sprintf('%s-%04d', $prefix, $next);
            $exists = $personnelModel->queryOne(
                "SELECT id FROM personnels WHERE matricule = ?", 
                [$matricule]
            );
            
            if (!$exists) {
                return $matricule;
            }
            $next++;
        }
    }

    
    public function edit($id) {
        $personnelModel = new Personnel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le membre existe vraiment avant de continuer
            $existing = $personnelModel->find($id);
            if (!$existing) {
                $_SESSION['error'] = "Erreur fatale : Le membre du personnel avec l'ID $id n'existe pas dans la base de données.";
                $this->redirect('liste-personnel');
            }

            // Gestion de l'upload de photo
            $photoPath = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = PUBLIC_PATH . '/uploads/photos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $maxFileSize = 2 * 1024 * 1024; // 2 Mo
                    if ($_FILES['photo']['size'] <= $maxFileSize) {
                        $newFileName = 'pers_' . uniqid() . '.' . $fileExtension;
                        $uploadPath = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                            // On stocke le chemin relatif "uploads/photos/..."
                            $photoPath = 'uploads/photos/' . $newFileName;
                        }
                    }
                }
            }

            $data = [
                'matricule' => $_POST['matricule'] ?? '',
                'nom' => isset($_POST['nom']) ? mb_strtoupper($_POST['nom']) : '',
                'prenom' => isset($_POST['prenom']) ? mb_convert_case($_POST['prenom'], MB_CASE_TITLE) : '',
                'sexe' => $_POST['sexe'] ?? 'M',
                'date_naissance' => $_POST['date_naissance'] ?? null,
                'lieu_naissance' => $_POST['lieu_naissance'] ?? '',
                'cin' => !empty($_POST['cin']) ? $_POST['cin'] : null,
                'numero_cnaps' => $_POST['numero_cnaps'] ?? null,
                'iban' => $_POST['iban'] ?? null,
                'situation_matrimoniale' => $_POST['situation_matrimoniale'] ?? 'celibataire',
                'nb_enfants' => $_POST['nb_enfants'] ?? 0,
                'adresse' => $_POST['adresse'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'email' => !empty($_POST['email']) ? $_POST['email'] : null,
                'date_embauche' => $_POST['date_embauche'] ?? '',
                'date_fin_contrat' => !empty($_POST['date_fin_contrat']) ? $_POST['date_fin_contrat'] : null,
                'type_contrat' => $_POST['type_contrat'] ?? 'cdi',
                'urgence_nom' => $_POST['urgence_nom'] ?? null,
                'urgence_telephone' => $_POST['urgence_telephone'] ?? null,
                'urgence_lien' => $_POST['urgence_lien'] ?? null,
                'diplome' => $_POST['diplome'] ?? null,
                'annee_obtention_diplome' => $_POST['annee_obtention_diplome'] ?? null,
            ];

            if ($photoPath) {
                $data['photo'] = $photoPath;
            }
            
            $personnelModel->update($id, $data);
            
            // Maj des sous-tables
            $typePersonnel = $_POST['type_personnel'] ?? '';
            
            if ($typePersonnel === 'enseignant') {
                $subModel = new PersonnelEnseignant();
                // Vérifier si l'entrée existe déjà
                $exists = $subModel->queryOne("SELECT id FROM personnels_enseignants WHERE personnel_id = ?", [$id]);
                
                if ($exists) {
                    $subModel->query("UPDATE personnels_enseignants SET specialite = ?, diplome = ?, grade = ? WHERE personnel_id = ?", [
                       $_POST['specialite'] ?? '', $_POST['diplome'] ?? '', $_POST['grade'] ?? '', $id 
                    ]);
                } else {
                    $subModel->create([
                        'personnel_id' => $id,
                        'specialite' => $_POST['specialite'] ?? '',
                        'diplome' => $_POST['diplome'] ?? '',
                        'grade' => $_POST['grade'] ?? 'vacataire'
                    ]);
                }
                
            } elseif ($typePersonnel === 'administratif') {
                 $subModel = new PersonnelAdministratif();
                 $exists = $subModel->queryOne("SELECT id FROM personnels_administratifs WHERE personnel_id = ?", [$id]);
                 
                 $posteId = !empty($_POST['poste_id']) ? $_POST['poste_id'] : null;
                 
                 if ($exists) {
                     $subModel->query("UPDATE personnels_administratifs SET departement = ?, poste_id = ? WHERE personnel_id = ?", [
                         $_POST['departement'] ?? '', $posteId, $id
                     ]);
                 } else {
                     $subModel->create([
                         'personnel_id' => $id,
                         'departement' => $_POST['departement'] ?? '',
                         'poste_id' => $posteId
                     ]);
                 }
            }
            
            $_SESSION['success_message'] = "Personnel mis à jour avec succès";
            $this->redirect('personnel/details/' . $id);
        } else {
            // Utiliser getDetailsComplets pour avoir toutes les infos (y compris poste_libelle)
            $personnel = $personnelModel->getDetailsComplets($id);
            
            if (!$personnel) {
                http_response_code(404);
                die("Membre du personnel non trouvé");
            }
            
            // Récupérer la liste des postes administratifs pour le dropdown
            $postes = [];
            if ($personnel['type_personnel'] === 'administratif') {
                require_once APP_PATH . '/Models/BaseModel.php';
                $baseIdx = new BaseModel();
                try {
                    $postes = $baseIdx->query("SELECT * FROM postes_administratifs ORDER BY libelle ASC");
                } catch (Exception $e) {
                    // Si la table n'existe pas ou erreur
                    $postes = [];
                }
            }
            
            $this->view('personnel/edit', [
                'personnel' => $personnel,
                'postes' => $postes
            ]);
        }
    }
    
    public function details($id) {
        $personnelModel = new Personnel();
        $personnel = $personnelModel->find($id);
        
        if (!$personnel) {
            http_response_code(404);
            die("Membre du personnel non trouvé");
        }
        
        // Récupérer les détails spécifiques (Enseignant ou Administratif)
        if ($personnel['type_personnel'] === 'enseignant') {
            $subModel = new PersonnelEnseignant();
            // On cherche par personnel_id
            $details = $subModel->queryOne("SELECT * FROM personnels_enseignants WHERE personnel_id = ?", [$id]);
            if ($details) {
                $personnel = array_merge($personnel, $details);
            }
        } elseif ($personnel['type_personnel'] === 'administratif') {
            $subModel = new PersonnelAdministratif();
            $details = $subModel->queryOne("SELECT * FROM personnels_administratifs WHERE personnel_id = ?", [$id]);
            if ($details) {
                $personnel = array_merge($personnel, $details);
            }
        }
        
        // Charger les documents (via modèle générique)
        require_once APP_PATH . '/Models/Document.php';
        require_once APP_PATH . '/Models/AbsencePersonnel.php';
        
        $docModel = new Document();
        $absModel = new AbsencePersonnel();
        
        $documents = $docModel->getByEntite('personnel', $id);
        $absences = $absModel->getByPersonnel($id);        
        $this->view('personnel/details', [
            'personnel' => $personnel,
            'documents' => $documents,
            'absences' => $absences
        ]);
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Soft delete : marquer comme supprimé
            $personnelModel = new Personnel();
            if ($personnelModel->columnExists('deleted_at')) {
                $personnelModel->softDelete($id);
            } else {
                // Fallback : changer le statut
                $personnelModel->update($id, ['statut' => 'inactif']);
            }
            $_SESSION['success'] = "Personnel supprimé avec succès";
            $this->redirect('personnel/list');
        } else {
            $personnelModel = new Personnel();
            $personnel = $personnelModel->find($id);
            if (!$personnel) {
                http_response_code(404);
                die("Membre du personnel non trouvé");
            }
            $this->view('personnel/delete', ['personnel' => $personnel]);
        }
    }

    // Gestion des Documents
    // Gestion des Documents
    public function addDocument($personnelId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
            require_once APP_PATH . '/Models/Document.php';
            $docModel = new Document();
            
            $uploadDir = PUBLIC_PATH . '/uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExt = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);
            $fileName = 'doc_' . uniqid() . '.' . $fileExt;
            
            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $uploadDir . $fileName)) {
                $docModel->create([
                    'entite_type' => 'personnel',
                    'entite_id' => $personnelId,
                    'type_document_id' => $_POST['type_document_id'] ?? null, // Idéalement ID, sinon gérer texte
                    'chemin_fichier' => 'uploads/documents/' . $fileName,
                    'nom_original' => $_FILES['fichier']['name'],
                    'taille' => $_FILES['fichier']['size'],
                    'mime_type' => $_FILES['fichier']['type'],
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'description' => $_POST['notes'] ?? ''
                ]);
            }
        }
        $this->redirect('personnel/details/' . $personnelId);
    }

    public function deleteDocument($id) {
        require_once APP_PATH . '/Models/Document.php';
        $docModel = new Document();
        $doc = $docModel->find($id);
        
        if ($doc) {
            $filePath = PUBLIC_PATH . '/' . $doc['chemin_fichier'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $docModel->delete($id);
            $this->redirect('personnel/details/' . $doc['entite_id']);
        } else {
            $this->redirect('personnel/list');
        }
    }

    /**
     * Génère un certificat de travail pour un membre du personnel
     */
    public function certificatTravail($id) {
        $personnelModel = new Personnel();
        $personnel = $personnelModel->find($id);
        
        if (!$personnel) {
            http_response_code(404);
            die("Personnel non trouvé");
        }
        
        require_once APP_PATH . '/Services/PdfService.php';
        $pdfService = new PdfService();
        
        // Préparation des données pour la vue
        $data = [
            'personnel' => $personnel,
            'date_actuelle' => date('d/m/Y'),
            'type_contrat' => strtoupper($personnel['type_contrat'] ?? 'CDI')
        ];
        
        // On utilise un template HTML simple pour le certificat
        ob_start();
        ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <div style="font-family: 'Outfit', sans-serif; padding: 40px; line-height: 1.6;">
            <div style="text-align: center; margin-bottom: 50px;">
                <h1 style="text-transform: uppercase;">Certificat de Travail</h1>
            </div>
            
            <p>Je soussigné, le Directeur de l'établissement ROSSIGNOLES, certifie par la présente que :</p>
            
            <div style="margin: 30px 0; font-weight: bold; font-size: 1.2em;">
                M./Mme <?= e($personnel['nom'] . ' ' . $personnel['prenom']) ?><br>
                Matricule : <?= e($personnel['matricule']) ?>
            </div>
            
            <p>est employé(e) au sein de notre établissement en qualité de <strong><?= e(ucfirst($personnel['type_personnel'])) ?></strong> 
            depuis le <?= formatDate($personnel['date_embauche']) ?> sous contrat <?= e($data['type_contrat']) ?>.</p>
            
            <p>Le présent certificat est délivré à l'intéressé(e) pour servir et valoir ce que de droit.</p>
            
            <div style="margin-top: 80px; text-align: right;">
                Fait à ......................., le <?= $data['date_actuelle'] ?><br><br>
                <strong>Le Directeur</strong><br>
                <div style="height: 100px;"></div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        
        $pdfService->generateCertificatTravail($html, "certificat_travail_" . $personnel['matricule'] . ".pdf");
    }
}

