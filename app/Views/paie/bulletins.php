<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white mr-4 shadow-lg shadow-blue-100">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                Bulletins de Paie
            </h1>
            <p class="text-gray-600">Génération et suivi des fiches de paie mensuelles</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= url('paie') ?>" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-sm font-semibold">
                <i class="fas fa-arrow-left text-blue-600"></i>
                <span>Retour</span>
            </a>
            <div class="flex bg-white rounded-xl border border-gray-200 p-1 shadow-sm">
                <input type="month" 
                       id="periode-filter" 
                       class="border-0 bg-transparent px-3 py-2 text-sm font-bold text-gray-700 focus:ring-0" 
                       value="<?= $periode ?>"
                       onchange="window.location.href='<?= url('paie/bulletins') ?>?periode=' + this.value">
            </div>
        </div>
    </div>

    <!-- Statistiques Globales -->
    <?php if (!empty($statistiques)): ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-full -mr-12 -mt-12 transition-all group-hover:scale-110"></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 relative">Nombre de bulletins</p>
                <div class="flex items-baseline gap-2 relative">
                    <p class="text-2xl font-black text-gray-800"><?= $statistiques['total'] ?? 0 ?></p>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Émis</span>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-full -mr-12 -mt-12 transition-all group-hover:scale-110"></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 relative">Masse Salariale Nette</p>
                <div class="flex items-baseline gap-1 relative">
                    <p class="text-2xl font-black text-emerald-600"><?= number_format($statistiques['total_net'] ?? 0, 0, ',', ' ') ?></p>
                    <span class="text-xs font-bold text-gray-400">Ar</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-full -mr-12 -mt-12 transition-all group-hover:scale-110"></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 relative">Total IRSA</p>
                <div class="flex items-baseline gap-1 relative">
                    <p class="text-2xl font-black text-amber-600"><?= number_format($statistiques['total_irsa'] ?? 0, 0, ',', ' ') ?></p>
                    <span class="text-xs font-bold text-gray-400">Ar</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 transition-all group-hover:scale-110"></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 relative">Budget Employeur</p>
                <div class="flex items-baseline gap-1 relative">
                    <p class="text-2xl font-black text-indigo-600"><?= number_format($statistiques['total_cout'] ?? 0, 0, ',', ' ') ?></p>
                    <span class="text-xs font-bold text-gray-400">Ar</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Liste des bulletins -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h5 class="text-lg font-bold text-gray-800">Détails de la période <?= strftime('%B %Y', strtotime($periode . '-01')) ?></h5>
            <form method="POST" action="<?= url('paie/bulletins/generer') ?>" class="inline">
                <?= csrf_field() ?>
                <input type="hidden" name="periode" value="<?= $periode ?>">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-lg shadow-blue-100" onclick="return confirm('Actualiser/Générer les bulletins pour <?= $periode ?> ?')">
                    <i class="fas fa-sync-alt"></i>
                    <span><?= empty($bulletins) ? 'Générer' : 'Actualiser' ?></span>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Matricule</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Personnel</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Brut</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Cotis Sal.</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">IRSA</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Net à payer</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (!empty($bulletins)): ?>
                        <?php foreach ($bulletins as $bulletin): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded">
                                        <?= htmlspecialchars($bulletin['matricule'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($bulletin['nom'] . ' ' . $bulletin['prenom']) ?></p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-semibold text-gray-600">
                                        <?= number_format($bulletin['salaire_brut'], 0, ',', ' ') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-semibold text-red-400">
                                        -<?= number_format($bulletin['montant_cnaps_sal'] + $bulletin['montant_ostie_sal'], 0, ',', ' ') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-semibold text-amber-500">
                                        -<?= number_format($bulletin['irsa_net'], 0, ',', ' ') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-base font-black text-emerald-600">
                                        <?= number_format($bulletin['salaire_net'], 0, ',', ' ') ?>
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 ml-1 uppercase">Ar</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                    $statutClass = match($bulletin['statut']) {
                                        'brouillon' => 'bg-gray-100 text-gray-500',
                                        'valide' => 'bg-emerald-100 text-emerald-700',
                                        'paye' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-red-100 text-red-700'
                                    };
                                    ?>
                                    <span class="inline-flex px-2 py-1 rounded text-[10px] font-black uppercase tracking-tighter <?= $statutClass ?>">
                                        <?= $bulletin['statut'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= url('paie/bulletins/detail?id=' . $bulletin['id']) ?>" 
                                           class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-blue-600 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <?php if ($bulletin['statut'] === 'brouillon'): ?>
                                            <form method="POST" action="<?= url('paie/bulletins/valider') ?>" onsubmit="return confirm('Valider ce bulletin ?')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= $bulletin['id'] ?>">
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <i class="fas fa-file-invoice text-2xl"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-800">Période non générée</h3>
                                <p class="text-xs text-gray-400 mt-1 mb-6">Aucun bulletin disponible pour <?= $periode ?>.</p>
                                <form method="POST" action="<?= url('paie/bulletins/generer') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="periode" value="<?= $periode ?>">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition shadow-lg shadow-blue-50">
                                        Lancer la génération
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($bulletins)): ?>
                <tfoot class="bg-gray-50 overflow-hidden rounded-b-3xl">
                    <tr class="font-black text-gray-800 text-sm">
                        <td colspan="2" class="px-6 py-4 text-right text-[10px] uppercase text-gray-400">Totaux mensuels :</td>
                        <td class="px-6 py-4 text-right"><?= number_format($statistiques['total_brut'] ?? 0, 0, ',', ' ') ?></td>
                        <td class="px-6 py-4 text-right text-red-400">-<?= number_format(array_sum(array_column($bulletins, 'montant_cnaps_sal')) + array_sum(array_column($bulletins, 'montant_ostie_sal')), 0, ',', ' ') ?></td>
                        <td class="px-6 py-4 text-right text-amber-500">-<?= number_format($statistiques['total_irsa'] ?? 0, 0, ',', ' ') ?></td>
                        <td class="px-6 py-4 text-right text-emerald-600"><?= number_format($statistiques['total_net'] ?? 0, 0, ',', ' ') ?> Ar</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
