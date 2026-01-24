<?php
$title = "Nouvelle " . ($type === 'examen' ? 'Examen' : 'Interrogation');
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => url('dashboard')],
    ['label' => 'Évaluations', 'url' => url('evaluations')],
    ['label' => $title]
];
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-<?= $type === 'examen' ? 'file-alt' : 'question-circle' ?> text-<?= $type === 'examen' ? 'blue' : 'green' ?>-600 mr-2"></i>
            <?= $title ?>
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Remplissez les informations de l'évaluation</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-4xl">
        <form method="POST" action="<?= url('evaluations/add?type=' . $type) ?>" class="space-y-6">
            <input type="hidden" name="annee_scolaire_id" value="<?= $anneeActive['id'] ?>">
            
            <!-- Informations de base -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Classe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users text-gray-400 mr-1"></i>
                        Classe <span class="text-red-500">*</span>
                    </label>
                    <select name="classe_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionnez une classe</option>
                        <?php foreach ($classes as $classe): ?>
                            <option value="<?= $classe['id'] ?>"><?= e($classe['nom']) ?> (<?= e($classe['code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Période -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar text-gray-400 mr-1"></i>
                        Période <span class="text-red-500">*</span>
                    </label>
                    <select name="periode_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionnez une période</option>
                        <?php foreach ($periodes as $periode): ?>
                            <option value="<?= $periode['id'] ?>"><?= e($periode['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Matière -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-book text-gray-400 mr-1"></i>
                        Matière <span class="text-red-500">*</span>
                    </label>
                    <select name="matiere_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionnez une matière</option>
                        <?php foreach ($matieres as $matiere): ?>
                            <option value="<?= $matiere['id'] ?>"><?= e($matiere['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-day text-gray-400 mr-1"></i>
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_evaluation" required value="<?= date('Y-m-d') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Paramètres spécifiques -->
            <?php if ($type === 'examen'): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Type d'examen -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag text-gray-400 mr-1"></i>
                            Type d'examen
                        </label>
                        <select name="type_examen" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="composition">Composition</option>
                            <option value="devoir_surveille">Devoir surveillé</option>
                            <option value="examen_blanc">Examen blanc</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <!-- Coefficient -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-weight text-gray-400 mr-1"></i>
                            Coefficient
                        </label>
                        <input type="number" name="coefficient" value="2" min="1" max="10" step="0.5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Note sur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-star text-gray-400 mr-1"></i>
                            Note sur
                        </label>
                        <input type="number" name="note_sur" value="20" min="1" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Durée -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock text-gray-400 mr-1"></i>
                            Durée (minutes)
                        </label>
                        <input type="number" name="duree_minutes" value="60" min="5" max="240" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Coefficient -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-weight text-gray-400 mr-1"></i>
                            Coefficient
                        </label>
                        <input type="number" name="coefficient" value="1" min="0.5" max="5" step="0.5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Note sur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-star text-gray-400 mr-1"></i>
                            Note sur
                        </label>
                        <input type="number" name="note_sur" value="20" min="1" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left text-gray-400 mr-1"></i>
                    Description / Titre
                </label>
                <textarea name="description" rows="3" placeholder="Ex: Composition du 1er trimestre, Chapitre 1-3..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>

            <!-- Boutons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-end pt-4 border-t">
                <a href="<?= url('evaluations') ?>" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition text-center">
                    <i class="fas fa-times mr-2"></i>
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-<?= $type === 'examen' ? 'blue' : 'green' ?>-600 hover:bg-<?= $type === 'examen' ? 'blue' : 'green' ?>-700 text-white rounded-lg transition shadow-lg">
                    <i class="fas fa-check mr-2"></i>
                    Créer et saisir les notes
                </button>
            </div>
        </form>
    </div>
</div>
