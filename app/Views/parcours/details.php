<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1">
                <i class="fas fa-route text-indigo-600 mr-2"></i>
                Parcours de <?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?>
            </h1>
            <p class="text-gray-600 text-sm md:text-base">
                Matricule : <span class="font-semibold"><?= e($eleve['matricule'] ?? '') ?></span>
            </p>
        </div>
        <a href="<?= url('parcours/list') ?>"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>

    <!-- Parcours par année -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Année scolaire
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Classe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Niveau / Série
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type inscription
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Moyenne / Rang
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Résultat / Mention
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Décision / Suite
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($parcours)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucun parcours validé trouvé pour cet élève.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($parcours as $p): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-l-4 <?= !empty($p['annee_active']) ? 'border-green-500' : 'border-transparent' ?>">
                                    <div class="font-medium"><?= e($p['annee_libelle']) ?></div>
                                    <?php if (!empty($p['annee_active'])): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 mt-1 text-[10px] font-bold rounded-full bg-green-100 text-green-800 uppercase">
                                            En cours
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-semibold text-blue-600"><?= e($p['classe_nom']) ?></div>
                                    <div class="text-[10px] text-gray-400 uppercase font-bold"><?= e($p['classe_code']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <div class="font-medium"><?= e($p['niveau_nom']) ?></div>
                                    <?php if (!empty($p['serie_nom'])): ?>
                                        <div class="text-xs text-gray-500 "><?= e($p['serie_nom']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($p['type_inscription'] === 'nouvelle'): ?>
                                        <span class="text-xs font-medium text-emerald-600">
                                            <i class="fas fa-plus-circle mr-1"></i>Nouvelle
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs font-medium text-indigo-600">
                                            <i class="fas fa-sync-alt mr-1"></i>Réinscription
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="text-xs">Inscrit le : <?= formatDate($p['date_inscription']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    <?php 
                                    $moyenne = $p['moyenne_annuelle'] ?? null;
                                    if ($moyenne): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold <?= $moyenne >= 10 ? 'text-green-600' : 'text-red-600' ?>">
                                                <?= number_format($moyenne, 2, ',', ' ') ?>
                                            </span>
                                            <span class="text-xs text-gray-400">/ 20</span>
                                        </div>
                                        <?php if (!empty($p['rang_classe'])): ?>
                                            <div class="text-xs text-gray-500">
                                                Rang : <span class="font-bold"><?= $p['rang_classe'] ?><sup><?= $p['rang_classe'] == 1 ? 'er' : 'ème' ?></sup></span> / <?= $p['effectif_classe'] ?? '?' ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 ">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if (!empty($p['resultat'])): ?>
                                        <?php 
                                        $resColor = match($p['resultat']) {
                                            'admis', 'admis_avec_mention' => 'green',
                                            'redouble' => 'orange',
                                            'exclus', 'abandonne' => 'red',
                                            default => 'gray'
                                        };
                                        ?>
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-<?= $resColor ?>-100 text-<?= $resColor ?>-800 uppercase border border-<?= $resColor ?>-200">
                                            <?= str_replace('_', ' ', $p['resultat']) ?>
                                        </span>
                                        <?php if (!empty($p['mention'])): ?>
                                            <div class="mt-1 text-xs font-bold text-amber-600  uppercase">
                                                Mention <?= str_replace('_', ' ', $p['mention']) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 ">En attente</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if (!empty($p['classe_suivante_nom'])): ?>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">Passage en :</div>
                                        <div class="font-bold text-indigo-700">
                                            <i class="fas fa-arrow-right mr-1 text-[10px]"></i>
                                            <?= e($p['classe_suivante_nom']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 ">Non définie</span>
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



