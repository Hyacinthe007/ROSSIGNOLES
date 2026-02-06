<div class="p-4 md:p-8 no-print">
    <!-- En-tête -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white mr-4 shadow-lg shadow-indigo-100">
                    <i class="fas fa-file-invoice"></i>
                </div>
                Détail du Bulletin
            </h1>
            <p class="text-gray-600">Période : <span class="font-bold text-gray-800 capitalize"><?= strftime('%B %Y', strtotime($bulletin['periode'] . '-01')) ?></span></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= url('paie/bulletins?periode=' . $bulletin['periode']) ?>" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-sm font-semibold">
                <i class="fas fa-arrow-left text-indigo-600"></i>
                <span>Retour</span>
            </a>
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-lg shadow-indigo-100 font-semibold">
                <i class="fas fa-print"></i>
                <span>Imprimer / PDF</span>
            </button>
        </div>
    </div>
</div>

<!-- Bulletin Format Papier -->
<div class="max-w-4xl mx-auto px-4 pb-12 print:px-0 print:pb-0">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden print:shadow-none print:border print:rounded-none">
        <!-- En-tête du Bulletin Graphique -->
        <div class="p-8 md:p-12 bg-gray-50/50 border-b border-gray-100 flex flex-col md:flex-row justify-between gap-8">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 tracking-tight">ÉCOLE MANDROSO</h2>
                        <p class="text-xs font-bold text-gray-400 tracking-widest uppercase">Établissement Scolaire Privé</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 font-medium">BP 1234 - Route de l'aéroport</p>
                    <p class="text-xs text-gray-500 font-medium">Antananarivo, Madagascar</p>
                    <p class="text-xs text-gray-500 font-medium font-bold">NIF: 1000234567 | STAT: 80101-11</p>
                </div>
            </div>
            
            <div class="text-right flex flex-col justify-between items-end">
                <div class="px-6 py-3 bg-white rounded-2xl border border-gray-100 shadow-sm w-fit">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Bulletin de Paie N°</p>
                    <p class="text-xl font-black text-indigo-600">PAIE-<?= date('Y', strtotime($bulletin['periode'])) ?>-<?= str_pad((string)$bulletin['id'], 3, '0', STR_PAD_LEFT) ?></p>
                </div>
                <div class="mt-4 text-sm">
                    <p class="font-bold text-gray-800 line-clamp-1"><?= htmlspecialchars($bulletin['nom'] . ' ' . $bulletin['prenom']) ?></p>
                    <p class="text-gray-500">Matricule : <span class="font-bold"><?= htmlspecialchars($bulletin['matricule'] ?? 'N/A') ?></span></p>
                    <p class="text-gray-500 uppercase text-[10px] font-black mt-1"><?= $bulletin['type_personnel'] ?? '' ?></p>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12">
            <!-- Grille de Détails -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div class="space-y-3">
                    <h6 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Informations Salarié</h6>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <span class="text-gray-500">Enfants à charge</span>
                        <span class="font-bold text-gray-800 text-right"><?= $bulletin['nb_enfants'] ?? 0 ?></span>
                        <span class="text-gray-500">Période de paie</span>
                        <span class="font-bold text-gray-800 capitalize text-right"><?= strftime('%B %Y', strtotime($bulletin['periode'] . '-01')) ?></span>
                    </div>
                </div>
                <div class="space-y-3">
                    <h6 class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Résumé Financier</h6>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <span class="text-gray-500">Salaire Brut</span>
                        <span class="font-bold text-gray-800 text-right"><?= number_format($bulletin['salaire_brut'], 0, ',', ' ') ?> Ar</span>
                        <span class="text-gray-500">Dernier Statut</span>
                        <span class="text-right uppercase font-black text-[10px] <?= $bulletin['statut'] == 'valide' ? 'text-emerald-600' : 'text-blue-600' ?>"><?= $bulletin['statut'] ?></span>
                    </div>
                </div>
            </div>

            <!-- Tableau Central -->
            <div class="rounded-2xl border border-gray-100 overflow-hidden mb-10">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-4">Rubriques / Libellés</th>
                            <th class="px-6 py-4 text-right">Base / Taux</th>
                            <th class="px-6 py-4 text-right">Gains</th>
                            <th class="px-6 py-4 text-right">Retenues</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <!-- Brut -->
                        <tr>
                            <td class="px-6 py-4 font-bold text-gray-800 italic">Salaire de Base Mensuel</td>
                            <td class="px-6 py-4 text-right text-gray-400">100%</td>
                            <td class="px-6 py-4 text-right font-black"><?= number_format($bulletin['salaire_brut'], 0, ',', ' ') ?></td>
                            <td class="px-6 py-4"></td>
                        </tr>
                        
                        <!-- Cotisations -->
                        <?php if ($bulletin['montant_cnaps_sal'] > 0): ?>
                        <tr>
                            <td class="px-6 py-4 text-gray-600">C.N.A.P.S (Part Salariale)</td>
                            <td class="px-6 py-4 text-right text-gray-400 text-xs">1% de <?= number_format($bulletin['salaire_brut'], 0, ',', ' ') ?></td>
                            <td class="px-6 py-4"></td>
                            <td class="px-6 py-4 text-right text-red-500 font-bold"><?= number_format($bulletin['montant_cnaps_sal'], 0, ',', ' ') ?></td>
                        </tr>
                        <?php endif; ?>

                        <?php if ($bulletin['montant_ostie_sal'] > 0): ?>
                        <tr>
                            <td class="px-6 py-4 text-gray-600">OSTIE / SMIE (Part Salariale)</td>
                            <td class="px-6 py-4 text-right text-gray-400 text-xs">1% de <?= number_format($bulletin['salaire_brut'], 0, ',', ' ') ?></td>
                            <td class="px-6 py-4"></td>
                            <td class="px-6 py-4 text-right text-red-500 font-bold"><?= number_format($bulletin['montant_ostie_sal'], 0, ',', ' ') ?></td>
                        </tr>
                        <?php endif; ?>

                        <!-- IRSA Separator -->
                        <tr class="bg-gray-50/30">
                            <td colspan="4" class="px-6 py-2 text-[10px] font-black text-gray-300 uppercase tracking-tighter">Impôt sur le Revenu</td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 text-gray-600">I.R.S.A (Impôt Progressif)</td>
                            <td class="px-6 py-4 text-right text-gray-400 text-xs">Barème 2026</td>
                            <td class="px-6 py-4"></td>
                            <td class="px-6 py-4 text-right text-gray-400"><?= number_format($bulletin['irsa_brut'], 0, ',', ' ') ?></td>
                        </tr>

                        <?php if ($bulletin['reduction_charges_famille'] > 0): ?>
                        <tr>
                            <td class="px-6 py-4 text-gray-600">Réduction Charges de Famille</td>
                            <td class="px-6 py-4 text-right text-gray-400 text-xs"><?= $bulletin['nb_enfants'] ?? 0 ?> enfant(s)</td>
                            <td class="px-6 py-4 text-right text-emerald-500 font-bold"><?= number_format($bulletin['reduction_charges_famille'], 0, ',', ' ') ?></td>
                            <td class="px-6 py-4"></td>
                        </tr>
                        <?php endif; ?>

                        <tr class="font-bold">
                            <td class="px-6 py-4 text-gray-800">I.R.S.A Net à déduire</td>
                            <td class="px-6 py-4 text-right text-gray-400 text-xs">Min. 3 000 Ar</td>
                            <td class="px-6 py-4"></td>
                            <td class="px-6 py-4 text-right text-amber-600"><?= number_format($bulletin['irsa_net'], 0, ',', ' ') ?></td>
                        </tr>

                        <?php if ($bulletin['total_retenues_diverses'] > 0): ?>
                        <tr class="bg-gray-50/50">
                            <td class="px-6 py-4 text-gray-800">Retenues Diverses</td>
                            <td class="px-6 py-4 text-right text-gray-400">-</td>
                            <td class="px-6 py-4"></td>
                            <td class="px-6 py-4 text-right text-red-600 font-black"><?= number_format($bulletin['total_retenues_diverses'], 0, ',', ' ') ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Net à Payer Card -->
            <div class="bg-gray-800 rounded-3xl p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl shadow-gray-200">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Net à Payer</p>
                    <p class="text-xs text-gray-400">Certifié conforme à la réglementation en vigueur.</p>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-4xl md:text-5xl font-black tracking-tight text-emerald-400"><?= number_format($bulletin['salaire_net'], 0, ',', ' ') ?> <span class="text-xl font-normal text-white/50">Ar</span></p>
                </div>
            </div>

            <!-- Signatures et Cachet -->
            <div class="grid grid-cols-2 gap-12 mt-16 text-center">
                <div class="space-y-4">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2 mb-12">Signature Salarié</p>
                    <p class="text-[10px] text-gray-300 italic mx-auto w-3/4">"Pour valoir ce que de droit, le salaire mentionné ci-dessus a été perçu en intégralité."</p>
                </div>
                <div class="space-y-4">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2 mb-12">Cachet Établissement</p>
                    <div class="w-32 h-32 border-2 border-dashed border-gray-100 rounded-full mx-auto flex items-center justify-center opacity-30">
                        <i class="fas fa-stamp text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 p-6 text-center border-t border-gray-100 hidden print:block">
            <p class="text-[8px] text-gray-400 italic">Ce document est un bulletin de paie électronique généré par l'ERP ROSSIGNOLES le <?= date('d/m/Y à H:i') ?>.</p>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; font-size: 10pt; }
    .no-print { display: none !important; }
    .main-content { padding-top: 0 !important; margin-left: 0 !important; }
    .print\:shadow-none { box-shadow: none !important; }
    .print\:border { border: 1px solid #e5e7eb !important; }
    .print\:rounded-none { border-radius: 0 !important; }
    .max-w-4xl { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
}
</style>
