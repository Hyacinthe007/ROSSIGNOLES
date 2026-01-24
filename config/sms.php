<?php
/**
 * Configuration SMS
 */

return [
    // Fournisseur à utiliser : 'simulation' (log uniquement), 'twilio', ou 'generic_http'
    'provider' => 'simulation', 
    
    'enabled' => false, // Passer à true pour activer l'envoi réel
    
    // Identifiant d'expéditeur (souvent limité à 11 caractères)
    'sender_id' => 'ECOLE',

    // --- Configuration Twilio ---
    'twilio' => [
        'sid' => '',
        'token' => '',
        'from' => '', // Votre numéro Twilio
    ],

    // --- Configuration Fournisseur Générique (API HTTP/REST) ---
    // Courant pour les passerelles SMS locales à Madagascar ou en Afrique
    'generic_http' => [
        'url' => '', // Exemple: https://api.provider.com/send
        'method' => 'GET', // GET ou POST
        'api_key' => '',
        'api_secret' => '',
        'params' => [
            'to_key' => 'to',      // Nom du paramètre pour le destinataire
            'msg_key' => 'message', // Nom du paramètre pour le message
            'extra_params' => [],   // Autres paramètres fixes (format key => value)
        ]
    ],
];
