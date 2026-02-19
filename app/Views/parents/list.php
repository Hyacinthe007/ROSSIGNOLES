<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    Liste des parents
                </h1>
                <p class="text-gray-600 text-sm md:text-base">Consultation et recherche des parents et tuteurs</p>
            </div>
        </div>
        
        <!-- Note informative -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded-r">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="text-blue-800 font-medium mb-1">Information importante</p>
                    <p class="text-blue-700 text-sm">
                        Les parents sont automatiquement créés lors de l'inscription des élèves. 
                        Cette page permet uniquement de <strong>consulter, rechercher et modifier</strong> les informations des parents existants.
                    </p>
                    <p class="text-blue-600 text-xs mt-2">
                        <i class="fas fa-lightbulb mr-1"></i>
                        <strong>Astuce :</strong> Vous pouvez rechercher un parent en tapant le nom de son enfant !
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Barre de recherche -->
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput"
                               value="<?= e($_GET['search'] ?? '') ?>"
                               placeholder="Rechercher un parent ou un élève (nom, prénom, téléphone, email)..." 
                               class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               autocomplete="off">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400" id="searchIcon"></i>
                        <div id="searchSpinner" class="absolute right-3 top-2.5 hidden">
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <button type="button" 
                        id="btnReset"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition flex items-center justify-center gap-2 hidden">
                    <i class="fas fa-times"></i>
                    <span>Réinitialiser</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-user mr-2"></i>Nom - prénom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-phone mr-2"></i>Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-envelope mr-2"></i>Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-link mr-2"></i>Lien</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-child mr-2"></i>Enfants</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-tools mr-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody id="parentsTableBody" class="bg-white divide-y divide-gray-200">
                    <?php if (empty($parents)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                <?php if (!empty($search)): ?>
                                    <p class="font-medium">Aucun parent trouvé pour "<?= e($search) ?>"</p>
                                    <p class="text-sm mt-2">Essayez avec d'autres termes de recherche</p>
                                <?php else: ?>
                                    <p>Aucun parent enregistré</p>
                                    <p class="text-sm mt-2">Les parents seront créés automatiquement lors de l'inscription des élèves</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($parents as $parent): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="font-medium"><?= e(($parent['nom'] ?? '') . ' ' . ($parent['prenom'] ?? '')) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e(formatTelephone($parent['telephone']) ?: 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e($parent['email'] ?: 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <?= e($parent['lien_parente'] ?? $parent['type_parent'] ?? 'Parent') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php 
                                    $nbEnfants = $parent['nb_enfants'] ?? 0;
                                    $badgeColor = $nbEnfants > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600';
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $badgeColor ?>">
                                        <i class="fas fa-child mr-1"></i>
                                        <?= $nbEnfants ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('parents/details/' . $parent['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition"
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('parents/edit/' . $parent['id']) ?>" 
                                           class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                         </a>
                                         <a href="<?= url('inscriptions/inscrire-enfant/' . $parent['id']) ?>" 
                                            class="text-purple-600 hover:text-purple-900 p-2 hover:bg-purple-50 rounded transition"
                                            title="Inscrire un autre enfant">
                                             <i class="fas fa-child"></i>
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
    const searchSpinner = document.getElementById('searchSpinner');
    const btnReset = document.getElementById('btnReset');
    const tableBody = document.getElementById('parentsTableBody');
    const baseUrl = '<?= url('') ?>';
    
    let debounceTimer = null;
    let currentXHR = null;
    
    // Afficher le bouton réinitialiser si le champ a déjà une valeur
    if (searchInput.value.trim() !== '') {
        btnReset.classList.remove('hidden');
    }
    
    // Formater le numéro de téléphone malgache (03X XX XXX XX)
    function formatTelephone(tel) {
        if (!tel) return 'N/A';
        const clean = tel.replace(/[^0-9]/g, '');
        if (clean.length === 10 && clean.substring(0, 2) === '03') {
            return clean.substring(0, 3) + ' ' + clean.substring(3, 5) + ' ' + clean.substring(5, 8) + ' ' + clean.substring(8, 10);
        }
        return tel || 'N/A';
    }
    
    // Échapper les caractères HTML
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
    
    // Construire le HTML d'une ligne de parent
    function buildRow(parent) {
        const nom = escapeHtml((parent.nom || '') + ' ' + (parent.prenom || ''));
        const tel = escapeHtml(formatTelephone(parent.telephone));
        const email = escapeHtml(parent.email || '') || 'N/A';
        const lien = escapeHtml(parent.lien_parente || parent.type_parent || 'Parent');
        const nbEnfants = parseInt(parent.nb_enfants) || 0;
        const badgeColor = nbEnfants > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600';
        
        return `<tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                <div class="font-medium">${nom}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${tel}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${email}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">${lien}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${badgeColor}">
                    <i class="fas fa-child mr-1"></i>${nbEnfants}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-2">
                    <a href="${baseUrl}parents/details/${parent.id}" class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition" title="Voir les détails">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="${baseUrl}parents/edit/${parent.id}" class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="${baseUrl}inscriptions/inscrire-enfant/${parent.id}" class="text-purple-600 hover:text-purple-900 p-2 hover:bg-purple-50 rounded transition" title="Inscrire un autre enfant">
                        <i class="fas fa-child"></i>
                    </a>
                </div>
            </td>
        </tr>`;
    }
    
    // Construire le HTML quand aucun résultat
    function buildEmptyRow(searchTerm) {
        if (searchTerm) {
            return `<tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4 block"></i>
                    <p class="font-medium">Aucun parent trouvé pour "${escapeHtml(searchTerm)}"</p>
                    <p class="text-sm mt-2">Essayez avec d'autres termes de recherche</p>
                </td>
            </tr>`;
        }
        return `<tr>
            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                <p>Aucun parent enregistré</p>
                <p class="text-sm mt-2">Les parents seront créés automatiquement lors de l'inscription des élèves</p>
            </td>
        </tr>`;
    }
    
    // Effectuer la recherche AJAX
    function performSearch(query) {
        // Annuler la requête précédente si en cours
        if (currentXHR) {
            currentXHR.abort();
        }
        
        searchSpinner.classList.remove('hidden');
        
        currentXHR = new XMLHttpRequest();
        currentXHR.open('GET', baseUrl + 'parents/search?q=' + encodeURIComponent(query), true);
        currentXHR.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        currentXHR.onload = function() {
            searchSpinner.classList.add('hidden');
            if (currentXHR.status === 200) {
                try {
                    const response = JSON.parse(currentXHR.responseText);
                    if (response.success && response.data) {
                        if (response.data.length === 0) {
                            tableBody.innerHTML = buildEmptyRow(query);
                        } else {
                            tableBody.innerHTML = response.data.map(buildRow).join('');
                        }
                    }
                } catch (e) {
                    console.error('Erreur de parsing JSON:', e);
                }
            }
            currentXHR = null;
        };
        
        currentXHR.onerror = function() {
            searchSpinner.classList.add('hidden');
            currentXHR = null;
        };
        
        currentXHR.send();
    }
    
    // Événement sur le champ de recherche (debounce 300ms)
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Afficher/masquer le bouton réinitialiser
        if (query !== '') {
            btnReset.classList.remove('hidden');
        } else {
            btnReset.classList.add('hidden');
        }
        
        // Annuler le timer précédent
        clearTimeout(debounceTimer);
        
        // Lancer la recherche après 300ms d'inactivité
        debounceTimer = setTimeout(function() {
            performSearch(query);
        }, 300);
    });
    
    // Bouton réinitialiser
    btnReset.addEventListener('click', function() {
        searchInput.value = '';
        btnReset.classList.add('hidden');
        clearTimeout(debounceTimer);
        performSearch('');
        searchInput.focus();
    });
    
    // Empêcher la soumission du formulaire sur Entrée
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});
</script>
