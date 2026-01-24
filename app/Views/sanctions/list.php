<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                Liste des sanctions
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des sanctions disciplinaires</p>
        </div>
        <a href="<?= url('sanctions/add') ?>" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Ajouter une sanction</span>
        </a>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($sanctions)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucune sanction trouvée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sanctions as $sanction): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Élève #<?= e($sanction['eleve_id']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= formatDate($sanction['date_sanction']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Type #<?= e($sanction['type_sanction_id']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= e(substr($sanction['motif'] ?? '', 0, 50)) ?><?= strlen($sanction['motif'] ?? '') > 50 ? '...' : '' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('sanctions/details/' . $sanction['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('sanctions/edit/' . $sanction['id']) ?>" 
                                           class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
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

