<div class="p-6 md:p-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="<?= url('finance/ecolage') ?>" class="text-gray-500 hover:text-gray-700 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Échéancier Écolage</h1>
            </div>
            <p class="text-gray-600">Détails de scolarité pour <span class="font-semibold text-gray-900"><?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?></span> (<?= e($eleve['classe_nom']) ?>)</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-calendar-alt mr-2"></i><?= e($annee['libelle']) ?>
            </div>
            <a href="<?= url('finance/add') ?>?eleve_id=<?= $eleve['id'] ?>&type=ecolage" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Nouveau Paiement
            </a>
        </div>
    </div>

    <!-- Alertes Statut -->
    <?php 
    $nbRetards = 0;
    $nbExclusions = 0;
    foreach ($echeancier as $ech) {
        if (in_array($ech['statut'], ['retard', 'retard_grave'])) $nbRetards++;
        if ($ech['statut'] === 'exclusion') $nbExclusions++;
    }
    ?>

    <?php if ($nbExclusions > 0): ?>
        <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-ban text-red-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">Exclusion en cours</h3>
                    <p class="text-sm text-red-700 mt-1">
                        Cet élève a <?= $nbExclusions ?> mensualité(s) en statut d'exclusion. L'accès aux cours doit être refusé jusqu'à régularisation.
                    </p>
                </div>
            </div>
        </div>
    <?php elseif ($nbRetards > 0): ?>
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-orange-800 uppercase tracking-wide">Paiements en retard</h3>
                    <p class="text-sm text-orange-700 mt-1">
                        Cet élève a <?= $nbRetards ?> mensualité(s) en retard. Merci de relancer les parents.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Échéancier (10 Mois) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-list-ol text-blue-600"></i> Échéancier (10 Mois)
                    </h2>
                    <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded border border-gray-200">
                        Total du: <?= formatMoney(array_sum(array_column($echeancier, 'montant_du'))) ?>
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-3">Mois</th>
                                <th class="px-6 py-3 text-right">Montant</th>
                                <th class="px-6 py-3 text-right">Payé</th>
                                <th class="px-6 py-3 text-right">Reste</th>
                                <th class="px-6 py-3 text-center">Date Limite</th>
                                <th class="px-6 py-3 text-center">Statut</th>
                                <th class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($echeancier as $ech): ?>
                                <?php
                                $badgeColor = match($ech['statut']) {
                                    'paye' => 'bg-green-100 text-green-700 ring-green-600/20',
                                    'partiel' => 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
                                    'retard' => 'bg-orange-100 text-orange-700 ring-orange-600/20',
                                    'retard_grave' => 'bg-orange-100 text-orange-800 font-bold ring-orange-600/20',
                                    'exclusion' => 'bg-red-100 text-red-700 font-bold ring-red-600/20',
                                    default => 'bg-gray-100 text-gray-600 ring-gray-500/10'
                                };
                                $statutLabel = match($ech['statut']) {
                                    'paye' => 'Payé',
                                    'partiel' => 'Partiel',
                                    'retard' => 'Retard',
                                    'retard_grave' => 'Retard +',
                                    'exclusion' => 'Exclu',
                                    'en_attente' => 'À venir',
                                    default => ucfirst($ech['statut'] ?? '')
                                };
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors <?= in_array($ech['statut'], ['exclusion', 'retard_grave']) ? 'bg-red-50/30' : '' ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= ucfirst($ech['mois_libelle']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600">
                                        <?= formatMoney($ech['montant_du']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-medium">
                                        <?= formatMoney($ech['montant_paye']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-bold">
                                        <?= formatMoney($ech['montant_restant']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        <?= date('d/m/Y', strtotime($ech['date_limite'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset <?= $badgeColor ?>">
                                            <?= $statutLabel ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php if ($ech['statut'] !== 'paye'): ?>
                                            <a href="<?= url('finance/payer-ecolage/' . $ech['id']) ?>" class="text-blue-600 hover:text-blue-900 text-sm font-medium hover:underline">
                                                Payer
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs"><i class="fas fa-check"></i></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($echeancier)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 ">
                                        Aucun échéancier généré pour cette année scolaire.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historique des Paiements -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-full">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-history text-purple-600"></i> Historique
                    </h2>
                </div>
                <div class="max-h-[600px] overflow-y-auto">
                    <?php if (empty($paiements)): ?>
                        <div class="p-8 text-center text-gray-500">
                            <i class="fas fa-receipt text-gray-300 text-4xl mb-3 block"></i>
                            Aucun paiement enregistré.
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-gray-100">
                            <?php foreach ($paiements as $p): ?>
                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-sm font-bold text-gray-900"><?= formatMoney($p['montant']) ?></span>
                                        <span class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($p['date_paiement'])) ?></span>
                                    </div>
                                    <div class="text-xs text-gray-600 mb-1">
                                        <i class="fas fa-file-invoice mr-1"></i> Facture n°<?= e($p['numero_facture']) ?>
                                    </div>
                                    <?php if (!empty($p['designation'])): ?>
                                        <div class="text-xs text-gray-500  truncate" title="<?= e($p['designation']) ?>">
                                            <?= e($p['designation']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                            <?= e($p['mode_paiement'] ?? 'Espèces') ?>
                                        </span>
                                        <a href="<?= url('finance/recus?id=' . $p['id']) ?>" class="text-blue-600 hover:text-blue-800 text-xs font-medium" target="_blank">
                                            <i class="fas fa-print mr-1"></i>Reçu
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

