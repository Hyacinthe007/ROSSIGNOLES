<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                Années Scolaires
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des années scolaires</p>
        </div>
        <div>
            <a href="<?= url('annees-scolaires/add') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Nouvelle Année Scolaire</span>
            </a>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Libellé
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Période
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                            <i class="fas fa-toggle-on mr-2"></i>Statut
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                            <i class="fas fa-users mr-2"></i>Inscriptions
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($annees)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucune année scolaire trouvée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($annees as $annee): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900"><?= e($annee['libelle']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">
                                        <?= formatDate($annee['date_debut']) ?> 
                                        <i class="fas fa-arrow-right mx-2 text-gray-400"></i>
                                        <?= formatDate($annee['date_fin']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($annee['actif']): ?>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-circle mr-1"></i>Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php
                                    $model = new \App\Models\AnneeScolaire();
                                    $stats = $model->getStatistiques($annee['id']);
                                    ?>
                                    <span class="text-gray-900 font-medium"><?= $stats['total_inscriptions'] ?></span> inscriptions
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('annees-scolaires/details/' . $annee['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition"
                                           title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('annees-scolaires/edit/' . $annee['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (!$annee['actif']): ?>
                                            <a href="<?= url('annees-scolaires/activate/' . $annee['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                               class="text-orange-600 hover:text-orange-900 p-2 hover:bg-orange-50 rounded transition"
                                               title="Activer"
                                               onclick="return confirm('Activer cette année scolaire ? Cela désactivera l\'année actuellement active.')">
                                                <i class="fas fa-toggle-on"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
