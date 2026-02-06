<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-times text-red-600 mr-2"></i>
                Enregistrer les absences
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Sélectionnez une classe et marquez les élèves absents</p>
        </div>
        <div>
            <a href="<?= url('absences/list') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-arrow-left"></i>
                <span class="text-sm font-medium">Retour à la liste</span>
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('absences/add') ?>" id="absenceForm">
            <?= csrf_field() ?>
            
            <!-- Sélection de la classe et date -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-door-open mr-2 text-purple-500"></i>Classe *
                    </label>
                    <select id="classe_id" 
                            name="classe_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Sélectionner une classe</option>
                        <?php if (isset($classes)): 
                            $currentCycle = '';
                            foreach ($classes as $classe): 
                                if ($currentCycle !== $classe['cycle_nom']): 
                                    if ($currentCycle !== '') echo '</optgroup>';
                                    $currentCycle = $classe['cycle_nom'];
                                    echo '<optgroup label="' . e($currentCycle) . '">';
                                endif;
                        ?>
                                <option value="<?= $classe['id'] ?>" <?= (isset($_GET['classe_id']) && $_GET['classe_id'] == $classe['id']) ? 'selected' : '' ?>>
                                    <?= e($classe['libelle']) ?>
                                </option>
                            <?php endforeach; 
                            if ($currentCycle !== '') echo '</optgroup>';
                        endif; ?>
                    </select>
                </div>

                <div>
                    <label for="date_absence" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Date *
                    </label>
                    <input type="date" 
                           id="date_absence" 
                           name="date_absence" 
                           value="<?= date('Y-m-d') ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <div>
                    <label for="emploi_temps_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2 text-blue-500"></i>Cours (optionnel)
                    </label>
                    <select id="emploi_temps_id" 
                            name="emploi_temps_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionner un cours</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Heure, matière et prof seront récupérés</p>
                </div>
            </div>

            <!-- Statut par défaut (déplacé en haut) -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" 
                           name="justifiee_default" 
                           id="justifiee_default"
                           value="1"
                           class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <span class="text-sm font-medium text-gray-700">
                        <i class="fas fa-check-circle mr-2 text-gray-500"></i>
                        Marquer comme justifiée par défaut
                    </span>
                </label>
            </div>

            <!-- Zone de chargement -->
            <div id="loading" class="hidden text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-purple-600 mb-2"></i>
                <p class="text-gray-600">Chargement des élèves...</p>
            </div>

            <!-- Tableau des élèves -->
            <div id="eleves_container" class="hidden">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-users mr-2 text-purple-600"></i>
                        Liste des élèves (<span id="total_eleves">0</span>)
                    </h3>
                </div>

                <!-- Tableau -->
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gradient-to-r from-purple-600 to-blue-600 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">
                                    N°
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">
                                    Nom - Prénom
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">
                                    Présence
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">
                                    Motif général (optionnel)
                                </th>
                            </tr>
                        </thead>
                        <tbody id="eleves_tbody" class="divide-y divide-gray-200">
                            <!-- Les lignes seront injectées ici -->
                        </tbody>
                    </table>
                </div>

                <!-- Résumé -->
                <div id="summary" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 hidden">
                    <h4 class="font-bold text-blue-900 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Résumé
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700">Présents:</span>
                            <span class="font-bold text-green-600 ml-1" id="count_present">0</span>
                        </div>
                        <div>
                            <span class="text-blue-700">Absents:</span>
                            <span class="font-bold text-red-600 ml-1" id="count_absent">0</span>
                        </div>
                        <div id="cours_info" class="col-span-2 text-blue-700">
                            <!-- Info cours si sélectionné -->
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                    <button type="submit" 
                            id="submit_btn"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-save"></i>
                        <span>Enregistrer les absences (<span id="count_to_save">0</span>)</span>
                    </button>
                    <a href="<?= url('absences/list') ?>" 
                       class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Annuler</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.radio-present:checked + label {
    background-color: #10b981;
    color: white;
    border-color: #10b981;
}

.radio-absent:checked + label {
    background-color: #ef4444;
    color: white;
    border-color: #ef4444;
}

.radio-label {
    transition: all 0.2s;
    cursor: pointer;
    padding: 8px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.875rem;
}

.radio-label:hover {
    border-color: #9ca3af;
}

.motif-input:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
    opacity: 0.5;
}

.motif-input:not(:disabled) {
    background-color: #fef3c7;
    border-color: #f59e0b;
}
</style>

<script>
let elevesData = [];
let emploisTempsData = [];
let absencesRecentes = {};

document.addEventListener('DOMContentLoaded', function() {
    const classeSelect = document.getElementById('classe_id');
    const dateInput = document.getElementById('date_absence');
    const emploiTempsSelect = document.getElementById('emploi_temps_id');
    const loading = document.getElementById('loading');
    const elevesContainer = document.getElementById('eleves_container');
    const elevesTbody = document.getElementById('eleves_tbody');
    const form = document.getElementById('absenceForm');
    const summary = document.getElementById('summary');

    // Charger les élèves quand une classe est sélectionnée
    classeSelect.addEventListener('change', loadEleves);
    dateInput.addEventListener('change', function() {
        loadEmploisTemps();
        loadAbsencesRecentes();
    });

    function loadEleves() {
        const classeId = classeSelect.value;
        if (!classeId) {
            elevesContainer.classList.add('hidden');
            return;
        }

        loading.classList.remove('hidden');
        elevesContainer.classList.add('hidden');

        fetch('<?= url('absences/get-eleves-classe') ?>?classe_id=' + classeId)
            .then(response => response.json())
            .then(data => {
                console.log('Élèves reçus:', data);
                elevesData = data;
                loadAbsencesRecentes();
                loadEmploisTemps();
            })
            .catch(error => {
                console.error('Erreur:', error);
                loading.classList.add('hidden');
                alert('Erreur lors du chargement des élèves');
            });
    }

    function loadAbsencesRecentes() {
        const classeId = classeSelect.value;
        const date = dateInput.value;
        
        if (!classeId || !date) return;

        // Récupérer les absences des 7 derniers jours
        fetch('<?= url('absences/get-absences-recentes') ?>?classe_id=' + classeId + '&date=' + date)
            .then(response => response.json())
            .then(data => {
                absencesRecentes = {};
                data.forEach(absence => {
                    absencesRecentes[absence.eleve_id] = absence.motif || '';
                });
                displayEleves();
                loading.classList.add('hidden');
                elevesContainer.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Erreur:', error);
                displayEleves();
                loading.classList.add('hidden');
                elevesContainer.classList.remove('hidden');
            });
    }

    function loadEmploisTemps() {
        const classeId = classeSelect.value;
        const date = dateInput.value;
        
        if (!classeId || !date) return;

        fetch('<?= url('absences/get-emplois-temps') ?>?classe_id=' + classeId + '&date=' + date)
            .then(response => response.json())
            .then(data => {
                emploisTempsData = data;
                displayEmploisTemps();
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
    }

    function displayEmploisTemps() {
        emploiTempsSelect.innerHTML = '<option value="">Sélectionner un cours</option>';
        
        emploisTempsData.forEach(et => {
            const option = document.createElement('option');
            option.value = et.id;
            option.textContent = `${et.heure_debut} - ${et.heure_fin} | ${et.matiere_nom} | ${et.enseignant_nom}`;
            option.dataset.heureDebut = et.heure_debut;
            option.dataset.heureFin = et.heure_fin;
            option.dataset.matiere = et.matiere_nom;
            option.dataset.enseignant = et.enseignant_nom;
            emploiTempsSelect.appendChild(option);
        });

        emploiTempsSelect.addEventListener('change', updateCoursInfo);
    }

    function updateCoursInfo() {
        const selected = emploiTempsSelect.selectedOptions[0];
        const coursInfo = document.getElementById('cours_info');
        
        if (selected && selected.value) {
            coursInfo.innerHTML = `<i class="fas fa-book mr-1"></i>${selected.dataset.matiere} | <i class="fas fa-user mr-1"></i>${selected.dataset.enseignant} | <i class="fas fa-clock mr-1"></i>${selected.dataset.heureDebut} - ${selected.dataset.heureFin}`;
        } else {
            coursInfo.innerHTML = '';
        }
    }

    function displayEleves() {
        document.getElementById('total_eleves').textContent = elevesData.length;
        elevesTbody.innerHTML = '';

        if (elevesData.length === 0) {
            elevesTbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-2"></i>
                        <p>Aucun élève trouvé dans cette classe</p>
                    </td>
                </tr>
            `;
            return;
        }

        elevesData.forEach((eleve, index) => {
            const avaitAbsence = absencesRecentes.hasOwnProperty(eleve.id);
            const motifPrecedent = absencesRecentes[eleve.id] || '';
            
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${index + 1}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div>
                            <div class="text-sm font-bold text-gray-900">${eleve.nom} ${eleve.prenom}</div>
                            <div class="text-xs text-gray-500">${eleve.matricule}</div>
                            ${avaitAbsence ? '<div class="text-xs text-orange-600 mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>Absent récemment</div>' : ''}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center justify-center gap-2">
                        <input type="radio" 
                               name="presence_${eleve.id}" 
                               id="present_${eleve.id}" 
                               value="present"
                               class="radio-present hidden"
                               checked
                               data-eleve-id="${eleve.id}">
                        <label for="present_${eleve.id}" class="radio-label bg-green-50 text-green-700 border-green-200">
                            <i class="fas fa-check mr-1"></i>Présent
                        </label>
                        
                        <input type="radio" 
                               name="presence_${eleve.id}" 
                               id="absent_${eleve.id}" 
                               value="absent"
                               class="radio-absent hidden"
                               data-eleve-id="${eleve.id}">
                        <label for="absent_${eleve.id}" class="radio-label bg-red-50 text-red-700 border-red-200">
                            <i class="fas fa-times mr-1"></i>Absent
                        </label>
                        
                        <input type="checkbox" name="absents[]" value="${eleve.id}" id="checkbox_${eleve.id}" class="hidden">
                    </div>
                </td>
                <td class="px-6 py-4">
                    <input type="text" 
                           name="motif_${eleve.id}" 
                           id="motif_${eleve.id}"
                           placeholder="${avaitAbsence ? 'Motif de retour...' : 'Ex: Maladie, Grève...'}"
                           value="${avaitAbsence ? motifPrecedent : ''}"
                           ${!avaitAbsence ? 'disabled' : ''}
                           class="motif-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </td>
            `;
            elevesTbody.appendChild(tr);
        });

        // Ajouter les événements après avoir créé tous les éléments
        document.querySelectorAll('.radio-present, .radio-absent').forEach(radio => {
            radio.addEventListener('change', function() {
                const eleveId = this.dataset.eleveId;
                updatePresenceStatus(eleveId);
            });
        });

        updateCounts();
    }

    function updateCounts() {
        const total = elevesData.length;
        const absents = document.querySelectorAll('input[name^="absents"]:checked').length;
        const presents = total - absents;

        document.getElementById('count_present').textContent = presents;
        document.getElementById('count_absent').textContent = absents;
        document.getElementById('count_to_save').textContent = absents;

        if (total > 0) {
            summary.classList.remove('hidden');
        }

        // Activer/désactiver le bouton submit
        const submitBtn = document.getElementById('submit_btn');
        submitBtn.disabled = absents === 0;
    }

    form.addEventListener('submit', function(e) {
        const absents = document.querySelectorAll('input[name^="absents"]:checked').length;
        if (absents === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un élève absent');
            return false;
        }

        if (!confirm(`Confirmer l'enregistrement de ${absents} absence(s) ?`)) {
            e.preventDefault();
            return false;
        }
    });

    // Charger automatiquement si classe pré-sélectionnée
    if (classeSelect.value) {
        loadEleves();
    }
});

// Fonction globale pour mettre à jour le statut de présence
function updatePresenceStatus(eleveId) {
    const presentRadio = document.getElementById(`present_${eleveId}`);
    const absentRadio = document.getElementById(`absent_${eleveId}`);
    const motifInput = document.getElementById(`motif_${eleveId}`);
    const checkbox = document.getElementById(`checkbox_${eleveId}`);
    
    const avaitAbsence = absencesRecentes.hasOwnProperty(eleveId);
    
    if (absentRadio && absentRadio.checked) {
        // Élève marqué absent
        console.log('Marqué absent:', eleveId);
        if (checkbox) {
            checkbox.checked = true;
            console.log('Checkbox cochée pour:', eleveId);
        }
        if (motifInput) {
            motifInput.disabled = false;
            motifInput.placeholder = "Ex: Maladie, Grève...";
        }
    } else {
        // Élève marqué présent
        console.log('Marqué présent:', eleveId);
        if (checkbox) {
            checkbox.checked = false;
            console.log('Checkbox décochée pour:', eleveId);
        }
        
        // Activer le motif seulement si l'élève avait une absence récente
        if (motifInput) {
            if (avaitAbsence) {
                motifInput.disabled = false;
                motifInput.placeholder = "Motif de retour...";
            } else {
                motifInput.disabled = true;
                motifInput.value = '';
            }
        }
    }
    
    // Mettre à jour les compteurs
    updateCountsGlobal();
}

// Fonction globale pour mettre à jour les compteurs
function updateCountsGlobal() {
    const total = elevesData.length;
    const absents = document.querySelectorAll('input[name^="absents"]:checked').length;
    const presents = total - absents;

    const countPresent = document.getElementById('count_present');
    const countAbsent = document.getElementById('count_absent');
    const countToSave = document.getElementById('count_to_save');
    const summary = document.getElementById('summary');
    const submitBtn = document.getElementById('submit_btn');

    if (countPresent) countPresent.textContent = presents;
    if (countAbsent) countAbsent.textContent = absents;
    if (countToSave) countToSave.textContent = absents;

    if (total > 0 && summary) {
        summary.classList.remove('hidden');
    }

    // Activer/désactiver le bouton submit
    if (submitBtn) {
        submitBtn.disabled = absents === 0;
    }
    
    console.log('Compteurs mis à jour - Présents:', presents, 'Absents:', absents);
}
</script>

