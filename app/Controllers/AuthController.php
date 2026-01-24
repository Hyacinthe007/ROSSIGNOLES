<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\LogActivite;
use App\Models\User;
use App\Models\Permission;

/**
 * Contrôleur d'authentification
 */

class AuthController extends BaseController {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $userModel = new User();
            $user = $userModel->authenticate($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                // Récupération des rôles et permissions
                $roles = $userModel->getRoles($user['id']);
                $roleCodes = array_column($roles, 'code');
                $_SESSION['roles'] = $roleCodes;
                
                $permissions = [];
                if (in_array('admin', $roleCodes) || $user['user_type'] === 'admin') {
                    // Les admins ont toutes les permissions
                    $permModel = new Permission();
                    $allPerms = $permModel->all();
                    $permissions = array_column($allPerms, 'code');
                } else {
                    $userPerms = $userModel->getPermissions($user['id']);
                    $permissions = array_column($userPerms, 'code');
                }
                $_SESSION['permissions'] = array_unique($permissions);
                
                // Champs utilisateur mis à jour dans la session
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['reference_id'] = $user['reference_id'];
                $_SESSION['last_activity'] = time();
                
                // Logging de la connexion
                LogActivite::log('Connexion', 'Authentification', "L'utilisateur {$user['username']} s'est connecté");
                
                $this->redirect('/dashboard');
            } else {
                $error = "Identifiants incorrects";
                $this->view('auth/login', ['error' => $error]);
            }
        } else {
            // Si déjà connecté, rediriger vers le dashboard
            if (isset($_SESSION['user_id'])) {
                $this->redirect('/dashboard');
            }
            $this->view('auth/login');
        }
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            LogActivite::log('Déconnexion', 'Authentification', "L'utilisateur {$_SESSION['username']} s'est déconnecté");
        }
        session_destroy();
        $this->redirect('/auth/login');
    }
    
    public function passwordReset() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            // Logique de réinitialisation
            $this->view('auth/password_reset', ['message' => 'Email envoyé']);
        } else {
            $this->view('auth/password_reset');
        }
    }
}

