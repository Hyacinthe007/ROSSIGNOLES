<?php
declare(strict_types=1);

namespace App\Models;

class UsersRoles extends BaseModel {
    protected $table = 'users_roles';
    protected $fillable = ['user_id', 'role_id'];
    
    public function assignRole($userId, $roleId) {
        $exists = $this->queryOne("SELECT 1 FROM {$this->table} WHERE user_id = ? AND role_id = ?", [$userId, $roleId]);
        if (!$exists) {
            return $this->query("INSERT INTO {$this->table} (user_id, role_id) VALUES (?, ?)", [$userId, $roleId]);
        }
        return false;
    }
    
    public function removeRole($userId, $roleId) {
         return $this->query("DELETE FROM {$this->table} WHERE user_id = ? AND role_id = ?", [$userId, $roleId]);
    }
    
    /**
     * Récupère les rôles d'un utilisateur
     */
    public function getUserRoles($userId) {
        return $this->query(
            "SELECT r.* 
             FROM roles r
             INNER JOIN {$this->table} ur ON r.id = ur.role_id
             WHERE ur.user_id = ?
             ORDER BY r.niveau DESC",
            [$userId]
        );
    }
    
    /**
     * Vérifie si un utilisateur a un rôle spécifique
     */
    public function hasRole($userId, $roleCode) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as count
             FROM {$this->table} ur
             INNER JOIN roles r ON ur.role_id = r.id
             WHERE ur.user_id = ? AND r.code = ?",
            [$userId, $roleCode]
        );
        return $result && $result['count'] > 0;
    }
    
    /**
     * Synchronise les rôles d'un utilisateur
     */
    public function syncRoles($userId, $roleIds) {
        // Supprimer tous les rôles existants
        $this->query(
            "DELETE FROM {$this->table} WHERE user_id = ?",
            [$userId]
        );
        
        // Ajouter les nouveaux rôles
        if (!empty($roleIds)) {
            $values = [];
            $params = [];
            foreach ($roleIds as $roleId) {
                $values[] = "(?, ?)";
                $params[] = $userId;
                $params[] = $roleId;
            }
            
            $sql = "INSERT INTO {$this->table} (user_id, role_id) VALUES " . implode(", ", $values);
            $this->query($sql, $params);
        }
        
        return true;
    }
}
