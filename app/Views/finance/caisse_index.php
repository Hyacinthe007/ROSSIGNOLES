<?php
$title = "Caisse Consolidée";
$breadcrumbs = [
    ['label' => 'Finance', 'url' => '#'],
    ['label' => 'Caisse']
];
?>

<div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-cash-register text-emerald-600 mr-2"></i>
                Caisse Centrale
            </h1>
            <p class="text-gray-600">Vue d'ensemble des flux financiers de l'établissement</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= url('finance/journal') ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl transition flex items-center gap-2 border border-gray-200">
                <i class="fas fa-book"></i>
                <span>Journal du jour</span>
            </a>
            <a href="<?= url('finance/recus') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 shadow-lg shadow-emerald-100">
                <i class="fas fa-receipt"></i>
                <span>Tous les reçus</span>
            </a>
        </div>
    </div>

    <!-- Kpis -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-vault"></i>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">Total Cumulé</span>
            </div>
            <div class="text-2xl font-black text-gray-800"><?= number_format($stats['total_caisse'] ?? 0, 0, ',', ' ') ?> Ar</div>
            <div class="text-sm text-gray-400 mt-1">Depuis le début de l'année</div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">Aujourd'hui</span>
            </div>
            <div class="text-2xl font-black text-gray-800"><?= number_format($stats['total_jour'] ?? 0, 0, ',', ' ') ?> Ar</div>
            <div class="text-sm text-gray-400 mt-1"><?= date('d F Y') ?></div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded">Transactions</span>
            </div>
            <div class="text-2xl font-black text-gray-800"><?= $stats['nb_paiements'] ?? 0 ?></div>
            <div class="text-sm text-gray-400 mt-1">Paiements enregistrés</div>
        </div>
    </div>

    <!-- Dernières Transactions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">Dernières Opérations</h2>
            <a href="<?= url('finance/recus') ?>" class="text-sm text-indigo-600 hover:underline">Voir tout</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase">Date</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase">Élève</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase">Mode</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase text-right">Montant</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($derniersPaiements)): ?>
                        <tr><td colspan="5" class="p-8 text-center text-gray-400">Aucune transaction récente</td></tr>
                    <?php else: ?>
                        <?php foreach ($derniersPaiements as $p): ?>
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4 text-sm text-gray-600"><?= date('d/m/Y H:i', strtotime($p['date_paiement'])) ?></td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-800"><?= e($p['nom'] . ' ' . $p['prenom']) ?></div>
                                <div class="text-[10px] text-gray-400 font-mono"><?= e($p['numero_paiement'] ?? 'PAY-'.$p['id']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 rounded text-[10px] font-bold text-gray-500 uppercase"><?= e($p['mode_paiement']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-sm font-black text-emerald-600 text-right"><?= number_format($p['montant'], 0, ',', ' ') ?> Ar</td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= url('finance/export-recu/' . $p['id']) ?>" class="text-gray-400 hover:text-emerald-600"><i class="fas fa-file-pdf"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
