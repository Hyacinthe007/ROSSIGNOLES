<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Paiement;

/**
 * Contrôleur de la caisse consolidée
 */

class FinanceCaisseController extends BaseController {
    private $paiementModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->requirePermission('finance.caisse');
        $this->paiementModel = new Paiement();
    }
    
    public function index() {
        // Résumé de la caisse
        $stats = $this->paiementModel->queryOne(
            "SELECT 
                COUNT(*) as nb_paiements,
                SUM(montant) as total_caisse,
                SUM(CASE WHEN date_paiement = CURRENT_DATE THEN montant ELSE 0 END) as total_jour
             FROM paiements"
        );
        
        $derniersPaiements = $this->paiementModel->query(
            "SELECT p.*, e.nom, e.prenom, mp.libelle as mode_paiement
             FROM paiements p
             JOIN factures f ON p.facture_id = f.id
             JOIN eleves e ON f.eleve_id = e.id
             JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
             ORDER BY p.date_paiement DESC, p.id DESC
             LIMIT 10"
        );
        
        $this->view('finance/caisse_index', [
            'stats' => $stats,
            'derniersPaiements' => $derniersPaiements
        ]);
    }
    
    public function journal() {
        $date = $_GET['date'] ?? date('Y-m-d');
        
        $paiements = $this->paiementModel->query(
            "SELECT p.*, e.nom, e.prenom, mp.libelle as mode_paiement, f.numero_facture
             FROM paiements p
             JOIN factures f ON p.facture_id = f.id
             JOIN eleves e ON f.eleve_id = e.id
             JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
             WHERE DATE(p.date_paiement) = ?
             ORDER BY p.id ASC",
            [$date]
        );
        
        $this->view('finance/journal_caisse', [
            'paiements' => $paiements,
            'date' => $date
        ]);
    }
}
