<?php

namespace App\Middleware;

class InputValidationMiddleware {
    public function handle() {
        // Sanitize GET data
        if (!empty($_GET)) {
            $_GET = $this->sanitize($_GET);
        }

        // Sanitize POST data
        if (!empty($_POST)) {
            $_POST = $this->sanitize($_POST);
        }

        // Sanitize COOKIE data
        if (!empty($_COOKIE)) {
            $_COOKIE = $this->sanitize($_COOKIE);
        }
        
        // Sanitize REQUEST data (which contains GET, POST and COOKIE)
        if (!empty($_REQUEST)) {
            $_REQUEST = $this->sanitize($_REQUEST);
        }
    }

    /**
     * Recursively sanitize an array or string
     *
     * @param mixed $input
     * @param string $key The key name for context-aware sanitization
     * @return mixed
     */
    private function sanitize($input, $key = '') {
        if (is_array($input)) {
            foreach ($input as $k => $value) {
                $input[$k] = $this->sanitize($value, $k);
            }
            return $input;
        }

        if (is_string($input)) {
            // Remove whitespace from beginning and end
            $input = trim($input);
            
            // Skip HTML entity conversion for sensitive fields
            // These fields should not be modified beyond trimming
            $sensitiveFields = [
                'password',
                'password_confirmation',
                'current_password',
                'new_password',
                'password_hash',
                'csrf_token',
                'token'
            ];
            
            // Check if this is a sensitive field
            $isSensitive = false;
            foreach ($sensitiveFields as $field) {
                if (stripos($key, $field) !== false) {
                    $isSensitive = true;
                    break;
                }
            }
            
            // Only apply HTML entity conversion to non-sensitive fields
            if (!$isSensitive) {
                // Convert special characters to HTML entities to prevent XSS
                // ENT_QUOTES converts both double and single quotes
                $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            }
            
            return $input;
        }

        return $input;
    }
}
