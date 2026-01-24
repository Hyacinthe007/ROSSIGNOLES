<?php
/**
 * Affichage des logs d'erreur PHP
 */

echo "<h1>Logs d'erreur PHP</h1>";
echo "<p>Dernières 100 lignes du fichier error.log</p>";

// Chemins possibles pour les logs
$logPaths = [
    'C:/xampp/apache/logs/error.log',
    'C:/wamp64/logs/php_error.log',
    'C:/laragon/www/logs/error.log',
    __DIR__ . '/logs/error.log',
    ini_get('error_log')
];

echo "<h2>Chemin du fichier de log configuré :</h2>";
echo "<p><code>" . ini_get('error_log') . "</code></p>";

foreach ($logPaths as $logPath) {
    if (file_exists($logPath)) {
        echo "<h2>Fichier trouvé : <code>$logPath</code></h2>";
        
        $lines = file($logPath);
        $lastLines = array_slice($lines, -100);
        
        echo "<div style='background: #f5f5f5; padding: 15px; border: 1px solid #ddd; max-height: 600px; overflow-y: scroll;'>";
        echo "<pre style='margin: 0; font-size: 12px;'>";
        
        foreach ($lastLines as $line) {
            // Colorer les lignes selon le type
            if (stripos($line, 'error') !== false || stripos($line, '❌') !== false) {
                echo "<span style='color: red;'>" . htmlspecialchars($line) . "</span>";
            } elseif (stripos($line, 'warning') !== false) {
                echo "<span style='color: orange;'>" . htmlspecialchars($line) . "</span>";
            } elseif (stripos($line, '✅') !== false || stripos($line, 'success') !== false) {
                echo "<span style='color: green;'>" . htmlspecialchars($line) . "</span>";
            } elseif (stripos($line, '===') !== false) {
                echo "<span style='color: blue; font-weight: bold;'>" . htmlspecialchars($line) . "</span>";
            } else {
                echo htmlspecialchars($line);
            }
        }
        
        echo "</pre>";
        echo "</div>";
        
        break;
    }
}

echo "<hr>";
echo "<h2>Configuration PHP</h2>";
echo "<p><strong>error_reporting:</strong> " . error_reporting() . "</p>";
echo "<p><strong>display_errors:</strong> " . ini_get('display_errors') . "</p>";
echo "<p><strong>log_errors:</strong> " . ini_get('log_errors') . "</p>";

echo "<hr>";
echo "<p><a href='?refresh=1' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🔄 Rafraîchir</a></p>";
