<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Roles
 * Correspond à la table 'roles'
 */

class Roles extends BaseModel {
    protected $table = 'roles';
    protected $fillable = [
        'code', 'nom', 'description', 'niveau', 'actif'
    ];
    
    /**
     * Récupère tous les rôles actifs
     */
    public function getActifs() {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE actif = 1 
             ORDER BY niveau DESC, nom ASC"
        );
    }
    
    /**
     * Récupère un rôle par son code
     */
    public function getByCode($code) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE code = ?",
            [$code]
        );
    }
    
    /**
     * Récupère les permissions d'un rôle
     */
    public function getPermissions($roleId) {
        return $this->query(
            "SELECT p.* 
             FROM permissions p
             INNER JOIN roles_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?
             ORDER BY p.module, p.action",
            [$roleId]
        );
    }
    
    /**
     * Vérifie si un rôle a une permission spécifique
     */
    public function hasPermission($roleId, $permissionCode) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count
             FROM roles_permissions rp
             INNER JOIN permissions p ON rp.permission_id = p.id
             WHERE rp.role_id = ? AND p.code = ?",
            [$roleId, $permissionCode]
        );
        return $result && $result['count'] > 0;
    }
    
    /**
     * Attache une permission à un rôle
     */
    public function attachPermission($roleId, $permissionId) {
        return $this->query(
            "INSERT INTO roles_permissions (role_id, permission_id) 
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE role_id = role_id",
            [$roleId, $permissionId]
        );
    }
    
    /**
     * Détache une permission d'un rôle
     */
    public function detachPermission($roleId, $permissionId) {
        return $this->query(
            "DELETE FROM roles_permissions 
             WHERE role_id = ? AND permission_id = ?",
            [$roleId, $permissionId]
        );
    }
}
