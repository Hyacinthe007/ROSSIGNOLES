<?php
/**
 * Contrôleur des rôles et permissions
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Role.php';
require_once APP_PATH . '/Models/Permission.php';

class RolesController extends BaseController {
    private $roleModel;
    private $permissionModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->requirePermission('systeme.roles');
        $this->roleModel = new Role();
        $this->permissionModel = new Permission();
    }
    
    public function list() {
        $roles = $this->roleModel->query("
            SELECT r.*, COUNT(DISTINCT ugm.user_id) as users_count 
            FROM roles r 
            LEFT JOIN user_group_roles ugr ON r.id = ugr.role_id 
            LEFT JOIN user_group_members ugm ON ugr.groups_id = ugm.groups_id 
            GROUP BY r.id 
            ORDER BY r.nom ASC
        ");
        $this->view('roles/list', ['roles' => $roles]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'] ?? '',
                'code' => strtolower(str_replace(' ', '.', $_POST['nom'] ?? '')),
                'description' => $_POST['description'] ?? '',
                'niveau' => $_POST['niveau'] ?? 1,
                'actif' => isset($_POST['actif']) ? 1 : 0
            ];
            
            $roleId = $this->roleModel->create($data);
            if ($roleId) {
                // Gérer les permissions
                if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
                    foreach ($_POST['permissions'] as $permId) {
                        $this->roleModel->addPermission($roleId, $permId);
                    }
                }
                
                $_SESSION['success_message'] = "Rôle créé avec succès.";
                $this->redirect('roles/list');
            } else {
                $_SESSION['error_message'] = "Erreur lors de la création du rôle.";
            }
        }
        
        $permissions = $this->permissionModel->all([], 'module ASC, code ASC');
        $this->view('roles/add', ['permissions' => $permissions]);
    }

    public function edit($id) {
        $role = $this->roleModel->find($id);
        if (!$role) {
            $_SESSION['error_message'] = "Rôle non trouvé.";
            $this->redirect('roles/list');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'] ?? '',
                'description' => $_POST['description'] ?? '',
                'niveau' => $_POST['niveau'] ?? 1,
                'actif' => isset($_POST['actif']) ? 1 : 0
            ];
            
            if ($this->roleModel->update($id, $data)) {
                // Mettre à jour les permissions (supprimer et ré-ajouter)
                $this->roleModel->query("DELETE FROM roles_permissions WHERE role_id = ?", [$id]);
                if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
                    foreach ($_POST['permissions'] as $permId) {
                        $this->roleModel->addPermission($id, $permId);
                    }
                }

                $_SESSION['success_message'] = "Rôle mis à jour avec succès.";
                $this->redirect('roles/list');
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour du rôle.";
            }
        }
        
    
        $rolePermissions = $this->roleModel->getPermissions($id);
        $rolePermissionIds = array_column((array)$rolePermissions, 'id');
        
        // NOUVEAU : Récupérer les groupes et utilisateurs liés au rôle
        $associatedGroups = $this->roleModel->query("
            SELECT g.*, (SELECT COUNT(*) FROM user_group_members WHERE groups_id = g.id) as users_count
            FROM user_groups g
            INNER JOIN user_group_roles ugr ON g.id = ugr.groups_id
            WHERE ugr.role_id = ?
        ", [$id]);

        $associatedUsers = $this->roleModel->query("
            SELECT DISTINCT u.id, u.username, g.nom as group_name
            FROM users u
            INNER JOIN user_group_members ugm ON u.id = ugm.user_id
            INNER JOIN user_group_roles ugr ON ugm.groups_id = ugr.groups_id
            INNER JOIN user_groups g ON ugm.groups_id = g.id
            WHERE ugr.role_id = ?
        ", [$id]);

        // Liste de tous les groupes pour pouvoir les lier
        $allGroups = $this->roleModel->query("SELECT * FROM user_groups ORDER BY nom ASC");
        $associatedGroupIds = array_column((array)$associatedGroups, 'id');

        $permissions = $this->permissionModel->all([], 'module ASC, code ASC');
        $this->view('roles/edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissionIds' => $rolePermissionIds,
            'associatedGroups' => (array)$associatedGroups,
            'associatedUsers' => (array)$associatedUsers,
            'allGroups' => (array)$allGroups,
            'associatedGroupIds' => $associatedGroupIds
        ]);
    }

    /**
     * Lier un groupe à ce rôle
     */
    public function linkGroup($roleId, $groupId) {
        require_once APP_PATH . '/Models/UserGroup.php';
        $groupModel = new UserGroup();
        $groupModel->addRole($groupId, $roleId);
        $_SESSION['success_message'] = "Groupe rattaché au rôle.";
        $this->redirect('roles/edit/' . $roleId);
    }

    /**
     * Délier un groupe de ce rôle
     */
    public function unlinkGroup($roleId, $groupId) {
        $this->roleModel->query("DELETE FROM user_group_roles WHERE groups_id = ? AND role_id = ?", [$groupId, $roleId]);
        $_SESSION['success_message'] = "Groupe détaché du rôle.";
        $this->redirect('roles/edit/' . $roleId);
    }

    public function delete($id) {
        if ($this->roleModel->delete($id)) {
            $_SESSION['success_message'] = "Rôle supprimé avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression du rôle.";
        }
        $this->redirect('roles/list');
    }
}
