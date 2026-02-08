<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Note;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Periode;
use App\Models\ExamenFinal;
use App\Models\Interrogation;
use App\Models\Bulletin;
use App\Models\BaseModel;
use App\Services\EligibiliteService;

/**
 * Contrôleur des notes
 */

class NotesController extends BaseController {
    private $noteModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->noteModel = new Note();
    }
    
    public function list() {
        // Modèles
        $anneeModel = new AnneeScolaire();
        $classeModel = new Classe();
        $periodeModel = new Periode();
        $examenModel = new ExamenFinal();
        $interroModel = new Interrogation();

        // Récupérer l'année scolaire active
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;

        // Récupérer les filtres
        $classeId = $_GET['classe_id'] ?? null;
        $periodeId = $_GET['periode_id'] ?? null;

        // Données pour les listes déroulantes
        $classes = $anneeId ? $classeModel->getSortedByLevel($anneeId) : [];
        $periodes = $anneeId ? $periodeModel->all(['annee_scolaire_id' => $anneeId], 'numero ASC') : [];

        $evaluations = [];

        if ($classeId && $periodeId) {
            // Récupérer les examens
            $examens = $examenModel->getByClassePeriode($classeId, $periodeId);

            // Récupérer les interrogations
            $interros = $interroModel->getByClassePeriode($classeId, $periodeId);

            $evaluations = array_merge($examens, $interros);
            
            // Trier par date (descendant)
            usort($evaluations, function($a, $b) {
                $dateA = $a['type'] === 'examen' ? $a['date_examen'] : $a['date_interrogation'];
                $dateB = $b['type'] === 'examen' ? $b['date_examen'] : $b['date_interrogation'];
                return strtotime($dateB) - strtotime($dateA);
            });
        }

        $this->view('notes/list', [
            'anneeActive' => $anneeActive,
            'classes' => $classes,
            'periodes' => $periodes,
            'selectedClasse' => $classeId,
            'selectedPeriode' => $periodeId,
            'evaluations' => $evaluations
        ]);
    }
    
    public function saisie() {
        // Saisie classique (formulaire complète avec submit)
        // Accès attendu principal :
        //   notes/saisie?type=examen|interrogation&id=XX
        // Compatibilité :
        //   notes/saisie?examen_id=XX  ou  notes/saisie?interrogation_id=XX
        $type = $_GET['type'] ?? null;
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;

        // Compatibilité avec anciens paramètres
        if (!$type && isset($_GET['examen_id'])) {
            $type = 'examen';
            $id = (int) $_GET['examen_id'];
        } elseif (!$type && isset($_GET['interrogation_id'])) {
            $type = 'interrogation';
            $id = (int) $_GET['interrogation_id'];
        }

        if (!$type || !$id) {
            die("Paramètres manquants pour la saisie des notes");
        }

        $context = $this->getEvaluationContext($type, $id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notes = $_POST['notes'] ?? [];
            $absences = $_POST['absences'] ?? [];
            $appreciations = $_POST['appreciations'] ?? [];

            $this->persistNotes(
                $context['model'],
                $context['tableNotes'],
                $context['fkId'],
                $id,
                $notes,
                $absences,
                $appreciations
            );
            
            $this->redirect('notes/list?classe_id=' . $context['evaluation']['classe_id'] . '&periode_id=' . $context['evaluation']['periode_id']);
        } else {
            $eleves = $this->getElevesWithNotes($context['model'], $context['tableNotes'], $context['fkId'], $id, $context['evaluation']);
            
            $this->view('notes/saisie', [
                'evaluation' => $context['details'],
                'eleves' => $eleves,
                'type' => $type
            ]);
        }
    }

    /**
     * Interface de saisie en masse (optimisée clavier + AJAX)
     * Accès: notes/saisie-masse?type=examen|interrogation&id=XX
     */
    public function saisieMasse() {
        $type = $_GET['type'] ?? null;
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;

        if (!$type || !$id) {
            die("Paramètres manquants pour la saisie en masse");
        }

        $context = $this->getEvaluationContext($type, $id);
        $eleves = $this->getElevesWithNotes($context['model'], $context['tableNotes'], $context['fkId'], $id, $context['evaluation']);

        // Notes éventuellement pré-chargées depuis un import Excel
        $importSummary = null;
        if (isset($_SESSION['notes_importees']) && isset($_SESSION['notes_importees'][$type][$id])) {
            $importData = $_SESSION['notes_importees'][$type][$id];
            $importSummary = $importData['summary'] ?? null;
            $notesParMatricule = $importData['notes'] ?? [];

            // Projection des notes importées sur la liste des élèves
            foreach ($eleves as &$eleve) {
                $mat = $eleve['matricule'];
                if (isset($notesParMatricule[$mat])) {
                    $row = $notesParMatricule[$mat];
                    if (array_key_exists('note', $row)) {
                        $eleve['note'] = $row['note'];
                    }
                    if (array_key_exists('absent', $row)) {
                        $eleve['absent'] = $row['absent'];
                    }
                    if (array_key_exists('appreciation', $row)) {
                        $eleve['appreciation'] = $row['appreciation'];
                    }
                }
            }
            unset($eleve);
        }

        $this->view('notes/saisie_masse', [
            'evaluation' => $context['details'],
            'eleves' => $eleves,
            'type' => $type,
            'import_summary' => $importSummary
        ]);
    }

    /**
     * Sauvegarde AJAX des notes (en masse, partielle)
     * Endpoint: POST notes/saisie-masse/save
     */
    public function saveAjax() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? null;
        $id = isset($input['evaluation_id']) ? (int) $input['evaluation_id'] : null;
        $changes = $input['changes'] ?? [];

        if (!$type || !$id || empty($changes)) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            return;
        }

        try {
            $context = $this->getEvaluationContext($type, $id);

            // Transformer le tableau "changes" pour réutiliser persistNotes
            $notes = [];
            $absences = [];
            $appreciations = [];

            foreach ($changes as $eleveId => $row) {
                // $row: ['note' => ..., 'absent' => 0|1, 'appreciation' => '...']
                if (array_key_exists('note', $row)) {
                    $notes[$eleveId] = $row['note'] === '' ? null : $row['note'];
                }
                if (isset($row['absent']) && $row['absent']) {
                    $absences[$eleveId] = 1;
                }
                if (array_key_exists('appreciation', $row)) {
                    $appreciations[$eleveId] = $row['appreciation'];
                }
            }

            $this->persistNotes(
                $context['model'],
                $context['tableNotes'],
                $context['fkId'],
                $id,
                $notes,
                $absences,
                $appreciations
            );

            echo json_encode([
                'success' => true,
                'message' => 'Notes enregistrées avec succès'
            ]);
        } catch (Exception $e) {
            error_log("Erreur saveAjax notes: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde des notes'
            ]);
        }
    }

    /**
     * Import Excel des notes (par matricule)
     * Endpoint: POST notes/saisie-masse/import
     */
    public function importExcel() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('notes/list');
        }

        $type = $_POST['type'] ?? null;
        $id = isset($_POST['evaluation_id']) ? (int) $_POST['evaluation_id'] : null;

        if (!$type || !$id) {
            $this->redirect('notes/list');
        }

        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['notes_importees'][$type][$id] = [
                'summary' => "Erreur lors du téléversement du fichier Excel.",
                'notes' => []
            ];
            $this->redirect('notes/saisie-masse?type=' . urlencode($type) . '&id=' . $id);
        }

        $tmpPath = $_FILES['excel_file']['tmp_name'];

        // On s'appuie sur PhpSpreadsheet (à ajouter via Composer)
        try {
            if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new Exception("Bibliothèque PhpSpreadsheet non installée (composer require phpoffice/phpspreadsheet).");
            }

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmpPath);
            $spreadsheet = $reader->load($tmpPath);
            $sheet = $spreadsheet->getActiveSheet();

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Lecture de l'entête pour trouver les colonnes
            $headers = [];
            $headerRow = 1;
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $value = trim((string) $sheet->getCell($col . $headerRow)->getValue());
                if ($value !== '') {
                    $headers[strtolower($value)] = $col;
                }
            }

            $required = ['matricule', 'note'];
            foreach ($required as $field) {
                if (!isset($headers[$field])) {
                    throw new Exception("Colonne obligatoire manquante dans le fichier: " . $field);
                }
            }

            $matriculeCol = $headers['matricule'];
            $noteCol = $headers['note'];
            $absentCol = $headers['absent'] ?? null;
            $appCol = $headers['appreciation'] ?? ($headers['appreciation'] ?? null);

            $notesParMatricule = [];
            $nbLignes = 0;

            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                $matricule = trim((string) $sheet->getCell($matriculeCol . $row)->getValue());
                if ($matricule === '') {
                    continue;
                }

                $noteVal = $sheet->getCell($noteCol . $row)->getValue();
                $noteVal = $noteVal === '' ? null : (float) $noteVal;

                $absentVal = 0;
                if ($absentCol) {
                    $raw = trim((string) $sheet->getCell($absentCol . $row)->getValue());
                    $absentVal = in_array(strtolower($raw), ['1', 'oui', 'o', 'x'], true) ? 1 : 0;
                }

                $appVal = '';
                if ($appCol) {
                    $appVal = trim((string) $sheet->getCell($appCol . $row)->getValue());
                }

                $notesParMatricule[$matricule] = [
                    'note' => $noteVal,
                    'absent' => $absentVal,
                    'appreciation' => $appVal
                ];
                $nbLignes++;
            }

            $_SESSION['notes_importees'][$type][$id] = [
                'summary' => "Import Excel réussi : " . $nbLignes . " ligne(s) lue(s). Les données sont pré-chargées dans la grille, pensez à sauvegarder.",
                'notes' => $notesParMatricule
            ];
        } catch (Exception $e) {
            error_log("Erreur importExcel notes: " . $e->getMessage());
            $_SESSION['notes_importees'][$type][$id] = [
                'summary' => "Erreur import Excel : " . $e->getMessage(),
                'notes' => []
            ];
        }

        $this->redirect('notes/saisie-masse?type=' . urlencode($type) . '&id=' . $id);
    }

    /**
     * Télécharge un modèle CSV avec la liste des élèves pour l'import Excel
     */
    public function downloadTemplate() {
        $type = $_GET['type'] ?? null;
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;

        if (!$type || !$id) {
            die("Paramètres manquants");
        }

        $context = $this->getEvaluationContext($type, $id);
        $eleves = $this->getElevesWithNotes($context['model'], $context['tableNotes'], $context['fkId'], $id, $context['evaluation']);

        $filename = "modele_notes_" . str_replace(' ', '_', $context['details']['matiere_nom']) . "_" . str_replace(' ', '_', $context['details']['classe_nom']) . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM UTF-8 pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Entête
        fputcsv($output, ['Matricule', 'Nom', 'Prenom', 'Note', 'Absent', 'Appreciation'], ';');

        foreach ($eleves as $eleve) {
            fputcsv($output, [
                $eleve['matricule'],
                $eleve['nom'],
                $eleve['prenom'],
                $eleve['note'] ?? '',
                $eleve['absent'] ?? 0,
                $eleve['appreciation'] ?? ''
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Récupération modèle / table / clé en fonction du type d'évaluation
     */
    private function getEvaluationContext($type, $id) {
        $model = null;
        $tableNotes = '';
        $fkId = '';
        
        if ($type === 'examen') {
            $model = new ExamenFinal();
            $tableNotes = 'notes_examens';
            $fkId = 'examen_id';
        } elseif ($type === 'interrogation') {
            $model = new Interrogation();
            $tableNotes = 'notes_interrogations';
            $fkId = 'interrogation_id';
        } else {
            die("Type d'évaluation invalide");
        }
        
        $evaluation = $model->find($id);
        if (!$evaluation) {
            die("Évaluation non trouvée");
        }

        $details = $model->getDetailsWithRelations($id);

        return [
            'model' => $model,
            'tableNotes' => $tableNotes,
            'fkId' => $fkId,
            'evaluation' => $evaluation,
            'details' => $details
        ];
    }

    /**
     * Vérifie l'éligibilité financière d'un élève pour passer une évaluation
     * 
     * Utilise la procédure stockée verifier_ecolage_eleve qui vérifie :
     * 1. Inscription validée
     * 2. Inscription non bloquée (paiement initial)
     * 3. Peut suivre les cours (pas exclu pour impayé mensuel)
     * 
     * @param int $eleveId ID de l'élève
     * @param int $anneeScolaireId ID de l'année scolaire
     * @return array ['eligible' => bool, 'raison' => string, 'eleve' => string]
     */
    private function verifierEligibiliteEleve($eleveId, $anneeScolaireId) {
        $eligibiliteService = new EligibiliteService();
        
        // Utiliser le service qui appelle la procédure stockée
        $result = $eligibiliteService->verifierEligibilite($eleveId, $anneeScolaireId);
        
        // Récupérer les informations de l'élève pour le message
        $model = new BaseModel();
        
        $sql = "SELECT 
                    CONCAT(e.nom, ' ', e.prenom, ' (', e.matricule, ')') as eleve_info
                FROM eleves e
                WHERE e.id = ?";
        
        $eleveData = $model->queryOne($sql, [$eleveId]);
        $eleveInfo = $eleveData ? $eleveData['eleve_info'] : 'Élève inconnu';
        
        // Adapter le format de retour pour compatibilité avec le code existant
        return [
            'eligible' => $result['peut_passer'],
            'raison' => $result['message'],
            'eleve' => $eleveInfo
        ];
    }

    /**
     * Récupérer les élèves de la classe + notes associées à cette évaluation
     */
    private function getElevesWithNotes($model, $tableNotes, $fkId, $evaluationId, $evaluation) {
        return $model->getElevesWithNotes($evaluationId, $evaluation['classe_id'], $evaluation['annee_scolaire_id']);
    }

    /**
     * Persister un ensemble de notes / absences / appréciations
     * 
     * Vérifie l'éligibilité financière de chaque élève avant de sauvegarder
     * Les élèves non éligibles sont ignorés et loggés
     */
    private function persistNotes($model, $tableNotes, $fkId, $evaluationId, $notes, $absences, $appreciations) {
        // Récupérer l'année scolaire de l'évaluation
        $evaluation = $model->find($evaluationId);
        if (!$evaluation) {
            error_log("Évaluation #{$evaluationId} introuvable lors de persistNotes");
            return;
        }
        
        $anneeScolaireId = $evaluation['annee_scolaire_id'];
        $elevesNonEligibles = [];
        $elevesEligibles = 0;
        
        foreach ($notes as $eleveId => $noteVal) {
            // Vérifier l'éligibilité de l'élève
            $eligibilite = $this->verifierEligibiliteEleve($eleveId, $anneeScolaireId);
            
            if (!$eligibilite['eligible']) {
                // Logger l'élève non éligible
                $elevesNonEligibles[] = [
                    'eleve' => $eligibilite['eleve'] ?? "Élève #{$eleveId}",
                    'raison' => $eligibilite['raison']
                ];
                error_log("Note NON sauvegardée pour élève #{$eleveId} ({$eligibilite['eleve']}) : {$eligibilite['raison']}");
                continue; // Passer cet élève
            }
            
            $elevesEligibles++;
            $isAbsent = isset($absences[$eleveId]) ? 1 : 0;
            $appreciation = $appreciations[$eleveId] ?? '';
            
            // Vérifier si la note existe déjà
            $exists = $model->queryOne(
                "SELECT id FROM $tableNotes WHERE $fkId = ? AND eleve_id = ?",
                [$evaluationId, $eleveId]
            );
            
            if ($exists) {
                $model->query(
                    "UPDATE $tableNotes SET note = ?, absent = ?, appreciation = ?, modifie_par = ?, date_modification = NOW() WHERE id = ?",
                    [$noteVal !== '' ? $noteVal : null, $isAbsent, $appreciation, $_SESSION['user_id'] ?? null, $exists['id']]
                );
            } else {
                $model->query(
                    "INSERT INTO $tableNotes ($fkId, eleve_id, note, absent, appreciation, saisi_par, date_saisie) VALUES (?, ?, ?, ?, ?, ?, NOW())",
                    [$evaluationId, $eleveId, $noteVal !== '' ? $noteVal : null, $isAbsent, $appreciation, $_SESSION['user_id'] ?? null]
                );
            }
        }
        
        // Si des élèves ont été bloqués, stocker l'info en session pour afficher un message
        if (!empty($elevesNonEligibles)) {
            $_SESSION['notes_blocage_info'] = [
                'nb_bloques' => count($elevesNonEligibles),
                'nb_sauvegardes' => $elevesEligibles,
                'details' => $elevesNonEligibles
            ];
        }
    }
    
    /**
     * Affiche les moyennes et statistiques
     */
    public function moyennes() {
        $bulletinModel = new Bulletin();
        $periodeModel = new Periode();
        $classeModel = new Classe();
        
        // Récupérer l'année scolaire active
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;
        
        // Récupérer les filtres
        $periodeId = $_GET['periode_id'] ?? null;
        $classeId = $_GET['classe_id'] ?? null;
        
        // Récupérer les périodes et classes
        $periodes = $anneeId ? $periodeModel->all(['annee_scolaire_id' => $anneeId, 'actif' => 1], 'numero ASC') : [];
        $classes = $anneeId ? $classeModel->getSortedByLevel($anneeId) : [];
        
        // Statistiques globales
        $statsGlobales = $anneeId ? $bulletinModel->getStatistiquesGlobales($anneeId, $periodeId, $classeId) : [];
        
        // Statistiques par classe
        $statsParClasse = $anneeId ? $bulletinModel->getStatistiquesParClasse($anneeId, $periodeId) : [];
        
        // Liste des moyennes par élève
        $moyennesEleves = $anneeId ? $bulletinModel->getMoyennesEleves($anneeId, $periodeId, $classeId) : [];
        
        $this->view('notes/moyennes', [
            'periodes' => $periodes,
            'classes' => $classes,
            'anneeActive' => $anneeActive,
            'statsGlobales' => $statsGlobales,
            'statsParClasse' => $statsParClasse,
            'moyennesEleves' => $moyennesEleves,
            'selectedPeriode' => $periodeId,
            'selectedClasse' => $classeId
        ]);
    }
}

