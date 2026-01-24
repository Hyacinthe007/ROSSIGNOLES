<?php
/**
 * Script de test pour l'enregistrement du personnel
 */

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test d'enregistrement du personnel</h1>";

// Simuler une session
session_start();
$_SESSION['personnel_data']['type_personnel'] = 'enseignant';

// Simuler des données POST
$_POST = [
    'matricule' => 'ENS-0001',
    'nom' => 'TEST',
    'prenom' => 'Utilisateur',
    'sexe' => 'M',
    'date_naissance' => '1990-01-01',
    'lieu_naissance' => 'Antananarivo',
    'cin' => '123456789012',
    'telephone' => '0340000000',
    'email' => 'test@example.com',
    'adresse' => 'Test Address',
    'date_embauche' => date('Y-m-d'),
    'diplome' => 'Licence',
    'specialite' => 'Mathématiques, Physique',
    'grade' => 'professeur'
];

echo "<h2>Données POST :</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Charger les fichiers nécessaires
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');

require_once BASE_PATH . '/config/database.php';
require_once APP_PATH . '/Models/Personnel.php';
require_once APP_PATH . '/Models/PersonnelEnseignant.php';

try {
    echo "<h2>Test de connexion à la base de données...</h2>";
    $pdo = getDbConnection();
    echo "✅ Connexion réussie<br>";
    
    echo "<h2>Test de création du personnel...</h2>";
    
    $type = $_SESSION['personnel_data']['type_personnel'];
    
    $personnelData = [
        'matricule' => $_POST['matricule'],
        'nom' => mb_strtoupper($_POST['nom']),
        'prenom' => mb_convert_case($_POST['prenom'], MB_CASE_TITLE, "UTF-8"),
        'sexe' => $_POST['sexe'],
        'date_naissance' => $_POST['date_naissance'],
        'lieu_naissance' => $_POST['lieu_naissance'],
        'cin' => $_POST['cin'],
        'adresse' => $_POST['adresse'],
        'telephone' => $_POST['telephone'],
        'email' => $_POST['email'],
        'date_embauche' => $_POST['date_embauche'],
        'type_contrat' => 'cdi',
        'statut' => 'actif',
        'type_personnel' => $type
    ];
    
    echo "<h3>Données à insérer dans personnels :</h3>";
    echo "<pre>";
    print_r($personnelData);
    echo "</pre>";
    
    $personnelModel = new Personnel();
    $personnelId = $personnelModel->create($personnelData);
    
    echo "✅ Personnel créé avec ID : $personnelId<br>";
    
    if ($type === 'enseignant' && $personnelId) {
        echo "<h2>Test de création de l'enseignant...</h2>";
        
        $enseignantData = [
            'personnel_id' => $personnelId,
            'specialite' => $_POST['specialite'],
            'diplome' => $_POST['diplome'],
            'grade' => $_POST['grade'],
            'anciennete_annees' => 0
        ];
        
        echo "<h3>Données à insérer dans personnels_enseignants :</h3>";
        echo "<pre>";
        print_r($enseignantData);
        echo "</pre>";
        
        $enseignantModel = new PersonnelEnseignant();
        $enseignantId = $enseignantModel->create($enseignantData);
        
        echo "✅ Enseignant créé avec ID : $enseignantId<br>";
    }
    
    echo "<h2 style='color: green;'>✅ SUCCÈS : Enregistrement terminé !</h2>";
    
    // Vérifier dans la base
    echo "<h2>Vérification dans la base de données...</h2>";
    $stmt = $pdo->query("SELECT * FROM personnels WHERE id = $personnelId");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ ERREUR :</h2>";
    echo "<p style='color: red; font-weight: bold;'>" . $e->getMessage() . "</p>";
    echo "<h3>Stack trace :</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
