    </main>

    <!-- ====== SCRIPT ====== -->
    <!-- ====== SIDEBAR LOGIC & STYLES (DELEGATED EVENTS) ====== -->


    <script>
        /**
         * Sidebar Controller
         * Gestion professionnelle de la barre latérale avec :
         * - Délégation d'événements (Event Delegation) pour la performance et le support dynamique.
         * - Gestion d'état via Classe ES6 pour une meilleure encapsulation.
         * - Logique "Accordion" stricte (un seul élément ouvert à la fois).
         */
        class SidebarController {
            constructor() {
                // Éléments DOM principaux
                this.sidebar = document.getElementById('sidebar');
                this.toggleBtn = document.getElementById('toggleSidebarBtn');
                this.mobileBurgerBtn = document.getElementById('burgerBtn');
                this.overlay = document.getElementById('sidebarOverlay');

                // Configuration
                this.storageKey = 'sidebarCollapsed';
                this.isMobile = window.innerWidth < 1024;

                // Helpers Cookie
                this.setCookie = (name, value, days = 30) => {
                    const d = new Date();
                    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
                    let expires = "expires=" + d.toUTCString();
                    document.cookie = name + "=" + value + ";" + expires + ";path=/";
                };

                // Initialisation
                this.init();
            }

            init() {
                if (!this.sidebar) {
                    console.error('Sidebar non trouvée !');
                    return;
                }
                console.log('Sidebar trouvée:', this.sidebar);

                // 1. Restaurer l'état (persistance)
                this.restoreState();

                // 2. Installer les écouteurs globaux (Délégation propre)
                this.bindEvents();
                console.log('Événements liés avec succès');
            }

            /**
             * Restaure l'état précédent (plié/déplié) depuis le LocalStorage
             */
            restoreState() {
                // La classe est déjà appliquée par PHP (via cookie) pour éviter le flash
                // On s'assure juste que l'icône est synchro
                const isCollapsed = this.sidebar.classList.contains('collapsed');
                this.updateToggleIcon(isCollapsed);

                if (!isCollapsed) {
                    this.openActiveRouteGroup();
                }
            }

            /**
             * Gestion centralisée des événements (Event Delegation)
             * Capture tous les clics et les route vers la bonne méthode.
             */
            bindEvents() {
                document.addEventListener('click', (e) => this.handleGlobalClick(e));
            }

            handleGlobalClick(e) {
                // A. Clic sur un en-tête de menu (Accordion)
                const header = e.target.closest('.menu-item-header');
                if (header) {
                    // Vérification de sécurité : l'élément est bien dans NOTRE sidebar
                    if (!this.sidebar || !this.sidebar.contains(header)) return;

                    // En mode "Collapsed" (réduit), on désactive le collapsed et on ouvre le module
                    if (this.sidebar.classList.contains('collapsed')) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // 1. Désactiver le mode collapsed
                        this.sidebar.classList.remove('collapsed');
                        localStorage.setItem(this.storageKey, 'false');
                        
                        // 2. Mettre à jour l'icône du bouton toggle
                        if (this.toggleBtn) {
                            this.updateToggleIcon(false);
                        }
                        
                        // 3. Ouvrir le module sélectionné
                        const group = header.closest('.menu-group');
                        if (group) {
                            // Fermer tous les autres groupes d'abord
                            const allGroups = this.sidebar.querySelectorAll('.menu-group');
                            allGroups.forEach(g => {
                                g.classList.remove('expanded');
                                const content = g.querySelector('.menu-sub-items');
                                if (content) {
                                    content.classList.add('hidden');
                                }
                                const chevron = g.querySelector('.menu-item-header .fa-chevron-right, .menu-item-header i[class*="chevron"]');
                                if (chevron) {
                                    chevron.style.transform = 'rotate(0deg)';
                                }
                            });
                            
                            // Ouvrir le groupe sélectionné
                            group.classList.add('expanded');
                            const content = group.querySelector('.menu-sub-items');
                            if (content) {
                                content.classList.remove('hidden');
                            }
                            const chevron = group.querySelector('.menu-item-header .fa-chevron-right, .menu-item-header i[class*="chevron"]');
                            if (chevron) {
                                chevron.style.transform = 'rotate(90deg)';
                                chevron.style.transition = 'transform 0.3s ease';
                            }
                        }
                        return;
                    }

                    // En mode normal, on bascule l'accordéon
                    e.preventDefault();
                    e.stopPropagation();
                    const group = header.closest('.menu-group');
                    if (group) {
                        console.log('Toggle accordion pour le groupe:', group);
                        this.toggleAccordion(group);
                    }
                    return;
                }

                // A2. Clic sur un menu-item (lien)
                const menuItem = e.target.closest('.menu-item');
                if (menuItem && this.sidebar) {
                    // Vérifier si c'est un sous-menu (dans menu-sub-items)
                    const isSubMenuItem = menuItem.closest('.menu-sub-items');
                    
                    // Vérifier si c'est le lien Dashboard
                    const href = menuItem.getAttribute('href');
                    const isDashboardLink = href && (href.includes('/dashboard') || href.endsWith('dashboard'));
                    
                    console.log('Clic sur menu-item détecté:', {
                        element: menuItem,
                        isSubMenuItem: !!isSubMenuItem,
                        isDashboardLink: isDashboardLink,
                        tagName: menuItem.tagName,
                        isCollapsed: this.sidebar.classList.contains('collapsed'),
                        href: href
                    });
                    
                    // CAS 1: En mode collapsed
                    if (this.sidebar.classList.contains('collapsed')) {
                        // EXCEPTION: Si c'est le lien Dashboard, on ne fait RIEN (on laisse juste le lien naviguer)
                        if (isDashboardLink) {
                            console.log('→ Mode COLLAPSED + Dashboard: Sidebar reste fermé, navigation simple');
                            // On laisse le lien fonctionner normalement sans ouvrir le sidebar
                            return;
                        }
                        
                        console.log('→ Mode COLLAPSED: Ouverture de la sidebar');
                        // Si c'est un autre lien, on désactive collapsed et on laisse le lien fonctionner
                        e.stopPropagation();
                        
                        // Désactiver le mode collapsed
                        this.sidebar.classList.remove('collapsed');
                        localStorage.setItem(this.storageKey, 'false');
                        
                        // Mettre à jour l'icône du bouton toggle
                        if (this.toggleBtn) {
                            this.updateToggleIcon(false);
                        }
                        
                        // Le lien fonctionnera normalement (pas besoin de preventDefault)
                        return;
                    }
                    
                    // CAS 2: En mode normal
                    // Suppression de l'auto-collapse automatique pour éviter les mouvements brusques
                    // demandés par l'utilisateur. La barre reste dans l'état choisi.
                    return;
                }

                // B. Clic sur le bouton de bascule (Toggle Sidebar)
                if (this.toggleBtn && this.toggleBtn.contains(e.target)) {
                    e.preventDefault();
                    this.toggleSidebar();
                    return;
                }

                // C. Clic sur le menu Mobile (Burger)
                if (this.mobileBurgerBtn && this.mobileBurgerBtn.contains(e.target)) {
                    e.preventDefault();
                    this.toggleMobileMenu();
                    return;
                }

                // D. Clic sur l'Overlay (Fermeture Mobile)
                if (this.overlay && this.overlay.contains(e.target)) {
                    e.preventDefault();
                    this.closeMobileMenu();
                    return;
                }
            }

            /**
             * Logique de l'Accordéon (Un seul ouvert à la fois)
             */
            toggleAccordion(targetGroup) {
                const isAlreadyOpen = targetGroup.classList.contains('expanded');

                // 1. Fermer TOUS les groupes
                const allGroups = this.sidebar.querySelectorAll('.menu-group');
                allGroups.forEach(group => {
                    group.classList.remove('expanded');
                    const content = group.querySelector('.menu-sub-items');
                    if (content) {
                        content.classList.add('hidden');
                    }
                    // Réinitialiser la rotation du chevron
                    const chevron = group.querySelector('.menu-item-header .fa-chevron-right, .menu-item-header i[class*="chevron"]');
                    if (chevron) {
                        chevron.style.transform = 'rotate(0deg)';
                    }
                });

                // 2. Si le groupe ciblé n'était pas ouvert, on l'ouvre
                if (!isAlreadyOpen) {
                    targetGroup.classList.add('expanded');
                    const content = targetGroup.querySelector('.menu-sub-items');
                    if (content) {
                        content.classList.remove('hidden');
                    }
                    // Faire tourner le chevron
                    const chevron = targetGroup.querySelector('.menu-item-header .fa-chevron-right, .menu-item-header i[class*="chevron"]');
                    if (chevron) {
                        chevron.style.transform = 'rotate(90deg)';
                        chevron.style.transition = 'transform 0.3s ease';
                    }
                }
            }

            /**
             * Bascule complet de la Sidebar (Réduit <-> Déplié)
             */
            toggleSidebar() {
                const isCollapsed = this.sidebar.classList.toggle('collapsed');

                // Mise à jour de l'icône
                this.updateToggleIcon(isCollapsed);

                // Sauvegarde (Storage + Cookie)
                localStorage.setItem(this.storageKey, isCollapsed);
                this.setCookie(this.storageKey, isCollapsed);

                // Si on réduit, on ferme proprement tous les accordéons pour éviter les bugs visuels
                if (isCollapsed) {
                    this.closeAllAccordions();
                }
            }

            closeAllAccordions() {
                this.sidebar.querySelectorAll('.menu-group').forEach(group => {
                    group.classList.remove('expanded');
                    const content = group.querySelector('.menu-sub-items');
                    if (content) {
                        content.classList.add('hidden');
                    }
                });
            }

            openActiveRouteGroup() {
                // Cherche le lien actif (texte bleu ou fond bleu)
                const activeLink = this.sidebar.querySelector('a.text-blue-600.font-medium, a.bg-blue-50');
                if (activeLink) {
                    const group = activeLink.closest('.menu-group');
                    if (group) {
                        group.classList.add('expanded');
                        const content = group.querySelector('.menu-sub-items');
                        if (content) {
                            content.classList.remove('hidden');
                        }
                        // Faire tourner le chevron
                        const chevron = group.querySelector('.menu-item-header .fa-chevron-right, .menu-item-header i[class*="chevron"]');
                        if (chevron) {
                            chevron.style.transform = 'rotate(90deg)';
                            chevron.style.transition = 'transform 0.3s ease';
                        }
                    }
                }
            }

            updateToggleIcon(isCollapsed) {
                if (!this.toggleBtn) return;
                const svg = this.toggleBtn.querySelector('svg');
                if (svg) {
                    svg.style.transform = isCollapsed ? 'rotate(180deg)' : '';
                    svg.style.transition = 'transform 0.3s ease';
                }
            }

            // --- Gestion Mobile ---
            toggleMobileMenu() {
                this.sidebar.classList.toggle('mobile-open');
                if (this.overlay) this.overlay.classList.toggle('active');
                document.body.classList.toggle('overflow-hidden');
            }

            closeMobileMenu() {
                this.sidebar.classList.remove('mobile-open');
                if (this.overlay) this.overlay.classList.remove('active');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Instanciation au chargement du DOM
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Initialisation du SidebarController...');
            const controller = new SidebarController();
            console.log('SidebarController initialisé:', controller);

            // Initialisation du formateur de montants
            initAmountFormatters();
        });

        /**
         * Gestion globale du formatage des montants (séparateur de milliers)
         */
        function initAmountFormatters() {
            const inputs = document.querySelectorAll('.amount-format, input[type="number"].auto-format');
            
            inputs.forEach(input => {
                // Si c'est un input number, on le passe en text pour permettre les espaces
                if (input.type === 'number') {
                    input.type = 'text';
                    input.inputMode = 'numeric'; // Garde le pavé numérique sur mobile
                }

                // Formatage initial
                if (input.value) {
                    input.value = formatAmount(input.value);
                }

                // Événement à la saisie
                input.addEventListener('input', function(e) {
                    let cursorPosition = this.selectionStart;
                    let value = this.value;
                    let originalLength = value.length;

                    // Enlever tout ce qui n'est pas chiffre
                    let cleanValue = value.replace(/[^\d]/g, '');
                    
                    // Formater
                    let formattedValue = formatAmount(cleanValue);
                    this.value = formattedValue;

                    // Gérer la position du curseur
                    let newLength = formattedValue.length;
                    cursorPosition = cursorPosition + (newLength - originalLength);
                    this.setSelectionRange(cursorPosition, cursorPosition);
                    
                    // Déclencher un événement personnalisé si d'autres scripts écoutent
                    this.dispatchEvent(new CustomEvent('amount-changed', { detail: { value: cleanValue } }));
                });

                // Avant la soumission du formulaire, on enlève les espaces
                const form = input.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        input.value = unformatAmount(input.value);
                    });
                }
            });
        }

        function formatAmount(value) {
            if (value === null || value === undefined || value === '') return '';
            // Enlever tout ce qui n'est pas chiffre
            value = value.toString().replace(/[^\d]/g, '');
            // Ajouter les séparateurs de milliers (espace)
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        function unformatAmount(value) {
            if (!value) return '0';
            return value.toString().replace(/\s/g, '');
        }
    </script>
</body>
</html>
