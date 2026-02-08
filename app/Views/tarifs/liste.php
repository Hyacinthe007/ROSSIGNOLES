<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-file-invoice-dollar text-green-600 mr-2"></i>
                Gestion des Tarifs Scolaires
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gérer tous les tarifs (inscription, écolage, etc.) par cycle</p>
        </div>
        <div>
            <a href="<?= url('tarifs/nouveau') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg font-bold">
                <i class="fas fa-plus"></i>
                <span>Nouveau Tarif</span>
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl p-4 mb-6">
        <form method="GET" action="<?= url('tarifs/liste') ?>" class="flex items-end gap-4">
            <?php if (isset($_GET['iframe']) && $_GET['iframe'] == '1'): ?>
                <input type="hidden" name="iframe" value="1">
            <?php endif; ?>
            <div class="flex-1 max-w-xs">
                <label for="annee_scolaire_id" class="block text-sm font-medium text-gray-700 mb-1">Année Scolaire</label>
                <select name="annee_scolaire_id" id="annee_scolaire_id" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500">
                    <?php foreach ($annees as $annee): ?>
                        <option value="<?= $annee['id'] ?>" <?= (isset($selectedAnnee) && $selectedAnnee == $annee['id']) ? 'selected' : '' ?>>
                            <?= e($annee['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                            <i class="fas fa-layer-group mr-2"></i>Cycle & Niveau
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                            <i class="fas fa-file-contract mr-2"></i>Inscription
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                            <i class="fas fa-money-bill-wave mr-2"></i>Écolage Mensuel
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Année Scolaire
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                            <i class="fas fa-toggle-on mr-2"></i>Statut
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($tarifs)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucun tarif trouvé pour cette sélection
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tarifs as $tarif): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= e($tarif['cycle_libelle'] ?? '-') ?></div>
                                    <div class="text-xs text-gray-500"><?= e($tarif['niveau_libelle']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-green-700">
                                        <?= number_format($tarif['frais_inscription'], 0, ',', ' ') ?> Ar
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-green-600">
                                        <?= number_format($tarif['ecolage_mensuel'], 0, ',', ' ') ?> Ar
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900"><?= e($tarif['annee_libelle']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($tarif['actif']): ?>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Actif
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-circle mr-1"></i>Inactif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('tarifs/modifier/' . $tarif['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($tarif['actif']): ?>
                                            <a href="<?= url('tarifs/desactiver/' . $tarif['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                               class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded transition"
                                               title="Désactiver"
                                               onclick="return confirm('Voulez-vous vraiment désactiver ce tarif ?')">
                                                <i class="fas fa-toggle-off"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= url('tarifs/activer/' . $tarif['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                               class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition"
                                               title="Activer">
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
