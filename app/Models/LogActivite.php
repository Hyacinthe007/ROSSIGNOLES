<?php
require_once __DIR__ . '/BaseModel.php';

class LogActivite extends BaseModel {
    protected $table = 'logs_activites';
    protected $fillable = ['user_id', 'action', 'module', 'description', 'entite_type', 'entite_id', 'ip_address', 'user_agent'];
    
    public static function log($action, $module, $description = null, $entite_type = null, $entite_id = null) {
        $model = new self();
        return $model->create([
            'user_id' => $_SESSION['user_id'] ?? null,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'entite_type' => $entite_type,
            'entite_id' => $entite_id,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    public function getByUser($userId, $limit = 50) {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }
}
