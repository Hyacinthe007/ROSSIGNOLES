<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;

/**
 * Service d'authentification
 */

class AuthService {
    
    /**
     * Vérifie les permissions d'un utilisateur
     */
    public function hasPermission($userId, $permission) {
        $userModel = new User();
        
        // Vérification via rôles et permissions
        // À implémenter selon la logique RBAC
        return true;
    }
}

