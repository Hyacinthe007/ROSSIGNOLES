<?php
/**
 * Point d'entrée de l'application ERP École ROSSIGNOLES
 */

// ── Vérification de la version PHP minimale ──────────────────────────────────
define('REQUIRED_PHP_VERSION', '8.2.12');

if (version_compare(PHP_VERSION, REQUIRED_PHP_VERSION, '<')) {
    http_response_code(503);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>Version PHP insuffisante - ROSSIGNOLES</title>'
       . '<style>body{font-family:system-ui,-apple-system,sans-serif;background:#f9fafb;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}'
       . '.box{background:#fff;border-radius:1rem;padding:2.5rem;max-width:520px;width:90%;text-align:center;border-top:4px solid #f59e0b}'
       . 'h1{color:#92400e;font-size:1.4rem;margin-bottom:.5rem}'
       . 'p{color:#4b5563;line-height:1.7;margin:.5rem 0}'
       . 'code{background:#fef3c7;padding:.15rem .5rem;border-radius:.25rem;font-size:.9rem;color:#92400e}</style></head>'
       . '<body><div class="box">'
       . '<h1>⚠ Version PHP insuffisante</h1>'
       . '<p>L\'application ROSSIGNOLES nécessite <strong>PHP ' . REQUIRED_PHP_VERSION . '</strong> ou supérieur.</p>'
       . '<p>Version actuellement installée : <code>PHP ' . PHP_VERSION . '</code></p>'
       . '<p style="font-size:.85rem;color:#6b7280;margin-top:1.5rem">Contactez votre administrateur système pour mettre à jour PHP.</p>'
       . '</div></body></html>';
    exit(1);
}

// Charger l'autoloader de Composer si disponible
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Configurer les paramètres de session pour plus de sécurité
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', '1');
}

// Configurer l'encodage UTF-8
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Démarrer la session
session_start();

// Définir les constantes de base
define('ROOT_PATH', __DIR__);
define('APP_PATH', __DIR__ . '/app');
define('CONFIG_PATH', __DIR__ . '/config');
define('STORAGE_PATH', __DIR__ . '/storage');
define('PUBLIC_PATH', __DIR__ . '/public');

// Charger la configuration
$config = require CONFIG_PATH . '/app.php';

// Définir le timezone
date_default_timezone_set($config['timezone']);

// Charger les helpers
require_once APP_PATH . '/Helpers/functions.php';

// Utiliser les classes avec namespaces
use App\Middleware\CsrfMiddleware;
use App\Core\Router;

// Charger les routes
$webRoutes = require __DIR__ . '/routes/web.php';
$apiRoutes = require __DIR__ . '/routes/api.php';
$routes = array_merge($webRoutes, $apiRoutes);

// Créer une instance du routeur
$router = new Router();

// Enregistrer toutes les routes
foreach ($routes as $route) {
    $router->addRoute(
        $route['pattern'],
        $route['handler'],
        $route['method'] ?? 'GET'
    );
}

// Récupérer l'URI demandée
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Retirer le préfixe de base si présent
$basePath = '/ROSSIGNOLES';
if (stripos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Retirer index.php de l'URI si présent
if (stripos($requestUri, '/index.php') === 0) {
    $requestUri = substr($requestUri, strlen('/index.php'));
}

// Retirer les paramètres de query string
if (($pos = strpos($requestUri, '?')) !== false) {
    $requestUri = substr($requestUri, 0, $pos);
}

// Nettoyer l'URI
$requestUri = trim($requestUri, '/');

// Si l'URI est vide, rediriger vers le dashboard ou la page de login
if (empty($requestUri)) {
    if (isset($_SESSION['user_id'])) {
        $requestUri = 'dashboard';
    } else {
        $requestUri = 'auth/login';
    }
}

// Initialiser et vérifier le CSRF
CsrfMiddleware::init();
CsrfMiddleware::verify();

// Dispatcher la requête
try {
    $router->dispatch($requestUri, $requestMethod);
} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    
    $isDev = ini_get('display_errors');
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Erreur Système - ROSSIGNOLES</title>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Outfit', sans-serif; background-color: #f3f4f6; color: #1f2937; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
            .container { background: white; padding: 2.5rem; border-radius: 1rem; shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); max-width: 600px; width: 90%; text-align: center; border-top: 4px solid #ef4444; }
            h1 { font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #b91c1c; }
            p { color: #4b5563; line-height: 1.6; margin-bottom: 2rem; }
            .error-box { text-align: left; background: #fee2e2; padding: 1rem; border-radius: 0.5rem; font-family: 'Outfit', sans-serif; font-size: 0.875rem; color: #991b1b; overflow-x: auto; margin-bottom: 1rem; }
            .btn { display: inline-block; background: #1f2937; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; transition: background 0.2s; }
            .btn:hover { background: #374151; }
        </style>
    </head>
    <body>
        <div class="container">
            <svg style="width: 64px; height: 64px; color: #ef4444; margin: 0 auto 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h1>Une erreur est survenue</h1>
            <?php if ($isDev): ?>
                <p><strong>Message :</strong> <?= htmlspecialchars($e->getMessage()) ?></p>
                <div class="error-box">
                    <strong>Trace :</strong><br>
                    <?= nl2br(htmlspecialchars($e->getTraceAsString())) ?>
                </div>
            <?php else: ?>
                <p>Oups ! Quelque chose s'est mal passé. Notre équipe technique a été informée et travaille sur la résolution du problème.</p>
            <?php endif; ?>
            <a href="/ROSSIGNOLES/dashboard" class="btn">Retour au tableau de bord</a>
        </div>
    </body>
    </html>
    <?php
}
