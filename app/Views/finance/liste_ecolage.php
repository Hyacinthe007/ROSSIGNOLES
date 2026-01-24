<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                Suivi Écolage Mensuel
            </h1>
            <p class="text-gray-600 text-sm md:text-base">
                <?= $moisNoms[$filters['mois']] ?? '' ?> <?= $filters['annee'] ?>
            </p>
        </div>
        <a href="<?= url('finance/dashboard') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Tableau de bord</span>
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="<?= url('finance/ecolage') ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Mois -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
                <select name="mois" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <?php foreach ($moisNoms as $num => $nom): ?>
                        <option value="<?= $num ?>" <?= $filters['mois'] == $num ? 'selected' : '' ?>><?= $nom ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Année -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                <select name="annee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <?php for ($y = date('Y') + 1; $y >= date('Y') - 2; $y--): ?>
                        <option value="<?= $y ?>" <?= $filters['annee'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <!-- Statut -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous</option>
                    <option value="PAYE" <?= $filters['statut'] === 'PAYE' ? 'selected' : '' ?>>Payé</option>
                    <option value="PARTIEL" <?= $filters['statut'] === 'PARTIEL' ? 'selected' : '' ?>>Partiel</option>
                    <option value="EN_ATTENTE" <?= $filters['statut'] === 'EN_ATTENTE' ? 'selected' : '' ?>>En attente</option>
                    <option value="EN_RETARD" <?= $filters['statut'] === 'EN_RETARD' ? 'selected' : '' ?>>En retard</option>
                </select>
            </div>
            
            <!-- Classe -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                <select name="classe_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes</option>
                    <?php foreach ($classes as $classe): ?>
                        <option value="<?= $classe['id'] ?>" <?= $filters['classe_id'] == $classe['id'] ? 'selected' : '' ?>>
                            <?= e($classe['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Bouton -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Résumé -->
    <?php 
    $totalDu = array_sum(array_column($echeances, 'montant_du'));
    $totalPaye = array_sum(array_column($echeances, 'montant_paye'));
    $nbPaye = count(array_filter($echeances, fn($e) => $e['statut'] === 'PAYE'));
    $nbImpaye = count($echeances) - $nbPaye;
    ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800"><?= count($echeances) ?></p>
                    <p class="text-xs text-gray-600">Échéances</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600"><?= $nbPaye ?></p>
                    <p class="text-xs text-gray-600">Payées</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-orange-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-orange-600"><?= $nbImpaye ?></p>
                    <p class="text-xs text-gray-600">En attente</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-coins text-purple-600"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800"><?= formatMoney($totalPaye) ?></p>
                    <p class="text-xs text-gray-600">/ <?= formatMoney($totalDu) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Montant Dû</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Montant Payé</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Reste</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Date Limite</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Parent</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($echeances)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Aucune échéance pour cette période</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($echeances as $ech): ?>
                            <?php 
                            $reste = $ech['montant_du'] - $ech['montant_paye'];
                            $badgeClass = match($ech['statut']) {
                                'PAYE' => 'bg-green-100 text-green-700',
                                'PARTIEL' => 'bg-yellow-100 text-yellow-700',
                                'EN_RETARD' => 'bg-red-100 text-red-700',
                                'EN_ATTENTE' => 'bg-orange-100 text-orange-800',
                                default => 'bg-gray-100 text-gray-700'
                            };
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900"><?= e($ech['eleve_nom'] . ' ' . $ech['eleve_prenom']) ?></div>
                                    <div class="text-sm text-gray-500"><?= e($ech['matricule'] ?? '') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?= e($ech['classe_nom'] ?? '-') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium"><?= formatMoney($ech['montant_du']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-green-600 font-medium"><?= formatMoney($ech['montant_paye']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-600 font-medium"><?= formatMoney($reste) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                        <?= $ech['statut'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    <?= date('d/m/Y', strtotime($ech['date_limite'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($ech['parent_telephone'])): ?>
                                        <div class="text-sm"><?= e($ech['parent_nom'] ?? '') ?></div>
                                        <div class="text-xs text-gray-500"><?= e($ech['parent_telephone']) ?></div>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <?php if ($ech['statut'] !== 'PAYE'): ?>
                                        <a href="<?= url('finance/ecolage/payer/' . $ech['id']) ?>" 
                                           class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors shadow-sm">
                                            <i class="fas fa-money-bill-wave mr-1"></i> Payer
                                        </a>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 cursor-default">
                                            <i class="fas fa-check mr-1"></i> Payé
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
