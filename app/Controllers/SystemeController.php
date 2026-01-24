<?php
/**
 * Contrôleur système
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Configuration.php';
require_once APP_PATH . '/Models/LogActivite.php';

class SystemeController extends BaseController {
    private $configModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->requirePermission('systeme.config');
        $this->configModel = new Configuration();
    }
    
    public function config() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                foreach ($_POST as $cle => $valeur) {
                    if (!empty($cle)) {
                        $this->configModel->set($cle, $valeur);
                    }
                }
                $_SESSION['success_message'] = 'Configuration enregistrée avec succès !';
                $this->redirect('systeme/config');
            } catch (PDOException $e) {
                error_log("Erreur lors de l'enregistrement de la configuration: " . $e->getMessage());
                $_SESSION['error_message'] = 'Erreur lors de l\'enregistrement de la configuration.';
                $this->redirect('systeme/config');
            }
        } else {
            try {
                $configs = $this->configModel->all([], 'cle ASC');
                $this->view('systeme/config', ['configs' => $configs]);
            } catch (PDOException $e) {
                // Si la table n'existe pas, afficher un message
                if ($e->getCode() == '42S02') {
                    $this->view('systeme/config', [
                        'configs' => [],
                        'error' => 'La table configuration_systeme n\'existe pas dans la base de données.'
                    ]);
                } else {
                    throw $e;
                }
            }
        }
    }
    
    /**
     * Gestion des utilisateurs et rôles
     */
    public function utilisateurs() {
        require_once APP_PATH . '/Models/User.php';
        $userModel = new User();
        
        try {
            // Récupérer les utilisateurs avec leurs groupes
            $utilisateurs = $userModel->query("
                SELECT u.*, GROUP_CONCAT(g.nom SEPARATOR ', ') as groupes_noms 
                FROM users u 
                LEFT JOIN user_group_members ugm ON u.id = ugm.user_id 
                LEFT JOIN user_groups g ON ugm.groups_id = g.id 
                GROUP BY u.id
                ORDER BY u.username ASC
            ");
            
            // Récupérer les rôles
            require_once APP_PATH . '/Models/Role.php';
            $roleModel = new Role();
            $roles = $roleModel->all([], 'nom ASC');

            // Récupérer les groupes
            require_once APP_PATH . '/Models/UserGroup.php';
            $groupModel = new UserGroup();
            $groupes = $groupModel->all([], 'nom ASC');
            
            $this->view('systeme/utilisateurs', [
                'utilisateurs' => $utilisateurs,
                'roles' => $roles,
                'groupes' => $groupes
            ]);
        } catch (Exception $e) {
            error_log("Erreur dans utilisateurs: " . $e->getMessage());
            $this->view('systeme/utilisateurs', [
                'utilisateurs' => [],
                'roles' => [],
                'error' => 'Erreur lors du chargement des données.'
            ]);
        }
    }

    /**
     * Ajouter un utilisateur
     */
    public function addUtilisateur() {
        require_once APP_PATH . '/Models/User.php';
        require_once APP_PATH . '/Models/Role.php';
        require_once APP_PATH . '/Models/UserGroup.php';
        $userModel = new User();
        $roleModel = new Role();
        $groupModel = new UserGroup();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'user_type' => $_POST['user_type'] ?? 'admin',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                $userId = $userModel->create($data);

                // Assigner les groupes et auto-lier les rôles correspondants
                if (isset($_POST['groups']) && is_array($_POST['groups'])) {
                    $userModel->setGroups($userId, $_POST['groups']);
                    
                    // AUTO-PROPAGATION: Lier le groupe à son rôle s'ils partagent le même nom
                    foreach ($_POST['groups'] as $groupId) {
                        $group = $groupModel->find($groupId);
                        if ($group) {
                            $role = $roleModel->queryOne("SELECT id FROM roles WHERE nom = ? OR code = ?", [$group['nom'], $group['code']]);
                            if ($role) {
                                $groupModel->addRole($groupId, $role['id']);
                            }
                        }
                    }
                }

                // Logging
                LogActivite::log('Nouvel Utilisateur', 'Administration', "Création de l'utilisateur {$_POST['username']}", 'users', $userId);
                
                $_SESSION['success_message'] = "Utilisateur créé avec succès.";
                $this->redirect('systeme/utilisateurs');
            } catch (Exception $e) {
                error_log("Erreur lors de l'ajout d'un utilisateur: " . $e->getMessage());
                $roles = $roleModel->all([], 'nom ASC');
                $groupes = $groupModel->all([], 'nom ASC');
                $this->view('systeme/user_form', [
                    'roles' => $roles,
                    'groupes' => $groupes,
                    'error' => "Erreur lors de la création de l'utilisateur."
                ]);
            }
        } else {
            $roles = $roleModel->all([], 'nom ASC');
            $groupes = $groupModel->all([], 'nom ASC');
            $this->view('systeme/user_form', ['roles' => $roles, 'groupes' => $groupes]);
        }
    }

    /**
     * Modifier un utilisateur
     */
    public function editUtilisateur($id) {
        require_once APP_PATH . '/Models/User.php';
        require_once APP_PATH . '/Models/Role.php';
        require_once APP_PATH . '/Models/UserGroup.php';
        $userModel = new User();
        $roleModel = new Role();
        $groupModel = new UserGroup();

        $user = $userModel->find($id);
        if (!$user) {
            $_SESSION['error_message'] = "Utilisateur non trouvé.";
            $this->redirect('systeme/utilisateurs');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'user_type' => $_POST['user_type'] ?? 'admin',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                // Si un mot de passe est fourni, on le met à jour
                if (!empty($_POST['password'])) {
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $userModel->update($id, $data);

                // Mettre à jour les groupes et auto-lier les rôles correspondants
                if (isset($_POST['groups']) && is_array($_POST['groups'])) {
                    $userModel->setGroups($id, $_POST['groups']);

                    // AUTO-PROPAGATION: Lier le groupe à son rôle s'ils partagent le même nom
                    foreach ($_POST['groups'] as $groupId) {
                        $group = $groupModel->find($groupId);
                        if ($group) {
                            $role = $roleModel->queryOne("SELECT id FROM roles WHERE nom = ? OR code = ?", [$group['nom'], $group['code']]);
                            if ($role) {
                                $groupModel->addRole($groupId, $role['id']);
                            }
                        }
                    }
                }

                // Logging
                LogActivite::log('Modification Utilisateur', 'Administration', "Mise à jour de l'utilisateur {$_POST['username']} (ID: $id)", 'users', $id);
                
                $_SESSION['success_message'] = "Utilisateur mis à jour avec succès.";
                $this->redirect('systeme/utilisateurs');
            } catch (Exception $e) {
                error_log("Erreur lors de la modification de l'utilisateur: " . $e->getMessage());
                $roles = $roleModel->all([], 'nom ASC');
                $groupes = $groupModel->all([], 'nom ASC');
                $userGroups = array_column($userModel->getGroups($id), 'id');
                $this->view('systeme/user_form', [
                    'user' => $user,
                    'roles' => $roles,
                    'groupes' => $groupes,
                    'userGroups' => $userGroups,
                    'error' => "Erreur lors de la mise à jour."
                ]);
            }
        } else {
            $roles = $roleModel->all([], 'nom ASC');
            $groupes = $groupModel->all([], 'nom ASC');
            $userGroups = array_column($userModel->getGroups($id), 'id');
            $this->view('systeme/user_form', [
                'user' => $user,
                'roles' => $roles,
                'groupes' => $groupes,
                'userGroups' => $userGroups
            ]);
        }
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once APP_PATH . '/Models/User.php';
            $userModel = new User();
            
            $user = $userModel->find($id);
            if ($user) {
                if ($id == $_SESSION['user_id']) {
                    $_SESSION['error_message'] = "Vous ne pouvez pas désactiver votre propre compte.";
                } else {
                    $newStatus = ($user['is_active'] ?? $user['actif'] ?? 1) ? 0 : 1;
                    $userModel->update($id, ['is_active' => $newStatus]);
                    $_SESSION['success_message'] = "Le statut de l'utilisateur a été mis à jour.";
                }
            }
        }
        $this->redirect('systeme/utilisateurs');
    }

    /**
     * Synchroniser le groupe parents (désactiver ceux sans enfants actifs)
     */
    public function syncParentsGroup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once APP_PATH . '/Models/User.php';
            $userModel = new User();
            
            try {
                // Requête pour désactiver les parents dont aucun enfant n'est 'actif'
                $sql = "UPDATE users 
                        SET is_active = 0 
                        WHERE user_type = 'parent' 
                        AND is_active = 1
                        AND reference_id NOT IN (
                            SELECT DISTINCT ep.parent_id 
                            FROM eleves_parents ep
                            JOIN eleves e ON ep.eleve_id = e.id
                            WHERE e.statut = 'actif'
                        )";
                
                $result = $userModel->query($sql);
                
                $_SESSION['success_message'] = "Nettoyage du groupe Parents terminé avec succès.";
            } catch (Exception $e) {
                error_log("Erreur lors de la synchro parents: " . $e->getMessage());
                $_SESSION['error_message'] = "Erreur lors de la synchronisation du groupe.";
            }
        }
        $this->redirect('systeme/utilisateurs');
    }

    /**
     * Liste des groupes
     */
    public function groupes() {
        require_once APP_PATH . '/Models/UserGroup.php';
        $groupModel = new UserGroup();
        $groupes = $groupModel->all([], 'nom ASC');
        $this->view('systeme/groupes', ['groupes' => $groupes]);
    }

    /**
     * Ajouter un groupe
     */
    public function addGroup() {
        require_once APP_PATH . '/Models/UserGroup.php';
        require_once APP_PATH . '/Models/Role.php';
        $groupModel = new UserGroup();
        $roleModel = new Role();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $groupId = $groupModel->create([
                'nom' => $_POST['nom'],
                'code' => $_POST['code'],
                'description' => $_POST['description']
            ]);

            if ($groupId) {
                // AUTO-LINK: Si un rôle porte le même nom ou code, on le lie automatiquement
                $this->roleModel = new Role();
                $sameRole = $this->roleModel->queryOne("SELECT id FROM roles WHERE nom = ? OR code = ?", [$_POST['nom'], $_POST['code']]);
                if ($sameRole) {
                    $groupModel->addRole($groupId, $sameRole['id']);
                }

                if (isset($_POST['roles'])) {
                    foreach ($_POST['roles'] as $roleId) {
                        $groupModel->addRole($groupId, $roleId);
                    }
                }
            }

            $_SESSION['success_message'] = "Groupe créé avec succès.";
            $this->redirect('systeme/utilisateurs');
        }

        $roles = $roleModel->all([], 'nom ASC');
        $this->view('systeme/group_form', ['roles' => $roles]);
    }

    /**
     * Modifier un groupe
     */
    public function editGroup($id) {
        require_once APP_PATH . '/Models/UserGroup.php';
        require_once APP_PATH . '/Models/Role.php';
        $groupModel = new UserGroup();
        $roleModel = new Role();

        $group = $groupModel->find($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $groupModel->update($id, [
                'nom' => $_POST['nom'],
                'code' => $_POST['code'],
                'description' => $_POST['description']
            ]);

            $groupModel->clearRoles($id);
            if (isset($_POST['roles'])) {
                foreach ($_POST['roles'] as $roleId) {
                    $groupModel->addRole($id, $roleId);
                }
            }

            $_SESSION['success_message'] = "Groupe mis à jour.";
            $this->redirect('systeme/utilisateurs');
        }

        $roles = $roleModel->all([], 'nom ASC');
        $groupRoles = array_column($groupModel->getRoles($id), 'id');
        $members = $groupModel->getMembers($id);

        $this->view('systeme/group_form', [
            'group' => $group,
            'roles' => $roles,
            'groupRoles' => $groupRoles,
            'members' => $members
        ]);
    }

    /**
     * Supprimer un groupe
     */
    public function deleteGroup($id) {
        require_once APP_PATH . '/Models/UserGroup.php';
        $groupModel = new UserGroup();
        $groupModel->delete($id);
        $_SESSION['success_message'] = "Groupe supprimé.";
        $this->redirect('systeme/utilisateurs');
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUtilisateur($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once APP_PATH . '/Models/User.php';
            $userModel = new User();
            
            // Ne pas permettre la suppression de soi-même
            if ($id == $_SESSION['user_id']) {
                $_SESSION['error_message'] = "Vous ne pouvez pas supprimer votre propre compte.";
            } else {
                $userModel->delete($id);
                $userModel->query("DELETE FROM user_group_members WHERE user_id = ?", [$id]);
                
                // Logging
                LogActivite::log('Suppression Utilisateur', 'Administration', "Suppression de l'utilisateur ID: $id", 'users', $id);
                
                $_SESSION['success_message'] = "Utilisateur supprimé avec succès.";
            }
        }
        $this->redirect('systeme/utilisateurs');
    }
    
    /**
     * Affichage des logs système
     */
    public function logs() {
        require_once APP_PATH . '/Models/BaseModel.php';
        $model = new BaseModel();
        
        try {
            // Récupérer les logs avec les informations utilisateur
            $logs = $model->query("
                SELECT la.*, u.username 
                FROM logs_activites la 
                LEFT JOIN users u ON la.user_id = u.id 
                ORDER BY la.created_at DESC 
                LIMIT 200
            ");
            
            $this->view('systeme/logs', ['logs' => $logs]);
        } catch (Exception $e) {
            error_log("Erreur dans logs: " . $e->getMessage());
            $this->view('systeme/logs', [
                'logs' => [],
                'error' => 'Erreur lors du chargement des logs d\'activité.'
            ]);
        }
    }
}

