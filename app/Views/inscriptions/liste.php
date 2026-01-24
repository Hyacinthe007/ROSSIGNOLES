<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-list text-blue-600 mr-2"></i>
                Inscriptions
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des inscriptions et réinscriptions</p>
        </div>
        <?php if (hasPermission('inscriptions_new.create')): ?>
        <div class="mt-4 md:mt-0">
            <a href="<?= url('inscriptions/nouveau') ?>" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle Inscription
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Statistiques -->
    <?php if ($statistiques): ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Inscriptions</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $statistiques['total_inscriptions'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Actives</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $statistiques['actives'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <i class="fas fa-money-bill-wave text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Frais</p>
                        <p class="text-lg font-bold text-gray-800"><?= number_format($statistiques['total_frais'], 0, ',', ' ') ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Impayés</p>
                        <p class="text-lg font-bold text-gray-800"><?= $statistiques['nb_impayes'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="<?= url('inscriptions/liste') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous</option>
                    <option value="nouvelle" <?= ($filters['type_inscription'] ?? '') === 'nouvelle' ? 'selected' : '' ?>>Nouvelle</option>
                    <option value="reinscription" <?= ($filters['type_inscription'] ?? '') === 'reinscription' ? 'selected' : '' ?>>Réinscription</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous</option>
                    <option value="validee" <?= ($filters['statut'] ?? '') === 'validee' ? 'selected' : '' ?>>Validée</option>
                    <option value="suspendue" <?= ($filters['statut'] ?? '') === 'suspendue' ? 'selected' : '' ?>>Suspendue</option>
                    <option value="terminee" <?= ($filters['statut'] ?? '') === 'terminee' ? 'selected' : '' ?>>Terminée</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                <select name="classe_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes</option>
                    <?php foreach ($classes as $classe): ?>
                        <option value="<?= $classe['id'] ?>" <?= ($filters['classe_id'] ?? '') == $classe['id'] ? 'selected' : '' ?>>
                            <?= e($classe['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des inscriptions -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Frais</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($inscriptions)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Aucune inscription trouvée</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inscriptions as $inscription): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">
                                                <?= e($inscription['eleve_nom']) ?> <?= e($inscription['eleve_prenom']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500"><?= e($inscription['eleve_matricule']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900"><?= e($inscription['classe_nom']) ?></div>
                                    <div class="text-sm text-gray-500"><?= e($inscription['classe_code']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $inscription['type_inscription'] === 'nouvelle' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= $inscription['type_inscription'] === 'nouvelle' ? 'Nouvelle' : 'Réinscription' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($inscription['date_inscription'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    // Utiliser les montants provenant de la facture liée (jointure dans getAllWithDetails)
                                    $totalFrais = $inscription['montant_total'] ?? 0;
                                    $totalPaye = $inscription['montant_paye'] ?? 0;
                                    $resteAPayer = $totalFrais - $totalPaye;
                                    ?>
                                    <div class="text-sm font-bold text-gray-900"><?= number_format($totalFrais, 0, ',', ' ') ?> MGA</div>
                                    <div class="text-xs <?= $resteAPayer > 0 ? 'text-orange-600' : 'text-green-600' ?>">
                                        Reste: <?= number_format($resteAPayer, 0, ',', ' ') ?> MGA
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                         <?php
                                         switch ($inscription['statut']) {
                                             case 'validee': echo 'bg-green-100 text-green-800'; break;
                                             case 'active': echo 'bg-green-100 text-green-800'; break;
                                             case 'brouillon': echo 'bg-gray-100 text-gray-500 border border-gray-200'; break;
                                             case 'en_attente': echo 'bg-orange-100 text-orange-800'; break;
                                             case 'suspendue': echo 'bg-red-100 text-red-800'; break;
                                             case 'terminee': echo 'bg-gray-100 text-gray-800'; break;
                                             default: echo 'bg-blue-100 text-blue-800';
                                         }
                                         ?>">
                                         <?= ucfirst(str_replace('_', ' ', $inscription['statut'])) ?>
                                     </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?= url('inscriptions/details/' . $inscription['id']) ?>" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">
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
