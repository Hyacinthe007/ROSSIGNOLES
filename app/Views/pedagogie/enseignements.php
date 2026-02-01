<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">
                <i class="fas fa-chalkboard-teacher text-indigo-600 mr-2"></i>
                Gestions des enseignements
            </h1>
            <p class="text-gray-500 text-xs md:text-sm">Association classe + matière + enseignant</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('pedagogie/enseignements/add') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md">
                <i class="fas fa-plus-circle"></i>
                <span>Nouvelle Attribution</span>
            </a>
            <?php if (empty($_GET['iframe'])): ?>
            <a href="<?= url('pedagogie/series') ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-stream"></i>
                <span>Séries</span>
            </a>
            <a href="<?= url('pedagogie/emplois-temps') ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-calendar-alt"></i>
                <span>Emplois du temps</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Code classe</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Enseignant</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Volume horaire</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($enseignements)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucun enseignement trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($enseignements as $enseignement): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    <?= e($enseignement['classe_code'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e($enseignement['matiere_libelle'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= e(($enseignement['enseignant_nom'] ?? '') . ' ' . ($enseignement['enseignant_prenom'] ?? '')) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $enseignement['volume_horaire'] ?? '-' ?> h
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= ($enseignement['actif'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= ($enseignement['actif'] ?? 0) ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('pedagogie/enseignements/edit/' . $enseignement['id']) ?><?= isset($_GET['iframe']) ? '&iframe=1' : '' ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50 transition" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('pedagogie/enseignements/delete/' . $enseignement['id']) ?><?= isset($_GET['iframe']) ? '&iframe=1' : '' ?>" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette attribution ?')"
                                           class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
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

