<?php
/**
 * Service d'authentification
 */

class AuthService {
    
    /**
     * Vérifie les permissions d'un utilisateur
     */
    public function hasPermission($userId, $permission) {
        require_once APP_PATH . '/Models/User.php';
        $userModel = new User();
        
        // Vérification via rôles et permissions
        // À implémenter selon la logique RBAC
        return true;
    }
}

