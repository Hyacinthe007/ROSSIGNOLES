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
            // Rate limiting - Protection contre les attaques par force brute
            $rateLimiter = new \App\Services\LoginRateLimiter(5, 15);
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $rateLimitKey = $ip . '|' . ($_POST['username'] ?? '');

            if ($rateLimiter->tooManyAttempts($rateLimitKey)) {
                $minutes = $rateLimiter->remainingMinutes($rateLimitKey);
                $error = "Trop de tentatives de connexion. Réessayez dans {$minutes} minute(s).";
                $this->view('auth/login', ['error' => $error]);
                return;
            }

            // Validation consistante
            $validator = new \App\Core\Validator($_POST);
            $isValid = $validator->validate([
                'username' => 'required|min:3',
                'password' => 'required'
            ]);

            if (!$isValid) {
                $_SESSION['errors'] = $validator->getErrors();
                $this->redirect('auth/login');
                return;
            }

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $userModel = new User();
            $user = $userModel->authenticate($username, $password);
            
            if ($user) {
                // Effacer le compteur de tentatives après connexion réussie
                $rateLimiter->clear($rateLimitKey);

                // Régénérer l'ID de session pour prévenir la fixation de session
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                // Photo/avatar en session (si présente)
                if (!empty($user['avatar'])) {
                    $_SESSION['avatar'] = $user['avatar'];
                } else {
                    $_SESSION['avatar'] = null;
                }
                
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
                // Enregistrer la tentative échouée
                $rateLimiter->hit($rateLimitKey);
                $attemptsLeft = 5 - $rateLimiter->getAttempts($rateLimitKey);

                $error = "Identifiants incorrects";
                if ($attemptsLeft > 0 && $attemptsLeft <= 2) {
                    $error .= ". Il vous reste {$attemptsLeft} tentative(s).";
                }
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
        // Nettoyer les données de session puis détruire
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        $this->redirect('/auth/login');
    }
    
    public function passwordReset() {
        // Fonctionnalité non disponible — rediriger avec message informatif
        $_SESSION['error'] = "La réinitialisation du mot de passe n'est pas encore disponible. Veuillez contacter l'administrateur système.";
        $this->redirect('auth/login');
    }
}

