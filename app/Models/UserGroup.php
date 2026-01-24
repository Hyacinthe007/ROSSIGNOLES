<?php
/**
 * Modèle UserGroup
 */

require_once __DIR__ . '/BaseModel.php';

class UserGroup extends BaseModel {
    protected $table = 'user_groups';
    protected $fillable = ['nom', 'code', 'description'];

    /**
     * Obtient les rôles d'un groupe
     */
    public function getRoles($groupId) {
        return $this->query(
            "SELECT r.* FROM roles r
             INNER JOIN user_group_roles ugr ON r.id = ugr.role_id
             WHERE ugr.groups_id = ? AND r.actif = 1",
            [$groupId]
        );
    }

    /**
     * Ajoute un rôle à un groupe
     */
    public function addRole($groupId, $roleId) {
        return $this->query(
            "INSERT IGNORE INTO user_group_roles (groups_id, role_id) VALUES (?, ?)",
            [$groupId, $roleId]
        );
    }

    /**
     * Retire tous les rôles d'un groupe
     */
    public function clearRoles($groupId) {
        return $this->query(
            "DELETE FROM user_group_roles WHERE groups_id = ?",
            [$groupId]
        );
    }

    /**
     * Obtient les membres d'un groupe
     */
    public function getMembers($groupId) {
        return $this->query(
            "SELECT u.* 
             FROM users u
             INNER JOIN user_group_members ugm ON u.id = ugm.user_id
             WHERE ugm.groups_id = ?",
            [$groupId]
        );
    }

    /**
     * Ajoute un utilisateur à un groupe
     */
    public function addMember($groupId, $userId) {
        return $this->query(
            "INSERT IGNORE INTO user_group_members (user_id, groups_id) VALUES (?, ?)",
            [$userId, $groupId]
        );
    }

    /**
     * Retire un utilisateur d'un groupe
     */
    public function removeMember($groupId, $userId) {
        return $this->query(
            "DELETE FROM user_group_members WHERE user_id = ? AND groups_id = ?",
            [$userId, $groupId]
        );
    }
}
