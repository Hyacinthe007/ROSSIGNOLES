<div class="p-4 md:p-8">
    <div class="mb-4">
        <a href="<?= url('classes/list') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la liste</span>
        </a>
    </div>
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-edit text-blue-600 mr-2"></i>
            Modifier la classe
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Modifiez les informations de la classe</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('classes/edit/' . $classe['id']) ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" class="space-y-6">
            <?= csrf_field() ?>
            <!-- Nom de la classe (Tout en haut) -->
            <div class="mb-6">
                <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tag mr-2 text-gray-500"></i>Nom de la classe *
                </label>
                <input type="text" 
                       id="nom" 
                       name="nom" 
                       value="<?= e($classe['nom']) ?>"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-lg"
                       placeholder="Ex: Sixième A">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Code (à la place du nom) -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2 text-gray-500"></i>Code
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code"
                           value="<?= e($classe['code'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ex: 6ème A">
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
                            <option value="<?= $niveau['id'] ?>" <?= ($classe['niveau_id'] == $niveau['id']) ? 'selected' : '' ?>>
                                <?= e($niveau['libelle'] ?? 'N/A') ?> (<?= e($niveau['cycle_libelle'] ?? 'Cycle inconnu') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Série -->
                <div>
                    <label for="serie_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-stream mr-2 text-gray-500"></i>Série
                    </label>
                    <select id="serie_id" 
                            name="serie_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Aucune série</option>
                        <?php foreach ($series as $serie): ?>
                            <option value="<?= $serie['id'] ?>" <?= ($classe['serie_id'] == $serie['id']) ? 'selected' : '' ?>>
                                <?= e($serie['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Année scolaire -->
                <div>
                    <label for="annee_scolaire_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Année scolaire *
                    </label>
                    <select id="annee_scolaire_id" 
                            name="annee_scolaire_id" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionner une année</option>
                        <?php foreach ($annees as $annee): ?>
                            <option value="<?= $annee['id'] ?>" <?= ($classe['annee_scolaire_id'] == $annee['id']) ? 'selected' : '' ?>>
                                <?= e($annee['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Établissement -->
                <div>
                    <label for="etablissement_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-2 text-gray-500"></i>Établissement
                    </label>
                    <select id="etablissement_id" 
                            name="etablissement_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionner un établissement</option>
                        <?php foreach ($etablissements as $etablissement): ?>
                            <option value="<?= $etablissement['id'] ?>" <?= ($classe['etablissement_id'] == $etablissement['id']) ? 'selected' : '' ?>>
                                <?= e($etablissement['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Capacité maximale -->
                <div>
                    <label for="capacite_max" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users mr-2 text-gray-500"></i>Capacité maximale
                    </label>
                    <input type="number" 
                           id="capacite_max" 
                           name="capacite_max" 
                           value="<?= e($classe['capacite_max'] ?? 40) ?>"
                           min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Salle principale -->
                <div>
                    <label for="salle_principale_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-door-open mr-2 text-gray-500"></i>Salle principale
                    </label>
                    <select id="salle_principale_id" 
                            name="salle_principale_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Aucune salle</option>
                        <?php foreach ($salles as $salle): ?>
                            <option value="<?= $salle['id'] ?>" <?= ($classe['salle_principale_id'] == $salle['id']) ? 'selected' : '' ?>>
                                <?= e($salle['nom']) ?> (<?= e($salle['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <a href="<?= url('classes/list') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer les modifications</span>
                </button>
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
            const currentSerieId = serieSelect.value;
            
            // Réinitialiser le select des séries
            serieSelect.innerHTML = '<option value="">Chargement...</option>';
            serieSelect.disabled = true;
            
            if (!niveauId) {
                serieSelect.innerHTML = '<option value="">Sélectionner d\'abord un niveau</option>';
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
                            // Tenter de resélectionner l'ancienne série si elle existe toujours dans ce niveau
                            if (serie.id == currentSerieId) {
                                option.selected = true;
                            }
                            serieSelect.appendChild(option);
                        });
                    }
                    serieSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des séries:', error);
                    serieSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    serieSelect.disabled = false;
                });
        });
    }
});
</script>

