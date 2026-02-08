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
            <a href="<?= url('absences/list') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2">
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
                           readonly
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <div>
                    <label for="emploi_temps_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2 text-blue-500"></i>Sélectionner un cours
                    </label>
                    <select id="emploi_temps_id" 
                            name="emploi_temps_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Choisir un cours de l'emploi du temps --</option>
                    </select>
                </div>
            </div>

            <!-- Zone de chargement -->
            <div id="loading" class="hidden text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-purple-600 mb-2"></i>
                <p class="text-gray-600">Chargement des élèves...</p>
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
                            </tr>
                        </thead>
                        <tbody id="eleves_tbody" class="divide-y divide-gray-200">
                            <!-- Les lignes seront injectées ici -->
                        </tbody>
                    </table>
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
/* Nouveaux boutons de présence ronds */
.presence-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e5e7eb;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    background-color: white;
    color: #9ca3af;
    position: relative;
}

.presence-circle:hover {
    transform: scale(1.1);
    border-color: #d1d5db;
}

.radio-present:checked + .presence-circle {
    background-color: #10b981;
    color: white;
    border-color: #10b981;
}

.radio-absent:checked + .presence-circle {
    background-color: #ef4444;
    color: white;
    border-color: #ef4444;
    
}

/* Animation douce pour l'icône */
.presence-circle i {
    font-size: 1.1rem;
    transition: transform 0.2s;
}

.radio-present:checked + .presence-circle i,
.radio-absent:checked + .presence-circle i {
    transform: scale(1.1);
}

.presence-container {
    display: flex;
    gap: 1.5rem;
    align-items: center;
    justify-content: center;
}

.presence-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.presence-label-text {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #6b7280;
    letter-spacing: 0.05em;
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
    classeSelect.addEventListener('change', function() {
        loadEleves();
        loadEmploisTemps(); // Charger immédiatement les cours
    });
    
    // Ne pas permettre de changer la date
    dateInput.addEventListener('click', function(e) {
        e.preventDefault();
        return false;
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
        
        loading.classList.remove('hidden');

        fetch('<?= url('absences/get-absences-recentes') ?>?classe_id=' + classeId + '&date=' + date)
            .then(response => response.json())
            .then(data => {
                absencesRecentes = {};
                // Grouper les absences par élève
                data.forEach(absence => {
                    if (!absencesRecentes[absence.eleve_id]) {
                        absencesRecentes[absence.eleve_id] = [];
                    }
                    absencesRecentes[absence.eleve_id].push(absence);
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
        emploiTempsSelect.innerHTML = '<option value="">-- Choisir un cours --</option>';
        
        if (emploisTempsData.length === 0) {
            emploiTempsSelect.innerHTML = '<option value="">Aucun cours prévu aujourd\'hui</option>';
            return;
        }
        
        const now = new Date();
        const currentH = now.getHours().toString().padStart(2, '0');
        const currentM = now.getMinutes().toString().padStart(2, '0');
        const currentTime = `${currentH}:${currentM}`;
        
        let courseToSelect = null;
        let closestCourse = null;
        let minTimeDiff = Infinity;

        emploisTempsData.forEach(et => {
            const option = document.createElement('option');
            option.value = et.id;
            option.textContent = et.matiere_nom; // Afficher uniquement le nom de la matière
            option.dataset.heureDebut = et.heure_debut;
            option.dataset.heureFin = et.heure_fin;
            option.dataset.matiere = et.matiere_nom;
            option.dataset.enseignant = et.enseignant_nom;
            
            const heureDebut = et.heure_debut.substring(0, 5);
            const heureFin = et.heure_fin.substring(0, 5);
            
            // Détection du cours actuel (en cours maintenant)
            if (currentTime >= heureDebut && currentTime <= heureFin) {
                option.selected = true;
                courseToSelect = option;
                option.style.fontWeight = 'bold';
                option.style.backgroundColor = '#dbeafe';
            }
            
            // Trouver le cours le plus proche si aucun cours n'est en cours
            if (!courseToSelect) {
                const timeDiff = Math.abs(
                    (parseInt(heureDebut.split(':')[0]) * 60 + parseInt(heureDebut.split(':')[1])) -
                    (parseInt(currentH) * 60 + parseInt(currentM))
                );
                
                if (timeDiff < minTimeDiff) {
                    minTimeDiff = timeDiff;
                    closestCourse = option;
                }
            }
            
            emploiTempsSelect.appendChild(option);
        });

        // Si aucun cours en cours, sélectionner le cours le plus proche
        if (!courseToSelect && closestCourse) {
            closestCourse.selected = true;
            courseToSelect = closestCourse;
        }

        emploiTempsSelect.addEventListener('change', updateCoursInfo);
        
        // Si un cours a été détecté automatiquement, on met à jour les infos
        if (courseToSelect) {
            updateCoursInfo();
        }
    }

    function updateCoursInfo() {
        const selected = emploiTempsSelect.selectedOptions[0];
        const heureDebutInput = document.getElementById('heure_debut');
        const heureFinInput = document.getElementById('heure_fin');
        const matiereInput = document.getElementById('display_matiere');
        const enseignantInput = document.getElementById('display_enseignant');
        
        if (selected && selected.value) {
            heureDebutInput.value = selected.dataset.heureDebut.substring(0, 5);
            heureFinInput.value = selected.dataset.heureFin.substring(0, 5);
            matiereInput.value = selected.dataset.matiere;
            enseignantInput.value = selected.dataset.enseignant;
            
            // Mettre en évidence les champs auto-remplis
            [heureDebutInput, heureFinInput].forEach(el => {
                el.classList.add('bg-blue-50', 'border-blue-400');
            });
        } else {
            heureDebutInput.value = '';
            heureFinInput.value = '';
            matiereInput.value = '';
            enseignantInput.value = '';
            [heureDebutInput, heureFinInput].forEach(el => {
                el.classList.remove('bg-blue-50', 'border-blue-400');
            });
        }
    }

    function displayEleves() {
        document.getElementById('total_eleves').textContent = elevesData.length;
        elevesTbody.innerHTML = '';

        if (elevesData.length === 0) {
            elevesTbody.innerHTML = `
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-2"></i>
                        <p>Aucun élève trouvé dans cette classe</p>
                    </td>
                </tr>
            `;
            return;
        }

        elevesData.forEach((eleve, index) => {
            const absencesE = absencesRecentes[eleve.id] || [];
            // Filtrer les absences non justifiées
            const nonJustifiees = absencesE.filter(a => !parseInt(a.justifiee));
            
            let messageAbsence = '';
            if (nonJustifiees.length > 0) {
                // Trier par date
                const sorted = nonJustifiees.sort((a, b) => new Date(a.date_absence) - new Date(b.date_absence));
                const first = sorted[0].date_absence;
                const last = sorted[sorted.length - 1].date_absence;
                
                const formatDateFr = (dateStr) => {
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('fr-FR');
                };
                
                if (sorted.length === 1) {
                    messageAbsence = `Absent le ${formatDateFr(first)}`;
                } else {
                    // Vérifier si ce sont des jours consécutifs (approximatif pour l'affichage)
                    messageAbsence = `Absent du ${formatDateFr(first)} au ${formatDateFr(last)}`;
                }
            }
            
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
                            ${messageAbsence ? `<div class="text-xs text-orange-600 mt-1 font-semibold"><i class="fas fa-exclamation-triangle mr-1"></i>${messageAbsence}</div>` : ''}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="presence-container">
                        <div class="presence-item">
                            <input type="radio" 
                                   name="presence_${eleve.id}" 
                                   id="present_${eleve.id}" 
                                   value="present"
                                   class="radio-present hidden"
                                   checked
                                   data-eleve-id="${eleve.id}">
                            <label for="present_${eleve.id}" class="presence-circle" title="Présent">
                                <i class="fas fa-check"></i>
                            </label>
                            <span class="presence-label-text">Présent</span>
                        </div>
                        
                        <div class="presence-item">
                            <input type="radio" 
                                   name="presence_${eleve.id}" 
                                   id="absent_${eleve.id}" 
                                   value="absent"
                                   class="radio-absent hidden"
                                   data-eleve-id="${eleve.id}">
                            <label for="absent_${eleve.id}" class="presence-circle" title="Absent">
                                <i class="fas fa-times"></i>
                            </label>
                            <span class="presence-label-text">Absent</span>
                        </div>
                        
                        <input type="checkbox" name="absents[]" value="${eleve.id}" id="checkbox_${eleve.id}" class="hidden">
                    </div>
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
    } else {
        // Élève marqué présent
        console.log('Marqué présent:', eleveId);
        if (checkbox) {
            checkbox.checked = false;
            console.log('Checkbox décochée pour:', eleveId);
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

