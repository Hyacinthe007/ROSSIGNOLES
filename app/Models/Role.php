<?php
declare(strict_types=1);

namespace App\Models;

class Role extends BaseModel {
    protected $table = 'roles';
    protected $fillable = ['nom', 'description', 'niveau', 'actif'];
    
    public function getPermissions($roleId) {
        return $this->query(
            "SELECT p.* FROM permissions p
             JOIN roles_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?",
            [$roleId]
        );
    }
    
    public function addPermission($roleId, $permissionId) {
        return $this->query(
            "INSERT INTO roles_permissions (role_id, permission_id) VALUES (?, ?)",
            [$roleId, $permissionId]
        );
    }
}
