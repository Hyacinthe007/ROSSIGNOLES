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

// État global
let selectedClasses = new Set();

/**
 * Mise à jour inline d'une association
 */
async function updateAssociation(selectElement) {
    const associationClasseId = selectElement.dataset.classeId;
    const associationField = selectElement.dataset.field;
    const associationValue = selectElement.value || null;

    // Afficher le spinner
    const container = selectElement.closest('.inline-edit-container');
    const spinner = container.querySelector('.spinner');
    spinner.classList.remove('hidden');
    selectElement.disabled = true;

    try {
        const response = await fetch(`${BASE_URL}/classes/associer/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                classe_id: associationClasseId,
                field: associationField,
                value: associationValue
            })
        });

        const responseData = await response.json();

        if (responseData.success) {
            showToast('✓ ' + responseData.message, 'success');
            // Rafraîchir les statistiques
            await refreshStats();
        } else {
            showToast('✗ ' + responseData.message, 'error');
            // Recharger la page pour restaurer l'état correct
            setTimeout(() => location.reload(), RELOAD_DELAY_LONG);
        }
    } catch (error) {
        showToast('✗ Erreur de connexion', 'error');
        setTimeout(() => location.reload(), RELOAD_DELAY_LONG);
    } finally {
        spinner.classList.add('hidden');
        selectElement.disabled = false;
    }
}

/**
 * Mise à jour en masse des associations
 */
async function bulkUpdate(field, value) {
    if (selectedClasses.size === 0) {
        showToast('⚠ Aucune classe sélectionnée', 'warning');
        return;
    }

    const classeIds = Array.from(selectedClasses);
    const confirmMsg = `Voulez-vous vraiment modifier ${classeIds.length} classe(s) ?`;

    if (!confirm(confirmMsg)) {
        return;
    }

    // Afficher un overlay de chargement
    showLoadingOverlay();

    try {
        const response = await fetch(`${BASE_URL}/classes/associer/bulk-update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                classe_ids: classeIds,
                field: field,
                value: value
            })
        });

        const data = await response.json();

        if (data.success) {
            showToast(`✓ ${data.updated_count} classe(s) mise(s) à jour`, 'success');
            // Recharger la page pour afficher les changements
            setTimeout(() => location.reload(), RELOAD_DELAY_SHORT);
        } else {
            showToast('✗ ' + data.message, 'error');
        }
    } catch (error) {
        showToast('✗ Erreur de connexion', 'error');
    } finally {
        hideLoadingOverlay();
    }
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
        // Erreur silencieuse pour les stats
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
document.addEventListener('DOMContentLoaded', () => {
    // Écouteur pour le niveau en masse
    const bulkNiveau = document.getElementById('bulk-niveau');
    if (bulkNiveau) {
        bulkNiveau.addEventListener('change', async (e) => {
            if (e.target.value) {
                await bulkUpdate('niveau_id', e.target.value);
                e.target.value = ''; // Réinitialiser le select
            }
        });
    }

    // Écouteur pour la section en masse
    const bulkSection = document.getElementById('bulk-section');
    if (bulkSection) {
        bulkSection.addEventListener('change', async (e) => {
            if (e.target.value) {
                const value = e.target.value === 'null' ? null : e.target.value;
                await bulkUpdate('serie_id', value);
                e.target.value = ''; // Réinitialiser le select
            }
        });
    }

    // Écouteur pour les selects d'édition inline (délégation)
    document.addEventListener('change', async (e) => {
        if (e.target.classList.contains('inline-edit-select')) {
            await updateAssociation(e.target);
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

    toast.className = `${bgColors[type]} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
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
