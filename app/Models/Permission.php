<?php
require_once __DIR__ . '/BaseModel.php';

class Permission extends BaseModel {
    protected $table = 'permissions';
    protected $fillable = ['code', 'module', 'action', 'description'];
    
    public function getRoles($permissionId) {
        return $this->query(
            "SELECT r.* FROM roles r
             JOIN roles_permissions rp ON r.id = rp.role_id
             WHERE rp.permission_id = ? AND r.actif = 1",
            [$permissionId]
        );
    }
}
