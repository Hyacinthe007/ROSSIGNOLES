<div class="p-6 max-w-7xl mx-auto animation-fade-in relative z-10">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Liste des notes</h1>
            <p class="text-gray-500 mt-1">Gérez les évaluations et les notes des élèves</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-filter text-blue-500"></i> Filtres
        </h2>
        <form id="filter-form" method="GET" action="<?= url('notes/list') ?>" class="flex flex-col lg:flex-row lg:items-end gap-6">
            <input type="hidden" name="periode_id" id="periode_id" value="<?= $selectedPeriode ?>">
            
            <!-- Période Selector (Modern Pills) -->
            <div class="flex-grow">
                <label class="block text-sm font-medium text-gray-700 mb-3">Période scolaire</label>
                <div class="flex flex-wrap gap-3">
                    <?php if (empty($periodes)): ?>
                        <p class="text-sm text-gray-500 ">Aucune période disponible</p>
                    <?php else: ?>
                        <?php foreach ($periodes as $periode): ?>
                            <button type="button" 
                                    onclick="setPeriodeAndSubmit('<?= $periode['id'] ?>')"
                                    class="<?= $selectedPeriode == $periode['id'] 
                                        ? 'bg-blue-600 text-white shadow-md ring-2 ring-offset-2 ring-blue-500' 
                                        : 'bg-white text-gray-600 border border-gray-200 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50' ?> 
                                        px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 flex items-center gap-2 group">
                                <span class="<?= $selectedPeriode == $periode['id'] ? 'text-blue-200' : 'text-gray-400 group-hover:text-blue-400' ?>">
                                    <i class="far fa-calendar-check"></i>
                                </span>
                                <?= e($periode['nom']) ?>
                            </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Classe Selector -->
            <div class="w-full lg:w-72 flex-shrink-0">
                <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-users text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <select name="classe_id" id="classe_id" 
                            class="appearance-none block w-full pl-10 pr-10 py-3 text-base border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-gray-50 border transition-all hover:bg-white cursor-pointer"
                            onchange="this.form.submit()">
                        <option value="">Sélectionner une classe</option>
                        <?php foreach ($classes as $classe): ?>
                            <option value="<?= $classe['id'] ?>" <?= $selectedClasse == $classe['id'] ? 'selected' : '' ?>>
                                <?= e($classe['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>
        </form>

<script>
    function setPeriodeAndSubmit(id) {
        document.getElementById('periode_id').value = id;
        document.getElementById('filter-form').submit();
    }
</script>
    </div>

    <!-- Content -->
    <?php if ($selectedClasse && $selectedPeriode): ?>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-lg font-semibold text-gray-800">Évaluations</h2>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                    <?= count($evaluations) ?> résultat(s)
                </span>
            </div>
            
            <?php if (empty($evaluations)): ?>
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-4 text-blue-500">
                        <i class="fas fa-clipboard-list text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Aucune évaluation trouvée</h3>
                    <p class="mt-2 text-gray-500 max-w-sm mx-auto">Il n'y a pas encore d'évaluations enregistrées pour cette classe et cette période.</p>
                    <a href="<?= url('evaluations/add') ?>" class="mt-6 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i> Ajouter une évaluation
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse table-responsive-text">
                        <thead>
                            <tr class="bg-gray-50/50 text-xs uppercase text-gray-500 font-semibold tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Matière</th>
                                <th class="px-6 py-4">Intitulé</th>
                                <th class="px-6 py-4 text-center">Barème</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($evaluations as $eval): ?>
                                <tr class="hover:bg-blue-50/30 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                        <?php 
                                            $date = $eval['type'] === 'examen' ? $eval['date_examen'] : $eval['date_interrogation'];
                                            echo formatDate($date);
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($eval['type'] === 'examen'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                Examen
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 border border-teal-200">
                                                Interro
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?= e($eval['matiere_nom']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        <?= e($eval['nom']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">
                                        Sur <span class="font-semibold text-gray-900"><?= number_format($eval['note_sur'], 2) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <a href="<?= url('notes/saisie?type=' . urlencode($eval['type']) . '&id=' . $eval['id']) ?>" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md transition-colors text-xs font-semibold"
                                               title="Saisie classique élève par élève">
                                                <i class="fas fa-edit"></i> Saisir
                                            </a>
                                            <a href="<?= url('notes/saisie-masse?type=' . urlencode($eval['type']) . '&id=' . $eval['id']) ?>" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-md transition-colors text-xs font-semibold"
                                               title="Saisie rapide ou Import Excel">
                                                <i class="fas fa-file-excel"></i> Import Excel
                                            </a>
                                            <?php
                                                // Rediriger le bouton "Détails" vers le bon module
                                                $detailsUrl = $eval['type'] === 'examen'
                                                    ? url('examens/edit/' . $eval['id'])
                                                    : url('interrogations/edit/' . $eval['id']);
                                            ?>
                                            <a href="<?= $detailsUrl ?>" 
                                               class="inline-flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md transition-colors"
                                               title="Détails de l'évaluation">
                                                <i class="fas fa-eye text-sm"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php elseif (!$anneeActive): ?>
        <div class="bg-amber-50 rounded-xl p-6 border border-amber-100 flex items-start gap-4">
            <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-amber-800">Aucune année scolaire active</h3>
                <p class="mt-1 text-amber-700">Veuillez activer une année scolaire dans les paramètres pour commencer à gérer les notes.</p>
                <a href="<?= url('annees-scolaires/list') ?>" class="mt-3 inline-block text-sm font-medium text-amber-800 hover:text-amber-900 underline">Gérer les années scolaires &rarr;</a>
            </div>
        </div>
        
    <?php else: ?>
        <div class="bg-blue-50 rounded-xl p-8 border border-blue-100 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-500 mb-4 animate-bounce">
                <i class="fas fa-search text-2xl"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Sélectionnez les filtres</h3>
            <p class="text-gray-500 max-w-md">Veuillez sélectionner une <strong>période</strong> et une <strong>classe</strong> ci-dessus pour afficher la liste des évaluations et saisir les notes.</p>
        </div>
    <?php endif; ?>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animation-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}
</style>

