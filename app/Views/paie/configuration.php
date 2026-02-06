<div class="p-4 md:p-8">
    <!-- En-tÃªte -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center text-white mr-4 shadow-lg shadow-purple-100">
                    <i class="fas fa-cog"></i>
                </div>
                Configuration de la Paie
            </h1>
            <p class="text-gray-600">ParamÃ©trage des taux de cotisations et tranches IRSA</p>
        </div>
        <a href="<?= url('paie') ?>" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-sm font-semibold">
            <i class="fas fa-arrow-left text-purple-600"></i>
            <span>Retour au module</span>
        </a>
    </div>

    <!-- Conteneur principal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- ParamÃ¨tres de cotisations -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h5 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-percentage text-purple-600 mr-3"></i>
                        Taux de Cotisations Sociales
                    </h5>
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full uppercase">Mise Ã  jour libre</span>
                </div>
                <div class="p-6">
                    <form method="POST" action="<?= url('paie/configuration/update') ?>" id="form-cotisations">
                        <?= csrf_field() ?>
                        <?php if (!empty($cotisations)): ?>
                            <div class="space-y-6">
                                <?php foreach ($cotisations as $cotisation): ?>
                                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 relative group transition-all hover:bg-white hover:shadow-md">
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <h6 class="text-base font-bold text-gray-800"><?= htmlspecialchars($cotisation['nom']) ?></h6>
                                                <?php if (!empty($cotisation['description'])): ?>
                                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($cotisation['description']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-gray-400 border border-gray-100">
                                                <i class="fas <?= $cotisation['nom'] == 'CNAPS' ? 'fa-shield-alt' : ($cotisation['nom'] == 'OSTIE' ? 'fa-heartbeat' : 'fa-graduation-cap') ?>"></i>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Part Salariale</label>
                                                <div class="relative">
                                                    <input type="number"
                                                           name="cotisations[<?= htmlspecialchars($cotisation['nom']) ?>][salarial]"
                                                           class="w-full pl-4 pr-10 py-3 rounded-xl border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-bold text-gray-800"
                                                           value="<?= ($cotisation['taux_salarial'] * 100) ?>"
                                                           step="0.01" min="0" max="100" required>
                                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Part Patronale</label>
                                                <div class="relative">
                                                    <input type="number"
                                                           name="cotisations[<?= htmlspecialchars($cotisation['nom']) ?>][patronal]"
                                                           class="w-full pl-4 pr-10 py-3 rounded-xl border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-bold text-gray-800"
                                                           value="<?= ($cotisation['taux_patronal'] * 100) ?>"
                                                           step="0.01" min="0" max="100" required>
                                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php foreach ($tranches_irsa as $tranche): ?>
                                <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-8">
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-purple-100 flex items-center justify-center gap-3">
                                    <i class="fas fa-save"></i>
                                    <span>Enregistrer les paramÃ¨tres</span>
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="p-8 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 border border-gray-100">
                                    <i class="fas fa-info-circle text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Aucun paramÃ¨tre configurÃ©. Ils seront crÃ©Ã©s automatiquement.</p>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tranches IRSA & Infos -->
        <div class="space-y-8">
            <!-- Tranches IRSA -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h5 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-table text-blue-600 mr-3"></i>
                        Tranches IRSA 2026
                    </h5>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full uppercase">LÃ©gislation Active</span>
                </div>
                <div class="p-0">
                    <form method="POST" action="<?= url('paie/configuration/update-irsa') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="annee" value="2026">
                        <table class="w-full text-left" id="table-irsa">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr class="text-xs font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-6 py-4">Min (Ar)</th>
                                    <th class="px-6 py-4">Max (Ar)</th>
                                    <th class="px-6 py-4 text-right">Taux (%)</th>
                                    <th class="px-6 py-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if (!empty($tranches_irsa)): ?>
                                    <?php foreach ($tranches_irsa as $index => $tranche): ?>
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-4 py-2">
                                                <input type="number" name="tranches[<?= $index ?>][min]" value="<?= (int)$tranche['montant_min'] ?>" class="w-full px-2 py-1 rounded border-gray-200 text-sm font-semibold" required>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="tranches[<?= $index ?>][max]" value="<?= $tranche['montant_max'] ? (int)$tranche['montant_max'] : '' ?>" class="w-full px-2 py-1 rounded border-gray-200 text-sm font-semibold" placeholder="âˆž">
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <div class="relative w-24 ml-auto">
                                                    <input type="number" name="tranches[<?= $index ?>][taux]" value="<?= ($tranche['taux'] * 100) ?>" step="0.01" class="w-full pl-2 pr-6 py-1 rounded border-gray-200 text-sm font-bold text-blue-600 text-right" required>
                                                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-400">%</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <button type="button" onclick="this.closest('tr').remove()" class="text-red-400 hover:text-red-600 transition-colors">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <div class="p-6 bg-gray-50/50 flex flex-col gap-4">
                            <div class="flex gap-2">
                                <button type="button" onclick="addRowIrsa()" class="flex-1 bg-white hover:bg-gray-50 text-blue-600 border border-blue-200 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-plus"></i>
                                    <span>Ajouter une tranche</span>
                                </button>
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl text-xs font-bold transition-all shadow-lg shadow-blue-100 flex items-center justify-center gap-2">
                                    <i class="fas fa-check"></i>
                                    <span>Appliquer les taux</span>
                                </button>
                            </div>

                            <div class="flex items-start gap-3 mt-2">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                                <p class="text-[10px] text-gray-500 leading-relaxed uppercase font-bold tracking-tight">
                                    Attention : Toute modification de ces taux impactera directement le calcul du salaire net sur tous les nouveaux bulletins gÃ©nÃ©rÃ©s.
                                </p>
                            </div>
                        </div>
                    </form>
                </div>

                <script>
                function addRowIrsa() {
                    const tbody = document.querySelector('#table-irsa tbody');
                    const index = tbody.querySelectorAll('tr').length;
                    const lastMax = index > 0 ? tbody.querySelector(`tr:last-child input[name*="[max]"]`).value : 0;

                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-gray-50/50 transition-colors animate-fade-in";
                    tr.innerHTML = `
                        <td class="px-4 py-2 text-sm">
                            <input type="number" name="tranches[${index}][min]" value="${lastMax ? parseInt(lastMax) + 1 : 0}" class="w-full px-2 py-1 rounded border-gray-200 text-sm font-semibold" required>
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" name="tranches[${index}][max]" value="" class="w-full px-2 py-1 rounded border-gray-200 text-sm font-semibold" placeholder="âˆž">
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="relative w-24 ml-auto">
                                <input type="number" name="tranches[${index}][taux]" value="0" step="0.01" class="w-full pl-2 pr-6 py-1 rounded border-gray-200 text-sm font-bold text-blue-600 text-right" required>
                                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-400">%</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <button type="button" onclick="this.closest('tr').remove()" class="text-red-400 hover:text-red-600 transition-colors">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                }
                </script>
            </div>

            <!-- Rappel des rÃ¨gles -->
            <div class="bg-indigo-600 rounded-2xl p-8 text-white relative overflow-hidden shadow-xl shadow-indigo-100">
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full"></div>
                <div class="absolute right-4 top-4 text-white/20 text-4xl">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h6 class="text-lg font-bold mb-6 flex items-center">
                    RÃ¨gles de calcul applicables
                </h6>
                <div class="space-y-5 relative">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-child text-sm"></i>
                        </div>
                        <p class="text-sm">
                            <span class="font-bold">Charges de famille :</span> Une rÃ©duction de 2 000 Ar est dÃ©duite de l'IRSA brut par enfant Ã  charge.
                        </p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-hand-holding-dollar text-sm"></i>
                        </div>
                        <p class="text-sm">
                            <span class="font-bold">Minimum de perception :</span> L'IRSA net ne peut Ãªtre infÃ©rieur Ã  3 000 Ar par mois.
                        </p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calculator text-sm"></i>
                        </div>
                        <p class="text-sm">
                            <span class="font-bold">Assiette imposable :</span> L'IRSA est assis sur le Brut dÃ©duit de la CNAPS et de l'OSTIE salariales.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
