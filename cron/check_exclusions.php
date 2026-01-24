<?php
/**
 * Script CRON - Vérification quotidienne des exclusions
 * 
 * À exécuter tous les jours à 6h00 du matin
 * 
 * Configuration CRON (Linux/Mac) :
 * 0 6 * * * /usr/bin/php /path/to/ROSSIGNOLES/cron/check_exclusions.php
 * 
 * Configuration CRON (Windows - Planificateur de tâches) :
 * Programme : C:\path\to\php.exe
 * Arguments : D:\WEB\htdocs\ROSSIGNOLES\cron\check_exclusions.php
 * Déclencheur : Quotidien à 06:00
 */

// Définir les constantes de base
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Charger la configuration
$config = require CONFIG_PATH . '/app.php';
date_default_timezone_set($config['timezone']);

// Charger le service
require_once APP_PATH . '/Services/EcolageService.php';

// Fonction pour logger
function logMessage($message, $type = 'INFO') {
    $logFile = ROOT_PATH . '/storage/logs/exclusions_' . date('Y-m-d') . '.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$type}] {$message}\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Afficher aussi dans la console
    echo $logEntry;
}

try {
    logMessage("=== DÉBUT DE LA VÉRIFICATION DES EXCLUSIONS ===");
    
    // Créer une instance du service
    $ecolageService = new EcolageService();
    
    // Exécuter la vérification
    $resultat = $ecolageService->verifierExclusions();
    
    if ($resultat['success']) {
        logMessage("Vérification terminée avec succès");
        logMessage("Exclusions appliquées : {$resultat['nb_exclusions']}");
        logMessage("Avertissements envoyés : {$resultat['nb_avertissements']}");
        logMessage("Relances envoyées : {$resultat['nb_relances']}");
        
        // Détails des exclusions
        if (!empty($resultat['eleves_exclus'])) {
            logMessage("--- ÉLÈVES EXCLUS ---");
            foreach ($resultat['eleves_exclus'] as $eleve) {
                logMessage("  - {$eleve['nom']} {$eleve['prenom']} ({$eleve['matricule']}) - Impayé: {$eleve['total_impaye']} FCFA");
            }
        }
        
        // Sauvegarder le rapport complet
        $rapportFile = ROOT_PATH . '/storage/reports/exclusions_' . date('Y-m-d') . '.txt';
        $rapportDir = dirname($rapportFile);
        
        if (!file_exists($rapportDir)) {
            mkdir($rapportDir, 0755, true);
        }
        
        file_put_contents($rapportFile, $resultat['rapport']);
        logMessage("Rapport sauvegardé : {$rapportFile}");
        
    } else {
        logMessage("ERREUR lors de la vérification : " . ($resultat['error'] ?? 'Erreur inconnue'), 'ERROR');
        
        // Envoyer une alerte aux administrateurs
        if (class_exists('NotificationService')) {
            $notifService = new NotificationService();
            $notifService->envoyerNotification([
                'destinataire_type' => 'administrateurs',
                'titre' => '🔴 ERREUR - Script d\'exclusions',
                'message' => "Le script de vérification des exclusions a échoué.\n\nErreur : " . ($resultat['error'] ?? 'Erreur inconnue'),
                'priorite' => 'urgente',
                'type' => 'erreur_systeme'
            ]);
        }
    }
    
    logMessage("=== FIN DE LA VÉRIFICATION ===\n");
    
    // Code de sortie
    exit($resultat['success'] ? 0 : 1);
    
} catch (Exception $e) {
    logMessage("EXCEPTION FATALE : " . $e->getMessage(), 'FATAL');
    logMessage("Trace : " . $e->getTraceAsString(), 'FATAL');
    
    // Tentative d'envoi d'alerte
    try {
        if (class_exists('NotificationService')) {
            $notifService = new NotificationService();
            $notifService->envoyerNotification([
                'destinataire_type' => 'administrateurs',
                'titre' => '🔴 ERREUR CRITIQUE - Script d\'exclusions',
                'message' => "Exception fatale dans le script d'exclusions.\n\n" . $e->getMessage(),
                'priorite' => 'urgente',
                'type' => 'erreur_critique'
            ]);
        }
    } catch (Exception $notifError) {
        logMessage("Impossible d'envoyer la notification d'erreur : " . $notifError->getMessage(), 'ERROR');
    }
    
    exit(1);
}
