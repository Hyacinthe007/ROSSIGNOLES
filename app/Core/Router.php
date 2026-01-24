<?php
declare(strict_types=1);

namespace App\Core;

use Exception;

/**
 * Routeur de l'application
 * Gère le dispatching des requêtes vers les contrôleurs appropriés
 */

class Router {
    private $routes = [];
    
    /**
     * Ajoute une route
     */
    public function addRoute($pattern, $handler, $method = 'GET') {
        $this->routes[] = [
            'pattern' => $pattern,
            'handler' => $handler,
            'method' => strtoupper($method)
        ];
    }
    
    /**
     * Dispatch une requête
     */
    public function dispatch($uri, $method = 'GET') {
        $method = strtoupper($method);
        
        foreach ($this->routes as $route) {
            // Vérifier la méthode HTTP
            if ($route['method'] !== $method) {
                continue;
            }
            
            // Convertir le pattern en regex
            $pattern = $this->convertPatternToRegex($route['pattern']);
            
            // Tester si l'URI correspond au pattern
            if (preg_match($pattern, $uri, $matches)) {
                // Extraire les paramètres
                array_shift($matches); // Retirer le match complet
                
                // Appeler le handler
                return $this->callHandler($route['handler'], $matches);
            }
        }
        
        // Aucune route trouvée
        $this->notFound();
    }
    
    /**
     * Convertit un pattern de route en expression régulière
     */
    private function convertPatternToRegex($pattern) {
        // Échapper les slashes
        $pattern = str_replace('/', '\/', $pattern);
        
        // Remplacer les paramètres {id} par des groupes de capture
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^\/]+)', $pattern);
        
        // Ajouter les délimiteurs
        return '/^' . $pattern . '$/';
    }
    
    /**
     * Appelle le handler (contrôleur@méthode)
     */
    private function callHandler($handler, $params = []) {
        // Séparer le contrôleur et la méthode
        list($controllerName, $methodName) = explode('@', $handler);
        
        // Nom complet de la classe avec namespace
        $fullControllerName = "\\App\\Controllers\\" . $controllerName;
        
        // Vérifier que la classe existe (l'autoloader PSR-4 s'occupe du chargement)
        if (!class_exists($fullControllerName)) {
            // Fallback pour le développement: tenter de charger le fichier si l'autoloader échoue
            $controllerFile = APP_PATH . '/Controllers/' . $controllerName . '.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
            }
            
            if (!class_exists($fullControllerName)) {
                throw new Exception("Classe de contrôleur non trouvée : {$fullControllerName}");
            }
        }
        
        $controller = new $fullControllerName();
        
        // Vérifier que la méthode existe
        if (!method_exists($controller, $methodName)) {
            throw new Exception("Méthode non trouvée : {$fullControllerName}@{$methodName}");
        }
        
        // Appeler la méthode avec les paramètres
        return call_user_func_array([$controller, $methodName], $params);
    }
    
    /**
     * Gère les erreurs 404
     */
    private function notFound() {
        http_response_code(404);
        
        // Vérifier si une vue 404 existe
        $view404 = APP_PATH . '/Views/errors/404.php';
        
        if (file_exists($view404)) {
            require $view404;
        } else {
            echo '<h1>404 - Page non trouvée</h1>';
            echo '<p>La page que vous recherchez n\'existe pas.</p>';
            echo '<p><a href="/ROSSIGNOLES/dashboard">Retour au dashboard</a></p>';
        }
        
        exit;
    }
}
