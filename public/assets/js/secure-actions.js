/**
 * Secure Actions - Convertit les liens GET dangereux en requêtes POST
 * Intercepte les clics sur les liens de suppression/toggle/activation
 * et les soumet en POST avec le token CSRF
 */
(function() {
    'use strict';

    var DANGEROUS_PATTERNS = [
        /\/delete\//i,
        /\/supprimer\//i,
        /\/toggle/i,
        /\/activer\//i,
        /\/activate\//i
    ];

    var CONFIRM_MESSAGES = {
        'delete': 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.',
        'supprimer': 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.',
        'toggle': 'Êtes-vous sûr de vouloir modifier le statut de cet élément ?',
        'activer': 'Êtes-vous sûr de vouloir modifier le statut de cet élément ?',
        'activate': 'Êtes-vous sûr de vouloir modifier le statut de cet élément ?'
    };

    function isDangerousUrl(url) {
        for (var i = 0; i < DANGEROUS_PATTERNS.length; i++) {
            if (DANGEROUS_PATTERNS[i].test(url)) return true;
        }
        return false;
    }

    function getConfirmMessage(url) {
        var keys = Object.keys(CONFIRM_MESSAGES);
        for (var i = 0; i < keys.length; i++) {
            if (url.toLowerCase().indexOf('/' + keys[i]) !== -1) {
                return CONFIRM_MESSAGES[keys[i]];
            }
        }
        return 'Êtes-vous sûr de vouloir effectuer cette action ?';
    }

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function submitAsPost(url) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.style.display = 'none';

        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = getCsrfToken();
        form.appendChild(csrfInput);

        document.body.appendChild(form);
        form.submit();
    }

    document.addEventListener('click', function(e) {
        var link = e.target.closest ? e.target.closest('a') : null;
        if (!link || !link.href) return;

        // Ignorer les liens marqués comme sûrs
        if (link.hasAttribute('data-safe') || link.hasAttribute('data-no-intercept')) return;

        // Ignorer les liens externes
        try {
            var linkUrl = new URL(link.href);
            if (linkUrl.origin !== window.location.origin) return;
        } catch(err) { return; }

        if (isDangerousUrl(link.href)) {
            e.preventDefault();
            e.stopPropagation();

            var message = link.getAttribute('data-confirm') || getConfirmMessage(link.href);

            if (confirm(message)) {
                submitAsPost(link.href);
            }
        }
    });
})();
