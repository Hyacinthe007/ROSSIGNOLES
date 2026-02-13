/**
 * Module Associer - Gestion avancée des associations Classes
 */

// Configuration
const BASE_URL = window.location.origin + '/ROSSIGNOLES';
const TOAST_DURATION = 3000;
const TOAST_FADE_DURATION = 300;
const RELOAD_DELAY_SHORT = 1000;
const RELOAD_DELAY_LONG = 1500;
const CSS_TRANSITION_DELAY = 10;
const ANIMATION_DURATION_MS = 300;
const PERCENTAGE_BASE = 100;
const STATS_RELOAD_DELAY = 1500;

// État global
let selectedClasses = new Set();

/**
 * Mise à jour inline d'une association
 */
function updateAssociation(selectElement) {
    var associationClasseId = selectElement.dataset.classeId;
    var associationField = selectElement.dataset.field;
    var associationValue = selectElement.value || null;

    // Afficher le spinner
    var container = selectElement.closest('.inline-edit-container');
    var spinner = container.querySelector('.spinner');
    spinner.classList.remove('hidden');
    selectElement.disabled = true;

    return fetch(BASE_URL + '/classes/associer/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
        },
        body: JSON.stringify({
            classe_id: associationClasseId,
            field: associationField,
            value: associationValue
        })
    })
        .then(function (response) { return response.json(); })
        .then(function (responseData) {
            if (responseData.success) {
                showToast('✓ ' + responseData.message, 'success');
                return refreshStats();
            } else {
                showToast('✗ ' + responseData.message, 'error');
                setTimeout(function () { location.reload(); }, RELOAD_DELAY_LONG);
            }
        })
        .catch(function (error) {
            console.error('Erreur updateAssociation:', error);
            showToast('✗ Erreur de connexion', 'error');
            setTimeout(function () { location.reload(); }, RELOAD_DELAY_LONG);
        })
        .finally(function () {
            spinner.classList.add('hidden');
            selectElement.disabled = false;
        });
}

/**
 * Mise à jour en masse des associations
 */
function bulkUpdate(field, value) {
    if (selectedClasses.size === 0) {
        showToast('⚠ Aucune classe sélectionnée', 'warning');
        return;
    }

    var classeIds = Array.from(selectedClasses);
    var confirmMsg = 'Voulez-vous vraiment modifier ' + classeIds.length + ' classe(s) ?';

    if (!confirm(confirmMsg)) {
        return;
    }

    // Afficher un overlay de chargement
    showLoadingOverlay();

    return fetch(BASE_URL + '/classes/associer/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
        },
        body: JSON.stringify({
            classe_ids: classeIds,
            field: field,
            value: value
        })
    })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                showToast('✓ ' + data.updated_count + ' classe(s) mise(s) à jour', 'success');
                setTimeout(function () { location.reload(); }, RELOAD_DELAY_SHORT);
            } else {
                showToast('✗ ' + data.message, 'error');
            }
        })
        .catch(function (error) {
            console.error('Erreur bulkUpdate:', error);
            showToast('✗ Erreur de connexion', 'error');
        })
        .finally(function () {
            hideLoadingOverlay();
        });
}

/**
 * Rafraîchir les statistiques
 */
async function refreshStats() {
    try {
        const response = await fetch(`${BASE_URL}/classes/associer/stats`);
        const data = await response.json();

        if (data.success) {
            const stats = data.stats;
            document.getElementById('stat-total').textContent = stats.total_classes;
            document.getElementById('stat-associees').textContent = stats.classes_associees;
            document.getElementById('stat-non-associees').textContent = stats.classes_non_associees;

            // Calculer et afficher le taux
            const taux = stats.total_classes > 0
                ? Math.round((stats.classes_associees / stats.total_classes) * 100)
                : 0;
            document.getElementById('stat-taux').textContent = taux + '%';
        }
    } catch (error) {
        // Erreur silencieuse pour les statistiques afin de ne pas bloquer l'interface utilisateur
        // mais nous pourrions ajouter un log si nécessaire pour le débogage
        console.debug('Échec de la mise à jour des statistiques:', error);
    }
}

/**
 * Gestion de la sélection multiple
 */
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.classe-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
        if (checkbox.checked) {
            selectedClasses.add(cb.value);
        } else {
            selectedClasses.delete(cb.value);
        }
    });
    updateBulkActionsBar();
}

function updateBulkActionsBar() {
    // Mettre à jour l'ensemble des classes sélectionnées
    selectedClasses.clear();
    document.querySelectorAll('.classe-checkbox:checked').forEach(cb => {
        selectedClasses.add(cb.value);
    });

    const bulkBar = document.getElementById('bulk-actions-bar');
    const countSpan = document.getElementById('selected-count');

    if (selectedClasses.size > 0) {
        bulkBar.classList.remove('hidden');
        countSpan.textContent = selectedClasses.size;
    } else {
        bulkBar.classList.add('hidden');
    }

    // Mettre à jour l'état de "Tout sélectionner"
    const selectAll = document.getElementById('select-all');
    const totalCheckboxes = document.querySelectorAll('.classe-checkbox').length;
    selectAll.checked = selectedClasses.size === totalCheckboxes && totalCheckboxes > 0;
}

function clearSelection() {
    selectedClasses.clear();
    document.querySelectorAll('.classe-checkbox').forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('select-all').checked = false;
    updateBulkActionsBar();
}

/**
 * Gestion des actions groupées
 */
document.addEventListener('DOMContentLoaded', function () {
    // Écouteur pour le niveau en masse
    var bulkNiveau = document.getElementById('bulk-niveau');
    if (bulkNiveau) {
        bulkNiveau.addEventListener('change', function (e) {
            if (e.target.value) {
                bulkUpdate('niveau_id', e.target.value);
                e.target.value = '';
            }
        });
    }

    // Écouteur pour la section en masse
    var bulkSection = document.getElementById('bulk-section');
    if (bulkSection) {
        bulkSection.addEventListener('change', function (e) {
            if (e.target.value) {
                var value = e.target.value === 'null' ? null : e.target.value;
                bulkUpdate('serie_id', value);
                e.target.value = '';
            }
        });
    }

    // Écouteur pour les selects d'édition inline (délégation)
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('inline-edit-select')) {
            updateAssociation(e.target);
        }
    });
});

/**
 * Afficher un toast notification
 */
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');

    const bgColors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-orange-500',
        info: 'bg-blue-500'
    };

    toast.className = `${bgColors[type]} text-white px-6 py-3 rounded-lg transform transition-all duration-${ANIMATION_DURATION_MS} translate-x-full`;
    toast.textContent = message;

    container.appendChild(toast);

    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, CSS_TRANSITION_DELAY);

    // Animation de sortie et suppression
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            container.removeChild(toast);
        }, TOAST_FADE_DURATION);
    }, TOAST_DURATION);
}

/**
 * Overlay de chargement
 */
function showLoadingOverlay() {
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';

    const container = document.createElement('div');
    container.className = 'bg-white rounded-xl p-8 flex flex-col items-center';

    const icon = document.createElement('i');
    icon.className = 'fas fa-spinner fa-spin text-blue-600 text-4xl mb-4';

    const text = document.createElement('p');
    text.className = 'text-gray-700 font-medium';
    text.textContent = 'Mise à jour en cours...';

    container.appendChild(icon);
    container.appendChild(text);
    overlay.appendChild(container);

    document.body.appendChild(overlay);
}

function hideLoadingOverlay() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

/**
 * Initialisation
 */
// Module chargé
