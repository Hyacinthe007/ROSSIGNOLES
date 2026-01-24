<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ExamenFinal;
use App\Models\Interrogation;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Periode;
use App\Models\Matiere;

/**
 * Contrôleur des évaluations (Examens et Interrogations)
 */

class EvaluationsController extends BaseController {
    
    public function __construct() {
        $this->requireAuth();
    }

    /**
     * Page principale des évaluations (Dashboard)
     */
    public function index() {
        $this->view('evaluations/index');
    }
}
