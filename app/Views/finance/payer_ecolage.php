<div class="p-4 md:p-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="<?= url('finance/ecolage') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition shadow-md mb-4 font-medium">
                <i class="fas fa-arrow-left"></i> 
                <span>Retour au suivi</span>
            </a>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-cash-register text-green-600"></i>
                Enregistrer un paiement
            </h1>
            <?php 
            $moisNoms = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];
            ?>
            <p class="text-gray-600">Écolage pour <span class="font-semibold text-blue-600"><?= $moisNoms[$echeance['mois']] ?? $echeance['mois'] ?> <?= $echeance['annee'] ?></span></p>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 bg-gray-50 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl">
                        <?= substr($echeance['eleve_prenom'], 0, 1) ?>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800"><?= e($echeance['eleve_nom'] . ' ' . $echeance['eleve_prenom']) ?></h2>
                        <p class="text-gray-600 text-sm">
                            <i class="fas fa-id-card mr-1"></i> <?= e($echeance['matricule']) ?> 
                            <span class="mx-2">•</span> 
                            <i class="fas fa-layer-group mr-1"></i> <?= e($echeance['classe_nom']) ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Statut Actuel -->
                <div class="flex items-center justify-between mb-6 p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-600 text-sm">Statut actuel</span>
                    <?php
                    $badgeClass = match($echeance['statut']) {
                        'PAYE' => 'bg-green-100 text-green-700',
                        'PARTIEL' => 'bg-yellow-100 text-yellow-700',
                        'EN_RETARD' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700'
                    };
                    ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass ?>">
                        <?= $echeance['statut'] ?>
                    </span>
                </div>

                <!-- Info Paiement -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-500 mb-1">Montant Dû</p>
                        <p class="text-lg font-bold text-gray-800"><?= formatMoney($echeance['montant_du']) ?></p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-green-600 mb-1">Déjà Payé</p>
                        <p class="text-lg font-bold text-green-700"><?= formatMoney($echeance['montant_paye']) ?></p>
                    </div>
                    <?php $reste = $echeance['montant_du'] - $echeance['montant_paye']; ?>
                    <div class="bg-red-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-red-600 mb-1">Reste</p>
                        <p class="text-lg font-bold text-red-700"><?= formatMoney($reste) ?></p>
                    </div>
                </div>

                <?php if ($reste <= 0): ?>
                    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-center mb-6">
                        <i class="fas fa-check-circle text-2xl mb-2 block"></i>
                        <p class="font-bold">Cet écolage est entièrement réglé.</p>
                    </div>
                    <div class="text-center">
                        <a href="<?= url('finance/ecolage') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition inline-block shadow-md font-medium">
                            Retour
                        </a>
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?= url('finance/ecolage/payer/' . $echeance['id']) ?>" class="space-y-6">
                        <?= csrf_field() ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant à payer (Ar)</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm font-bold">Ar</span>
                                </div>
                                <input type="text" name="montant" id="montant" 
                                       value="<?= $reste ?>"
                                       class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 sm:text-lg border-gray-300 rounded-md py-3 font-semibold amount-format" 
                                       required autofocus>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Le montant maximum est de <?= formatMoney($reste) ?></p>
                        </div>
                        
                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition font-medium text-lg flex items-center justify-center gap-2 shadow-lg">
                                <i class="fas fa-check"></i>
                                Valider le paiement
                            </button>
                            <a href="<?= url('finance/ecolage') ?>" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Annuler
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($echeance['parent_telephone'])): ?>
            <div class="bg-yellow-50 px-6 py-4 border-t border-yellow-100 flex items-start gap-3">
                <i class="fas fa-info-circle text-yellow-600 mt-1"></i>
                <div>
                    <p class="text-sm text-yellow-800 font-semibold">Contact Parent</p>
                    <p class="text-sm text-yellow-700"><?= e($echeance['parent_nom']) ?> : <?= e($echeance['parent_telephone']) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
