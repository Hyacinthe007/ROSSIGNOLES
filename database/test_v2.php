<?php
/**
 * Script de test pour ROSSIGNOLES V2
 */
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');

require_once APP_PATH . '/Helpers/functions.php';
require_once APP_PATH . '/Models/BaseModel.php';
require_once APP_PATH . '/Models/EvenementCalendrier.php';
require_once APP_PATH . '/Core/Cache.php';
require_once APP_PATH . '/Core/EventDispatcher.php';
require_once APP_PATH . '/Middleware/AuditMiddleware.php';

use App\Models\EvenementCalendrier;
use App\Core\Cache;
use App\Core\EventDispatcher;
use App\Middleware\AuditMiddleware;

session_start();
$_SESSION['user_id'] = 1; // Simuler un utilisateur admin
$_SESSION['username'] = 'AdminTest';

try {
    echo "--- Test ROSSIGNOLES V2 ---\n";

    // 1. Test du Cache
    echo "1. Test Cache : ";
    Cache::put('test_key', 'Hello V2', 60);
    $val = Cache::get('test_key');
    if ($val === 'Hello V2') {
        echo "✅ OK\n";
    } else {
        echo "❌ Échec (reçu: $val)\n";
    }

    // 2. Test EventDispatcher
    echo "2. Test EventDispatcher : ";
    $eventTriggered = false;
    EventDispatcher::listen('test.event', function($data) use (&$eventTriggered) {
        $eventTriggered = true;
    });
    EventDispatcher::dispatch('test.event');
    echo $eventTriggered ? "✅ OK\n" : "❌ Échec\n";

    // 3. Test Audit Automatique (via EvenementCalendrier)
    echo "3. Test Audit Automatique : ";
    $evtModel = new EvenementCalendrier();
    
    // Créer un événement test
    $id = $evtModel->create([
        'annee_scolaire_id' => 1,
        'type' => 'autre',
        'libelle' => 'Événement Test V2',
        'date_debut' => date('Y-m-d'),
        'date_fin' => date('Y-m-d'),
        'bloque_cours' => 0
    ]);
    
    // Vérifier si une entrée d'audit a été créée
    $audit = AuditMiddleware::getHistory('evenements_calendrier', (int)$id);
    if (!empty($audit)) {
        echo "✅ OK (Audit ID: {$audit[0]['id']})\n";
    } else {
        echo "❌ Échec (Pas d'audit trouvé pour ID $id)\n";
    }

    // Nettoyage partiel
    $evtModel->delete($id);
    echo "Actions terminées.\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
