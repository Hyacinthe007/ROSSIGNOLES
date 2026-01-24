<?php
$title = "Conseils de Classe";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Évaluations', 'url' => '#'],
    ['label' => 'Conseils de classe', 'url' => '/conseils/list'],
    ['label' => 'Liste']
];
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-users-class text-blue-600 mr-2"></i>
                Conseils de Classe
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des conseils de classe par période</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('conseils/add') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Planifier un conseil</span>
            </a>
            <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-filter"></i>
                <span>Filtrer</span>
            </button>
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 text-sm sm:text-base">
                <i class="fas fa-file-excel"></i>
                <span class="hidden sm:inline">Export</span>
            </button>
        </div>
    </div>

    <!-- Statistiques -->
    <?php
    $stats = [
        'total' => count($conseils),
        'prevus' => count(array_filter($conseils, fn($c) => in_array($c['statut'], ['prevu', 'planifie']))),
        'en_cours' => count(array_filter($conseils, fn($c) => $c['statut'] === 'en_cours')),
        'clotures' => count(array_filter($conseils, fn($c) => in_array($c['statut'], ['cloture', 'termine'])))
    ];
    ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['total'] ?></p>
                </div>
                <div class="bg-blue-100 rounded-lg p-3">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Prévus</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['prevus'] ?></p>
                </div>
                <div class="bg-yellow-100 rounded-lg p-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">En cours</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['en_cours'] ?></p>
                </div>
                <div class="bg-indigo-100 rounded-lg p-3">
                    <i class="fas fa-spinner text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Clôturés</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['clotures'] ?></p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" 
                           id="searchInput"
                           placeholder="Rechercher par classe, période, année..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <select id="statutFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                <option value="prevu">Prévu</option>
                <option value="en_cours">En cours</option>
                <option value="cloture">Clôturé</option>
                <option value="annule">Annulé</option>
            </select>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-door-open mr-2"></i>Classe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-calendar-alt mr-2"></i>Période
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-school mr-2"></i>Année scolaire
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Date du conseil
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-user-tie mr-2"></i>Président
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Statut
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="conseilsTableBody">
                    <?php if (empty($conseils)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucun conseil de classe trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($conseils as $conseil): ?>
                            <tr class="hover:bg-gray-50 transition conseil-row" 
                                data-classe="<?= strtolower(htmlspecialchars($conseil['classe_nom'])) ?>"
                                data-periode="<?= strtolower(htmlspecialchars($conseil['periode_nom'])) ?>"
                                data-annee="<?= strtolower(htmlspecialchars($conseil['annee_libelle'])) ?>"
                                data-statut="<?= htmlspecialchars($conseil['statut']) ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-50 text-blue-600 p-2 rounded-lg mr-3">
                                            <i class="fas fa-chalkboard"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($conseil['classe_nom']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($conseil['periode_nom']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($conseil['annee_libelle']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                        <?= date('d/m/Y', strtotime($conseil['date_conseil'])) ?>
                                    </div>
                                    <?php if (!empty($conseil['heure_debut'])): ?>
                                        <div class="text-xs text-gray-500">
                                            <i class="fas fa-clock text-gray-400 mr-1"></i>
                                            <?= date('H:i', strtotime($conseil['heure_debut'])) ?>
                                            <?php if (!empty($conseil['heure_fin'])): ?>
                                                - <?= date('H:i', strtotime($conseil['heure_fin'])) ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($conseil['president_nom'])): ?>
                                        <div class="text-sm text-gray-900">
                                            <?= htmlspecialchars($conseil['president_nom'] . ' ' . $conseil['president_prenom']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400 ">Non spécifié</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statuts = [
                                        'prevu' => ['Prévu', 'bg-yellow-100 text-yellow-800'],
                                        'en_cours' => ['En cours', 'bg-blue-100 text-blue-800'],
                                        'cloture' => ['Clôturé', 'bg-green-100 text-green-800'],
                                        'annule' => ['Annulé', 'bg-red-100 text-red-800']
                                    ];
                                    $statutInfo = $statuts[$conseil['statut']] ?? [$conseil['statut'], 'bg-gray-100 text-gray-800'];
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statutInfo[1] ?>">
                                        <?= $statutInfo[0] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?= url('conseils/details/' . $conseil['id']) ?>" 
                                           class="bg-blue-100 text-blue-600 hover:bg-blue-200 p-2 rounded-lg transition"
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('conseils/edit/' . $conseil['id']) ?>" 
                                           class="bg-indigo-100 text-indigo-600 hover:bg-indigo-200 p-2 rounded-lg transition"
                                           title="Modifier/Remplir">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('conseils/delete/' . $conseil['id']) ?>" 
                                           onclick="return confirm('Supprimer ce conseil ?')"
                                           class="bg-red-100 text-red-600 hover:bg-red-200 p-2 rounded-lg transition"
                                           title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statutFilter = document.getElementById('statutFilter');
    const rows = document.querySelectorAll('.conseil-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statutValue = statutFilter.value;

        rows.forEach(row => {
            const classe = row.dataset.classe || '';
            const periode = row.dataset.periode || '';
            const annee = row.dataset.annee || '';
            const statut = row.dataset.statut || '';

            const matchesSearch = !searchTerm || 
                classe.includes(searchTerm) || 
                periode.includes(searchTerm) || 
                annee.includes(searchTerm);

            const matchesStatut = !statutValue || statut === statutValue;

            if (matchesSearch && matchesStatut) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTable);
    statutFilter.addEventListener('change', filterTable);
});
</script>

