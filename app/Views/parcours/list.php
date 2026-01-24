<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-route text-indigo-600 mr-2"></i>
            Parcours Scolaires
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Historique des inscriptions et parcours des élèves</p>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="<?= url('parcours/list') ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Filtre Élève -->
            <div>
                <label for="eleve_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-graduate mr-1"></i>Élève
                </label>
                <select name="eleve_id" id="eleve_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les élèves</option>
                    <?php foreach ($eleves as $eleve): ?>
                        <option value="<?= $eleve['id'] ?>" <?= ($eleveId == $eleve['id']) ? 'selected' : '' ?>>
                            <?= e($eleve['matricule'] . ' - ' . $eleve['nom'] . ' ' . $eleve['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filtre Classe -->
            <div>
                <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-chalkboard mr-1"></i>Classe
                </label>
                <select name="classe_id" id="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toutes les classes</option>
                    <?php foreach ($classes as $classe): ?>
                        <option value="<?= $classe['id'] ?>" <?= ($classeId == $classe['id']) ? 'selected' : '' ?>>
                            <?= e($classe['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filtre Année -->
            <div>
                <label for="annee_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-1"></i>Année scolaire
                </label>
                <select name="annee_id" id="annee_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Toutes les années</option>
                    <?php foreach ($annees as $annee): ?>
                        <option value="<?= $annee['id'] ?>" <?= ($anneeId == $annee['id']) ? 'selected' : '' ?>>
                            <?= e($annee['libelle']) ?> <?= $annee['actif'] ? '(Active)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Bouton Filtrer -->
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Filtrer</span>
                </button>
                <a href="<?= url('parcours/list') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-redo"></i>
                    <span>Réinitialiser</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des parcours -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Année Scolaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Série</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Inscription</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($parcours)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucun parcours trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($parcours as $p): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= e($p['eleve_nom'] . ' ' . $p['eleve_prenom']) ?>
                                            </div>
                                            <div class="text-xs text-gray-500"><?= e($p['matricule']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e($p['annee_libelle']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e($p['classe_nom']) ?>
                                    <div class="text-xs text-gray-500"><?= e($p['classe_code']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        <?= e($p['niveau_nom']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= e($p['serie_nom'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($p['type_inscription'] === 'nouvelle'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-star mr-1"></i>Nouvelle
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                            <i class="fas fa-redo mr-1"></i>Réinscription
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= formatDate($p['date_inscription']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= url('parcours/details/' . $p['eleve_id']) ?>" 
                                       class="text-indigo-600 hover:text-indigo-900 p-2 hover:bg-indigo-50 rounded transition"
                                       title="Voir le parcours complet">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
