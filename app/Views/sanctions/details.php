<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                Détails de la sanction
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Informations complètes de la sanction</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('sanctions/edit/' . $sanction['id']) ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Modifier</span>
            </a>
            <a href="<?= url('sanctions/list') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if ($eleve): ?>
            <div class="bg-red-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-user-graduate text-red-600"></i>
                    Élève
                </h3>
                <p class="text-gray-800"><?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?></p>
                <p class="text-sm text-gray-600">Matricule: <?= e($eleve['matricule']) ?></p>
            </div>
            <?php endif; ?>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Date</p>
                    <p class="font-semibold text-gray-800"><?= formatDate($sanction['date_sanction']) ?></p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tag text-orange-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Type</p>
                    <p class="font-semibold text-gray-800"><?= e($sanction['type_sanction_libelle'] ?? 'N/A') ?></p>
                    <?php if (isset($sanction['type_gravite']) && $sanction['type_gravite']): ?>
                        <?php 
                            $gravite = (int)$sanction['type_gravite'];
                            $color = $gravite >= 7 ? 'red' : ($gravite >= 4 ? 'orange' : 'yellow');
                            $label = $gravite >= 7 ? 'Grave' : ($gravite >= 4 ? 'Moyen' : 'Léger');
                        ?>
                        <span class="px-2 py-1 bg-<?= $color ?>-100 text-<?= $color ?>-800 rounded text-xs mt-1 inline-block">
                            <?= $label ?> (<?= $gravite ?>)
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($sanction['motif'])): ?>
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Motif</h3>
                <p class="text-gray-800"><?= e($sanction['motif']) ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

