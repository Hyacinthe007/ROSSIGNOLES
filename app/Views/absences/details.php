<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-times text-red-600 mr-2"></i>
                Détails de l'absence
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Informations complètes de l'absence</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('absences/edit/' . $absence['id']) ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Modifier</span>
            </a>
            <a href="<?= url('absences/list') ?>" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations de l'élève -->
            <?php if ($eleve): ?>
            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-user-graduate text-blue-600"></i>
                    Élève
                </h3>
                <p class="text-gray-800"><?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?></p>
                <p class="text-sm text-gray-600">Matricule: <?= e($eleve['matricule']) ?></p>
            </div>
            <?php endif; ?>

            <!-- Statut -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle text-gray-600"></i>
                    Statut
                </h3>
                <?php if ($absence['justifiee']): ?>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle mr-1"></i>Justifiée
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-times-circle mr-1"></i>Non justifiée
                    </span>
                <?php endif; ?>
            </div>

            <!-- Date début -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Date début</p>
                    <p class="font-semibold text-gray-800"><?= formatDate($absence['date_debut']) ?></p>
                </div>
            </div>

            <!-- Date fin -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-orange-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Date fin</p>
                    <p class="font-semibold text-gray-800"><?= formatDate($absence['date_fin']) ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($absence['motif'])): ?>
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Motif</h3>
                <p class="text-gray-800"><?= e($absence['motif']) ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

