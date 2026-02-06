    </main>

    <!-- ====== SCRIPT ====== -->
    <!-- ====== SIDEBAR LOGIC & STYLES (DELEGATED EVENTS) ====== -->


    <script>
        /**
         * Sidebar Controller
         * Gestion professionnelle de la barre latÃ©rale avec :
         * - DÃ©lÃ©gation d'Ã©vÃ©nements (Event Delegation) pour la performance et le support dynamique.
         * - Gestion d'Ã©tat via Classe ES6 pour une meilleure encapsulation.
         * - Logique "Accordion" stricte (un seul Ã©lÃ©ment ouvert Ã  la fois).
         */
        class SidebarController {
            constructor() {
                // Ã‰lÃ©ments DOM principaux
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
                    console.error('Sidebar non trouvÃ©e !');
                    return;
                }
                console.log('Sidebar trouvÃ©e:', this.sidebar);

                // 1. Restaurer l'Ã©tat (persistance)
                this.restoreState();

                // 2. Installer les Ã©couteurs globaux (DÃ©lÃ©gation propre)
                this.bindEvents();
                console.log('Ã‰vÃ©nements liÃ©s avec succÃ¨s');
            }

            /**
             * Restaure l'Ã©tat prÃ©cÃ©dent (pliÃ©/dÃ©pliÃ©) depuis le LocalStorage
             */
            restoreState() {
                // La classe est dÃ©jÃ  appliquÃ©e par PHP (via cookie) pour Ã©viter le flash
                // On s'assure juste que l'icÃ´ne est synchro
                const isCollapsed = this.sidebar.classList.contains('collapsed');
                this.updateToggleIcon(isCollapsed);

                if (!isCollapsed) {
                    this.openActiveRouteGroup();
                }
            }

            /**
             * Gestion centralisÃ©e des Ã©vÃ©nements (Event Delegation)
             * Capture tous les clics et les route vers la bonne mÃ©thode.
             */
            bindEvents() {
                document.addEventListener('click', (e) => this.handleGlobalClick(e));
            }

            handleGlobalClick(e) {
                // A. Clic sur un en-tÃªte de menu (Accordion)
                const header = e.target.closest('.menu-item-header');
                if (header) {
                    // VÃ©rification de sÃ©curitÃ© : l'Ã©lÃ©ment est bien dans NOTRE sidebar
                    if (!this.sidebar || !this.sidebar.contains(header)) return;

                    // En mode "Collapsed" (rÃ©duit), on dÃ©sactive le collapsed et on ouvre le module
                    if (this.sidebar.classList.contains('collapsed')) {
                        e.preventDefault();
                        e.stopPropagation();

                        // 1. DÃ©sactiver le mode collapsed
                        this.sidebar.classList.remove('collapsed');
                        localStorage.setItem(this.storageKey, 'false');

                        // 2. Mettre Ã  jour l'icÃ´ne du bouton toggle
                        if (this.toggleBtn) {
                            this.updateToggleIcon(false);
                        }

                        // 3. Ouvrir le module sÃ©lectionnÃ©
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

                            // Ouvrir le groupe sÃ©lectionnÃ©
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

                    // En mode normal, on bascule l'accordÃ©on
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
                    // VÃ©rifier si c'est un sous-menu (dans menu-sub-items)
                    const isSubMenuItem = menuItem.closest('.menu-sub-items');

                    // VÃ©rifier si c'est le lien Dashboard
                    const href = menuItem.getAttribute('href');
                    const isDashboardLink = href && (href.includes('/dashboard') || href.endsWith('dashboard'));

                    console.log('Clic sur menu-item dÃ©tectÃ©:', {
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
                            console.log('â†’ Mode COLLAPSED + Dashboard: Sidebar reste fermÃ©, navigation simple');
                            // On laisse le lien fonctionner normalement sans ouvrir le sidebar
                            return;
                        }

                        console.log('â†’ Mode COLLAPSED: Ouverture de la sidebar');
                        // Si c'est un autre lien, on dÃ©sactive collapsed et on laisse le lien fonctionner
                        e.stopPropagation();

                        // DÃ©sactiver le mode collapsed
                        this.sidebar.classList.remove('collapsed');
                        localStorage.setItem(this.storageKey, 'false');

                        // Mettre Ã  jour l'icÃ´ne du bouton toggle
                        if (this.toggleBtn) {
                            this.updateToggleIcon(false);
                        }

                        // Le lien fonctionnera normalement (pas besoin de preventDefault)
                        return;
                    }

                    // CAS 2: En mode normal
                    // Suppression de l'auto-collapse automatique pour Ã©viter les mouvements brusques
                    // demandÃ©s par l'utilisateur. La barre reste dans l'Ã©tat choisi.
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
             * Logique de l'AccordÃ©on (Un seul ouvert Ã  la fois)
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
                    // RÃ©initialiser la rotation du chevron
                    const chevron = group.querySelector('.menu-item-header .fa-chevron-right, .menu-item-header i[class*="chevron"]');
                    if (chevron) {
                        chevron.style.transform = 'rotate(0deg)';
                    }
                });

                // 2. Si le groupe ciblÃ© n'Ã©tait pas ouvert, on l'ouvre
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
             * Bascule complet de la Sidebar (RÃ©duit <-> DÃ©pliÃ©)
             */
            toggleSidebar() {
                const isCollapsed = this.sidebar.classList.toggle('collapsed');

                // Mise Ã  jour de l'icÃ´ne
                this.updateToggleIcon(isCollapsed);

                // Sauvegarde (Storage + Cookie)
                localStorage.setItem(this.storageKey, isCollapsed);
                this.setCookie(this.storageKey, isCollapsed);

                // Si on rÃ©duit, on ferme proprement tous les accordÃ©ons pour Ã©viter les bugs visuels
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
            console.log('SidebarController initialisÃ©:', controller);

            // Initialisation du formateur de montants
            initAmountFormatters();
        });

        /**
         * Gestion globale du formatage des montants (sÃ©parateur de milliers)
         */
        function initAmountFormatters() {
            const inputs = document.querySelectorAll('.amount-format, input[type="number"].auto-format');

            inputs.forEach(input => {
                // Si c'est un input number, on le passe en text pour permettre les espaces
                if (input.type === 'number') {
                    input.type = 'text';
                    input.inputMode = 'numeric'; // Garde le pavÃ© numÃ©rique sur mobile
                }

                // Formatage initial
                if (input.value) {
                    input.value = formatAmount(input.value);
                }

                // Ã‰vÃ©nement Ã  la saisie
                input.addEventListener('input', function(e) {
                    let cursorPosition = this.selectionStart;
                    let value = this.value;
                    let originalLength = value.length;

                    // Enlever tout ce qui n'est pas chiffre
                    let cleanValue = value.replace(/[^\d]/g, '');

                    // Formater
                    let formattedValue = formatAmount(cleanValue);
                    this.value = formattedValue;

                    // GÃ©rer la position du curseur
                    let newLength = formattedValue.length;
                    cursorPosition = cursorPosition + (newLength - originalLength);
                    this.setSelectionRange(cursorPosition, cursorPosition);

                    // DÃ©clencher un Ã©vÃ©nement personnalisÃ© si d'autres scripts Ã©coutent
                    this.dispatchEvent(new CustomEvent('amount-changed', { detail: { value: cleanValue } }));
                });

                // Avant la soumission du formulaire, on enlÃ¨ve les espaces
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
            // Ajouter les sÃ©parateurs de milliers (espace)
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        function unformatAmount(value) {
            if (!value) return '0';
            return value.toString().replace(/\s/g, '');
        }
    </script>
</body>
</html>
