<?php
/**
 * Modèle User
 */

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    protected $fillable = [
        'username', 'email', 'password', 'user_type', 'reference_id', 
        'avatar', 'telephone', 'is_active', 'email_verified_at', 
        'last_login_at', 'last_login_ip'
    ];
    
    /**
     * Vérifie les identifiants de connexion
     */
    public function authenticate($username, $password) {
        $user = $this->queryOne(
            "SELECT * FROM {$this->table} WHERE (username = ? OR email = ?) AND is_active = 1",
            [$username, $username]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            $this->update($user['id'], [
                'last_login_at' => date('Y-m-d H:i:s'),
                'last_login_ip' => $_SERVER['REMOTE_ADDR'] ?? null
            ]);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Obtient les rôles d'un utilisateur (via ses groupes)
     */
    public function getRoles($userId) {
        return $this->query(
            "SELECT DISTINCT r.* 
             FROM roles r
             INNER JOIN user_group_roles ugr ON r.id = ugr.role_id
             INNER JOIN user_group_members ugm ON ugr.groups_id = ugm.groups_id
             WHERE ugm.user_id = ? AND r.actif = 1",
            [$userId]
        );
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle
     */
    public function hasRole($userId, $roleCode) {
        $role = $this->queryOne(
            "SELECT r.id 
             FROM roles r
             INNER JOIN user_group_roles ugr ON r.id = ugr.role_id
             INNER JOIN user_group_members ugm ON ugr.groups_id = ugm.groups_id
             WHERE ugm.user_id = ? AND r.code = ? AND r.actif = 1 LIMIT 1",
            [$userId, $roleCode]
        );
        
        return $role !== false;
    }

    /**
     * Obtient les groupes d'un utilisateur
     */
    public function getGroups($userId) {
        return $this->query(
            "SELECT g.* 
             FROM user_groups g
             INNER JOIN user_group_members ugm ON g.id = ugm.groups_id
             WHERE ugm.user_id = ?",
            [$userId]
        );
    }

    /**
     * Définit les groupes d'un utilisateur
     */
    public function setGroups($userId, $groupIds) {
        // Supprimer les anciens groupes
        $this->query("DELETE FROM user_group_members WHERE user_id = ?", [$userId]);
        
        // Ajouter les nouveaux groupes
        if (!empty($groupIds)) {
            foreach ($groupIds as $groupId) {
                $this->query(
                    "INSERT INTO user_group_members (user_id, groups_id) VALUES (?, ?)",
                    [$userId, $groupId]
                );
            }
        }
        return true;
    }
    
    /**
     * Obtient toutes les permissions d'un utilisateur (via ses rôles et groupes)
     */
    public function getPermissions($userId) {
        return $this->query(
            "SELECT DISTINCT p.* 
             FROM permissions p
             INNER JOIN roles_permissions rp ON p.id = rp.permission_id
             INNER JOIN user_group_roles ugr ON rp.role_id = ugr.role_id
             INNER JOIN user_group_members ugm ON ugr.groups_id = ugm.groups_id
             WHERE ugm.user_id = ?",
            [$userId]
        );
    }

    /**
     * Vérifie si l'utilisateur a une permission spécifique
     */
    public function hasPermission($userId, $permissionCode) {
        // Si admin, il a tout par défaut (logique métier à confirmer, mais souvent vrai)
        // Alternativement, on vérifie explicitement en DB
        $perm = $this->queryOne(
            "SELECT p.id 
             FROM permissions p
             INNER JOIN roles_permissions rp ON p.id = rp.permission_id
             INNER JOIN user_group_roles ugr ON rp.role_id = ugr.role_id
             INNER JOIN user_group_members ugm ON ugr.groups_id = ugm.groups_id
             WHERE ugm.user_id = ? AND p.code = ? LIMIT 1",
            [$userId, $permissionCode]
        );
        
        return $perm !== false;
    }
    
    /**
     * Assigne automatiquement l'utilisateur à un groupe admin si nécessaire
     */
    public function ensureAdminRole($userId) {
        try {
            // Vérifier si un groupe admin existe
            $adminGroup = $this->queryOne(
                "SELECT id FROM user_groups WHERE code = 'admin' LIMIT 1"
            );
            
            if (!$adminGroup) {
                // Créer le groupe admin s'il n'existe pas
                $adminGroupId = $this->query(
                    "INSERT INTO user_groups (code, nom, description) VALUES (?, ?, ?)",
                    ['admin', 'Administrateur', 'Groupe administrateur avec tous les droits']
                );
            } else {
                $adminGroupId = $adminGroup['id'];
            }
            
            // Ajouter l'utilisateur au groupe admin s'il n'y est pas
            $isMember = $this->queryOne(
                "SELECT id FROM user_group_members WHERE user_id = ? AND groups_id = ? LIMIT 1",
                [$userId, $adminGroupId]
            );
            
            if (!$isMember) {
                $this->query(
                    "INSERT INTO user_group_members (user_id, groups_id) VALUES (?, ?)",
                    [$userId, $adminGroupId]
                );
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Impossible d'assigner le rôle admin : " . $e->getMessage());
            return false;
        }
    }
}

