<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Validateur de données simple et moderne
 */
class Validator {
    private $data;
    private $errors = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * Valide les données selon les règles fournies
     * Exemple: ['nom' => 'required|min:3', 'email' => 'required|email']
     */
    public function validate(array $rules): bool {
        foreach ($rules as $field => $fieldRules) {
            $rulesArray = explode('|', $fieldRules);
            $value = $this->data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule, $value);
            }
        }

        return empty($this->errors);
    }

    /**
     * Applique une règle spécifique
     */
    private function applyRule($field, $rule, $value) {
        $params = [];
        if (strpos($rule, ':') !== false) {
            list($rule, $paramString) = explode(':', $rule);
            $params = explode(',', $paramString);
        }

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, "Le champ {$field} est obligatoire.");
                }
                break;
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Le champ {$field} doit être une adresse email valide.");
                }
                break;
            case 'min':
                if (!empty($value) && strlen((string)$value) < (int)$params[0]) {
                    $this->addError($field, "Le champ {$field} doit contenir au moins {$params[0]} caractères.");
                }
                break;
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "Le champ {$field} doit être un nombre.");
                }
                break;
            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->addError($field, "Le champ {$field} doit être une date valide.");
                }
                break;
        }
    }

    private function addError($field, $message) {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
