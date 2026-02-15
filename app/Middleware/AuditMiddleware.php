<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Models\BaseModel;
use PDO;

/**
 * Middleware d'audit pour ROSSIGNOLES V2
 * 
 * Enregistre automatiquement toutes les actions dans la table audit_trail.
 * S'utilise dans les contrôleurs/services pour tracer les opérations CRUD.
 * 
 * Usage dans un contrôleur :
 *   AuditMiddleware::log('create', 'eleves', $eleveId, null, $newData, 'Création élève');
 *   AuditMiddleware::log('update', 'factures', $factureId, $oldData, $newData, 'Paiement enregistré');
 *   AuditMiddleware::log('delete', 'absences', $absenceId, $oldData, null, 'Suppression absence');
 */
class AuditMiddleware
{
    /** @var PDO|null Connexion BDD */
    private static ?PDO $db = null;

    /** @var bool La table audit_trail existe-t-elle ? */
    private static ?bool $tableExists = null;

    /**
     * Enregistre une action dans l'audit trail
     *
     * @param string      $action      Type d'action (create, update, delete, validate, reject, export, login, logout)
     * @param string      $tableName   Nom de la table concernée
     * @param int|null    $recordId    ID de l'enregistrement
     * @param array|null  $oldValues   Anciennes valeurs (pour update/delete)
     * @param array|null  $newValues   Nouvelles valeurs (pour create/update)
     * @param string|null $description Description lisible
     * @return bool
     */
    public static function log(
        string $action,
        string $tableName,
        ?int $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): bool {
        try {
            if (!self::isAvailable()) {
                return false;
            }

            $db = self::getConnection();

            // Récupérer les infos utilisateur de la session
            $userId = $_SESSION['user_id'] ?? null;
            $username = $_SESSION['user_name'] ?? $_SESSION['username'] ?? null;

            // Nettoyer les valeurs sensibles avant stockage
            $oldValues = self::sanitizeValues($oldValues);
            $newValues = self::sanitizeValues($newValues);

            // Pour les updates, ne garder que les champs modifiés
            if ($action === 'update' && $oldValues !== null && $newValues !== null) {
                [$oldValues, $newValues] = self::diffValues($oldValues, $newValues);
            }

            $sql = "INSERT INTO `audit_trail` 
                    (`user_id`, `username`, `action`, `table_name`, `record_id`, 
                     `old_values`, `new_values`, `description`,
                     `ip_address`, `user_agent`, `request_uri`, `request_method`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            return $stmt->execute([
                $userId,
                $username,
                $action,
                $tableName,
                $recordId,
                $oldValues !== null ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                $newValues !== null ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 500) : null,
                isset($_SERVER['REQUEST_URI']) ? substr($_SERVER['REQUEST_URI'], 0, 500) : null,
                $_SERVER['REQUEST_METHOD'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // L'audit ne doit JAMAIS bloquer l'application
            error_log("[AuditMiddleware] Erreur d'enregistrement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Raccourci pour logger une création
     */
    public static function logCreate(string $table, int $recordId, array $data, ?string $desc = null): bool
    {
        return self::log('create', $table, $recordId, null, $data, $desc ?? "Création dans {$table}");
    }

    /**
     * Raccourci pour logger une mise à jour
     */
    public static function logUpdate(string $table, int $recordId, array $oldData, array $newData, ?string $desc = null): bool
    {
        return self::log('update', $table, $recordId, $oldData, $newData, $desc ?? "Modification dans {$table}");
    }

    /**
     * Raccourci pour logger une suppression
     */
    public static function logDelete(string $table, int $recordId, array $oldData, ?string $desc = null): bool
    {
        return self::log('delete', $table, $recordId, $oldData, null, $desc ?? "Suppression dans {$table}");
    }

    /**
     * Raccourci pour logger une validation
     */
    public static function logValidation(string $table, int $recordId, ?string $desc = null): bool
    {
        return self::log('validate', $table, $recordId, null, null, $desc ?? "Validation dans {$table}");
    }

    /**
     * Raccourci pour logger un export
     */
    public static function logExport(string $table, ?string $desc = null): bool
    {
        return self::log('export', $table, null, null, null, $desc ?? "Export de {$table}");
    }

    /**
     * Raccourci pour logger une connexion
     */
    public static function logLogin(int $userId, string $username): bool
    {
        return self::log('login', 'users', $userId, null, ['username' => $username], "Connexion de {$username}");
    }

    /**
     * Raccourci pour logger une déconnexion
     */
    public static function logLogout(): bool
    {
        $userId = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['user_name'] ?? 'inconnu';
        return self::log('logout', 'users', $userId ? (int)$userId : null, null, null, "Déconnexion de {$username}");
    }

    /**
     * Récupère l'historique d'audit pour une entité
     */
    public static function getHistory(string $tableName, int $recordId, int $limit = 50): array
    {
        try {
            if (!self::isAvailable()) {
                return [];
            }

            $db = self::getConnection();
            $sql = "SELECT * FROM `audit_trail` 
                    WHERE `table_name` = ? AND `record_id` = ?
                    ORDER BY `created_at` DESC 
                    LIMIT ?";

            $stmt = $db->prepare($sql);
            $stmt->execute([$tableName, $recordId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            error_log("[AuditMiddleware] Erreur lecture historique: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les dernières actions d'un utilisateur
     */
    public static function getUserActivity(int $userId, int $limit = 20): array
    {
        try {
            if (!self::isAvailable()) {
                return [];
            }

            $db = self::getConnection();
            $sql = "SELECT * FROM `audit_trail` 
                    WHERE `user_id` = ?
                    ORDER BY `created_at` DESC 
                    LIMIT ?";

            $stmt = $db->prepare($sql);
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Vérifie si la table audit_trail est disponible
     */
    private static function isAvailable(): bool
    {
        if (self::$tableExists !== null) {
            return self::$tableExists;
        }

        try {
            $db = self::getConnection();
            $stmt = $db->query("SELECT 1 FROM `audit_trail` LIMIT 1");
            self::$tableExists = ($stmt !== false);
        } catch (\Throwable $e) {
            self::$tableExists = false;
        }

        return self::$tableExists;
    }

    /**
     * Obtient la connexion BDD
     */
    private static function getConnection(): PDO
    {
        if (self::$db === null) {
            self::$db = BaseModel::getDBConnection();
        }
        return self::$db;
    }

    /**
     * Supprime les champs sensibles des données
     */
    private static function sanitizeValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $sensitiveFields = ['password', 'mot_de_passe', 'token', 'secret', 'csrf_token'];

        foreach ($sensitiveFields as $field) {
            if (isset($values[$field])) {
                $values[$field] = '***MASQUÉ***';
            }
        }

        return $values;
    }

    /**
     * Compare les anciennes et nouvelles valeurs, retourne uniquement les différences
     */
    private static function diffValues(array $old, array $new): array
    {
        $diffOld = [];
        $diffNew = [];

        foreach ($new as $key => $newValue) {
            $oldValue = $old[$key] ?? null;

            // Comparer en string pour gérer les types mixtes
            if ((string)$oldValue !== (string)$newValue) {
                $diffOld[$key] = $oldValue;
                $diffNew[$key] = $newValue;
            }
        }

        // S'il n'y a pas de différence, retourner null
        if (empty($diffNew)) {
            return [null, null];
        }

        return [$diffOld, $diffNew];
    }
}
