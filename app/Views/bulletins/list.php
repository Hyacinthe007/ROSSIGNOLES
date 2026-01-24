<?php
$title = "Bulletins";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Évaluations', 'url' => '#'],
    ['label' => 'Bulletins', 'url' => '/bulletins/list'],
    ['label' => 'Liste']
];
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                Bulletins de Notes
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des bulletins scolaires par période</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('bulletins/generer') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-magic"></i>
                <span class="hidden sm:inline">Générer les bulletins</span>
            </a>
            <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition flex items-center gap-2 border">
                <i class="fas fa-filter"></i>
                <span class="hidden sm:inline">Filtrer</span>
            </button>
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
                           placeholder="Rechercher par élève, classe, période..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <select id="statutFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                <option value="brouillon">Brouillon</option>
                <option value="valide">Validé</option>
                <option value="imprime">Imprimé</option>
                <option value="envoye">Envoyé</option>
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
                            <i class="fas fa-user-graduate mr-2"></i>Élève
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-door-open mr-2"></i>Classe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-calendar-alt mr-2"></i>Période
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-chart-line mr-2"></i>Moyenne
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-trophy mr-2"></i>Rang
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Date
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="bulletinsTableBody">
                    <?php if (empty($bulletins)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucun bulletin trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bulletins as $bulletin): ?>
                            <tr class="hover:bg-gray-50 transition bulletin-row" 
                                data-eleve="<?= strtolower(htmlspecialchars(($bulletin['eleve_nom'] ?? '') . ' ' . ($bulletin['eleve_prenom'] ?? ''))) ?>"
                                data-classe="<?= strtolower(htmlspecialchars($bulletin['classe_nom'] ?? '')) ?>"
                                data-periode="<?= strtolower(htmlspecialchars($bulletin['periode_nom'] ?? '')) ?>"
                                data-statut="<?= htmlspecialchars($bulletin['statut'] ?? '') ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-50 text-blue-600 p-2 rounded-lg mr-3">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars(($bulletin['eleve_nom'] ?? '') . ' ' . ($bulletin['eleve_prenom'] ?? '')) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?= htmlspecialchars($bulletin['matricule'] ?? 'N/A') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($bulletin['classe_nom'] ?? 'N/A') ?>
                                    </div>
                                    <?php if (!empty($bulletin['classe_code'])): ?>
                                        <div class="text-xs text-gray-500 font-mono">
                                            <?= htmlspecialchars($bulletin['classe_code']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($bulletin['periode_nom'] ?? 'N/A') ?>
                                    </div>
                                    <?php if (!empty($bulletin['periode_debut']) && !empty($bulletin['periode_fin'])): ?>
                                        <div class="text-xs text-gray-500">
                                            <?= date('d/m', strtotime($bulletin['periode_debut'])) ?> - <?= date('d/m/Y', strtotime($bulletin['periode_fin'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($bulletin['moyenne_generale'])): ?>
                                        <?php
                                        $moyenne = floatval($bulletin['moyenne_generale']);
                                        $colorClass = $moyenne >= 16 ? 'text-green-600' : ($moyenne >= 14 ? 'text-blue-600' : ($moyenne >= 10 ? 'text-yellow-600' : 'text-red-600'));
                                        ?>
                                        <div class="flex items-center">
                                            <span class="text-lg font-bold <?= $colorClass ?>">
                                                <?= number_format($moyenne, 2) ?>
                                            </span>
                                            <span class="text-xs text-gray-500 ml-1">/20</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($bulletin['rang'])): ?>
                                        <div class="flex items-center">
                                            <i class="fas fa-trophy text-yellow-500 mr-1"></i>
                                            <span class="text-sm font-semibold text-gray-900">
                                                <?= $bulletin['rang'] ?><sup><?= $bulletin['rang'] == 1 ? 'er' : 'ème' ?></sup>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">-</span>
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
                                    $statutInfo = $statuts[$bulletin['statut'] ?? 'brouillon'] ?? ['Inconnu', 'bg-gray-100 text-gray-800'];
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statutInfo[1] ?>">
                                        <?= $statutInfo[0] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= !empty($bulletin['created_at']) ? date('d/m/Y', strtotime($bulletin['created_at'])) : '-' ?>
                                    </div>
                                    <?php if (!empty($bulletin['date_validation'])): ?>
                                        <div class="text-xs text-gray-500">
                                            Validé le <?= date('d/m/Y', strtotime($bulletin['date_validation'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <?php if (($bulletin['statut'] ?? 'brouillon') === 'brouillon'): ?>
                                            <a href="<?= url('bulletins/valider/' . $bulletin['id']) ?>" 
                                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition flex items-center gap-1.5"
                                               title="Valider"
                                               onclick="return confirm('Voulez-vous vraiment valider ce bulletin ? Cette action est irréversible.')">
                                                <i class="fas fa-check-circle text-xs"></i>
                                                <span class="hidden sm:inline">Valider</span>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= url('bulletins/pdf/' . $bulletin['id']) ?>" 
                                           class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg transition flex items-center gap-1.5"
                                           title="Télécharger PDF"
                                           target="_blank">
                                            <i class="fas fa-file-pdf text-xs"></i>
                                            <span class="hidden sm:inline">PDF</span>
                                        </a>
                                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition flex items-center gap-1.5"
                                                title="Voir les détails">
                                            <i class="fas fa-eye text-xs"></i>
                                            <span class="hidden sm:inline">Voir</span>
                                        </button>
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
    const rows = document.querySelectorAll('.bulletin-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statutValue = statutFilter.value;

        rows.forEach(row => {
            const eleve = row.dataset.eleve || '';
            const classe = row.dataset.classe || '';
            const periode = row.dataset.periode || '';
            const statut = row.dataset.statut || '';

            const matchesSearch = !searchTerm || 
                eleve.includes(searchTerm) || 
                classe.includes(searchTerm) || 
                periode.includes(searchTerm);

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
