<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Eleve;
use App\Models\Personnel;
use App\Models\Classe;
use App\Models\Paiement;
use App\Models\AnneeScolaire;
use App\Models\Absence;

/**
 * Contrôleur du tableau de bord
 */

class DashboardController extends BaseController {
    
    public function index() {
        $this->requireAuth();
        
        $eleveModel = new Eleve();
        $personnelModel = new Personnel();
        $classeModel = new Classe();
        $paiementModel = new Paiement();
        $anneeModel = new AnneeScolaire();
        
        // Récupération de l'année active
        $anneeActive = $anneeModel->getActive();
        $anneeId = $anneeActive ? $anneeActive['id'] : null;
        
        // Statistiques réelles
        // Pour les élèves, on compte ceux qui ont une inscription validée cette année
        $elevesCount = 0;
        if ($anneeId) {
            $result = $eleveModel->queryOne(
                "SELECT COUNT(DISTINCT eleve_id) as total FROM inscriptions WHERE annee_scolaire_id = ? AND statut = 'validee'",
                [$anneeId]
            );
            $elevesCount = $result['total'] ?? 0;
        } else {
            // Fallback: total élèves actifs
            $result = $eleveModel->queryOne("SELECT COUNT(*) as total FROM eleves WHERE statut = 'actif'");
            $elevesCount = $result['total'] ?? 0;
        }
        
        // Total classes de l'année
        $classesCount = 0;
        if ($anneeId) {
            $result = $classeModel->queryOne("SELECT COUNT(*) as total FROM classes WHERE annee_scolaire_id = ?", [$anneeId]);
            $classesCount = $result['total'] ?? 0;
        }
        
        // Total enseignants actifs
        $result = $personnelModel->queryOne("SELECT COUNT(*) as total FROM personnels WHERE statut = 'actif' AND type_personnel = 'enseignant'");
        $ensCount = $result['total'] ?? 0;
        
        // Paiements du mois en cours (filtrés par année scolaire)
        $debutMois = date('Y-m-01');
        $finMois = date('Y-m-t');
        $paiementsMois = $paiementModel->getTotalEncaisse($debutMois, $finMois, $anneeId);
        
        // Élèves en classe aujourd'hui
        $absenceModel = new Absence();
        $dateAujourdhui = date('Y-m-d');
        $elevesAbsentsAujourdhui = 0;
        
        if ($anneeId) {
            $result = $absenceModel->queryOne(
                "SELECT COUNT(DISTINCT a.eleve_id) as total FROM absences a 
                 INNER JOIN classes c ON a.classe_id = c.id 
                 WHERE a.date_absence = ? AND c.annee_scolaire_id = ? AND a.justifiee = 0",
                [$dateAujourdhui, $anneeId]
            );
            $elevesAbsentsAujourdhui = $result['total'] ?? 0;
        }
        
        $elevesEnClasseAujourdhui = $elevesCount - $elevesAbsentsAujourdhui;
        
        $stats = [
            'total_eleves' => $elevesCount,
            'total_classes' => $classesCount,
            'total_enseignants' => $ensCount,
            'paiements_du_mois' => $paiementsMois,
            'eleves_en_classe_aujourd_hui' => $elevesEnClasseAujourdhui,
            'annee_scolaire' => $anneeActive ? $anneeActive['libelle'] : 'N/A'
        ];

        // Dernières activités relatives à l'utilisateur ou globales pour l'admin
        $logModel = new \App\Models\LogActivite();
        $recentLogs = $logModel->query(
            "SELECT l.*, u.username as user_name 
             FROM logs_activites l 
             LEFT JOIN users u ON l.user_id = u.id 
             ORDER BY l.created_at DESC LIMIT 6"
        );
        
        $this->view('dashboard/index', [
            'stats' => $stats,
            'recentLogs' => $recentLogs
        ]);
    }
}

