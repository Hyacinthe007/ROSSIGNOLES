<?php
/**
 * Script de diagnostic simple - À placer au début de la méthode enregistrer()
 */

// Ajouter ce code au début de PersonnelController::enregistrer()

error_log("=== DÉBUT ENREGISTREMENT PERSONNEL ===");
error_log("SESSION personnel_data: " . print_r($_SESSION['personnel_data'] ?? 'NON DÉFINI', true));
error_log("POST data: " . print_r($_POST, true));
error_log("FILES: " . print_r($_FILES, true));
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);

// Vérifier si la session existe
if (!isset($_SESSION['personnel_data']['type_personnel'])) {
    error_log("ERREUR: type_personnel non défini dans la session");
    $_SESSION['error'] = "Session expirée. Veuillez recommencer.";
    header('Location: ' . url('personnel/nouveau?etape=1'));
    exit;
}

// Le reste du code continue...
