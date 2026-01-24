<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-graduate text-blue-600 mr-2"></i>
                Liste des élèves
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des élèves de l'école</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if (hasPermission('eleves.export_pdf')): ?>
            <a href="<?= url('eleves/export-pdf') ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-file-pdf"></i>
                <span>Export PDF</span>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('eleves.export_excel')): ?>
            <a href="<?= url('eleves/export-excel') ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('inscriptions_new.create')): ?>
            <a href="<?= url('inscriptions/nouveau') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Ajouter un élève</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Barre de recherche -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <form method="GET" action="<?= url('eleves/list') ?>" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-grow">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Recherche globale</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" 
                           id="searchInput"
                           name="search"
                           value="<?= e($_GET['search'] ?? '') ?>"
                           placeholder="Rechercher un élève par nom, prénom ou matricule..." 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 transition flex items-center gap-2">
                <i class="fas fa-filter"></i>
                <span>Rechercher</span>
            </button>
        </form>
    </div>


    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-900 capitalize tracking-wider">
                            <i class="fas fa-id-card mr-2"></i>Matricule
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-900 capitalize tracking-wider">
                            <i class="fas fa-user mr-2"></i>Nom & Prénom
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-900 capitalize tracking-wider">
                            <i class="fas fa-venus-mars mr-2"></i>Sexe
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-900 capitalize tracking-wider">
                            <i class="fas fa-door-open mr-2"></i>Classe
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-900 capitalize tracking-wider">
                            <i class="fas fa-phone mr-2"></i>Contact
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-900 capitalize tracking-wider">
                            <i class="fas fa-map-marker-alt mr-2"></i>Adresse
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-900 capitalize tracking-wider">
                            <i class="fas fa-credit-card mr-2"></i>Statut
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-bold text-gray-900 capitalize tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="elevesTableBody">
                    <?php if (empty($eleves)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucun élève trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($eleves as $eleve): ?>
                            <tr class="eleve-row hover:bg-gray-50 transition" data-search="<?= strtolower(e($eleve['matricule'] . ' ' . $eleve['nom'] . ' ' . $eleve['prenom'] . ' ' . ($eleve['derniere_classe'] ?? ''))) ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900"><?= e($eleve['matricule']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Photo -->
                                        <?php if (!empty($eleve['photo'])): ?>
                                            <img src="<?= public_url($eleve['photo']) ?>" 
                                                 alt="Photo" 
                                                 class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                                <i class="fas fa-user text-sm"></i>
                                            </div>
                                        <?php endif; ?>
                                        <!-- Nom et Prénom -->
                                        <div class="text-sm">
                                            <span class="font-semibold text-gray-900"><?= e($eleve['nom']) ?></span>
                                            <span class="text-gray-900"> <?= e($eleve['prenom']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $eleve['sexe'] == 'M' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' ?>">
                                        <?= e($eleve['sexe']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($eleve['derniere_classe'])): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                            <?= e($eleve['derniere_classe']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400 ">Non inscrit</span>
                                    <?php endif; ?>
                                </td>

                                 <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($eleve['parent_telephone'])): ?>
                                        <span class="text-sm text-gray-900">
                                            <?= e($eleve['parent_telephone']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400 ">Non renseigné</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if (!empty($eleve['parent_adresse'])): ?>
                                        <span class="text-sm text-gray-900"><?= e($eleve['parent_adresse']) ?></span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400 ">Non renseignée</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $statut = $eleve['statut'] ?? 'actif';
                                    $statutLabels = [
                                        'actif' => ['Actif', 'bg-green-100 text-green-800', 'fa-check-circle'],
                                        'nouveau' => ['Nouveau', 'bg-blue-100 text-blue-800', 'fa-plus-circle'],
                                        'suspendu' => ['Suspendu', 'bg-red-100 text-red-800', 'fa-ban'],
                                        'parti' => ['Parti', 'bg-gray-100 text-gray-800', 'fa-sign-out-alt']
                                    ];
                                    $label = $statutLabels[$statut] ?? ['Inconnu', 'bg-gray-100 text-gray-800', 'fa-question-circle'];
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $label[1] ?>">
                                        <i class="fas <?= $label[2] ?> mr-1"></i><?= $label[0] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('eleves/details/' . $eleve['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                         <a href="<?= url('eleves/edit/' . $eleve['id']) ?>" 
                                            class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition"
                                            title="Modifier">
                                             <i class="fas fa-edit"></i>
                                         </a>
                                         <a href="<?= url('eleves/parcours/' . $eleve['id']) ?>" 
                                            class="text-purple-600 hover:text-purple-900 p-2 hover:bg-purple-50 rounded transition"
                                            title="Parcours Scolaire">
                                             <i class="fas fa-history"></i>
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
    const tableBody = document.getElementById('elevesTableBody');
    const noResultsRow = document.querySelector('#elevesTableBody tr:first-child');
    
    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('tr.eleve-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                const isVisible = searchData.includes(searchTerm);
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });
            
            // Afficher/masquer le message "Aucun élève trouvé"
            if (noResultsRow && noResultsRow.querySelector('td[colspan]')) {
                noResultsRow.style.display = visibleCount === 0 && searchTerm !== '' ? '' : 'none';
            }
        });
    }
});
</script>

