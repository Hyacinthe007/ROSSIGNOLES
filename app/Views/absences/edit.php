<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Modifier <?= $absence['type'] === 'retard' ? "le retard" : "l'absence" ?>
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Mettez à jour les informations enregistrées</p>
        </div>
        <a href="<?= url('absences/details/' . $absence['id']) ?>" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition flex items-center gap-2 text-sm font-semibold">
            <i class="fas fa-arrow-left"></i>
            <span>Retour aux détails</span>
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border-t-4 border-blue-600">
        <form method="POST" action="<?= url('absences/edit/' . $absence['id']) ?>" class="space-y-8">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Section Élève (Grisée car non modifiable pour préserver l'intégrité) -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        <i class="fas fa-user-graduate mr-2"></i>Élève
                    </label>
                    <select name="eleve_id" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed" onfocus="this.blur()">
                        <?php foreach ($eleves as $eleve): ?>
                            <option value="<?= $eleve['id'] ?>" <?= ($absence['eleve_id'] == $eleve['id']) ? 'selected' : '' ?>>
                                <?= e($eleve['matricule'] . ' - ' . $eleve['nom'] . ' ' . $eleve['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1 italic">L'élève ne peut pas être changé après enregistrement.</p>
                </div>

                <!-- Date de l'absence -->
                <div>
                    <label for="date_absence" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        <i class="fas fa-calendar-alt mr-2"></i>Date *
                    </label>
                    <input type="date" 
                           id="date_absence" 
                           name="date_absence" 
                           value="<?= e($absence['date_absence']) ?>"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        <i class="fas fa-tag mr-2"></i>Type d'enregistrement *
                    </label>
                    <select id="type" name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition uppercase font-bold text-sm">
                        <option value="absence" <?= $absence['type'] === 'absence' ? 'selected' : '' ?>>Absence</option>
                        <option value="retard" <?= $absence['type'] === 'retard' ? 'selected' : '' ?>>Retard</option>
                    </select>
                </div>

                <!-- Statut Justifiée -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                        <i class="fas fa-balance-scale mr-2"></i>Statut de justification
                    </label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="justifiee" value="1" class="sr-only peer" <?= ($absence['justifiee'] ?? 0) ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Cocher si justifiée</span>
                    </label>
                </div>

                <!-- Période / Heures -->
                <div class="md:col-span-2 bg-gray-50 p-6 rounded-xl border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-500"></i>
                        Période et Horaires
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="periode" class="block text-xs text-gray-500 mb-1">Période générale</label>
                            <select id="periode" name="periode" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="journee" <?= $absence['periode'] === 'journee' ? 'selected' : '' ?>>Journée complète</option>
                                <option value="matin" <?= $absence['periode'] === 'matin' ? 'selected' : '' ?>>Matin</option>
                                <option value="apres_midi" <?= $absence['periode'] === 'apres_midi' ? 'selected' : '' ?>>Après-midi</option>
                            </select>
                        </div>
                        <div>
                            <label for="heure_debut" class="block text-xs text-gray-500 mb-1">Heure Début (optionnel)</label>
                            <input type="time" id="heure_debut" name="heure_debut" value="<?= e($absence['heure_debut']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label for="heure_fin" class="block text-xs text-gray-500 mb-1">Heure Fin (optionnel)</label>
                            <input type="time" id="heure_fin" name="heure_fin" value="<?= e($absence['heure_fin']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Motif -->
                <div class="md:col-span-2">
                    <label for="motif" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        <i class="fas fa-comment-alt mr-2"></i>Motif ou Observation
                    </label>
                    <textarea id="motif" 
                              name="motif"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                              placeholder="Expliquez la raison de l'absence ou du retard..."><?= e($absence['motif']) ?></textarea>
                </div>
            </div>

            <!-- Pied du formulaire -->
            <div class="flex items-center justify-end gap-4 pt-8 border-t">
                <a href="<?= url('absences/details/' . $absence['id']) ?>" 
                   class="px-8 py-3 text-gray-600 hover:text-gray-900 font-bold transition">
                    Annuler
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-12 rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                    <i class="fas fa-check"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
