<?php
/**
 * Contrôleur de base avec méthodes communes
 */

class BaseController {
    
    /**
     * Affiche une vue
     */
    protected function view($viewPath, $data = []) {
        extract($data);
        
        $viewFile = APP_PATH . '/Views/' . $viewPath . '.php';
        
        if (!file_exists($viewFile)) {
            die("Vue non trouvée : {$viewPath}");
        }
        
        // Si mode iframe, on n'inclut pas le header/footer complet ou on utilise un layout minimal
        $isIframe = isset($_GET['iframe']) && $_GET['iframe'] == '1';
        
        if (!$isIframe) {
            require_once APP_PATH . '/Views/layout/header.php';
        } else {
            // En mode iframe, on a quand même besoin des styles CSS de base
            // On ajoute un script pour préserver globalement le mode iframe sur les liens et formulaires
            echo '<!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <script src="https://cdn.tailwindcss.com"></script>
                <script>
                    tailwind.config = {
                        theme: {
                            extend: {
                                fontFamily: {
                                    sans: ["Outfit", "sans-serif"],
                                    mono: ["Outfit", "sans-serif"],
                                    serif: ["Outfit", "sans-serif"],
                                },
                            }
                        }
                    }
                </script>
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
                <link rel="stylesheet" href="' . url('public/assets/css/admin-style.css') . '">
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Pour les liens
                        document.addEventListener("click", function(e) {
                            const link = e.target.closest("a");
                            if (link && link.href && !link.target && !link.href.startsWith("javascript:") && !link.href.startsWith("#")) {
                                try {
                                    const url = new URL(link.href);
                                    if (url.origin === window.location.origin && !url.searchParams.has("iframe")) {
                                        url.searchParams.set("iframe", "1");
                                        link.href = url.toString();
                                    }
                                } catch(err) {}
                            }
                        });
                        
                        // Pour les formulaires
                        document.addEventListener("submit", function(e) {
                            const form = e.target;
                            if (form.method.toLowerCase() === "get") {
                                if (!form.querySelector(\'input[name="iframe"]\')) {
                                    const input = document.createElement("input");
                                    input.type = "hidden";
                                    input.name = "iframe";
                                    input.value = "1";
                                    form.appendChild(input);
                                }
                            } else {
                                try {
                                    const url = new URL(form.action);
                                    if (url.origin === window.location.origin && !url.searchParams.has("iframe")) {
                                        url.searchParams.set("iframe", "1");
                                        form.action = url.toString();
                                    }
                                } catch(err) {}
                            }
                        });
                    });
                </script>
            </head>
            <body class="bg-gray-50 p-4">';
        }
        
        require_once $viewFile;
        
        if (!$isIframe) {
            require_once APP_PATH . '/Views/layout/footer.php';
        } else {
            echo '</body></html>';
        }
    }
    
    /**
     * Retourne une réponse JSON
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Redirige vers une URL
     */
    protected function redirect($url) {
        // Si l'URL ne commence pas par http, utiliser la fonction url()
        if (strpos($url, 'http') !== 0) {
            $url = url($url);
        }

        // Préserver le paramètre iframe lors des redirections
        if (isset($_GET['iframe']) && $_GET['iframe'] == '1') {
            $separator = (strpos($url, '?') !== false) ? '&' : '?';
            $url .= $separator . 'iframe=1';
        }

        header("Location: {$url}");
        exit;
    }
    
    /**
     * Vérifie si l'utilisateur est connecté et gère la déconnexion automatique
     */
    protected function requireAuth() {
        // Durée d'inactivité autorisée (en secondes) - 15 minutes = 900s
        $timeout = 900; 

        if (isset($_SESSION['user_id'])) {
            // Vérifier le dernier moment d'activité
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
                // Trop d'inactivité : déconnexion
                session_unset();
                session_destroy();
                
                // Redémarrer une session pour porter le message d'erreur
                session_start();
                $_SESSION['error'] = "Votre session a expiré après {$timeout} secondes d'inactivité. Veuillez vous reconnecter.";
                
                $this->redirect('auth/login');
                return;
            }
            
            // Mettre à jour le timestamp de dernière activité
            $_SESSION['last_activity'] = time();
        } else {
            // Pas de session active
            $this->redirect('auth/login');
            return;
        }
    }
    
    /**
     * Vérifie les permissions
     */
    protected function requirePermission($permission) {
        $this->requireAuth();
        
        // Les administrateurs ont tous les droits
        if (hasRole('admin')) {
            return;
        }

        // Vérification de la permission spécifique via la session
        if (hasPermission($permission)) {
            return;
        }
        
        // Mode secours/installation : si le système de rôles n'est pas encore peuplé
        // ou si l'utilisateur est le seul actif dans la base.
        require_once APP_PATH . '/Models/User.php';
        $userModel = new User();
        
        try {
            $totalUsersResult = $userModel->queryOne("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
            $userCount = $totalUsersResult['total'] ?? 0;
            
            if ($userCount <= 1) {
                // S'il n'y a qu'un utilisateur, on lui accorde l'accès par défaut (setup)
                return;
            }
        } catch (Exception $e) {
            // Si erreur de table (ex: is_active vs actif), on laisse passer car cela signifie
            // que le système est encore en cours de migration/setup
            return;
        }

        // Sinon, refuser l'accès de manière propre
        http_response_code(403);
        $this->view('errors/403', [
            'permission' => $permission,
            'title' => 'Accès Refusé'
        ]);
        exit;
    }
}

