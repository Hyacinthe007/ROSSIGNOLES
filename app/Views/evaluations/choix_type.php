<?php
$title = "Nouvelle Évaluation";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => url('dashboard')],
    ['label' => 'Évaluations', 'url' => url('evaluations')],
    ['label' => 'Nouvelle évaluation']
];
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
            Nouvelle Évaluation
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Choisissez le type d'évaluation à créer</p>
    </div>

    <!-- Choix du type -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
        <!-- Examen Final -->
        <a href="<?= url('evaluations/add?type=examen') ?>" class="group">
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300 border-2 border-transparent hover:border-blue-500 cursor-pointer">
                <div class="flex flex-col items-center text-center">
                    <div class="bg-blue-100 p-6 rounded-full mb-4 group-hover:bg-blue-600 transition-colors duration-300">
                        <i class="fas fa-file-alt text-4xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Examen Final</h3>
                    <p class="text-gray-600 text-sm mb-4">
                        Composition, devoir surveillé ou examen de fin de période
                    </p>
                    <div class="flex flex-wrap gap-2 justify-center">
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            Coefficient élevé
                        </span>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            Note importante
                        </span>
                    </div>
                </div>
            </div>
        </a>

        <!-- Interrogation -->
        <a href="<?= url('evaluations/add?type=interrogation') ?>" class="group">
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all duration-300 border-2 border-transparent hover:border-green-500 cursor-pointer">
                <div class="flex flex-col items-center text-center">
                    <div class="bg-green-100 p-6 rounded-full mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <i class="fas fa-question-circle text-4xl text-green-600 group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Interrogation</h3>
                    <p class="text-gray-600 text-sm mb-4">
                        Interrogation écrite, contrôle continu ou quiz
                    </p>
                    <div class="flex flex-wrap gap-2 justify-center">
                        <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium">
                            Évaluation rapide
                        </span>
                        <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium">
                            Contrôle continu
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Bouton retour -->
    <div class="mt-8 text-center">
        <a href="<?= url('evaluations') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la liste</span>
        </a>
    </div>
</div>
