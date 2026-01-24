<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-edit text-red-600 mr-2"></i>
            Modifier l'absence
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Modifiez les informations de l'absence</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('absences/edit/' . $absence['id']) ?>" class="space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Élève -->
                <div>
                    <label for="eleve_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-graduate mr-2 text-gray-500"></i>Élève *
                    </label>
                    <select id="eleve_id" 
                            name="eleve_id" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Sélectionner un élève</option>
                        <?php foreach ($eleves as $eleve): ?>
                            <option value="<?= $eleve['id'] ?>" <?= ($absence['eleve_id'] == $eleve['id']) ? 'selected' : '' ?>>
                                <?= e($eleve['matricule'] . ' - ' . $eleve['nom'] . ' ' . $eleve['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date début -->
                <div>
                    <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Date début *
                    </label>
                    <input type="date" 
                           id="date_debut" 
                           name="date_debut" 
                           value="<?= e($absence['date_debut']) ?>"
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
                           value="<?= e($absence['date_fin']) ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <!-- Justifiée -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Statut
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" 
                               name="justifiee" 
                               value="1"
                               <?= ($absence['justifiee'] ?? 0) ? 'checked' : '' ?>
                               class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="text-sm text-gray-700">Absence justifiée</span>
                    </label>
                </div>

                <!-- Motif -->
                <div class="md:col-span-2">
                    <label for="motif" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment-alt mr-2 text-gray-500"></i>Motif
                    </label>
                    <input type="text" 
                           id="motif" 
                           name="motif"
                           value="<?= e($absence['motif']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Raison de l'absence...">
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <button type="submit" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer</span>
                </button>
                <a href="<?= url('absences/details/' . $absence['id']) ?>" 
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

