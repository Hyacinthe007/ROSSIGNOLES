<div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                <i class="fas fa-users text-blue-600 mr-2"></i>
                Liste du Personnel
            </h1>
            <p class="text-gray-600 text-sm mt-1">Enseignants et Personnel Administratif</p>
        </div>

        <div class="flex gap-2">
            <?php if (hasPermission('personnel_new.create')): ?>
            <a href="<?= url('personnel/nouveau') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition shadow-md">
                <i class="fas fa-plus-circle"></i> Nouveau Personnel
            </a>
            <?php endif; ?>
            <a href="<?= url('liste-personnel/export-excel') ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="<?= url('liste-personnel/export-pdf') ?>" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="relative">
             <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
             <input type="text"
                    id="searchPersonnel"
                    placeholder="Rechercher par nom, prÃ©nom, matricule..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
             <div id="searchResults" class="hidden absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto"></div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg run-flow">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-900 font-bold">
                        <th class="p-4">Matricule</th>
                        <th class="p-4">Nom & PrÃ©nom</th>
                        <th class="p-4">Sexe</th>
                        <th class="p-4">Fonction</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700" id="personnelTableBody">
                    <?php if (empty($list)): ?>
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">Aucun personnel trouvÃ©</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($list as $p): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-medium text-gray-900"><?= htmlspecialchars($p['matricule']) ?></td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <!-- Photo -->
                                    <?php if (!empty($p['photo'])): ?>
                                        <img src="<?= public_url($p['photo']) ?>"
                                             alt="Photo"
                                             class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                    <?php endif; ?>
                                    <!-- Nom et PrÃ©nom cÃ´te Ã  cÃ´te -->
                                    <div class="text-sm">
                                        <span class="font-semibold text-gray-900"><?= htmlspecialchars($p['nom']) ?></span>
                                        <span class="text-gray-900"><?= htmlspecialchars($p['prenom']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4"><?= htmlspecialchars($p['sexe']) ?></td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?= $p['fonction'] == 'Enseignant' ? 'bg-indigo-100 text-indigo-700' : 'bg-teal-100 text-teal-700' ?>">
                                    <?= htmlspecialchars($p['fonction']) ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <!-- TÃ©lÃ©phone au-dessus (sans icÃ´ne), Email en-dessous -->
                                <div class="text-gray-900"><?= htmlspecialchars($p['telephone']) ?></div>
                                <?php if($p['email']): ?>
                                <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($p['email']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <?php if ($p['statut'] === 'Actif'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"><?= ucfirst($p['statut']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="<?= url($p['type'] . '/details/' . $p['id']) ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Voir dÃ©tails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= url($p['type'] . '/edit/' . $p['id']) ?>" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Modifier">
                                        <i class="fas fa-edit"></i>
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
    const searchInput = document.getElementById('searchPersonnel');
    const tableBody = document.getElementById('personnelTableBody');
    const rows = tableBody.getElementsByTagName('tr');

    searchInput.addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();

        // Filtrage simple cÃ´tÃ© client pour fluiditÃ©
        Array.from(rows).forEach(row => {
            const text = row.innerText.toLowerCase();
            if (text.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Autocomplete AJAX simulÃ© (bonus pour l'UX si on veut cliquer sur un rÃ©sultat)
        // Mais le filtrage direct du tableau est souvent plus efficace pour une liste admin
    });
});
</script>
