/**
 * Système de Tooltips Global
 * Remplace les tooltips natifs du navigateur par des tooltips uniformisés
 * Style identique à ceux de la sidebar en mode collapsed
 */

class GlobalTooltipManager {
    constructor() {
        this.tooltip = null;
        this.currentElement = null;
        this.showDelay = 200; // Délai avant affichage (ms) - réduit pour plus de réactivité
        this.hideDelay = 0; // Pas de délai pour le masquage
        this.showTimer = null;
        this.hideTimer = null;

        this.init();
    }

    init() {
        // Créer l'élément tooltip une seule fois
        this.createTooltip();

        // Écouter tous les éléments avec attribut title
        this.bindEvents();
    }

    createTooltip() {
        this.tooltip = document.createElement('div');
        this.tooltip.className = 'global-tooltip';
        document.body.appendChild(this.tooltip);
    }

    bindEvents() {
        // Utiliser la délégation d'événements pour capturer tous les éléments avec title
        document.addEventListener('mouseover', (e) => this.handleMouseOver(e), true);
        document.addEventListener('mouseout', (e) => this.handleMouseOut(e), true);
        document.addEventListener('mousemove', (e) => this.handleMouseMove(e), true);
    }

    handleMouseOver(e) {
        const element = e.target.closest('[title], [data-original-title]');

        if (!element) return;

        // Vérifier si l'élément est dans la sidebar
        const sidebar = document.getElementById('sidebar');
        const isInSidebar = sidebar && sidebar.contains(element);

        // Si l'élément est dans la sidebar mais qu'elle n'est PAS en mode collapsed, ne rien faire
        if (isInSidebar && sidebar && !sidebar.classList.contains('collapsed')) {
            return;
        }

        // Annuler le timer de masquage si on revient sur un élément
        if (this.hideTimer) {
            clearTimeout(this.hideTimer);
            this.hideTimer = null;
        }

        // Si c'est le même élément, ne rien faire
        if (this.currentElement === element) return;

        // Masquer le tooltip précédent immédiatement si on change d'élément
        if (this.currentElement && this.currentElement !== element) {
            this.hideTooltip();
        }

        this.currentElement = element;

        // Sauvegarder le title original et le retirer pour éviter le tooltip natif
        if (element.hasAttribute('title') && !element.hasAttribute('data-original-title')) {
            element.setAttribute('data-original-title', element.getAttribute('title'));
            element.removeAttribute('title');
        }

        const text = element.getAttribute('data-original-title');
        if (!text) return;

        // Délai avant affichage
        this.showTimer = setTimeout(() => {
            this.showTooltip(text, element);
        }, this.showDelay);
    }

    handleMouseOut(e) {
        const element = e.target.closest('[data-original-title]');

        if (!element || element !== this.currentElement) return;

        // Vérifier si la souris quitte vraiment l'élément (pas juste un enfant)
        const relatedTarget = e.relatedTarget;
        if (relatedTarget && element.contains(relatedTarget)) {
            // La souris est toujours sur l'élément ou un de ses enfants
            return;
        }

        // Annuler le timer d'affichage si on quitte avant qu'il ne s'affiche
        if (this.showTimer) {
            clearTimeout(this.showTimer);
            this.showTimer = null;
        }

        // Masquer immédiatement
        this.hideTooltip();
        this.currentElement = null;
    }

    showTooltip(text, element) {
        this.tooltip.textContent = text;
        this.tooltip.classList.add('show');

        // Positionner initialement
        const rect = element.getBoundingClientRect();
        this.positionTooltipRelativeToElement(rect);
    }

    handleMouseMove(e) {
        if (!this.tooltip.classList.contains('show')) return;

        // Mettre à jour la position du tooltip en suivant la souris
        this.positionTooltip(e.clientX, e.clientY);
    }

    hideTooltip() {
        this.tooltip.classList.remove('show');
        this.tooltip.className = 'global-tooltip'; // Retirer les classes de position
    }

    positionTooltipRelativeToElement(rect) {
        const tooltipRect = this.tooltip.getBoundingClientRect();
        const spacing = 10; // Espace entre l'élément et le tooltip

        // Calculer la position optimale
        let top, left;
        let position = 'bottom'; // Position par défaut

        // Vérifier s'il y a assez d'espace en bas
        if (window.innerHeight - rect.bottom > tooltipRect.height + spacing + 20) {
            // Afficher en bas
            top = rect.bottom + spacing;
            left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
            position = 'bottom';
        }
        // Sinon, afficher en haut
        else if (rect.top > tooltipRect.height + spacing + 20) {
            top = rect.top - tooltipRect.height - spacing;
            left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
            position = 'top';
        }
        // Sinon, afficher à droite
        else if (window.innerWidth - rect.right > tooltipRect.width + spacing + 20) {
            top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
            left = rect.right + spacing;
            position = 'right';
        }
        // Sinon, afficher à gauche
        else {
            top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
            left = rect.left - tooltipRect.width - spacing;
            position = 'left';
        }

        // Ajuster si le tooltip dépasse à gauche ou à droite
        if (left < 10) left = 10;
        if (left + tooltipRect.width > window.innerWidth - 10) {
            left = window.innerWidth - tooltipRect.width - 10;
        }

        // Ajuster si le tooltip dépasse en haut ou en bas
        if (top < 10) top = 10;
        if (top + tooltipRect.height > window.innerHeight - 10) {
            top = window.innerHeight - tooltipRect.height - 10;
        }

        this.tooltip.style.top = `${top}px`;
        this.tooltip.style.left = `${left}px`;
        this.tooltip.classList.add(`position-${position}`);
    }

    positionTooltip(mouseX, mouseY) {
        const tooltipRect = this.tooltip.getBoundingClientRect();
        const offset = 15;

        let top = mouseY + offset;
        let left = mouseX + offset;

        // Ajuster si le tooltip dépasse à droite
        if (left + tooltipRect.width > window.innerWidth - 10) {
            left = mouseX - tooltipRect.width - offset;
        }

        // Ajuster si le tooltip dépasse en bas
        if (top + tooltipRect.height > window.innerHeight - 10) {
            top = mouseY - tooltipRect.height - offset;
        }

        this.tooltip.style.top = `${top}px`;
        this.tooltip.style.left = `${left}px`;
    }
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    window.tooltipManager = new GlobalTooltipManager();
    console.log('Système de tooltips global initialisé');
});
