<?php
/**
 * Script utilitaire universel pour exécuter les migrations SQL
 * Usage: php database/run_migration.php [nom_du_fichier.sql]
 */
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once APP_PATH . '/Helpers/functions.php';
require_once APP_PATH . '/Models/BaseModel.php';

use App\Models\BaseModel;

try {
    $db = BaseModel::getDBConnection();
    
    // Déterminer le fichier à migrer
    $fileName = $argv[1] ?? '2026_02_15_phase2_evaluations.sql';
    $sqlFile = strpos($fileName, DIRECTORY_SEPARATOR) !== false ? $fileName : ROOT_PATH . '/database/migrations/' . $fileName;

    if (!file_exists($sqlFile)) {
        die("❌ Erreur : Fichier SQL non trouvé : $sqlFile\n");
    }

    echo "--- Exécution de la migration : " . basename($sqlFile) . " ---\n";
    $sql = file_get_contents($sqlFile);
    
    // Nettoyer et découper les requêtes
    $sql = preg_replace('/^DELIMITER .*$/m', '', $sql);
    $queries = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($queries as $i => $query) {
        if (empty($query)) continue;
        try {
            $db->exec($query);
        } catch (Exception $e) {
            // Ignorer les erreurs "déjà existant"
            if (strpos($e->getMessage(), 'Duplicate') === false && strpos($e->getMessage(), 'already exists') === false) {
                echo "⚠️ Erreur à la requête " . ($i+1) . " : " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "✅ Migration réussie !\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
