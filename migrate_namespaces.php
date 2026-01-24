<?php
/**
 * Script de migration automatique vers namespaces PSR-4
 * Usage: php migrate_namespaces.php
 */

$modelsDir = __DIR__ . '/app/Models';
$controllersDir = __DIR__ . '/app/Controllers';

function addNamespaceToFile($filePath, $namespace, $useStatements = []) {
    if (!file_exists($filePath)) {
        echo "❌ Fichier non trouvé: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    
    // Vérifier si le namespace existe déjà
    if (strpos($content, "namespace $namespace;") !== false) {
        echo "✓ Namespace déjà présent: $filePath\n";
        return true;
    }
    
    // Trouver la position après <?php
    $phpTagPos = strpos($content, '<?php');
    if ($phpTagPos === false) {
        echo "❌ Pas de tag PHP trouvé: $filePath\n";
        return false;
    }
    
    // Construire le nouveau header
    $newHeader = "<?php\ndeclare(strict_types=1);\n\nnamespace $namespace;\n\n";
    
    // Ajouter les use statements
    foreach ($useStatements as $use) {
        $newHeader .= "use $use;\n";
    }
    
    if (!empty($useStatements)) {
        $newHeader .= "\n";
    }
    
    // Remplacer <?php par le nouveau header
    $content = preg_replace('/<\?php\s*/', $newHeader, $content, 1);
    
    // Sauvegarder
    file_put_contents($filePath, $content);
    echo "✅ Migré: $filePath\n";
    return true;
}

// Liste des modèles à migrer
$models = [
    'Eleve.php',
    'Classe.php',
    'Inscription.php',
    'Facture.php',
    'Paiement.php',
    'Personnel.php',
    'User.php',
    'AnneeScolaire.php',
    'Niveau.php',
    'Serie.php',
    'Parent.php',
    'Bulletin.php',
    'Note.php',
    'Matiere.php',
    'ExamenFinal.php',
    'Interrogation.php',
    'ModePaiement.php',
    'TypeFacture.php',
    'LigneFacture.php',
    'TarifInscription.php',
    'Article.php',
    'InscriptionArticle.php',
    'DocumentsInscription.php',
    'EcheancierEcolage.php',
];

echo "🚀 Migration des Models vers App\\Models\n";
echo str_repeat("=", 50) . "\n\n";

foreach ($models as $model) {
    $filePath = $modelsDir . '/' . $model;
    addNamespaceToFile($filePath, 'App\\Models', ['PDO', 'PDOException', 'Exception']);
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Migration des models terminée!\n\n";

// Liste des contrôleurs à migrer
$controllers = [
    'BaseController.php',
    'ElevesController.php',
    'ClassesController.php',
    'InscriptionsController.php',
    'FinanceController.php',
    'NotesController.php',
    'BulletinsController.php',
    'PedagogieController.php',
    'DashboardController.php',
    'AuthController.php',
    'UsersController.php',
];

echo "🚀 Migration des Controllers vers App\\Controllers\n";
echo str_repeat("=", 50) . "\n\n";

foreach ($controllers as $controller) {
    $filePath = $controllersDir . '/' . $controller;
    $useStatements = [
        'App\\Models\\BaseModel',
    ];
    addNamespaceToFile($filePath, 'App\\Controllers', $useStatements);
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Migration des controllers terminée!\n\n";

echo "📝 Prochaines étapes:\n";
echo "1. Exécuter: composer dump-autoload\n";
echo "2. Mettre à jour les routes dans routes/web.php\n";
echo "3. Mettre à jour index.php\n";
echo "4. Tester l'application\n";
