<?php
/**
 * Contrôleur API
 */

require_once __DIR__ . '/BaseController.php';

class ApiController extends BaseController {
    
    public function __construct() {
        // Pas de requireAuth ici, géré par token API
    }
    
    public function eleves() {
        header('Content-Type: application/json');
        // Logique API
        echo json_encode([]);
    }
}

