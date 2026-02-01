<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Absence;
use App\Models\Eleve;
use PDOException;

class AbsencesController extends BaseController {
    private $absenceModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->absenceModel = new Absence();
    }
    
    public function list() {
        // Détection automatique du type basé sur l'URL
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $autoType = null;
        
        if (strpos($requestUri, '/retards/') !== false) {
            $autoType = 'retard';
        } elseif (strpos($requestUri, '/presences/') !== false) {
            $autoType = 'absence'; // Présences = Absences
        }
        
        // Filtrer par type si spécifié dans l'URL ou détecté automatiquement
        $type = $_GET['type'] ?? $autoType;
        $where = [];
        $params = [];
        
        if ($type && in_array($type, ['absence', 'retard'])) {
            $where[] = "type = ?";
            $params[] = $type;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $absences = $this->absenceModel->query(
            "SELECT a.*, 
                    e.matricule, e.nom, e.prenom, 
                    c.code as classe_code,
                    m.nom as matiere_nom,
                    CONCAT(p.nom, ' ', p.prenom) as professeur_nom
             FROM absences a
             JOIN eleves e ON a.eleve_id = e.id
             JOIN classes c ON a.classe_id = c.id
             LEFT JOIN emplois_temps et ON (
                 et.classe_id = a.classe_id 
                 AND et.heure_debut = a.heure_debut 
                 AND et.heure_fin = a.heure_fin
             )
             LEFT JOIN matieres m ON et.matiere_id = m.id
             LEFT JOIN personnels p ON et.personnel_id = p.id
             {$whereClause}
             ORDER BY a.date_absence DESC, a.heure_debut DESC",
            $params
        );
        
        $this->view('absences/list', [
            'absences' => $absences,
            'type_filtre' => $type
        ]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classeId = $_POST['classe_id'] ?? '';
            $dateAbsence = $_POST['date_absence'] ?? '';
            $absents = $_POST['absents'] ?? [];
            $emploiTempsId = $_POST['emploi_temps_id'] ?? null;
            $motif = $_POST['motif'] ?? '';
            $justifieeDefault = isset($_POST['justifiee_default']) ? 1 : 0;
            
            if (empty($absents)) {
                $_SESSION['error'] = "Aucun élève sélectionné";
                $this->redirect('absences/add');
                return;
            }

            $currentUserId = $_SESSION['user_id'] ?? null;
            
            // Récupérer les infos de l'emploi du temps si sélectionné
            $heureDebut = null;
            $heureFin = null;
            if ($emploiTempsId) {
                $emploiTemps = $this->absenceModel->queryOne(
                    "SELECT heure_debut, heure_fin FROM emplois_temps WHERE id = ?",
                    [$emploiTempsId]
                );
                if ($emploiTemps) {
                    $heureDebut = $emploiTemps['heure_debut'];
                    $heureFin = $emploiTemps['heure_fin'];
                }
            }

            // Récupérer l'année scolaire active
            $anneeScolaire = $this->absenceModel->queryOne(
                "SELECT id FROM annees_scolaires WHERE actif = 1 LIMIT 1"
            );
            $anneeScolaireId = $anneeScolaire['id'] ?? null;

            $count = 0;
            foreach ($absents as $eleveId) {
                // Récupérer le motif individuel pour cet élève
                $motifIndividuel = $_POST["motif_$eleveId"] ?? '';
                
                $data = [
                    'eleve_id' => $eleveId,
                    'classe_id' => $classeId,
                    'annee_scolaire_id' => $anneeScolaireId,
                    'date_absence' => $dateAbsence,
                    'type' => 'absence',
                    'periode' => 'journee',
                    'heure_debut' => $heureDebut,
                    'heure_fin' => $heureFin,
                    'motif' => $motifIndividuel,
                    'justifiee' => $justifieeDefault,
                    'saisi_par' => $currentUserId,
                ];
                
                $this->absenceModel->create($data);
                $count++;
            }
            
            $_SESSION['success'] = "$count absence(s) enregistrée(s) avec succès";
            $this->redirect('absences/list');
        } else {
            // Charger les classes pour le formulaire
            $classes = $this->absenceModel->query(
                "SELECT c.id, c.code as libelle, cy.libelle as cycle_nom
                 FROM classes c
                 JOIN niveaux n ON c.niveau_id = n.id
                 JOIN cycles cy ON n.cycle_id = cy.id
                 JOIN annees_scolaires a ON c.annee_scolaire_id = a.id
                 WHERE a.actif = 1 AND c.statut = 'actif'
                 ORDER BY cy.ordre, n.ordre, c.code"
            );
            
            $this->view('absences/add', ['classes' => $classes]);
        }
    }
    
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'eleve_id' => $_POST['eleve_id'] ?? '',
                'date_absence' => $_POST['date_absence'] ?? '',
                'type' => $_POST['type'] ?? 'absence',
                'periode' => $_POST['periode'] ?? 'journee',
                'heure_debut' => !empty($_POST['heure_debut']) ? $_POST['heure_debut'] : null,
                'heure_fin' => !empty($_POST['heure_fin']) ? $_POST['heure_fin'] : null,
                'motif' => $_POST['motif'] ?? '',
                'justifiee' => isset($_POST['justifiee']) ? 1 : 0,
            ];
            
            // Note: On ne met pas à jour classe_id lors de l'édition pour éviter incohérence 
            // si l'élève a changé de classe entre temps (historique).
            
            $this->absenceModel->update($id, $data);
            $this->redirect('absences/details/' . $id);
        } else {
            $absence = $this->absenceModel->find($id);
            if (!$absence) {
                http_response_code(404);
                die("Absence non trouvée");
            }
            $eleves = $this->absenceModel->query("SELECT id, matricule, nom, prenom FROM eleves WHERE statut = 'actif' ORDER BY nom ASC");
            $this->view('absences/edit', ['absence' => $absence, 'eleves' => $eleves]);
        }
    }

    /**
     * Recherche autocomplete pour les élèves
     */
    public function searchEleves() {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            $this->json([]);
            return;
        }
        
        // Essayer avec statut='actif', sinon sans
        try {
            $eleves = $this->absenceModel->query(
                "SELECT id, matricule, nom, prenom 
                 FROM eleves 
                 WHERE (matricule LIKE ? OR nom LIKE ? OR prenom LIKE ? OR CONCAT(nom, ' ', prenom) LIKE ?)
                 AND statut = 'actif'
                 ORDER BY nom, prenom
                 LIMIT 10",
                ["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"]
            );
        } catch (PDOException $e) {
             // Fallback générique si erreur (ex: colonne statut introuvable, peu probable ici)
             error_log("Erreur recherche élèves: " . $e->getMessage());
             $this->json([]);
             return;
        }
        
        $results = [];
        foreach ($eleves as $eleve) {
            $results[] = [
                'id' => $eleve['id'],
                'matricule' => $eleve['matricule'] ?? '',
                'nom' => $eleve['nom'] ?? '',
                'prenom' => $eleve['prenom'] ?? '',
                'display' => ($eleve['matricule'] ?? '') . ' - ' . ($eleve['nom'] ?? '') . ' ' . ($eleve['prenom'] ?? '')
            ];
        }
        
        $this->json($results);
    }
    
    /**
     * Affiche les détails d'une absence/retard
     */
    public function details($id) {
        $absence = $this->absenceModel->queryOne(
            "SELECT a.*, 
                    e.matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                    c.nom as classe_nom,
                    u1.username as saisi_par_username,
                    u2.username as valide_par_username
             FROM absences a
             JOIN eleves e ON a.eleve_id = e.id
             JOIN classes c ON a.classe_id = c.id
             LEFT JOIN users u1 ON a.saisi_par = u1.id
             LEFT JOIN users u2 ON a.valide_par = u2.id
             WHERE a.id = ?",
            [$id]
        );
        
        if (!$absence) {
            http_response_code(404);
            die("Absence/Retard non trouvé(e)");
        }
        
        $this->view('absences/details', ['absence' => $absence]);
    }
    
    /**
     * Supprime une absence/retard
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->absenceModel->delete($id);
            $_SESSION['success'] = "Absence/Retard supprimé(e) avec succès";
            $this->redirect('absences/list');
        } else {
            $absence = $this->absenceModel->find($id);
            if (!$absence) {
                http_response_code(404);
                die("Absence/Retard non trouvé(e)");
            }
            $this->view('absences/delete', ['absence' => $absence]);
        }
    }

    /**
     * Récupère la liste des élèves d'une classe (API JSON)
     */
    public function getElevesClasse() {
        $classeId = $_GET['classe_id'] ?? '';
        
        if (!$classeId) {
            $this->json([]);
            return;
        }

        // Essayer d'abord avec inscriptions
        $eleves = $this->absenceModel->query(
            "SELECT DISTINCT e.id, e.matricule, e.nom, e.prenom
             FROM eleves e
             JOIN inscriptions i ON e.id = i.eleve_id
             WHERE i.classe_id = ? 
               AND i.statut IN ('active', 'validee', 'en_cours')
               AND e.statut = 'actif'
             ORDER BY e.nom, e.prenom",
            [$classeId]
        );

        // Si aucun élève trouvé, essayer sans le statut de l'inscription
        if (empty($eleves)) {
            $eleves = $this->absenceModel->query(
                "SELECT DISTINCT e.id, e.matricule, e.nom, e.prenom
                 FROM eleves e
                 JOIN inscriptions i ON e.id = i.eleve_id
                 WHERE i.classe_id = ? 
                   AND e.statut = 'actif'
                 ORDER BY e.nom, e.prenom",
                [$classeId]
            );
        }

        $this->json($eleves);
    }

    /**
     * Récupère les emplois du temps d'une classe pour une date donnée (API JSON)
     */
    public function getEmploisTemps() {
        $classeId = $_GET['classe_id'] ?? '';
        $date = $_GET['date'] ?? '';
        
        if (!$classeId || !$date) {
            $this->json([]);
            return;
        }

        // Déterminer le jour de la semaine
        $jourSemaine = strtolower(date('l', strtotime($date)));
        $joursMap = [
            'monday' => 'lundi',
            'tuesday' => 'mardi',
            'wednesday' => 'mercredi',
            'thursday' => 'jeudi',
            'friday' => 'vendredi',
            'saturday' => 'samedi'
        ];
        $jour = $joursMap[$jourSemaine] ?? '';

        if (!$jour) {
            $this->json([]);
            return;
        }

        $emploisTemps = $this->absenceModel->query(
            "SELECT et.id, et.heure_debut, et.heure_fin,
                    m.nom as matiere_nom,
                    CONCAT(p.nom, ' ', p.prenom) as enseignant_nom
             FROM emplois_temps et
             JOIN matieres m ON et.matiere_id = m.id
             LEFT JOIN personnels p ON et.personnel_id = p.id
             JOIN annees_scolaires a ON et.annee_scolaire_id = a.id
             WHERE et.classe_id = ? 
               AND et.jour_semaine = ?
               AND a.actif = 1
               AND et.actif = 1
             ORDER BY et.heure_debut",
            [$classeId, $jour]
        );

        $this->json($emploisTemps);
    }

    /**
     * Récupère les absences récentes des élèves d'une classe (API JSON)
     */
    public function getAbsencesRecentes() {
        $classeId = $_GET['classe_id'] ?? '';
        $date = $_GET['date'] ?? '';
        
        if (!$classeId || !$date) {
            $this->json([]);
            return;
        }

        // Récupérer les absences des 7 derniers jours avant la date donnée
        $absences = $this->absenceModel->query(
            "SELECT a.eleve_id, a.motif, a.date_absence
             FROM absences a
             WHERE a.classe_id = ? 
               AND a.date_absence < ?
               AND a.date_absence >= DATE_SUB(?, INTERVAL 7 DAY)
               AND a.type = 'absence'
             ORDER BY a.date_absence DESC",
            [$classeId, $date, $date]
        );

        // Garder seulement la dernière absence par élève
        $absencesParEleve = [];
        foreach ($absences as $absence) {
            if (!isset($absencesParEleve[$absence['eleve_id']])) {
                $absencesParEleve[$absence['eleve_id']] = $absence;
            }
        }

        $this->json(array_values($absencesParEleve));
    }
}

