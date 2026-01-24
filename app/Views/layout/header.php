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
        
        <div class="flex items-center gap-3">
            <!-- Notifications -->
            <button class="relative p-2 hover:bg-gray-100 rounded-full transition-colors">
                <i class="fas fa-bell text-gray-600"></i>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- Profil utilisateur -->
            <div class="flex items-center gap-2">
                <div class="hidden md:block text-right">
                    <p class="text-sm font-medium text-gray-800"><?= e(session('username', 'Utilisateur')) ?></p>
                    <p class="text-xs text-gray-500"><?= e(implode(', ', session('roles', []))) ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                    <?= strtoupper(substr(session('username', 'U'), 0, 1)) ?>
                </div>
            </div>

            <!-- Déconnexion -->
            <a href="<?= url('auth/logout') ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-sign-out-alt"></i>
                <span class="hidden sm:inline">Déconnexion</span>
            </a>
        </div>
    </header>

    <?php require_once APP_PATH . '/Views/layout/sidebar.php'; ?>
    
    <!-- ====== OVERLAY (voile noir mobile) ====== -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
<?php endif; ?>

<!-- ====== CONTENU PRINCIPAL ====== -->
<main class="<?= isLoggedIn() ? 'main-content pt-16' : '' ?>" id="mainContent">
