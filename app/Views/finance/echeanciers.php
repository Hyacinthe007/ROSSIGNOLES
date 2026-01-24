<div class="p-4 md:p-8">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-center justify-between no-print animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                <span class="text-green-800 font-medium"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-400 hover:text-green-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-center justify-between no-print animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
                <span class="text-red-800 font-medium"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas <?= $iconClass ?> mr-2"></i>
                <?= $pageTitle ?>
            </h1>
            <p class="text-gray-600 text-sm md:text-base"><?= $pageSubtitle ?></p>
        </div>
        <div class="flex gap-2">
            <?php if (!empty($echeances)): ?>
            <form action="<?= url('finance/echeanciers/sms-all') ?>" method="POST" class="inline">
                <input type="hidden" name="statut" value="<?= $statutFilter ?>">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-sm" onclick="return confirm('Relancer par SMS tous les élèves affichés dans cette liste ?')">
                    <i class="fas fa-sms"></i>
                    <span>Relancer par SMS (Tous)</span>
                </button>
            </form>
            <?php endif; ?>
            <button onclick="window.print()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition flex items-center gap-2 border border-gray-300">
                <i class="fas fa-print"></i>
                <span>Imprimer la liste</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap gap-2 mb-6 bg-gray-50 p-2 rounded-xl border border-gray-100 no-print">
        <a href="<?= url('finance/echeanciers') ?>" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= ($statutFilter === 'retard' || $statutFilter === 'retard_10') ? 'bg-orange-600 text-white shadow-md' : 'text-gray-600 hover:bg-white hover:text-orange-600' ?>">
            <i class="fas fa-hand-holding-usd mr-2"></i>Recouvrement
        </a>
        <a href="<?= url('finance/echeanciers?statut=exclusion') ?>" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= ($statutFilter === 'exclusion') ? 'bg-gray-800 text-white shadow-md' : 'text-gray-600 hover:bg-white hover:text-gray-800' ?>">
            <i class="fas fa-user-slash mr-2"></i>Liste des exclus
        </a>
    </div>

    <!-- Alert Status -->
    <div class="<?= $alertClass ?> border-l-4 p-4 mb-6 rounded-r-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm">
                    <?= $alertMessage ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Table des impayés -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <?php if (empty($echeances)): ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">
                        <?= $statutFilter === 'exclusion' ? 'Aucune suspension en cours' : 'Aucun impayé critique' ?>
                    </h3>
                    <p class="text-gray-500">
                        <?= $statutFilter === 'exclusion' 
                            ? 'Aucun élève n\'est actuellement suspendu pour motif financier.' 
                            : 'Tous les élèves sont à jour ou dans la période de grâce.' ?>
                    </p>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Élève</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mois Concerné</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Parent / Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Montant Dû</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($echeances as $e): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900"><?= e($e['eleve_nom'] . ' ' . $e['eleve_prenom']) ?></span>
                                        <span class="text-xs text-blue-600 font-mono"><?= e($e['matricule']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?= e($e['classe_nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold uppercase transition hover:bg-gray-200">
                                        <?= e($e['mois_libelle']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <p class="text-gray-900 font-medium"><?= e($e['parent_nom'] ?? 'N/A') ?></p>
                                    <p class="text-gray-500">
                                        <?= e($e['parent_telephone'] ?? 'N/A') ?>
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-bold text-red-600"><?= number_format($e['montant_restant'], 0, ',', ' ') ?> Ar</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php if ($e['statut'] === 'exclusion'): ?>
                                        <span class="px-2 py-1 bg-red-600 text-white rounded-full text-[10px] font-bold uppercase shadow-sm">
                                            Suspendu
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-[10px] font-bold uppercase">
                                            Recouvrement
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= url('finance/echeanciers/sms/' . $e['id']) ?>" 
                                       class="bg-orange-50 hover:bg-orange-100 text-orange-600 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1 text-xs border border-orange-200 mr-2"
                                       title="Envoyer une relance SMS au parent">
                                        <i class="fas fa-sms"></i>
                                        <span>SMS</span>
                                    </a>
                                    <a href="<?= url('finance/paiement-mensuel/saisir?eleve_id=' . $e['eleve_id'] . '&annee_scolaire_id=' . $e['annee_scolaire_id']) ?>" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition inline-flex items-center gap-2 text-xs shadow-sm">
                                        <i class="fas fa-wallet"></i>
                                        <span>Régler</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
