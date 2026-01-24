<?php
$title = "Gestion des Évaluations";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => url('dashboard')],
    ['label' => 'Évaluations']
];
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
                Gestion des Évaluations
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Configurez et gérez les interrogations, devoirs et examens</p>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Interrogations & Devoirs -->
            <a href="<?= url('interrogations/list') ?>" class="p-8 bg-white rounded-2xl border-2 border-transparent shadow-xl hover:border-blue-400 transition-all text-left flex items-start gap-6 group">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-bold group-hover:bg-blue-600 group-hover:text-white transition-all shrink-0">
                    <i class="fas fa-edit text-2xl"></i>
                </div>
                <div class="flex-grow">
                    <span class="block text-xl font-bold text-gray-800 mb-2">Interrogations & Devoirs</span>
                    <p class="text-gray-500 text-sm mb-4">
                        Gérez les notes de contrôle continu, les interrogations écrites et les devoirs de classe.
                    </p>
                    <div class="flex items-center text-blue-600 font-bold text-sm group-hover:translate-x-2 transition-transform">
                        Voir la liste <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </div>
            </a>

            <!-- Examens -->
            <a href="<?= url('examens/list') ?>" class="p-8 bg-white rounded-2xl border-2 border-transparent shadow-xl hover:border-purple-400 transition-all text-left flex items-start gap-6 group">
                <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center font-bold group-hover:bg-purple-600 group-hover:text-white transition-all shrink-0">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
                <div class="flex-grow">
                    <span class="block text-xl font-bold text-gray-800 mb-2">Examens</span>
                    <p class="text-gray-500 text-sm mb-4">
                        Gérez les compositions trimestrielles, les examens blancs et les épreuves de fin d'année.
                    </p>
                    <div class="flex items-center text-purple-600 font-bold text-sm group-hover:translate-x-2 transition-transform">
                        Voir la liste <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
