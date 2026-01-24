<?php
/**
 * Fonctions helper globales
 */

/**
 * Échappe les données pour l'affichage HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Génère une URL
 */
function url($path = '') {
    static $baseUrl = null;
    
    if ($baseUrl === null) {
        $config = require CONFIG_PATH . '/app.php';
        $baseUrl = rtrim($config['app_url'], '/');
        
        // Si app_url n'est pas défini, utiliser le chemin relatif
        if (empty($baseUrl) || $baseUrl === 'http://localhost') {
            $scriptName = dirname($_SERVER['SCRIPT_NAME']);
            $baseUrl = rtrim($scriptName, '/');
        }
    }
    
    $path = ltrim($path, '/');
    return $baseUrl . '/' . $path;
}

/**
 * Génère une URL pour un fichier public
 */
function public_url($path = '') {
    $path = ltrim($path, '/');
    return url('public/' . $path);
}

/**
 * Redirige vers une URL
 */
function redirect($url) {
    header("Location: {$url}");
    exit;
}

/**
 * Obtient une valeur de session
 */
function session($key, $default = null) {
    return $_SESSION[$key] ?? $default;
}

/**
 * Définit une valeur de session
 */
function session_set($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Obtient l'ID de l'utilisateur connecté
 */
function userId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Vérifie si l'utilisateur a un rôle spécifique
 */
function hasRole($roleCode) {
    if (($_SESSION['user_type'] ?? '') === 'admin') return true;
    $roles = $_SESSION['roles'] ?? [];
    return in_array($roleCode, $roles) || in_array('admin', $roles);
}

/**
 * Vérifie si l'utilisateur a une permission spécifique
 */
function hasPermission($permissionCode) {
    if (($_SESSION['user_type'] ?? '') === 'admin' || hasRole('admin')) return true;
    $permissions = $_SESSION['permissions'] ?? [];
    return in_array($permissionCode, $permissions);
}

/**
 * Formate une date
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) {
        return '';
    }
    $dateObj = new DateTime($date);
    return $dateObj->format($format);
}

/**
 * Formate un montant
 */
function formatMoney($amount) {
    return number_format($amount, 0, ',', ' ');
}

/**
 * Génère un matricule automatique selon le type
 * Format: PREFIXE-NUMERO (ex: EL-00001)
 */
function generateMatricule($type, $table = null) {
    $prefixes = [
        'eleve' => 'EL',
        'enseignant' => 'ENS',
        'personnel' => 'PA',
        'materiel' => 'MAT',
        'classe' => 'CLASSE',
        'matiere' => 'MATIERE',
        'parent' => 'PAR',
    ];
    
    $normalizedType = strtolower($type);
    $prefix = $prefixes[$normalizedType] ?? strtoupper(substr($type, 0, 4));
    
    // Initialisation de la connexion BD via le BaseModel si disponible
    $pdo = null;
    $count = 1;

    try {
        if (class_exists('BaseModel')) {
            $pdo = BaseModel::getDBConnection();
        } else {
            $config = require CONFIG_PATH . '/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
            $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        }
        
        // Si une table est fournie, utiliser le MAX(id) pour la suite logique globale
        // Cela permet que l'incrémentation continue même si l'année scolaire change
        if ($table) {
            $stmt = $pdo->query("SELECT MAX(id) as max_id FROM {$table}");
            $result = $stmt->fetch();
            // On utilise MAX(id) + 1 pour garantir un matricule unique basé sur l'ID technique
            $count = ($result['max_id'] ?? 0) + 1;
        }
        
    } catch (Exception $e) {
        // Fallback en cas d'erreur de connexion
        $count = rand(1000, 9999);
    }
    
    // Format final : PREFIXE-NUMERO (ex: EL-00001)
    // Utilise 5 chiffres pour permettre jusqu'à 99999 enregistrements
    return $prefix . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
}

/**
 * Vérifie si une route est active
 */
function isActiveRoute($routePattern) {
    // Obtenir l'URI de la requête actuelle
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $requestUri = parse_url($requestUri, PHP_URL_PATH);
    
    // Nettoyer l'URI
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptName === '/' || $scriptName === '\\' || $scriptName === '.') {
        $scriptName = '';
    }
    
    if (!empty($scriptName) && strpos($requestUri, $scriptName) === 0) {
        $requestUri = substr($requestUri, strlen($scriptName));
    }
    
    if (strpos($requestUri, '/ROSSIGNOLES') === 0) {
        $requestUri = substr($requestUri, strlen('/ROSSIGNOLES'));
    }
    
    $requestUri = trim($requestUri, '/');
    
    // Si vide, c'est le dashboard
    if (empty($requestUri) || $requestUri === 'index.php') {
        $requestUri = 'dashboard';
    }
    
    // Normaliser le pattern de route
    $routePattern = trim($routePattern, '/');
    
    // Vérifier si la route correspond exactement
    if ($requestUri === $routePattern) {
        return true;
    }
    
    // Vérifier si la route commence par le pattern (pour les sous-routes)
    // Par exemple, 'eleves' correspond à 'eleves/list', 'eleves/add', etc.
    if (strpos($requestUri, $routePattern . '/') === 0) {
        return true;
    }
    
    // Cas spécial pour le dashboard
    if ($routePattern === 'dashboard' && $requestUri === 'dashboard') {
        return true;
    }
    
    return false;
}

/**
 * Vérifie si une route est exactement active (correspondance exacte uniquement)
 */
function isExactActiveRoute($routePattern) {
    // Obtenir l'URI de la requête actuelle
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $requestUri = parse_url($requestUri, PHP_URL_PATH);
    
    // Nettoyer l'URI
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptName === '/' || $scriptName === '\\' || $scriptName === '.') {
        $scriptName = '';
    }
    
    if (!empty($scriptName) && strpos($requestUri, $scriptName) === 0) {
        $requestUri = substr($requestUri, strlen($scriptName));
    }
    
    if (strpos($requestUri, '/ROSSIGNOLES') === 0) {
        $requestUri = substr($requestUri, strlen('/ROSSIGNOLES'));
    }
    
    $requestUri = trim($requestUri, '/');
    
    // Si vide, c'est le dashboard
    if (empty($requestUri) || $requestUri === 'index.php') {
        $requestUri = 'dashboard';
    }
    
    // Normaliser le pattern de route
    $routePattern = trim($routePattern, '/');
    
    // Vérifier uniquement la correspondance exacte
    return $requestUri === $routePattern;
}

/**
 * Génère un champ de formulaire CSRF
 */
function csrf_field() {
    $token = \App\Middleware\CsrfMiddleware::getToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Obtient le token CSRF actuel
 */
function csrf_token() {
    return \App\Middleware\CsrfMiddleware::getToken();
}

/**
 * Convertit un nombre en lettres (Français)
 */
function numberToWords($number) {
    $number = (int)$number;
    if ($number == 0) return 'zéro';
    
    $hyphen      = '-';
    $conjunction = ' ';
    $separator   = ' ';
    $negative    = 'moins ';
    
    $dictionary = array(
        0                   => 'zéro',
        1                   => 'un',
        2                   => 'deux',
        3                   => 'trois',
        4                   => 'quatre',
        5                   => 'cinq',
        6                   => 'six',
        7                   => 'sept',
        8                   => 'huit',
        9                   => 'neuf',
        10                  => 'dix',
        11                  => 'onze',
        12                  => 'douze',
        13                  => 'treize',
        14                  => 'quatorze',
        15                  => 'quinze',
        16                  => 'seize',
        17                  => 'dix-sept',
        18                  => 'dix-huit',
        19                  => 'dix-neuf',
        20                  => 'vingt',
        30                  => 'trente',
        40                  => 'quarante',
        50                  => 'cinquante',
        60                  => 'soixante',
        70                  => 'soixante-dix',
        80                  => 'quatre-vingt',
        90                  => 'quatre-vingt-dix',
        100                 => 'cent',
        1000                => 'mille',
        1000000             => 'million',
        1000000000          => 'milliard',
    );
    
    if (!is_numeric($number)) return false;
    
    if ($number < 0) {
        return $negative . numberToWords(abs($number));
    }
    
    $string = $fraction = null;
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                if ($units == 1 && $tens != 80 && $tens != 90) {
                    $string .= ' et un';
                } elseif ($tens == 70 || $tens == 90) {
                    $string = french_tens_complex($number); 
                } else {
                    $string .= $hyphen . $dictionary[$units];
                }
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            if ($hundreds >= 1 && $hundreds < 2) {
                $string = $dictionary[100];
            } else {
                $string = $dictionary[(int) $hundreds] . $conjunction . $dictionary[100];
            }
            if ($remainder) {
                $string .= $separator . numberToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            if ($baseUnit == 1000) {
                if ($numBaseUnits == 1) {
                    $string = $dictionary[1000];
                } else {
                    $string = numberToWords($numBaseUnits) . $conjunction . $dictionary[1000];
                }
            } else {
                $string = numberToWords($numBaseUnits) . $conjunction . $dictionary[$baseUnit];
                if ($numBaseUnits > 1) {
                    $string .= 's';
                }
            }
            if ($remainder) {
                $string .= $separator . numberToWords($remainder);
            }
            break;
    }
    
    if (substr($string, -12) == 'quatre-vingt') $string .= 's';
    if (substr($string, -4) == 'cent' && $number >= 200 && $number % 100 == 0) $string .= 's';
    
    return $string;
}

function french_tens_complex($number) {
    $tens = ((int) ($number / 10)) * 10;
    $units = $number % 10;
    if ($tens == 70) {
        if ($units == 1) return 'soixante et onze';
        return 'soixante-' . numberToWords(10 + $units);
    }
    if ($tens == 90) {
        return 'quatre-vingt-' . numberToWords(10 + $units);
    }
    return '';
}
