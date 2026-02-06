<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center text-white mr-4 shadow-lg shadow-emerald-100">
                    <i class="fas fa-file-contract"></i>
                </div>
                Contrats de Paie
            </h1>
            <p class="text-gray-600">Gestion des salaires et paramètres sociaux du personnel</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= url('paie') ?>" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-sm font-semibold">
                <i class="fas fa-arrow-left text-emerald-600"></i>
                <span>Retour</span>
            </a>
            <a href="<?= url('paie/contrats/form') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-lg shadow-emerald-100 font-semibold">
                <i class="fas fa-plus-circle"></i>
                <span>Nouveau Contrat</span>
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Contrats actifs</p>
            <p class="text-2xl font-black text-gray-800"><?= count($contrats) ?></p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Masse salariale brute</p>
            <p class="text-2xl font-black text-emerald-600"><?= number_format(array_sum(array_column($contrats, 'salaire_brut_base')), 0, ',', ' ') ?> <span class="text-sm font-normal text-gray-400">Ar</span></p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Soumis Cotisations</p>
            <p class="text-2xl font-black text-blue-600"><?= count(array_filter($contrats, fn($c) => $c['soumis_cotisations'])) ?></p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Total Enfants</p>
            <p class="text-2xl font-black text-orange-500"><?= array_sum(array_column($contrats, 'nb_enfants')) ?></p>
        </div>
    </div>

    <!-- Liste des contrats -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Personnel</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Type / Contrat</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Salaire Brut</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Cotis.</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Enfants</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (!empty($contrats)): ?>
                        <?php foreach ($contrats as $contrat): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mr-3 border border-emerald-100">
                                            <?= strtoupper(substr($contrat['nom'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 line-clamp-1"><?= htmlspecialchars($contrat['nom'] . ' ' . $contrat['prenom']) ?></p>
                                            <p class="text-xs text-gray-500 font-medium tracking-tighter">Matricule: <?= htmlspecialchars($contrat['matricule']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <?php
                                        $typeClass = match($contrat['type_personnel']) {
                                            'enseignant' => 'bg-blue-50 text-blue-600',
                                            'administratif' => 'bg-indigo-50 text-indigo-600',
                                            'service' => 'bg-gray-100 text-gray-600',
                                            default => 'bg-gray-50 text-gray-400'
                                        };
                                        $contratClass = match($contrat['type_contrat']) {
                                            'cdi' => 'bg-emerald-50 text-emerald-600',
                                            'cdd' => 'bg-amber-50 text-amber-600',
                                            'stagiaire' => 'bg-cyan-50 text-cyan-600',
                                            default => 'bg-gray-100 text-gray-500'
                                        };
                                        ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider w-fit <?= $typeClass ?>">
                                            <?= $contrat['type_personnel'] ?>
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider w-fit <?= $contratClass ?>">
                                            <?= $contrat['type_contrat'] ?? 'N/A' ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-black text-gray-800 text-sm">
                                        <?= number_format($contrat['salaire_brut_base'], 0, ',', ' ') ?>
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 ml-1">MGA</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($contrat['soumis_cotisations']): ?>
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto" title="Soumis aux cotisations">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto" title="Non soumis">
                                            <i class="fas fa-minus text-xs"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 text-gray-700 text-xs font-bold">
                                        <?= $contrat['nb_enfants'] ?? 0 ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= url('paie/contrats/form?personnel_id=' . $contrat['personnel_id']) ?>" 
                                       class="w-10 h-10 rounded-xl bg-white border border-gray-200 text-emerald-600 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all inline-flex items-center justify-center shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <i class="fas fa-inbox text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800 mb-2">Aucun contrat trouvé</h3>
                                <p class="text-gray-400 max-w-xs mx-auto mb-6 text-sm">Commencez par créer un contrat de paie pour un membre du personnel.</p>
                                <a href="<?= url('paie/contrats/form') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold transition">
                                    Créer le premier contrat
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Aide contextuelle -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100">
            <h6 class="text-emerald-900 font-bold mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                Salaire de base
            </h6>
            <p class="text-xs text-emerald-800 leading-relaxed">
                Le salaire brut stipulé au contrat sert de base unique pour tous les calculs de cotisations et d'impôts mensuels.
            </p>
        </div>
        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
            <h6 class="text-blue-900 font-bold mb-3 flex items-center gap-2">
                <i class="fas fa-shield-alt"></i>
                Soumission sociale
            </h6>
            <p class="text-xs text-blue-800 leading-relaxed">
                Définit si l'employé contribue à la CNAPS et à l'OSTIE. Les stagiaires sont généralement exemptés.
            </p>
        </div>
        <div class="bg-orange-50 rounded-2xl p-6 border border-orange-100">
            <h6 class="text-orange-900 font-bold mb-3 flex items-center gap-2">
                <i class="fas fa-users"></i>
                Situation familiale
            </h6>
            <p class="text-xs text-orange-800 leading-relaxed">
                Le nombre d'enfants impacte directement le Net à payer via la réduction d'impôt IRSA (2 000 Ar / enfant).
            </p>
        </div>
    </div>
</div>
