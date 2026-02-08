<div class="p-4 md:p-8">
    <!-- En-tÃªte -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-edit text-red-600 mr-2"></i>
            Modifier la sanction
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Modifiez les informations de la sanction</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl p-6 md:p-8">
        <form method="POST" action="<?= url('sanctions/edit/' . $sanction['id']) ?>" class="space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ã‰lÃ¨ve -->
                <div>
                    <label for="eleve_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-graduate mr-2 text-gray-500"></i>Ã‰lÃ¨ve *
                    </label>
                    <select id="eleve_id"
                            name="eleve_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">SÃ©lectionner un Ã©lÃ¨ve</option>
                        <?php foreach ($eleves as $eleve): ?>
                            <option value="<?= $eleve['id'] ?>" <?= ($sanction['eleve_id'] == $eleve['id']) ? 'selected' : '' ?>>
                                <?= e($eleve['matricule'] . ' - ' . $eleve['nom'] . ' ' . $eleve['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Type de sanction -->
                <div>
                    <label for="type_sanction_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-gray-500"></i>Type de sanction *
                    </label>
                    <select id="type_sanction_id"
                            name="type_sanction_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">SÃ©lectionner un type</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= ($sanction['type_sanction_id'] == $type['id']) ? 'selected' : '' ?>>
                                <?= e($type['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label for="date_sanction" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Date de sanction *
                    </label>
                    <input type="date"
                           id="date_sanction"
                           name="date_sanction"
                           value="<?= e($sanction['date_sanction']) ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            <!-- Motif -->
            <div>
                <label for="motif" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-comment-alt mr-2 text-gray-500"></i>Motif *
                </label>
                <textarea id="motif"
                          name="motif"
                          rows="4"
                          required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"><?= e($sanction['motif']) ?></textarea>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer</span>
                </button>
                <a href="<?= url('sanctions/details/' . $sanction['id']) ?>"
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
            </div>
        </form>
    </div>
</div>

