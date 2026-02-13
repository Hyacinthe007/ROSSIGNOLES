// JavaScript principal pour l'ERP École

// Constantes de configuration
const TOOLTIP_OFFSET = 10;
const NOTIFICATION_DURATION = 3000;
const NOTIFICATION_FADE_DURATION = 300;

document.addEventListener('DOMContentLoaded', function () {
    // Application chargée


    // Initialisation des tooltips
    initTooltips();

    // Initialisation des confirmations de suppression
    initDeleteConfirmations();

    // Initialisation des formulaires
    initFormValidation();
});

// Fonction pour les tooltips
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', handleTooltipShow);
        element.addEventListener('mouseleave', handleTooltipHide);
    });
}

// Gestionnaire d'affichage du tooltip
function handleTooltipShow(e) {
    const element = e.currentTarget;
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = element.getAttribute('data-tooltip');
    document.body.appendChild(tooltip);

    const rect = element.getBoundingClientRect();
    tooltip.style.top = (rect.top - tooltip.offsetHeight - TOOLTIP_OFFSET) + 'px';
    tooltip.style.left = (rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)) + 'px';

    element._tooltip = tooltip;
}

// Gestionnaire de masquage du tooltip
function handleTooltipHide(e) {
    const element = e.currentTarget;
    if (element._tooltip) {
        element._tooltip.remove();
        element._tooltip = null;
    }
}

// Fonction pour les confirmations de suppression
function initDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('[data-delete]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
            }
        });
    });
}

// Fonction pour la validation des formulaires
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');

                    // Retirer la classe après validation
                    field.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                    });
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    });
}

// Fonction helper pour obtenir la couleur de notification
function getNotificationColor(type) {
    const colors = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500'
    };
    return colors[type] || colors['info'];
}

// Fonction pour afficher les notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${getNotificationColor(type)} text-white`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), NOTIFICATION_FADE_DURATION);
    }, NOTIFICATION_DURATION);
}

// Fonction pour le formatage des montants
function formatMoney(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'MGA'
    }).format(amount);
}

// Fonction pour la recherche en temps réel
function initSearch(inputId, tableId) {
    const searchInput = document.getElementById(inputId);
    const table = document.getElementById(tableId);

    if (searchInput && table) {
        searchInput.addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
}

// Fonction pour les modales
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }
}

// Export des fonctions pour utilisation globale
window.ERP = {
    showNotification,
    formatMoney,
    initSearch,
    openModal,
    closeModal
};

function toggleSubMenu(element) {
    let parent = element.nextElementSibling;

    if (document.querySelector('.sidebar').classList.contains('collapsed')) {
        return; // En mode collapsed : pas de sous-menus
    }

    parent.style.display =
        parent.style.display === "block" ? "none" : "block";
}
