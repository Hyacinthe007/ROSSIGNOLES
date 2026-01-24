<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                Tableau de bord financier
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Vue d'ensemble de la situation financière</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('finance/ecolage') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-calendar-alt"></i>
                <span>Suivi Écolage</span>
            </a>
            <a href="<?= url('finance/list') ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-list"></i>
                <span>Tous les frais</span>
            </a>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Ecolage reçu (MGA)</p>
                    <p class="text-3xl font-bold text-green-600"><?= formatMoney($stats['total_recu'] ?? 0) ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Ecolage attendu (MGA)</p>
                    <p class="text-3xl font-bold text-blue-600"><?= formatMoney($stats['total_attendu'] ?? 0) ?></p>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Impayés (MGA)</p>
                    <p class="text-3xl font-bold text-red-600"><?= formatMoney($stats['impayes'] ?? 0) ?></p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques secondaires -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['eleves_total'] ?? 0 ?></p>
                    <p class="text-xs text-gray-600">Élèves inscrits</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['eleves_a_jour'] ?? 0 ?></p>
                    <p class="text-xs text-gray-600">À jour</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-user-clock text-orange-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-orange-600"><?= $stats['eleves_retard'] ?? 0 ?></p>
                    <p class="text-xs text-gray-600">En retard</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-file-invoice text-purple-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['inscriptions_droit_paye'] ?? 0 ?></p>
                    <p class="text-xs text-gray-600">Droits payés</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux -->
    <div class="mb-6">


        <!-- Échéances du mois courant -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                    Mois en cours (<?= date('F Y') ?>)
                </h2>
                <a href="<?= url('finance/ecolage') ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <?php if (!empty($stats['echeances_mois_courant'])): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left text-xs text-gray-500 uppercase border-b">
                                <th class="pb-2">Élève</th>
                                <th class="pb-2">Classe</th>
                                <th class="pb-2">Statut</th>
                                <th class="pb-2">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach (array_slice($stats['echeances_mois_courant'], 0, 10) as $ech): ?>
                                <tr class="text-sm">
                                    <td class="py-2 font-medium"><?= e($ech['eleve_nom'] . ' ' . $ech['eleve_prenom']) ?></td>
                                    <td class="py-2 text-gray-600"><?= e($ech['classe_nom']) ?></td>
                                    <td class="py-2">
                                        <?php
                                        $badgeClass = match($ech['statut']) {
                                            'PAYE' => 'bg-green-100 text-green-700',
                                            'PARTIEL' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                        ?>
                                        <span class="px-2 py-1 rounded-full text-xs <?= $badgeClass ?>"><?= $ech['statut'] ?></span>
                                    </td>
                                    <td class="py-2 font-semibold"><?= formatMoney($ech['montant_du']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">Aucune échéance pour ce mois</p>
            <?php endif; ?>
        </div>
    </div>


</div>
