<?php

namespace App\Middleware;

use App\Core\Exceptions\SecurityException;

/**
 * Middleware de protection CSRF
 */
class CsrfMiddleware {
    
    /**
     * Nom de la clé de session pour le token
     */
    const SESSION_KEY = 'csrf_token';
    
    /**
     * Nom du champ de formulaire / header
     */
    const TOKEN_NAME = 'csrf_token';

    /**
     * Liste des routes exclues du contrôle CSRF (patterns regex)
     */
    private static $excludedRoutes = [
        'auth/login',
        'api/.*' // Exemple pour de futures routes API si nécessaire
    ];

    /**
     * Initialise le token s'il n'existe pas
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Vérifie le token pour les requêtes POST, PUT, DELETE, PATCH
     * 
     * @throws SecurityException Si le token est invalide
     */
    public static function verify() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            // Récupérer l'URI relative pour la comparaison
            $requestUri = self::getNormalizedUri();

            // Vérifier si la route est exclue
            foreach (self::$excludedRoutes as $pattern) {
                if (preg_match('#^' . $pattern . '$#', $requestUri)) {
                    return;
                }
            }

            $token = $_POST[self::TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;
            
            if (!$token) {
                throw new SecurityException('Sécurité : Token CSRF manquant. Veuillez rafraîchir la page.');
            }
            
            if (!$sessionToken) {
                throw new SecurityException('Sécurité : Session expirée. Veuillez rafraîchir la page.');
            }
            
            if (!hash_equals($sessionToken, $token)) {
                throw new SecurityException('Sécurité : Token CSRF invalide. Action non autorisée.');
            }
        }
    }

    /**
     * Récupère l'URI nettoyée pour le middleware
     */
    private static function getNormalizedUri() {
        $uri = $_SERVER['REQUEST_URI'];
        $basePath = '/ROSSIGNOLES';
        
        if (stripos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        if (stripos($uri, '/index.php') === 0) {
            $uri = substr($uri, strlen('/index.php'));
        }
        
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        return trim($uri, '/');
    }

    /**
     * Récupère le token actuel
     */
    public static function getToken() {
        self::init();
        return $_SESSION[self::SESSION_KEY];
    }
}
