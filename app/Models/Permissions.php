<?php
/**
 * Modèle Permissions
 * Correspond à la table 'permissions'
 */

require_once __DIR__ . '/BaseModel.php';

class Permissions extends BaseModel {
    protected $table = 'permissions';
    protected $fillable = [
        'code', 'module', 'action', 'description'
    ];
    
    /**
     * Récupère toutes les permissions groupées par module
     */
    public function getAllGroupedByModule() {
        $permissions = $this->query(
            "SELECT * FROM {$this->table} ORDER BY module, action"
        );
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $module = $permission['module'];
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $permission;
        }
        
        return $grouped;
    }
    
    /**
     * Récupère les permissions d'un module spécifique
     */
    public function getByModule($module) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE module = ? 
             ORDER BY action",
            [$module]
        );
    }
    
    /**
     * Récupère une permission par son code
     */
    public function getByCode($code) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE code = ?",
            [$code]
        );
    }
    
    /**
     * Récupère les rôles ayant une permission spécifique
     */
    public function getRoles($permissionId) {
        return $this->query(
            "SELECT r.* 
             FROM roles r
             INNER JOIN roles_permissions rp ON r.id = rp.role_id
             WHERE rp.permission_id = ?
             ORDER BY r.niveau DESC, r.nom",
            [$permissionId]
        );
    }
}
