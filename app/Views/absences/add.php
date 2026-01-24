<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-user-times text-red-600 mr-2"></i>
            Ajouter une absence
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Enregistrez une nouvelle absence</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('absences/add') ?>" class="space-y-6">
            <?= csrf_field() ?>
            <!-- Recherche élève avec autocomplete -->
            <div>
                <label for="eleve_search" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-gray-500"></i>Rechercher un élève *
                </label>
                <div class="relative">
                    <input type="text" 
                           id="eleve_search" 
                           autocomplete="off"
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Tapez le matricule, nom ou prénom de l'élève...">
                    <i class="fas fa-user-graduate absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="hidden" id="eleve_id" name="eleve_id" required>
                    
                    <!-- Liste des résultats autocomplete -->
                    <div id="eleve_results" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        <!-- Les résultats seront injectés ici par JavaScript -->
                    </div>
                </div>
                <div id="eleve_selected" class="mt-2 hidden">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                            <div>
                                <p class="font-semibold text-gray-800" id="eleve_selected_name"></p>
                                <p class="text-sm text-gray-600" id="eleve_selected_matricule"></p>
                            </div>
                        </div>
                        <button type="button" id="eleve_clear" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date début -->
                <div>
                    <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Date début *
                    </label>
                    <input type="date" 
                           id="date_debut" 
                           name="date_debut" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <!-- Date fin -->
                <div>
                    <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-2 text-gray-500"></i>Date fin *
                    </label>
                    <input type="date" 
                           id="date_fin" 
                           name="date_fin" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <!-- Justifiée -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-check-circle mr-2 text-gray-500"></i>Statut
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" 
                               name="justifiee" 
                               value="1"
                               class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="text-sm text-gray-700">Absence justifiée</span>
                    </label>
                </div>
            </div>

            <!-- Motif -->
            <div>
                <label for="motif" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-comment-alt mr-2 text-gray-500"></i>Motif
                </label>
                <textarea id="motif" 
                          name="motif"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                          placeholder="Raison de l'absence..."></textarea>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <button type="submit" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer l'absence</span>
                </button>
                <a href="<?= url('absences/list') ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const eleveSearch = document.getElementById('eleve_search');
    const eleveId = document.getElementById('eleve_id');
    const eleveResults = document.getElementById('eleve_results');
    const eleveSelected = document.getElementById('eleve_selected');
    const eleveSelectedName = document.getElementById('eleve_selected_name');
    const eleveSelectedMatricule = document.getElementById('eleve_selected_matricule');
    const eleveClear = document.getElementById('eleve_clear');
    let searchTimeout = null;
    
    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!eleveId.value) {
            e.preventDefault();
            alert('Veuillez sélectionner un élève');
            eleveSearch.focus();
            return false;
        }
    });
    
    // Recherche autocomplete
    eleveSearch.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            eleveResults.classList.add('hidden');
            return;
        }
        
        // Debounce
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            fetch('<?= url('absences/search-eleves') ?>?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        eleveResults.innerHTML = '<div class="p-4 text-center text-gray-500">Aucun élève trouvé</div>';
                        eleveResults.classList.remove('hidden');
                        return;
                    }
                    
                    let html = '';
                    data.forEach(function(eleve) {
                        html += `
                            <div class="eleve-result-item p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                 data-id="${eleve.id}" 
                                 data-matricule="${eleve.matricule}"
                                 data-nom="${eleve.nom}"
                                 data-prenom="${eleve.prenom}">
                                <p class="font-semibold text-gray-800">${eleve.display}</p>
                            </div>
                        `;
                    });
                    
                    eleveResults.innerHTML = html;
                    eleveResults.classList.remove('hidden');
                    
                    // Ajouter les événements de clic
                    document.querySelectorAll('.eleve-result-item').forEach(function(item) {
                        item.addEventListener('click', function() {
                            selectEleve(
                                this.dataset.id,
                                this.dataset.matricule,
                                this.dataset.nom,
                                this.dataset.prenom
                            );
                        });
                    });
                })
                .catch(error => {
                    console.error('Erreur recherche:', error);
                    eleveResults.classList.add('hidden');
                });
        }, 300);
    });
    
    // Sélectionner un élève
    function selectEleve(id, matricule, nom, prenom) {
        eleveId.value = id;
        eleveSearch.value = '';
        eleveResults.classList.add('hidden');
        eleveSelectedName.textContent = nom + ' ' + prenom;
        eleveSelectedMatricule.textContent = 'Matricule: ' + matricule;
        eleveSelected.classList.remove('hidden');
    }
    
    // Effacer la sélection
    eleveClear.addEventListener('click', function() {
        eleveId.value = '';
        eleveSearch.value = '';
        eleveSelected.classList.add('hidden');
        eleveSearch.focus();
    });
    
    // Fermer les résultats si on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!eleveSearch.contains(e.target) && !eleveResults.contains(e.target)) {
            eleveResults.classList.add('hidden');
        }
    });
    
    // Validation des dates
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    
    if (dateDebut && dateFin) {
        dateDebut.addEventListener('change', function() {
            if (dateFin.value && dateFin.value < dateDebut.value) {
                dateFin.value = dateDebut.value;
            }
            dateFin.min = dateDebut.value;
        });
    }
});
</script>
