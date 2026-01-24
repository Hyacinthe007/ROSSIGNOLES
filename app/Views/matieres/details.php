<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-book text-blue-600 mr-2"></i>
                Détails de la matière
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Informations complètes de la matière</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('matieres/edit/' . $matiere['id']) ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Modifier</span>
            </a>
            <a href="<?= url('matieres/list') ?>" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <div class="flex items-center gap-6 mb-6">
            <div class="bg-blue-100 p-4 rounded-lg">
                <i class="fas fa-book text-blue-600 text-4xl"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    <?= e($matiere['nom']) ?>
                </h2>
                <p class="text-gray-600">
                    <i class="fas fa-id-card mr-2"></i>Code: <span class="font-semibold"><?= e($matiere['code']) ?></span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
            <!-- Coefficients par Niveau -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-layer-group text-blue-600"></i>
                    Coefficients par Niveau
                </h3>
                <?php if (empty($coefficientsNiveaux)): ?>
                    <p class="text-gray-500 text-sm ">Aucun coefficient configuré par niveau.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($coefficientsNiveaux as $cn): ?>
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                <div>
                                    <span class="font-semibold text-gray-700"><?= e($cn['niveau_nom']) ?></span>
                                    <span class="text-xs text-gray-400 ml-2">(<?= e($cn['code']) ?>)</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs text-gray-400"><?= $cn['heures_semaine'] ?? 0 ?>h/sem</span>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-bold text-sm">
                                        Coef: <?= $cn['coefficient'] ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Coefficients par Série -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-stream text-purple-600"></i>
                    Coefficients par Série
                </h3>
                <?php if (empty($coefficientsSeries)): ?>
                    <p class="text-gray-500 text-sm ">Aucun coefficient configuré par série.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($coefficientsSeries as $cs): ?>
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                <div>
                                    <div class="font-semibold text-gray-700"><?= e($cs['serie_nom']) ?></div>
                                    <div class="text-[10px] text-gray-400 uppercase"><?= e($cs['niveau_nom']) ?></div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs text-gray-400"><?= $cs['heures_semaine'] ?? 0 ?>h/sem</span>
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full font-bold text-sm">
                                        Coef: <?= $cs['coefficient'] ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-8 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Statut de la matière</p>
                <p class="font-semibold text-gray-800">
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                        <?= ($matiere['actif'] ?? 1) ? 'Active' : 'Inactive' ?>
                    </span>
                </p>
            </div>
        </div>

        <?php if (!empty($matiere['description'])): ?>
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
                <p class="text-gray-800"><?= e($matiere['description']) ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>


