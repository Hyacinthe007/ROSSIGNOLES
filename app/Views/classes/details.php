<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-door-open text-blue-600 mr-2"></i>
                Détails de la classe
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Informations complètes de la classe</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('classes/edit/' . $classe['id']) ?>?iframe=1" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Modifier</span>
            </a>
            <a href="<?= url('classes/eleves') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 mb-6">
        <div class="flex items-center gap-6 mb-6">
            <div class="bg-blue-100 p-4 rounded-lg">
                <i class="fas fa-door-open text-blue-600 text-4xl"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    <?= e($classe['nom']) ?>
                </h2>
                <p class="text-gray-600">
                    <i class="fas fa-id-card mr-2"></i>Code: <span class="font-semibold"><?= e($classe['code'] ?: 'N/A') ?></span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <!-- Ligne 1 -->
            <!-- Niveau -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Niveau</p>
                    <p class="font-semibold text-gray-800"><?= e($classe['niveau_nom'] ?? 'N/A') ?></p>
                </div>
            </div>

            <!-- Section/Filière -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-stream text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Section/Filière</p>
                    <p class="font-semibold text-gray-800"><?= e($classe['section_nom'] ?? 'Aucune') ?></p>
                </div>
            </div>

            <!-- Salle principale -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-door-open text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Salle principale</p>
                    <p class="font-semibold text-gray-800"><?= e($classe['salle_nom'] ?? 'Aucune') ?></p>
                </div>
            </div>

            <!-- Ligne 2 -->
            <!-- Année scolaire -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Année scolaire</p>
                    <p class="font-semibold text-gray-800"><?= e($classe['annee_scolaire'] ?? 'N/A') ?></p>
                </div>
            </div>

            <!-- Établissement -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-orange-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Établissement</p>
                    <p class="font-semibold text-gray-800"><?= e($classe['etablissement_nom'] ?? 'N/A') ?></p>
                </div>
            </div>

            <!-- Capacité maximale -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Capacité maximale</p>
                    <p class="font-semibold text-gray-800"><?= e($classe['capacite_max'] ?? 40) ?> élèves</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des élèves -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-graduate text-blue-600"></i>
                Élèves de la classe (<?= count($eleves) ?>)
            </h3>
        </div>

        <?php if (empty($eleves)): ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                <p>Aucun élève inscrit dans cette classe</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-id-card mr-2"></i>Matricule
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-2"></i>Nom
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prénom
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar-check mr-2"></i>Date d'inscription
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($eleves as $eleve): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= e($eleve['matricule']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    <?= e($eleve['nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    <?= e($eleve['prenom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= formatDate($eleve['date_inscription'] ?? '') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= url('notes/list?eleve_id=' . $eleve['id']) ?>" 
                                       class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition" title="Voir les notes">
                                        <i class="fas fa-clipboard-list"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
