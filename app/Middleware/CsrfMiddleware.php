<?php

namespace App\Middleware;

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
     * @throws \Exception Si le token est invalide
     */
    public static function verify() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            // Exclure la route de login du CSRF pour le moment si problème
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            if (strpos($requestUri, '/auth/login') !== false) {
                return;
            }

            $token = $_POST[self::TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;
            
            // Debug logging
            error_log("=== CSRF VERIFICATION ===");
            error_log("Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
            error_log("Method: " . $method);
            error_log("Token from POST: " . ($token ? substr($token, 0, 10) . '...' : 'MISSING'));
            error_log("Token from SESSION: " . ($sessionToken ? substr($sessionToken, 0, 10) . '...' : 'MISSING'));
            error_log("Session ID: " . session_id());
            error_log("========================");
            
            if (!$token) {
                http_response_code(403);
                die('Erreur de sécurité : Token CSRF manquant dans le formulaire. Veuillez rafraîchir la page et réessayer.');
            }
            
            if (!$sessionToken) {
                http_response_code(403);
                die('Erreur de sécurité : Session expirée. Veuillez rafraîchir la page et réessayer.');
            }
            
            if ($token !== $sessionToken) {
                http_response_code(403);
                die('Erreur de sécurité : Token CSRF invalide. Veuillez rafraîchir la page et réessayer.');
            }
        }
    }

    /**
     * Récupère le token actuel
     */
    public static function getToken() {
        self::init();
        return $_SESSION[self::SESSION_KEY];
    }
}
