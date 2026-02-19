<?php
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
require_once APP_PATH . '/Helpers/functions.php';
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_PATH . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

$pdo = \App\Models\BaseModel::getDBConnection();

// Add "evaluations" permissions (Gestion évaluations)
$exists = $pdo->query("SELECT id FROM permissions WHERE code = 'evaluations.view'")->fetch();
if (!$exists) {
    $pdo->exec("INSERT INTO permissions (code, module, action, description) VALUES 
        ('evaluations.view', 'Évaluations', 'view', 'Visualiser les évaluations'),
        ('evaluations.create', 'Évaluations', 'create', 'Créer des évaluations'),
        ('evaluations.update', 'Évaluations', 'update', 'Modifier les évaluations'),
        ('evaluations.delete', 'Évaluations', 'delete', 'Supprimer les évaluations')
    ");
    echo "[ADD] Added 'evaluations.*' permissions (Gestion évaluations)\n";
} else {
    echo "[SKIP] 'evaluations.*' already exists\n";
}

// Verify
$stmt = $pdo->query("SELECT id, code, action, description FROM permissions WHERE module = 'Évaluations' ORDER BY code");
echo "\n=== Évaluations permissions ===\n";
while ($r = $stmt->fetch()) {
    echo "  [{$r['id']}] {$r['code']} ({$r['action']}) - {$r['description']}\n";
}
