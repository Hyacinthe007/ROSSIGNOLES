<div class="p-4 md:p-8">

    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Ajouter une classe
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Créez une nouvelle classe</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('classes/list') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à la liste</span>
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('classes/add') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" class="space-y-6">
            <?= csrf_field() ?>
            <div class="space-y-6">
                <!-- Ligne 1 : Nom de la classe -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-gray-500"></i>Nom de la classe *
                    </label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold"
                           placeholder="Ex: Sixième A">
                </div>

                <!-- Ligne 2 : Code, Niveau, Série -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2 text-gray-500"></i>Code
                            <span class="text-xs text-gray-500 ml-2">(Auto si vide)</span>
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code"
                               value="<?= e($code_auto ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ex: 6A, TLE-A2">
                    </div>

                    <!-- Niveau -->
                    <div>
                        <label for="niveau_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-layer-group mr-2 text-gray-500"></i>Niveau *
                        </label>
                        <select id="niveau_id" 
                                name="niveau_id" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sélectionner un niveau</option>
                            <?php foreach ($niveaux as $niveau): ?>
                                <option value="<?= $niveau['id'] ?>">
                                    <?= e($niveau['libelle'] ?? 'N/A') ?> (<?= e($niveau['cycle_libelle'] ?? 'Cycle inconnu') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Série (facultatif ou obligatoire selon logique) -->
                    <div>
                        <label for="serie_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-stream mr-2 text-gray-500"></i>Série
                        </label>
                        <select id="serie_id" 
                                name="serie_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                                disabled>
                            <option value="">Sélectionner d'abord un niveau</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 3 : Capacité, Seuil, Salle -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Capacité -->
                    <div>
                        <label for="capacite" class="block text-sm font-medium text-gray-700 mb-2">
                             <i class="fas fa-users mr-2 text-gray-500"></i>Capacité
                        </label>
                        <input type="number" 
                               id="capacite" 
                               name="capacite" 
                               value="40"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Seuil Admission -->
                    <div>
                        <label for="seuil_admission" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-chart-line mr-2 text-gray-500"></i>Seuil Admission
                        </label>
                        <input type="number" 
                               step="0.01"
                               id="seuil_admission" 
                               name="seuil_admission" 
                               value="10.00"
                               min="0"
                               max="20"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Salle -->
                    <div>
                        <label for="salle" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-door-open mr-2 text-gray-500"></i>Salle
                        </label>
                        <input type="text" 
                               id="salle" 
                               name="salle" 
                               placeholder="Ex: Salle 101"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Ligne 4 : Année, Prof Principal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Année scolaire -->
                    <div>
                        <label for="annee_scolaire_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Année scolaire
                        </label>
                        <select id="annee_scolaire_id" 
                                name="annee_scolaire_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50" readonly>
                            <?php if (isset($anneeActive) && $anneeActive): ?>
                                <option value="<?= $anneeActive['id'] ?>" selected>
                                    <?= e($anneeActive['libelle']) ?> (Active)
                                </option>
                            <?php else: ?>
                                <option value="">Aucune année active</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Professeur Principal -->
                    <div>
                        <label for="enseignant_principal_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-chalkboard-teacher mr-2 text-gray-500"></i>Professeur Principal
                        </label>
                        <select id="enseignant_principal_id" 
                                name="enseignant_principal_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sélectionner un professeur</option>
                            <?php foreach ($enseignants as $enseignant): ?>
                                <option value="<?= $enseignant['id'] ?>">
                                    <?= e($enseignant['nom']) ?> <?= e($enseignant['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer la classe</span>
                </button>
                <a href="<?= url('classes/list') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" 
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
    const niveauSelect = document.getElementById('niveau_id');
    const serieSelect = document.getElementById('serie_id');

    if (niveauSelect && serieSelect) {
        niveauSelect.addEventListener('change', function() {
            const niveauId = this.value;
            
            // Réinitialiser le select des séries
            serieSelect.innerHTML = '<option value="">Chargement...</option>';
            serieSelect.disabled = true;
            serieSelect.classList.add('bg-gray-50');

            if (!niveauId) {
                serieSelect.innerHTML = '<option value="">Sélectionner d'abord un niveau</option>';
                return;
            }

            // Charger les séries via AJAX
            fetch('<?= url("classes/series-by-niveau") ?>/' + niveauId)
                .then(response => response.json())
                .then(data => {
                    serieSelect.innerHTML = '<option value="">Aucune série (Général)</option>';
                    
                    if (data && data.length > 0) {
                        data.forEach(serie => {
                            const option = document.createElement('option');
                            option.value = serie.id;
                            option.textContent = serie.libelle || serie.code || 'N/A';
                            serieSelect.appendChild(option);
                        });
                        serieSelect.disabled = false;
                        serieSelect.classList.remove('bg-gray-50');
                    } else {
                        // Si aucune série n'est trouvée pour ce niveau, on laisse "Aucune série"
                        // et on peut rester désactivé ou non selon si c'est optionnel
                        serieSelect.disabled = false;
                        serieSelect.classList.remove('bg-gray-50');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des séries:', error);
                    serieSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        });
    }
});
</script>


