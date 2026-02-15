<?php
declare(strict_types=1);

/**
 * Configuration des événements métier ROSSIGNOLES V2
 * 
 * Ce fichier est chargé au démarrage de l'application (index.php)
 * Il enregistre tous les listeners pour les événements métier.
 */

use App\Core\EventDispatcher;

return function() {
    // ─────────────────────────────────────────────────────
    // PAIEMENTS
    // ─────────────────────────────────────────────────────

    // Quand un paiement est reçu → mettre à jour les montants de la facture
    EventDispatcher::listen('paiement.recu', function(array $data) {
        error_log("[Event] paiement.recu — Facture #{$data['facture_id']} montant {$data['montant']}");
        // La logique existante dans FinanceController sera progressivement déplacée ici
    }, priority: 10);

    // Quand un paiement est reçu → vérifier et mettre à jour l'échéancier
    EventDispatcher::listen('paiement.recu', function(array $data) {
        error_log("[Event] paiement.recu → Vérification échéancier pour élève #{$data['eleve_id']}");
    }, priority: 5);

    // ─────────────────────────────────────────────────────
    // INSCRIPTIONS
    // ─────────────────────────────────────────────────────

    // Quand une inscription est validée → générer la facture + échéancier
    EventDispatcher::listen('inscription.validee', function(array $data) {
        error_log("[Event] inscription.validee — Élève #{$data['eleve_id']} Classe #{$data['classe_id']}");
    }, priority: 10);

    // Quand une inscription est annulée → vérifier les finances
    EventDispatcher::listen('inscription.annulee', function(array $data) {
        error_log("[Event] inscription.annulee — Inscription #{$data['inscription_id']}");
    }, priority: 10);

    // ─────────────────────────────────────────────────────
    // NOTES & BULLETINS
    // ─────────────────────────────────────────────────────

    // Quand des notes sont saisies → invalider le cache du bulletin
    EventDispatcher::listen('notes.saisies', function(array $data) {
        \App\Core\Cache::forgetPrefix('bulletin_');
        error_log("[Event] notes.saisies → Cache bulletins invalidé (classe #{$data['classe_id']})");
    }, priority: 10);

    // Quand un bulletin est généré → notifier
    EventDispatcher::listen('bulletin.genere', function(array $data) {
        error_log("[Event] bulletin.genere — Élève #{$data['eleve_id']} Période #{$data['periode_id']}");
    }, priority: 5);

    // ─────────────────────────────────────────────────────
    // ABSENCES
    // ─────────────────────────────────────────────────────

    // Quand une absence est enregistrée → vérifier les seuils d'alerte
    EventDispatcher::listen('absence.enregistree', function(array $data) {
        error_log("[Event] absence.enregistree — Élève #{$data['eleve_id']}");
    }, priority: 5);

    // ─────────────────────────────────────────────────────
    // CACHE — Invalidation automatique
    // ─────────────────────────────────────────────────────

    // Quand une classe est modifiée → invalider le cache des classes
    EventDispatcher::listen('classe.modifiee', function(array $data) {
        \App\Core\Cache::forgetPrefix('classes_');
        \App\Core\Cache::forgetPrefix('effectifs_');
    });

    // Quand un élève est modifié → invalider le cache des élèves
    EventDispatcher::listen('eleve.modifie', function(array $data) {
        \App\Core\Cache::forgetPrefix('eleves_');
    });

    // Quand la configuration est modifiée → tout invalider
    EventDispatcher::listen('config.modifiee', function(array $data) {
        \App\Core\Cache::flush();
        error_log("[Event] config.modifiee → Cache entièrement vidé");
    });
};
