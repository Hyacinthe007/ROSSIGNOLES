<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'ERP École') ?> | École Mandroso</title>
    <link rel="icon" type="image/png" href="<?= url('public/uploads/favicone/favicon.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['Outfit', 'sans-serif'],
                        serif: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('public/assets/css/admin-style.css') ?>">
    <link rel="stylesheet" href="<?= url('public/assets/css/global-tooltips.css') ?>">
    <script src="<?= url('public/assets/js/global-tooltips.js') ?>" defer></script>
    <script src="<?= url('public/assets/js/secure-actions.js') ?>" defer></script>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <style>
        /* Barre de recherche avec bordure tournante continue */
        .smart-search-box {
            position: relative;
            padding: 3px; /* Bordure légèrement plus épaisse pour qu'elle soit bien "pleine" */
            border-radius: 9999px;
            background: #fff;
            display: flex;
            align-items: center;
            width: 100%;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .smart-search-box::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            /* Utilisation d'une dimension dynamique pour couvrir toute la largeur/hauteur même en format ultra-large */
            width: 150%; 
            padding-bottom: 150%; 
            background: conic-gradient(
                from 0deg,
                #3b82f6, #6366f1, #8b5cf6, #d946ef, #ec4899, #f43f5e, 
                #f97316, #eab308, #84cc16, #22c55e, #10b981, #06b6d4, #3b82f6
            );
            animation: rotate-gradient 4s linear infinite;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .smart-search-inner {
            position: relative;
            z-index: 1;
            background: #fff; /* Retour à un fond plein pour une ligne bien nette sur le bord */
            width: 100%;
            height: 100%;
            border-radius: 9999px;
            display: flex;
            align-items: center;
        }

        @keyframes rotate-gradient {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .smart-search-input {
            flex: 1;
            height: 36px; /* Réduit de 42px à 36px */
            padding: 0 12px;
            border: none !important;
            background: transparent !important;
            font-size: 0.95rem;
            color: #1f2937;
            outline: none !important;
            font-weight: 500;
        }

        .smart-search-input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .smart-search-icon {
            margin-left: 16px;
            color: #9ca3af;
            font-size: 15px;
        }

        .smart-search-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-right: 14px;
        }

        .smart-search-action-btn {
            color: #71717a;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .smart-search-action-btn:hover {
            color: #3b82f6;
            transform: scale(1.1);
        }

        /* Copilot-style Icon */
        .copilot-swirl {
            width: 26px;
            height: 26px;
            background: linear-gradient(135deg, #10b981, #3b82f6, #6366f1, #a855f7, #ec4899);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-10deg);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .copilot-swirl::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.4), transparent);
            left: -100%;
            top: 0;
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            100% { left: 100%; }
        }
    </style>
</head>
<body class="bg-gray-50">

<?php if (isLoggedIn()): ?>
    <!-- ====== HEADER ====== -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white border-b z-50 flex items-center px-4 justify-between shadow-sm no-print">
        <div class="flex items-center gap-3">
            <!-- Bouton burger : visible uniquement < lg -->
            <button id="burgerBtn" class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-md border hover:bg-gray-100 transition-colors"
                aria-controls="sidebar" aria-expanded="false" aria-label="Ouvrir le menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Bouton toggle sidebar -->
            <button id="toggleSidebarBtn" class="hidden lg:inline-flex items-center justify-center w-10 h-10 rounded-md border hover:bg-gray-100 transition-colors"
                aria-label="Réduire/Agrandir le menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>

            <a href="<?= url('dashboard') ?>" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                <i class="fas fa-graduation-cap text-2xl text-blue-600"></i>
                <span class="text-xl font-bold text-gray-800 hidden sm:block">École Mandroso</span>
            </a>
        </div>

        <!-- Recherche globale inteligente (Desktop) -->
        <?php 
        $currentUri = $_SERVER['REQUEST_URI'];
        $isHelpPage = strpos($currentUri, 'systeme/aide') !== false 
                   || strpos($currentUri, 'systeme/apropos') !== false
                   || (strpos($currentUri, 'finance/recus') !== false && isset($_GET['id']));
        if (!$isHelpPage): 
        ?>
        <div class="hidden md:flex flex-1 max-w-lg mx-8 relative">
            <div class="smart-search-box group w-full">
                <div class="smart-search-inner">
                    <span class="smart-search-icon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           id="globalSearch"
                           autocomplete="off"
                           placeholder="Rechercher Ctrl + k..."
                           class="smart-search-input">
                    <div class="smart-search-actions">
                    </div>
                </div>
            </div>
            <!-- Résultats de recherche -->
            <div id="searchResults" class="absolute top-full left-0 right-0 mt-3 bg-white rounded-2xl shadow-2xl border border-gray-100 hidden overflow-hidden z-[60] animate-in fade-in slide-in-from-top-2 duration-200">
            </div>
        </div>

        <!-- Bouton recherche mobile -->
        <button id="mobileSearchBtn" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-md border hover:bg-gray-100 transition-colors"
            aria-label="Recherche">
            <i class="fas fa-search text-gray-600"></i>
        </button>
        <?php endif; ?>
        
        <div class="flex items-center gap-3">
            <!-- Notifications: removed per request -->

            <!-- Profil utilisateur -->
            <div class="flex items-center gap-2">
                <div class="hidden md:block text-right">
                    <p class="text-sm font-medium text-gray-800"><?= e(session('username', 'Utilisateur')) ?></p>
                    <p class="text-xs text-gray-500"><?= e(implode(', ', session('roles', []))) ?></p>
                </div>
                <?php $userPhoto = session('avatar') ?? null; $userId = session('user_id') ?? null; ?>
                <div class="relative">
                    <button id="profileMenuBtn" aria-haspopup="true" aria-expanded="false" class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center focus:outline-none ring-2 ring-transparent hover:ring-blue-100 transition">
                        <?php if ($userPhoto): ?>
                            <img src="<?= public_url($userPhoto) ?>" alt="Photo utilisateur" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-blue-500 text-white flex items-center justify-center font-medium">
                                <?= strtoupper(substr(session('username', 'U'), 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </button>

                    <!-- Dropdown -->
                    <div id="profileMenu" class="hidden origin-top-right absolute right-0 mt-2 w-64 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                        <div class="p-4 border-b">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                    <?php if ($userPhoto): ?>
                                        <img src="<?= public_url($userPhoto) ?>" alt="Avatar" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-blue-500 text-white flex items-center justify-center font-medium"><?= strtoupper(substr(session('username', 'U'), 0, 1)) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-gray-800"><?= e(session('username', 'Utilisateur')) ?></div>
                                    <div class="text-xs text-gray-500"><?= e(session('email', '')) ?></div>
                                    <a href="<?= url('systeme/utilisateurs/edit/' . ($userId ?? '')) ?>" class="block text-xs text-blue-600 mt-2">Modifier profil</a>
                                </div>
                            </div>
                        </div>

                        <div class="py-2">
                            <a href="<?= url('systeme/config') ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-cog text-gray-400 w-4"></i>
                                <span>Paramètres</span>
                            </a>
                            <a href="<?= url('systeme/utilisateurs') ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user text-gray-400 w-4"></i>
                                <span>Utilisateurs</span>
                            </a>
                            <a href="<?= url('systeme/aide') ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-question-circle text-gray-400 w-4"></i>
                                <span>Aide</span>
                            </a>
                            <a href="<?= url('systeme/apropos') ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-info-circle text-gray-400 w-4"></i>
                                <span>À propos</span>
                            </a>
                        </div>

                        <div class="border-t">
                            <a href="<?= url('auth/logout') ?>" class="flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-gray-50">
                                <i class="fas fa-sign-out-alt text-red-500 w-4"></i>
                                <span>Déconnexion</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <?php require_once APP_PATH . '/Views/layout/sidebar.php'; ?>

    <!-- ====== MODAL RECHERCHE MOBILE ====== -->
    <div id="mobileSearchModal" class="fixed inset-0 bg-black/50 hidden z-[100] flex items-start justify-center pt-4 px-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg animate-in slide-in-from-top-4 duration-200">
            <div class="p-4 border-b flex items-center gap-3">
                <i class="fas fa-search text-gray-400"></i>
                <input type="text" 
                       id="mobileSearchInput"
                       autocomplete="off"
                       placeholder="Rechercher un élève, parent, enseignant..." 
                       class="flex-1 bg-transparent outline-none text-sm placeholder-gray-500">
                <button id="closeMobileSearchBtn" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div id="mobileSearchResults" class="max-h-[70vh] overflow-y-auto">
            </div>
        </div>
    </div>
    
    <!-- ====== OVERLAY (voile noir mobile) ====== -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
<?php endif; ?>

<!-- ====== CONTENU PRINCIPAL ====== -->
<main class="<?= isLoggedIn() ? 'main-content pt-16' : '' ?>" id="mainContent">
    <!-- Messages Flash -->
    <div class="px-0 sm:px-6 lg:px-8 mt-4">
        <?php if ($success = session_flash('success')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm flex justify-between items-center animate-fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <p><?= e($success) ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($error = session_flash('error')): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm flex justify-between items-center animate-fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <p><?= e($error) ?></p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        (function(){
            const btn = document.getElementById('profileMenuBtn');
            const menu = document.getElementById('profileMenu');
            if (!btn || !menu) return;

            function openMenu() {
                menu.classList.remove('hidden');
                btn.setAttribute('aria-expanded', 'true');
            }
            function closeMenu() {
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }

            btn.addEventListener('click', function(e){
                e.stopPropagation();
                if (menu.classList.contains('hidden')) openMenu(); else closeMenu();
            });

            // Close when clicking outside
            document.addEventListener('click', function(e){
                if (!menu.contains(e.target) && !btn.contains(e.target)) closeMenu();
            });

            // Close on Escape
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape') closeMenu();
            });
        })();

        // Global Search Logic - Desktop & Mobile
        (function() {
            const searchInput = document.getElementById('globalSearch');
            const resultsContainer = document.getElementById('searchResults');
            const mobileSearchBtn = document.getElementById('mobileSearchBtn');
            const mobileSearchModal = document.getElementById('mobileSearchModal');
            const mobileSearchInput = document.getElementById('mobileSearchInput');
            const mobileSearchResults = document.getElementById('mobileSearchResults');
            const closeMobileSearchBtn = document.getElementById('closeMobileSearchBtn');
            
            let debounceTimer;

            if (!searchInput || !resultsContainer) return;

            // ===== DESKTOP SEARCH =====
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    resultsContainer.innerHTML = '';
                    resultsContainer.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    performSearch(query, resultsContainer);
                }, 300);
            });

            searchInput.addEventListener('focus', function() {
                if (this.value.trim().length >= 2 && resultsContainer.innerHTML !== '') {
                    resultsContainer.classList.remove('hidden');
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                    resultsContainer.classList.add('hidden');
                }
            });

            // ===== MOBILE SEARCH =====
            if (mobileSearchBtn && mobileSearchModal && mobileSearchInput) {
                mobileSearchBtn.addEventListener('click', () => {
                    mobileSearchModal.classList.remove('hidden');
                    mobileSearchInput.focus();
                });

                closeMobileSearchBtn.addEventListener('click', () => {
                    mobileSearchModal.classList.add('hidden');
                    mobileSearchInput.value = '';
                    mobileSearchResults.innerHTML = '';
                });

                mobileSearchModal.addEventListener('click', (e) => {
                    if (e.target === mobileSearchModal) {
                        mobileSearchModal.classList.add('hidden');
                    }
                });

                mobileSearchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        mobileSearchResults.innerHTML = '';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        performSearch(query, mobileSearchResults);
                    }, 300);
                });
            }

            // ===== KEYBOARD SHORTCUT =====
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    if (window.innerWidth >= 768) {
                        // Desktop: focus search
                        searchInput.focus();
                        searchInput.select();
                    } else {
                        // Mobile: open modal
                        if (mobileSearchModal) {
                            mobileSearchModal.classList.remove('hidden');
                            mobileSearchInput.focus();
                        }
                    }
                }
                // Close on Escape
                if (e.key === 'Escape') {
                    if (mobileSearchModal && !mobileSearchModal.classList.contains('hidden')) {
                        mobileSearchModal.classList.add('hidden');
                    }
                    resultsContainer.classList.add('hidden');
                }
            });

            // ===== PERFORM SEARCH =====
            function performSearch(query, container) {
                fetch(`<?= url('search/global') ?>?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        renderResults(data, container);
                    })
                    .catch(err => console.error('Erreur recherche:', err));
            }

            function renderResults(results, container) {
                // Flatten results if they come in grouped format
                let allResults = [];
                if (Array.isArray(results)) {
                    allResults = results;
                } else {
                    // Grouped format
                    if (results.eleves) allResults = allResults.concat(results.eleves);
                    if (results.parents) allResults = allResults.concat(results.parents);
                    if (results.personnel) allResults = allResults.concat(results.personnel);
                }

                if (allResults.length === 0) {
                    container.innerHTML = `
                        <div class="p-4 text-center text-gray-400">
                            <i class="fas fa-search-minus mb-2 text-xl block"></i>
                            <span class="text-sm">Aucun résultat trouvé</span>
                        </div>`;
                } else {
                    let html = '<div class="py-2">';
                    
                    // Group results by category
                    let grouped = { 'élève': [], 'parent': [], 'enseignant': [], 'personnel': [] };
                    allResults.forEach(item => {
                        const category = item.type || 'personnel';
                        if (!grouped[category]) grouped[category] = [];
                        grouped[category].push(item);
                    });

                    // Render grouped results
                    let hasContent = false;
                    const categoryLabels = { 
                        'élève': { label: 'Élèves', icon: 'fas fa-user-graduate', color: 'blue' },
                        'parent': { label: 'Parents', icon: 'fas fa-user-friends', color: 'purple' },
                        'enseignant': { label: 'Enseignants', icon: 'fas fa-chalkboard-teacher', color: 'green' },
                        'personnel': { label: 'Personnel', icon: 'fas fa-user-tie', color: 'orange' }
                    };

                    Object.keys(categoryLabels).forEach(category => {
                        if (grouped[category] && grouped[category].length > 0) {
                            hasContent = true;
                            const label = categoryLabels[category];
                            html += `
                                <div class="px-4 py-2 mt-2 mb-1">
                                    <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                        <i class="${label.icon}"></i>
                                        <span>${label.label}</span>
                                        <span class="ml-auto bg-gray-100 text-gray-600 rounded-full px-2 py-0.5 text-xs">${grouped[category].length}</span>
                                    </div>
                                </div>
                            `;
                            
                            grouped[category].forEach(item => {
                                const photoUrl = item.photo 
                                    ? `<?= public_url('') ?>${item.photo}`
                                    : null;
                                
                                const photoHtml = photoUrl 
                                    ? `<img src="${photoUrl}" class="w-10 h-10 rounded-lg object-cover ring-1 ring-gray-100">`
                                    : `<div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center ring-1 ring-blue-100">
                                         <i class="${item.icon}"></i>
                                       </div>`;
                                
                                html += `
                                    <a href="${item.url}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 transition-all group border-l-4 border-transparent hover:border-blue-500">
                                        ${photoHtml}
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-gray-800 group-hover:text-blue-700 transition-colors truncate">${item.title}</div>
                                            <div class="text-[10px] text-gray-400 group-hover:text-blue-400 mt-0.5 truncate">${item.subtitle}</div>
                                        </div>
                                        <div class="text-gray-300 group-hover:text-blue-400 transition-all transform group-hover:translate-x-1 hidden md:block">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </div>
                                    </a>
                                `;
                            });
                        }
                    });

                    if (!hasContent) {
                        html = `
                            <div class="p-4 text-center text-gray-400">
                                <i class="fas fa-search-minus mb-2 text-xl block"></i>
                                <span class="text-sm">Aucun résultat trouvé</span>
                            </div>`;
                    }
                    
                    html += '</div>';
                    container.innerHTML = html;
                }
                
                if (container === resultsContainer) {
                    container.classList.remove('hidden');
                }
            }
        })();
    </script>
