<?php
/**
 * Script de réparation et migration forcée de la base de données paie
 */

// Définir les constantes de base nécessaires pour les modèles et helpers
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once ROOT_PATH . '/vendor/autoload.php';
require_once APP_PATH . '/Helpers/functions.php';
require_once CONFIG_PATH . '/database.php';

use App\Models\BaseModel;

echo "=== Réparation de la structure des tables Paie ===\n\n";

$db = BaseModel::getDBConnection();

try {
    // 1. paie_parametres_cotisations
    echo "1. Vérification de paie_parametres_cotisations...\n";
    $columns = $db->query("SHOW COLUMNS FROM paie_parametres_cotisations LIKE 'nom'")->fetchAll();
    if (empty($columns)) {
        echo "   -> Ajout de la colonne 'nom'...\n";
        $db->exec("ALTER TABLE paie_parametres_cotisations ADD COLUMN nom VARCHAR(50) NOT NULL UNIQUE AFTER id");
    }
    
    $columns = $db->query("SHOW COLUMNS FROM paie_parametres_cotisations LIKE 'description'")->fetchAll();
    if (empty($columns)) {
        echo "   -> Ajout de la colonne 'description'...\n";
        $db->exec("ALTER TABLE paie_parametres_cotisations ADD COLUMN description TEXT DEFAULT NULL AFTER taux_patronal");
    }

    // 2. paie_tranches_irsa
    echo "2. Vérification de paie_tranches_irsa...\n";
    $columns = $db->query("SHOW COLUMNS FROM paie_tranches_irsa LIKE 'annee_validite'")->fetchAll();
    if (empty($columns)) {
        echo "   -> Ajout de la colonne 'annee_validite'...\n";
        $db->exec("ALTER TABLE paie_tranches_irsa ADD COLUMN annee_validite INT(4) DEFAULT 2026 COMMENT 'Année de validité de cette grille' AFTER taux");
    }

    // 3. paie_bulletins
    echo "3. Vérification des index paie_bulletins...\n";
    $indexes = $db->query("SHOW INDEX FROM paie_bulletins WHERE Key_name = 'idx_periode_statut'")->fetchAll();
    if (empty($indexes)) {
        echo "   -> Création de l'index idx_periode_statut...\n";
        $db->exec("ALTER TABLE paie_bulletins ADD INDEX idx_periode_statut (periode, statut)");
    }

    // 4. paie_contrats
    echo "4. Vérification de paie_contrats...\n";
    $indexes = $db->query("SHOW INDEX FROM paie_contrats WHERE Key_name = 'idx_actif'")->fetchAll();
    if (empty($indexes)) {
        echo "   -> Création de l'index idx_actif...\n";
        $db->exec("ALTER TABLE paie_contrats ADD INDEX idx_actif (actif)");
    }
    
    $indexes = $db->query("SHOW INDEX FROM paie_contrats WHERE Key_name = 'unique_personnel_actif'")->fetchAll();
    if (empty($indexes)) {
        echo "   -> Création de la contrainte unique_personnel_actif...\n";
        // On désactive les anciens contrats pour éviter les doublons au cas où
        $db->exec("UPDATE paie_contrats SET actif = 0 WHERE id NOT IN (SELECT max_id FROM (SELECT MAX(id) as max_id FROM paie_contrats GROUP BY personnel_id) as t)");
        $db->exec("ALTER TABLE paie_contrats ADD UNIQUE KEY unique_personnel_actif (personnel_id, actif)");
    }

    echo "\n✅ Structure de la base de données mise à jour avec succès.\n\n";

    // Maintenant on peut lancer l'initialisation des données
    echo "=== Initialisation des données par défaut ===\n\n";
    
    require_once __DIR__ . '/init_paie.php';

} catch (Exception $e) {
    echo "\n❌ ERREUR : " . $e->getMessage() . "\n";
    exit(1);
}
