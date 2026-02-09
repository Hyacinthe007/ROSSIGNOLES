<?php
$title = "Absences du Personnel";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '../../dashboard'],
    ['label' => 'Personnel', 'url' => '../../personnel/list'],
    ['label' => 'Absences', 'url' => 'list'],
    ['label' => 'Liste']
];
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-calendar-times text-blue-600 mr-2"></i>
                Absences du Personnel
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des congés et absences du personnel</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="add" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Nouvelle Absence</span>
            </a>
        </div>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personnel</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10"></i>
                        <input 
                            type="text" 
                            id="personnel_search" 
                            placeholder="Rechercher un personnel..." 
                            autocomplete="off"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="<?php 
                                if (isset($filters['personnel_id']) && !empty($filters['personnel_id'])) {
                                    foreach ($personnels as $p) {
                                        if ($p['id'] == $filters['personnel_id']) {
                                            echo htmlspecialchars($p['matricule'] . ' - ' . $p['nom'] . ' ' . $p['prenom']);
                                            break;
                                        }
                                    }
                                }
                            ?>"
                        >
                        <input type="hidden" name="personnel_id" id="personnel_id" value="<?= isset($filters['personnel_id']) ? htmlspecialchars($filters['personnel_id']) : '' ?>">
                        
                        <!-- Liste déroulante des résultats -->
                        <div id="personnel_dropdown" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                            <div class="py-1">
                                <div class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm text-gray-700" data-id="" data-text="Tous">
                                    <span class="font-medium">Tous les personnels</span>
                                </div>
                                <?php foreach ($personnels as $p): ?>
                                    <div class="personnel-option px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm text-gray-700" 
                                         data-id="<?= $p['id'] ?>" 
                                         data-text="<?= htmlspecialchars($p['matricule'] . ' - ' . $p['nom'] . ' ' . $p['prenom']) ?>"
                                         data-search="<?= strtolower(htmlspecialchars($p['matricule'] . ' ' . $p['nom'] . ' ' . $p['prenom'])) ?>">
                                        <span class="font-medium"><?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?></span>
                                        <span class="text-gray-500 text-xs ml-2"><?= htmlspecialchars($p['matricule']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type_absence" id="type_absence" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="conge_annuel" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'conge_annuel' ? 'selected' : '' ?>>Congé annuel</option>
                        <option value="conge_maladie" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'conge_maladie' ? 'selected' : '' ?>>Congé maladie</option>
                        <option value="conge_maternite" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'conge_maternite' ? 'selected' : '' ?>>Congé maternité</option>
                        <option value="conge_paternite" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'conge_paternite' ? 'selected' : '' ?>>Congé paternité</option>
                        <option value="conge_sans_solde" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'conge_sans_solde' ? 'selected' : '' ?>>Congé sans solde</option>
                        <option value="absence_autorisee" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'absence_autorisee' ? 'selected' : '' ?>>Absence autorisée</option>
                        <option value="absence_non_justifiee" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'absence_non_justifiee' ? 'selected' : '' ?>>Absence non justifiée</option>
                        <option value="formation" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'formation' ? 'selected' : '' ?>>Formation</option>
                        <option value="mission" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'mission' ? 'selected' : '' ?>>Mission</option>
                        <option value="autre" <?= isset($filters['type_absence']) && $filters['type_absence'] == 'autre' ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="demande" <?= isset($filters['statut']) && $filters['statut'] == 'demande' ? 'selected' : '' ?>>Demande</option>
                        <option value="validee" <?= isset($filters['statut']) && $filters['statut'] == 'validee' ? 'selected' : '' ?>>Validée</option>
                        <option value="refusee" <?= isset($filters['statut']) && $filters['statut'] == 'refusee' ? 'selected' : '' ?>>Refusée</option>
                        <option value="annulee" <?= isset($filters['statut']) && $filters['statut'] == 'annulee' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?= isset($filters['date_debut']) ? htmlspecialchars($filters['date_debut']) : '' ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?= isset($filters['date_fin']) ? htmlspecialchars($filters['date_fin']) : '' ?>">
                </div>
            </div>
        </form>
    </div>                

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Personnel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-calendar-alt mr-2"></i>Dates
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-clock mr-2"></i>Jours
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-paper-plane mr-2"></i>Demandé le
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i class="fas fa-tools mr-2"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($absences)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucune absence trouvée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($absences as $absence): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-semibold text-gray-900"><?= htmlspecialchars($absence['personnel_nom'] . ' ' . $absence['personnel_prenom']) ?></div>
                                        <div class="text-gray-500 text-xs"><?= htmlspecialchars($absence['matricule']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $types = [
                                        'conge_annuel' => 'Congé annuel',
                                        'conge_maladie' => 'Congé maladie',
                                        'conge_maternite' => 'Congé maternité',
                                        'conge_paternite' => 'Congé paternité',
                                        'conge_sans_solde' => 'Congé sans solde',
                                        'absence_autorisee' => 'Absence autorisée',
                                        'absence_non_justifiee' => 'Absence non justifiée',
                                        'formation' => 'Formation',
                                        'mission' => 'Mission',
                                        'autre' => 'Autre'
                                    ];
                                    ?>
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($types[$absence['type_absence']] ?? $absence['type_absence']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        Du <span class="font-medium"><?= date('d/m/Y', strtotime($absence['date_debut'])) ?></span><br>
                                        Au <span class="font-medium"><?= date('d/m/Y', strtotime($absence['date_fin'])) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= htmlspecialchars($absence['nb_jours']) ?> j
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statuts = [
                                        'demande' => ['Demande', 'bg-yellow-100 text-yellow-800'],
                                        'validee' => ['Validée', 'bg-green-100 text-green-800'],
                                        'refusee' => ['Refusée', 'bg-red-100 text-red-800'],
                                        'annulee' => ['Annulée', 'bg-gray-100 text-gray-800']
                                    ];
                                    $statutInfo = $statuts[$absence['statut']] ?? [$absence['statut'], 'bg-gray-100 text-gray-800'];
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statutInfo[1] ?>">
                                        <?= $statutInfo[0] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($absence['date_demande']): ?>
                                        <?= date('d/m/Y H:i', strtotime($absence['date_demande'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="details/<?= $absence['id'] ?>" class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit/<?= $absence['id'] ?>" class="text-yellow-600 hover:text-yellow-900 p-2 hover:bg-yellow-50 rounded transition" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete/<?= $absence['id'] ?>" class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded transition" title="Supprimer">
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('personnel_search');
    const personnelIdInput = document.getElementById('personnel_id');
    const dropdown = document.getElementById('personnel_dropdown');
    const form = searchInput.closest('form');
    
    // Récupérer les autres champs de filtrage
    const typeAbsenceSelect = document.getElementById('type_absence');
    const statutSelect = document.getElementById('statut');
    const dateDebutInput = document.getElementById('date_debut');
    const dateFinInput = document.getElementById('date_fin');
    
    // Soumission automatique pour les champs select (Type et Statut)
    if (typeAbsenceSelect) {
        typeAbsenceSelect.addEventListener('change', function() {
            form.submit();
        });
    }
    
    if (statutSelect) {
        statutSelect.addEventListener('change', function() {
            form.submit();
        });
    }
    
    // Soumission automatique pour les champs date
    if (dateDebutInput) {
        dateDebutInput.addEventListener('change', function() {
            form.submit();
        });
    }
    
    if (dateFinInput) {
        dateFinInput.addEventListener('change', function() {
            form.submit();
        });
    }
    
    // Afficher le dropdown au focus
    searchInput.addEventListener('focus', function() {
        dropdown.classList.remove('hidden');
        filterOptions(searchInput.value);
    });
    
    // Filtrer les options pendant la saisie
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterOptions(searchTerm);
        dropdown.classList.remove('hidden');
        
        // Si le champ est vide, réinitialiser la sélection
        if (searchTerm === '') {
            personnelIdInput.value = '';
        }
    });
    
    // Fonction de filtrage
    function filterOptions(searchTerm) {
        const options = dropdown.querySelectorAll('[data-search], [data-text="Tous"]');
        let visibleCount = 0;
        
        options.forEach(option => {
            const searchText = option.getAttribute('data-search') || '';
            const isAllOption = option.getAttribute('data-text') === 'Tous';
            
            if (searchTerm === '' || isAllOption || searchText.includes(searchTerm)) {
                option.style.display = 'block';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });
        
        // Cacher le dropdown si aucun résultat
        if (visibleCount === 0) {
            dropdown.classList.add('hidden');
        }
    }
    
    // Gérer la sélection d'une option
    dropdown.addEventListener('click', function(e) {
        const option = e.target.closest('[data-id]');
        if (option) {
            const id = option.getAttribute('data-id');
            const text = option.getAttribute('data-text');
            
            searchInput.value = text;
            personnelIdInput.value = id;
            dropdown.classList.add('hidden');
            
            // Soumettre automatiquement le formulaire
            form.submit();
        }
    });
    
    // Fermer le dropdown en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Navigation au clavier
    searchInput.addEventListener('keydown', function(e) {
        const visibleOptions = Array.from(dropdown.querySelectorAll('[data-id]')).filter(opt => opt.style.display !== 'none');
        const currentFocus = dropdown.querySelector('.bg-blue-100');
        let currentIndex = currentFocus ? visibleOptions.indexOf(currentFocus) : -1;
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (currentIndex < visibleOptions.length - 1) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateFocus(visibleOptions, currentIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (currentIndex > 0) {
                currentIndex--;
            } else {
                currentIndex = visibleOptions.length - 1;
            }
            updateFocus(visibleOptions, currentIndex);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (currentFocus) {
                currentFocus.click();
            }
        } else if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
        }
    });
    
    function updateFocus(options, index) {
        options.forEach(opt => opt.classList.remove('bg-blue-100'));
        if (options[index]) {
            options[index].classList.add('bg-blue-100');
            options[index].scrollIntoView({ block: 'nearest' });
        }
    }
});
</script>