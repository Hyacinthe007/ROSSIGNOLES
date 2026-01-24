<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas <?= isset($enseignement) ? 'fa-edit text-orange-600' : 'fa-plus-circle text-blue-600' ?> mr-2"></i>
                <?= isset($enseignement) ? 'Modifier l\'attribution' : 'Nouvelle attribution' ?>
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Affecter un enseignant à une matière pour une classe</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('pedagogie/enseignements') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-4xl mx-auto border border-gray-100">
        <form method="POST" action="<?= isset($enseignement) ? url('pedagogie/enseignements/edit/' . $enseignement['id']) : url('pedagogie/enseignements/add') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" class="space-y-6">
            
            <?php if (empty($anneeActive) && !isset($enseignement)): ?>
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-amber-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">
                                Attention : Aucune année scolaire active détectée. L'attribution risque de ne pas être prise en compte correctement.
                             </p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="annee_scolaire_id" value="<?= $enseignement['annee_scolaire_id'] ?? $anneeActive['id'] ?>">
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded-r-lg">
                    <p class="text-sm text-blue-700">
                        <span class="font-bold">Année scolaire :</span> <?= e($anneeActive['libelle'] ?? 'N/A') ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Classe -->
                <div>
                    <label for="classe_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-chalkboard mr-2 text-gray-400"></i>Classe *
                    </label>
                    <select id="classe_id" name="classe_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                        <option value="">Sélectionner une classe</option>
                        <?php foreach ($classes as $classe): ?>
                            <?php $selected = (isset($enseignement) && $enseignement['classe_id'] == $classe['id']) ? 'selected' : ''; ?>
                            <option value="<?= $classe['id'] ?>" <?= $selected ?>>
                                <?= !empty($classe['code']) ? e($classe['code']) : e($classe['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Matière -->
                <div>
                    <label for="matiere_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-book mr-2 text-gray-400"></i>Matière *
                    </label>
                    <select id="matiere_id" name="matiere_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                        <option value="">Sélectionner une matière</option>
                        <?php foreach ($matieres as $matiere): ?>
                            <?php $selected = (isset($enseignement) && $enseignement['matiere_id'] == $matiere['id']) ? 'selected' : ''; ?>
                            <option value="<?= $matiere['id'] ?>" <?= $selected ?>>
                                <?= e($matiere['nom']) ?> <?= !empty($matiere['code']) ? '('.e($matiere['code']).')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Enseignant -->
                <div class="md:col-span-2">
                    <label for="personnel_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-chalkboard-teacher mr-2 text-gray-400"></i>Enseignant *
                    </label>
                    <select id="personnel_id" name="personnel_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                        <option value="">Sélectionner un enseignant</option>
                        <?php foreach ($enseignants as $enseignant): ?>
                            <?php $selected = (isset($enseignement) && $enseignement['personnel_id'] == $enseignant['id']) ? 'selected' : ''; ?>
                            <option value="<?= $enseignant['id'] ?>" <?= $selected ?>>
                                <?= e($enseignant['nom']) ?> <?= e($enseignant['prenom']) ?> 
                                <?= !empty($enseignant['specialite']) ? '- '.e($enseignant['specialite']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Volume Horaire -->
                <div>
                    <label for="volume_horaire" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2 text-gray-400"></i>Volume Horaire (Heures/Semaine)
                    </label>
                    <input type="number" id="volume_horaire" name="volume_horaire" min="0" step="0.5" placeholder="Ex: 4"
                           value="<?= $enseignement['volume_horaire'] ?? '' ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                </div>

                <!-- Statut -->
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center cursor-pointer group">
                        <?php $checked = (!isset($enseignement) || ($enseignement['actif'] ?? 0)) ? 'checked' : ''; ?>
                        <input type="checkbox" name="actif" value="1" <?= $checked ?> class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition">
                        <span class="ml-2 text-gray-700 font-medium group-hover:text-blue-600 transition">Enseignement actif</span>
                    </label>
                </div>
            </div>

            <div class="pt-6 border-t flex flex-col sm:flex-row justify-end gap-3">
                <a href="<?= url('pedagogie/enseignements') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-bold text-center">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-bold shadow-lg shadow-blue-100 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span><?= isset($enseignement) ? 'Mettre à jour' : 'Enregistrer' ?></span>
                </button>
            </div>
        </form>
    </div>
</div>
