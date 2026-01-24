<?php
/**
 * Configuration générale de l'application
 */

return [
    'app_name' => 'ERP École - ROSSIGNOLES',
    'app_version' => '1.0.0',
    'app_url' => 'http://localhost/ROSSIGNOLES',
    'timezone' => 'Indian/Antananarivo',
    'locale' => 'fr_FR',
    'charset' => 'UTF-8',
    
    // Chemins
    'paths' => [
        'root' => dirname(__DIR__),
        'app' => dirname(__DIR__) . '/app',
        'public' => dirname(__DIR__) . '/public',
        'storage' => dirname(__DIR__) . '/storage',
        'uploads' => dirname(__DIR__) . '/public/uploads',
    ],
    
    // Session
    'session' => [
        'lifetime' => 7200, // 2 heures
        'name' => 'rossignoles_session',
    ],
    
    // Pagination
    'pagination' => [
        'per_page' => 20,
    ],
    
    // Uploads
    'uploads' => [
        'max_size' => 10485760, // 10 MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
    ],
];

