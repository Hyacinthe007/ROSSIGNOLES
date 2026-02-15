<?php
/**
 * Routes API
 * 
 * Tous les endpoints sont préfixés par /api/ et protégés par authentification session.
 * Retournent du JSON (sauf les exports CSV).
 * 
 * Correspondance avec les pages web :
 *   /bulletins/list                       →  GET /api/bulletins
 *   /liste-personnel                      →  GET /api/personnel
 *   /eleves/list                          →  GET /api/eleves             (?search=xxx)
 *   /finance/recus                        →  GET /api/finance/recus      (?search=xxx)
 *   /finance/recus (export CSV)           →  GET /api/finance/recus/export-excel (?search=xxx)
 *   /finance/echeanciers                  →  GET /api/finance/echeanciers (?statut=retard)
 *   /finance/echeanciers?statut=exclusion →  GET /api/finance/echeanciers?statut=exclusion
 *   /parents/list                         →  GET /api/parents            (?search=xxx)
 *   /notes/list                           →  GET /api/notes              (?classe_id=X&periode_id=Y)
 */

$apiRoutes = [
    // Bulletins
    ['pattern' => 'api/bulletins',                 'method' => 'GET', 'handler' => 'ApiController@bulletins'],
    
    // Personnel
    ['pattern' => 'api/personnel',                 'method' => 'GET', 'handler' => 'ApiController@personnel'],
    
    // Élèves
    ['pattern' => 'api/eleves',                    'method' => 'GET', 'handler' => 'ApiController@eleves'],
    
    // Finance - Reçus
    ['pattern' => 'api/finance/recus',             'method' => 'GET', 'handler' => 'ApiController@getRecus'],
    ['pattern' => 'api/finance/recus/export-excel','method' => 'GET', 'handler' => 'ApiController@exportRecusExcel'],
    
    // Finance - Échéanciers
    ['pattern' => 'api/finance/echeanciers',       'method' => 'GET', 'handler' => 'ApiController@echeanciers'],
    
    // Parents
    ['pattern' => 'api/parents',                   'method' => 'GET', 'handler' => 'ApiController@parents'],
    
    // Notes
    ['pattern' => 'api/notes',                     'method' => 'GET', 'handler' => 'ApiController@notes'],
    
    // Classes - Élèves
    ['pattern' => 'api/classes/eleves',            'method' => 'GET', 'handler' => 'ApiController@classesEleves'],
];

return $apiRoutes;
