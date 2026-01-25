<?php
/**
 * Vue : Saisie des paiements mensuels d'écolage
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="p-4 md:p-8 space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-hand-holding-usd text-green-600 mr-2"></i>
                Paiements Mensuels
            </h1>
            <p class="text-gray-500 mt-1">Gérez les écolages et suivis de paiements des élèves.</p>
        </div>
        <a href="<?= url('finance/dashboard') ?>" 
            class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Retour</span>
        </a>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-search text-teal-500"></i>
            Rechercher un élève
        </h2>
        
        <form method="GET" action="<?= url('finance/paiement-mensuel') ?>" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <?= csrf_field() ?>
            <div class="md:col-span-7">

                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" 
                           name="search" 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors" 
                           placeholder="Nom, prénom ou matricule..."
                           value="<?= e($_GET['search'] ?? '') ?>"
                           autofocus>
                </div>
            </div>
            
            <div class="md:col-span-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                <select name="classe_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white">
                    <option value="">Toutes les classes</option>
                    <?php 
                    $currentCycle = '';
                    foreach ($classes as $classe): 
                        if ($currentCycle !== $classe['cycle_nom']): 
                            if ($currentCycle !== '') echo '</optgroup>';
                            $currentCycle = $classe['cycle_nom'];
                            echo '<optgroup label="' . e($currentCycle) . '">';
                        endif;
                    ?>
                        <option value="<?= $classe['id'] ?>" <?= ($_GET['classe_id'] ?? '') == $classe['id'] ? 'selected' : '' ?>>
                            <?= e(!empty($classe['code']) ? $classe['code'] : $classe['nom']) ?>
                        </option>
                    <?php endforeach; 
                    if ($currentCycle !== '') echo '</optgroup>';
                    ?>
                </select>
            </div>
            
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-transparent mb-1">Recherche</label>
                <input type="hidden" name="annee_scolaire_id" value="<?= $anneeScolaireId ?>">
                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium h-[37px] rounded-lg transition-colors shadow-md hover:shadow-lg flex items-center justify-center">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    <?php if (isset($eleves) && !empty($eleves)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-semibold text-gray-800">
                    Résultats <span class="ml-2 px-2 py-0.5 bg-teal-100 text-teal-700 text-xs rounded-full"><?= count($eleves) ?></span>
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-white uppercase bg-teal-600 border-b border-teal-700">
                            <th class="px-6 py-3 font-semibold">Élève</th>
                            <th class="px-6 py-3 font-semibold text-center">Classe</th>
                            <th class="px-6 py-3 font-semibold text-center">Statut Paiements</th>
                            <th class="px-6 py-3 font-semibold text-center">Reste à payer (MGA)</th>
                            <th class="px-6 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($eleves as $eleve): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center font-bold text-sm">
                                            <?= strtoupper(substr($eleve['nom'], 0, 1) . substr($eleve['prenom'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900"><?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?></div>
                                            <div class="text-xs text-gray-500 font-mono"><?= e($eleve['matricule']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?= e($eleve['classe_nom']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($eleve['nb_impayees'] > 0): ?>
                                        <div class="inline-flex flex-col items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <?= $eleve['nb_impayees'] ?> impayé(s)
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> À jour
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="font-mono font-medium <?= $eleve['total_impaye'] > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                        <?= number_format($eleve['total_impaye'], 0, ',', ' ') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= url('finance/paiement-mensuel/saisir') ?>?eleve_id=<?= $eleve['id'] ?>&annee_scolaire_id=<?= $anneeScolaireId ?>" 
                                       class="inline-flex items-center justify-center px-4 py-2 bg-teal-600 text-white hover:bg-teal-700 rounded-lg text-sm font-medium transition-colors shadow-sm">
                                        <i class="fas fa-cash-register mr-2"></i>
                                        Encaisser
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    
    <?php elseif (isset($_GET['search']) || isset($_GET['classe_id'])): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-search text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun élève trouvé</h3>
            <p class="text-gray-500">Essayez de modifier vos critères de recherche.</p>
        </div>
    <?php endif; ?>

    <!-- Instructions -->
    <?php if (!isset($_GET['search']) && !isset($_GET['classe_id'])): ?>
        <div class="bg-gradient-to-br from-teal-50 to-teal-100/50 rounded-xl border border-teal-100 p-6 md:p-8">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-white rounded-lg shadow-sm text-teal-600">
                    <i class="fas fa-lightbulb text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-teal-900 mb-2">Comment saisir un paiement ?</h3>
                    <ul class="space-y-2 text-teal-800/80">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-teal-500 text-sm"></i>
                            Recherchez l'élève par son nom ou sélectionnez une classe.
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-teal-500 text-sm"></i>
                            Cliquez sur le bouton "Encaisser" qui apparaît au survol de la ligne.
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-teal-500 text-sm"></i>
                            Sélectionnez les mois à régler et validez le paiement.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Auto-submit sur changement de classe
document.querySelectorAll('select[name="classe_id"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
