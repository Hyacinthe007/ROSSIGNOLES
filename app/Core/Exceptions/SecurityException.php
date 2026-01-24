<?php
declare(strict_types=1);

namespace App\Core\Exceptions;

use Exception;

/**
 * Exception spécifique aux erreurs de sécurité (CSRF, Auth, etc.)
 */
class SecurityException extends Exception {
    protected $code = 403;
}
