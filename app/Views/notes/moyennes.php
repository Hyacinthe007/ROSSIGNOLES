<?php
$title = "Moyennes & Statistiques";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Évaluations', 'url' => '#'],
    ['label' => 'Moyennes & Statistiques', 'url' => '/notes/moyennes']
];
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                Moyennes & Statistiques
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Analyse des performances scolaires par classe et période</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </button>
            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-file-pdf"></i>
                <span>Export PDF</span>
            </button>
        </div>
    </div>

    <?php if (empty($anneeActive)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-400 mr-3"></i>
                <p class="text-yellow-700">Aucune année scolaire active. Veuillez activer une année scolaire.</p>
            </div>
        </div>
    <?php else: ?>
        <!-- Filtres -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-blue-500"></i> Filtres
            </h2>
            <form id="filter-form" method="GET" action="<?= url('notes/moyennes') ?>" class="flex flex-col lg:flex-row lg:items-end gap-6">
                <input type="hidden" name="periode_id" id="periode_id" value="<?= $selectedPeriode ?>">
                
                <!-- Période Selector (Modern Pills) -->
                <div class="flex-grow">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Période scolaire</label>
                    <div class="flex flex-wrap gap-3">
                        <?php if (empty($periodes)): ?>
                            <p class="text-sm text-gray-500 ">Aucune période disponible</p>
                        <?php else: ?>
                            <button type="button" 
                                    onclick="setPeriodeAndSubmit('')"
                                    class="<?= empty($selectedPeriode) 
                                        ? 'bg-blue-600 text-white shadow-md ring-2 ring-offset-2 ring-blue-500' 
                                        : 'bg-white text-gray-600 border border-gray-200 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50' ?> 
                                        px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 flex items-center gap-2 group">
                                <span class="<?= empty($selectedPeriode) ? 'text-blue-200' : 'text-gray-400 group-hover:text-blue-400' ?>">
                                    <i class="fas fa-th-large"></i>
                                </span>
                                Toutes
                            </button>
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
                                    <?= htmlspecialchars($periode['nom']) ?>
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
                            <option value="">Toutes les classes</option>
                            <?php foreach ($classes as $classe): ?>
                                <option value="<?= $classe['id'] ?>" <?= $selectedClasse == $classe['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($classe['code']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistiques globales -->
        <?php if (!empty($statsGlobales)): ?>
            <!-- Répartition des moyennes -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
                    <div class="text-center">
                        <p class="text-sm font-medium opacity-90">Excellent (≥16)</p>
                        <p class="text-3xl font-bold mt-2"><?= $statsGlobales['nb_excellents'] ?? 0 ?></p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
                    <div class="text-center">
                        <p class="text-sm font-medium opacity-90">Très bien (14-16)</p>
                        <p class="text-3xl font-bold mt-2"><?= $statsGlobales['nb_tres_bien'] ?? 0 ?></p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-4 text-white">
                    <div class="text-center">
                        <p class="text-sm font-medium opacity-90">Bien (12-14)</p>
                        <p class="text-3xl font-bold mt-2"><?= $statsGlobales['nb_bien'] ?? 0 ?></p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
                    <div class="text-center">
                        <p class="text-sm font-medium opacity-90">Passable (10-12)</p>
                        <p class="text-3xl font-bold mt-2"><?= $statsGlobales['nb_passables'] ?? 0 ?></p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
                    <div class="text-center">
                        <p class="text-sm font-medium opacity-90">Insuffisant (&lt;10)</p>
                        <p class="text-3xl font-bold mt-2"><?= $statsGlobales['nb_insuffisants'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistiques par classe -->
        <?php if (!empty($statsParClasse)): ?>
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-door-open text-blue-600 mr-2"></i>
                    Statistiques par classe
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Classe</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Élèves</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Moyenne classe</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Min</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Max</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Admis</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Taux réussite</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($statsParClasse as $stat): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-blue-50 text-blue-600 p-2 rounded-lg mr-3">
                                                <i class="fas fa-chalkboard"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($stat['classe_nom']) ?></div>
                                                <?php if (!empty($stat['classe_code'])): ?>
                                                    <div class="text-xs text-gray-500 font-mono"><?= htmlspecialchars($stat['classe_code']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $stat['nb_eleves'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg font-bold text-blue-600">
                                            <?= number_format($stat['moyenne_classe'], 2) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?= number_format($stat['moyenne_min'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?= number_format($stat['moyenne_max'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $stat['nb_admis'] ?> / <?= $stat['nb_bulletins'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                        $taux = $stat['nb_bulletins'] > 0 
                                            ? ($stat['nb_admis'] / $stat['nb_bulletins']) * 100 
                                            : 0;
                                        $colorClass = $taux >= 80 ? 'text-green-600' : ($taux >= 60 ? 'text-yellow-600' : 'text-red-600');
                                        ?>
                                        <span class="text-sm font-semibold <?= $colorClass ?>">
                                            <?= number_format($taux, 1) ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Liste des moyennes par élève -->
        <?php if (!empty($moyennesEleves)): ?>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-user-graduate text-blue-600 mr-2"></i>
                        Détail des moyennes par élève
                    </h2>
                    <div class="mt-2 sm:mt-0">
                        <input type="text" 
                               id="searchEleve" 
                               placeholder="Rechercher un élève..." 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Élève</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Classe</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Période</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Moyenne</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Rang</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Points</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="elevesTableBody">
                            <?php foreach ($moyennesEleves as $eleve): ?>
                                <tr class="hover:bg-gray-50 transition eleve-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-blue-50 text-blue-600 p-2 rounded-lg mr-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($eleve['eleve_nom'] . ' ' . $eleve['eleve_prenom']) ?>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <?= htmlspecialchars($eleve['matricule']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($eleve['classe_nom']) ?>
                                        <?php if (!empty($eleve['classe_code'])): ?>
                                            <div class="text-xs text-gray-500 font-mono"><?= htmlspecialchars($eleve['classe_code']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($eleve['periode_nom']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $moyenne = floatval($eleve['moyenne_generale']);
                                        $colorClass = $moyenne >= 16 ? 'text-green-600' : ($moyenne >= 14 ? 'text-blue-600' : ($moyenne >= 10 ? 'text-yellow-600' : 'text-red-600'));
                                        ?>
                                        <span class="text-lg font-bold <?= $colorClass ?>">
                                            <?= number_format($moyenne, 2) ?>
                                        </span>
                                        <span class="text-xs text-gray-500 ml-1">/20</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($eleve['rang'])): ?>
                                            <div class="flex items-center">
                                                <i class="fas fa-trophy text-yellow-500 mr-1"></i>
                                                <span class="text-sm font-semibold text-gray-900">
                                                    <?= $eleve['rang'] ?><sup><?= $eleve['rang'] == 1 ? 'er' : 'ème' ?></sup>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if (!empty($eleve['total_points']) && !empty($eleve['total_coefficients'])): ?>
                                            <?= number_format($eleve['total_points'], 2) ?> / <?= number_format($eleve['total_coefficients'], 2) ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statuts = [
                                            'brouillon' => ['Brouillon', 'bg-gray-100 text-gray-800'],
                                            'valide' => ['Validé', 'bg-green-100 text-green-800'],
                                            'imprime' => ['Imprimé', 'bg-purple-100 text-purple-800'],
                                            'envoye' => ['Envoyé', 'bg-blue-100 text-blue-800']
                                        ];
                                        $statutInfo = $statuts[$eleve['statut'] ?? 'brouillon'] ?? ['Inconnu', 'bg-gray-100 text-gray-800'];
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statutInfo[1] ?>">
                                            <?= $statutInfo[0] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-lg p-8 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                Aucune donnée disponible pour les critères sélectionnés
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function setPeriodeAndSubmit(id) {
    document.getElementById('periode_id').value = id;
    document.getElementById('filter-form').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchEleve');
    const rows = document.querySelectorAll('.eleve-row');

    if (searchInput && rows.length > 0) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>

