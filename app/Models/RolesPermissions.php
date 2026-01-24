<?php
require_once __DIR__ . '/BaseModel.php';

class RolesPermissions extends BaseModel {
    protected $table = 'roles_permissions';
    protected $fillable = ['role_id', 'permission_id'];
    
    // Pas de timestamps par défaut sur cette table pivot sauf si le schéma le prévoit
    // Si le BaseModel gère created_at automatiquement, il faut vérifier si la table l'a.
    // Supposons que non pour une table de liaison simple.
    
    public function addPermission($roleId, $permissionId) {
        // Vérifier l'existence pour éviter doublon
        $exists = $this->queryOne("SELECT 1 FROM {$this->table} WHERE role_id = ? AND permission_id = ?", [$roleId, $permissionId]);
        if (!$exists) {
            return $this->query("INSERT INTO {$this->table} (role_id, permission_id) VALUES (?, ?)", [$roleId, $permissionId]);
        }
        return false;
    }
    
     public function removePermission($roleId, $permissionId) {
        return $this->query("DELETE FROM {$this->table} WHERE role_id = ? AND permission_id = ?", [$roleId, $permissionId]);
    }
    
    /**
     * Synchronise les permissions d'un rôle
     * Supprime les anciennes et ajoute les nouvelles
     */
    public function syncPermissions($roleId, $permissionIds) {
        // Supprimer toutes les permissions existantes
        $this->query(
            "DELETE FROM {$this->table} WHERE role_id = ?",
            [$roleId]
        );
        
        // Ajouter les nouvelles permissions
        if (!empty($permissionIds)) {
            $values = [];
            $params = [];
            foreach ($permissionIds as $permissionId) {
                $values[] = "(?, ?)";
                $params[] = $roleId;
                $params[] = $permissionId;
            }
            
            $sql = "INSERT INTO {$this->table} (role_id, permission_id) VALUES " . implode(", ", $values);
            $this->query($sql, $params);
        }
        
        return true;
    }
}
