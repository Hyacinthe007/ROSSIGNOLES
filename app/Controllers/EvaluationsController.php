<?php
/**
 * Contrôleur des évaluations (Examens et Interrogations)
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/ExamenFinal.php';
require_once APP_PATH . '/Models/Interrogation.php';
require_once APP_PATH . '/Models/AnneeScolaire.php';
require_once APP_PATH . '/Models/Classe.php';
require_once APP_PATH . '/Models/Periode.php';
require_once APP_PATH . '/Models/Matiere.php';

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
