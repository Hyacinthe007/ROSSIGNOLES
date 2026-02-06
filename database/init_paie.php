<?php
/**
 * Script d'initialisation du module de paie
 * À exécuter une seule fois pour configurer le système
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use App\Models\PaieParametreCotisation;
use App\Models\PaieTrancheIrsa;

echo "=== Initialisation du module de paie ===\n\n";

try {
    // 1. Initialiser les paramètres de cotisations
    echo "1. Initialisation des paramètres de cotisations...\n";
    $parametreCotisationModel = new PaieParametreCotisation();
    $result = $parametreCotisationModel->initialiserDefauts();
    
    if ($result) {
        echo "   ✓ Paramètres de cotisations initialisés (CNAPS, OSTIE, FMFP)\n";
    } else {
        echo "   ℹ Les paramètres de cotisations existent déjà\n";
    }
    
    // 2. Initialiser les tranches IRSA 2026
    echo "\n2. Initialisation des tranches IRSA 2026...\n";
    $trancheIrsaModel = new PaieTrancheIrsa();
    $result = $trancheIrsaModel->initialiserDefauts2026();
    
    if ($result) {
        echo "   ✓ Tranches IRSA 2026 initialisées (6 tranches)\n";
    } else {
        echo "   ℹ Les tranches IRSA 2026 existent déjà\n";
    }
    
    echo "\n=== Initialisation terminée avec succès ! ===\n";
    echo "\nVous pouvez maintenant accéder au module de paie via :\n";
    echo "http://localhost/ROSSIGNOLES/paie\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
    exit(1);
}
