<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Absence;
use App\Models\EmploisTemps;
use App\Models\Classe;
use App\Models\Eleve;

/**
 * Contrôleur pour la gestion des présences par cours
 * Permet de visualiser la liste des élèves présents/absents pour chaque cours
 */
class PresencesController extends BaseController {
    private $absenceModel;
    private $emploiTempsModel;
    private $classeModel;
    private $eleveModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->absenceModel = new Absence();
        $this->emploiTempsModel = new EmploisTemps();
        $this->classeModel = new Classe();
        $this->eleveModel = new Eleve();
    }
    
    /**
     * Page principale : Liste des cours du jour avec statut de présence
     */
    public function index() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $classeId = $_GET['classe_id'] ?? null;
        $enseignantId = $_GET['enseignant_id'] ?? null;
        
        // Récupérer l'année scolaire active
        $anneeScolaire = $this->absenceModel->queryOne(
            "SELECT id FROM annees_scolaires WHERE actif = 1 LIMIT 1"
        );
        $anneeScolaireId = $anneeScolaire['id'] ?? null;
        
        // Déterminer le jour de la semaine
        $jourSemaine = $this->getJourSemaineFr($date);
        
        // Construire la requête pour récupérer les cours
        $sql = "SELECT et.id, et.heure_debut, et.heure_fin,
                       m.nom as matiere_nom, m.code as matiere_code, m.couleur,
                       c.id as classe_id, c.code as classe_code, c.nom as classe_nom,
                       CONCAT(p.nom, ' ', p.prenom) as enseignant_nom,
                       p.id as enseignant_id,
                       (SELECT COUNT(*) FROM inscriptions i 
                        WHERE i.classe_id = c.id 
                        AND i.statut IN ('active', 'validee', 'en_cours')) as nb_eleves_total
                FROM emplois_temps et
                JOIN matieres m ON et.matiere_id = m.id
                JOIN classes c ON et.classe_id = c.id
                LEFT JOIN personnels p ON et.personnel_id = p.id
                WHERE et.annee_scolaire_id = ? 
                  AND et.jour_semaine = ?
                  AND et.actif = 1";
        
        $params = [$anneeScolaireId, $jourSemaine];
        
        if ($classeId) {
            $sql .= " AND et.classe_id = ?";
            $params[] = $classeId;
        }
        
        if ($enseignantId) {
            $sql .= " AND et.personnel_id = ?";
            $params[] = $enseignantId;
        }
        
        $sql .= " ORDER BY et.heure_debut ASC";
        
        $cours = $this->absenceModel->query($sql, $params);
        
        // Pour chaque cours, compter les absents
        foreach ($cours as &$c) {
            $absents = $this->absenceModel->query(
                "SELECT COUNT(*) as count 
                 FROM absences 
                 WHERE classe_id = ? 
                   AND date_absence = ?
                   AND heure_debut = ?
                   AND heure_fin = ?
                   AND type = 'absence'",
                [$c['classe_id'], $date, $c['heure_debut'], $c['heure_fin']]
            );
            
            $c['nb_absents'] = $absents[0]['count'] ?? 0;
            $c['nb_presents'] = $c['nb_eleves_total'] - $c['nb_absents'];
            $c['taux_presence'] = $c['nb_eleves_total'] > 0 
                ? round(($c['nb_presents'] / $c['nb_eleves_total']) * 100, 1) 
                : 0;
        }
        
        // Récupérer la liste des classes pour le filtre
        $classes = $this->absenceModel->query(
            "SELECT c.id, c.code, c.nom
             FROM classes c
             JOIN annees_scolaires a ON c.annee_scolaire_id = a.id
             WHERE a.actif = 1 AND c.statut = 'actif'
             ORDER BY c.code"
        );
        
        // Récupérer la liste des enseignants pour le filtre
        $enseignants = $this->absenceModel->query(
            "SELECT DISTINCT p.id, CONCAT(p.nom, ' ', p.prenom) as nom_complet
             FROM personnels p
             JOIN emplois_temps et ON et.personnel_id = p.id
             WHERE et.annee_scolaire_id = ?
             ORDER BY p.nom, p.prenom",
            [$anneeScolaireId]
        );
        
        $this->view('presences/index', [
            'cours' => $cours,
            'date' => $date,
            'classe_id' => $classeId,
            'enseignant_id' => $enseignantId,
            'classes' => $classes,
            'enseignants' => $enseignants
        ]);
    }
    
    /**
     * Affiche la liste détaillée des élèves présents/absents pour un cours
     */
    public function detailsCours() {
        $emploiTempsId = $_GET['emploi_temps_id'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d');
        
        if (!$emploiTempsId) {
            $_SESSION['error'] = "Cours non spécifié";
            $this->redirect('presences');
            return;
        }
        
        // Récupérer les informations du cours
        $cours = $this->absenceModel->queryOne(
            "SELECT et.id, et.heure_debut, et.heure_fin, et.jour_semaine,
                    m.nom as matiere_nom, m.code as matiere_code, m.couleur,
                    c.id as classe_id, c.code as classe_code, c.nom as classe_nom,
                    CONCAT(p.nom, ' ', p.prenom) as enseignant_nom,
                    p.id as enseignant_id
             FROM emplois_temps et
             JOIN matieres m ON et.matiere_id = m.id
             JOIN classes c ON et.classe_id = c.id
             LEFT JOIN personnels p ON et.personnel_id = p.id
             WHERE et.id = ?",
            [$emploiTempsId]
        );
        
        if (!$cours) {
            $_SESSION['error'] = "Cours non trouvé";
            $this->redirect('presences');
            return;
        }
        
        // Récupérer tous les élèves de la classe
        $eleves = $this->absenceModel->query(
            "SELECT DISTINCT e.id, e.matricule, e.nom, e.prenom, e.photo
             FROM eleves e
             JOIN inscriptions i ON e.id = i.eleve_id
             WHERE i.classe_id = ? 
               AND i.statut IN ('active', 'validee', 'en_cours')
               AND e.statut = 'actif'
             ORDER BY e.nom, e.prenom",
            [$cours['classe_id']]
        );
        
        // Récupérer les absences pour ce cours à cette date
        $absences = $this->absenceModel->query(
            "SELECT a.*, e.id as eleve_id
             FROM absences a
             JOIN eleves e ON a.eleve_id = e.id
             WHERE a.classe_id = ? 
               AND a.date_absence = ?
               AND a.heure_debut = ?
               AND a.heure_fin = ?
               AND a.type = 'absence'",
            [$cours['classe_id'], $date, $cours['heure_debut'], $cours['heure_fin']]
        );
        
        // Créer un tableau des absents pour faciliter la recherche
        $absentsIds = [];
        $absencesMap = [];
        foreach ($absences as $absence) {
            $absentsIds[] = $absence['eleve_id'];
            $absencesMap[$absence['eleve_id']] = $absence;
        }
        
        // Marquer chaque élève comme présent ou absent
        foreach ($eleves as &$eleve) {
            $eleve['present'] = !in_array($eleve['id'], $absentsIds);
            $eleve['absence'] = $absencesMap[$eleve['id']] ?? null;
        }
        
        // Statistiques
        $stats = [
            'total' => count($eleves),
            'presents' => count(array_filter($eleves, fn($e) => $e['present'])),
            'absents' => count($absentsIds),
            'taux_presence' => count($eleves) > 0 
                ? round((count(array_filter($eleves, fn($e) => $e['present'])) / count($eleves)) * 100, 1)
                : 0
        ];
        
        $this->view('presences/details_cours', [
            'cours' => $cours,
            'date' => $date,
            'eleves' => $eleves,
            'stats' => $stats
        ]);
    }
    
    /**
     * Historique des cours avec statistiques de présence
     */
    public function historique() {
        $classeId = $_GET['classe_id'] ?? null;
        $enseignantId = $_GET['enseignant_id'] ?? null;
        $dateDebut = $_GET['date_debut'] ?? date('Y-m-d', strtotime('-30 days'));
        $dateFin = $_GET['date_fin'] ?? date('Y-m-d');
        
        // Récupérer l'année scolaire active
        $anneeScolaire = $this->absenceModel->queryOne(
            "SELECT id FROM annees_scolaires WHERE actif = 1 LIMIT 1"
        );
        $anneeScolaireId = $anneeScolaire['id'] ?? null;
        
        // Récupérer tous les cours de l'emploi du temps
        $sql = "SELECT et.id, et.heure_debut, et.heure_fin, et.jour_semaine,
                       m.nom as matiere_nom, m.code as matiere_code,
                       c.id as classe_id, c.code as classe_code, c.nom as classe_nom,
                       CONCAT(p.nom, ' ', p.prenom) as enseignant_nom,
                       p.id as enseignant_id
                FROM emplois_temps et
                JOIN matieres m ON et.matiere_id = m.id
                JOIN classes c ON et.classe_id = c.id
                LEFT JOIN personnels p ON et.personnel_id = p.id
                WHERE et.annee_scolaire_id = ? 
                  AND et.actif = 1";
        
        $params = [$anneeScolaireId];
        
        if ($classeId) {
            $sql .= " AND et.classe_id = ?";
            $params[] = $classeId;
        }
        
        if ($enseignantId) {
            $sql .= " AND et.personnel_id = ?";
            $params[] = $enseignantId;
        }
        
        $sql .= " ORDER BY c.code, et.jour_semaine, et.heure_debut";
        
        $cours = $this->absenceModel->query($sql, $params);
        
        // Générer les dates dans la période
        $dates = [];
        $currentDate = strtotime($dateDebut);
        $endDate = strtotime($dateFin);
        
        while ($currentDate <= $endDate) {
            $dates[] = date('Y-m-d', $currentDate);
            $currentDate = strtotime('+1 day', $currentDate);
        }
        
        // Pour chaque cours, calculer les statistiques sur la période
        $historique = [];
        foreach ($cours as $c) {
            $nbCoursEffectues = 0;
            $totalPresents = 0;
            $totalAbsents = 0;
            
            foreach ($dates as $date) {
                $jourDate = $this->getJourSemaineFr($date);
                
                // Vérifier si ce cours a lieu ce jour
                if ($jourDate === $c['jour_semaine']) {
                    $nbCoursEffectues++;
                    
                    // Compter les absents pour ce cours à cette date
                    $absents = $this->absenceModel->query(
                        "SELECT COUNT(*) as count 
                         FROM absences 
                         WHERE classe_id = ? 
                           AND date_absence = ?
                           AND heure_debut = ?
                           AND heure_fin = ?
                           AND type = 'absence'",
                        [$c['classe_id'], $date, $c['heure_debut'], $c['heure_fin']]
                    );
                    
                    $nbAbsents = $absents[0]['count'] ?? 0;
                    $totalAbsents += $nbAbsents;
                }
            }
            
            // Récupérer le nombre total d'élèves dans la classe
            $nbEleves = $this->absenceModel->queryOne(
                "SELECT COUNT(*) as count 
                 FROM inscriptions 
                 WHERE classe_id = ? 
                 AND statut IN ('active', 'validee', 'en_cours')",
                [$c['classe_id']]
            );
            
            $nbElevesTotal = $nbEleves['count'] ?? 0;
            $totalPresents = ($nbCoursEffectues * $nbElevesTotal) - $totalAbsents;
            
            $historique[] = [
                'cours' => $c,
                'nb_cours_effectues' => $nbCoursEffectues,
                'nb_eleves_total' => $nbElevesTotal,
                'total_presents' => $totalPresents,
                'total_absents' => $totalAbsents,
                'taux_presence_moyen' => $nbCoursEffectues > 0 && $nbElevesTotal > 0
                    ? round(($totalPresents / ($nbCoursEffectues * $nbElevesTotal)) * 100, 1)
                    : 0
            ];
        }
        
        // Récupérer la liste des classes pour le filtre
        $classes = $this->absenceModel->query(
            "SELECT c.id, c.code, c.nom
             FROM classes c
             JOIN annees_scolaires a ON c.annee_scolaire_id = a.id
             WHERE a.actif = 1 AND c.statut = 'actif'
             ORDER BY c.code"
        );
        
        // Récupérer la liste des enseignants pour le filtre
        $enseignants = $this->absenceModel->query(
            "SELECT DISTINCT p.id, CONCAT(p.nom, ' ', p.prenom) as nom_complet
             FROM personnels p
             JOIN emplois_temps et ON et.personnel_id = p.id
             WHERE et.annee_scolaire_id = ?
             ORDER BY p.nom, p.prenom",
            [$anneeScolaireId]
        );
        
        $this->view('presences/historique', [
            'historique' => $historique,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'classe_id' => $classeId,
            'enseignant_id' => $enseignantId,
            'classes' => $classes,
            'enseignants' => $enseignants
        ]);
    }
    
    /**
     * Convertit une date en jour de la semaine en français
     */
    private function getJourSemaineFr($date) {
        $jourSemaine = strtolower(date('l', strtotime($date)));
        $joursMap = [
            'monday' => 'lundi',
            'tuesday' => 'mardi',
            'wednesday' => 'mercredi',
            'thursday' => 'jeudi',
            'friday' => 'vendredi',
            'saturday' => 'samedi',
            'sunday' => 'dimanche'
        ];
        return $joursMap[$jourSemaine] ?? '';
    }
}
