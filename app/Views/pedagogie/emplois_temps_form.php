<div class="p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas <?= isset($emploiTemps) ? 'fa-calendar-check text-blue-600' : 'fa-calendar-plus text-purple-600' ?> mr-2"></i>
                    <?= isset($emploiTemps) ? 'Modifier le créneau' : 'Programmer un cours' ?>
                </h1>
                <p class="text-gray-500"><?= isset($emploiTemps) ? "Mettre à jour les horaires de ce cours" : "Ajouter un nouveau créneau à l'emploi du temps" ?></p>
            </div>
            <a href="<?= url('pedagogie/emplois-temps') ?><?= isset($emploiTemps) ? '?classe_id=' . $emploiTemps['classe_id'] : '' ?>" class="text-gray-500 hover:text-gray-700 transition flex items-center gap-2 font-medium">
                <i class="fas fa-times"></i>
                <span>Annuler</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <form action="<?= isset($emploiTemps) ? url('pedagogie/emplois-temps/edit/' . $emploiTemps['id']) : url('pedagogie/emplois-temps/add') ?>" method="POST" class="p-8">
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm animate-bounce">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                            <p class="text-red-700 font-medium"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <p class="text-green-700 font-medium"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <!-- Sélection de l'enseignement -->
                    <div>
                        <label for="enseignement_id" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">
                            Enseignement (Classe - Matière - Professeur)
                        </label>
                        <select id="enseignement_id" name="enseignement_id" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all outline-none ">
                            <option value="">Sélectionner un enseignement défini</option>
                            <?php foreach ($enseignements as $e): ?>
                                <?php 
                                    $selected = '';
                                    if (isset($currentEcId) && $currentEcId == $e['id']) $selected = 'selected';
                                    elseif (!isset($emploiTemps) && isset($prefilled['classe_id']) && $prefilled['classe_id'] == $e['classe_id']) $selected = 'selected';
                                ?>
                                <option value="<?= $e['id'] ?>" <?= $selected ?>>
                                    <?= e($e['classe_code'] ?? $e['classe_libelle']) ?> - <?= e($e['matiere_code'] ?? $e['matiere_libelle']) ?> - <?= e($e['enseignant_nom']) ?> <?= e($e['enseignant_prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-2 text-xs text-gray-400 ">
                            <i class="fas fa-info-circle mr-1"></i>
                            Si l'enseignement n'existe pas, <a href="<?= url('pedagogie/enseignements') ?>" class="text-blue-500 hover:underline">créez-le d'abord ici</a>.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jour de la semaine -->
                        <div>
                            <label for="jour_semaine" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">
                                Jour de la semaine
                            </label>
                            <select id="jour_semaine" name="jour_semaine" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all outline-none">
                                <?php 
                                $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
                                foreach ($jours as $j): 
                                    $selected = '';
                                    if (isset($emploiTemps) && $emploiTemps['jour_semaine'] == $j) $selected = 'selected';
                                    elseif (isset($prefilled) && ($prefilled['jour'] ?? '') == $j) $selected = 'selected';
                                ?>
                                    <option value="<?= $j ?>" <?= $selected ?>>
                                        <?= ucfirst($j) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Remarque -->
                        <div>
                            <label for="remarque" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">
                                Remarque (optionnel)
                            </label>
                            <input type="text" id="remarque" name="remarque" placeholder="Ex: Salle de labo, cours bilingue..."
                                   value="<?= e($emploiTemps['remarque'] ?? '') ?>"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                        <!-- Heure début -->
                        <div>
                            <label for="heure_debut" class="block text-sm font-bold text-blue-700 mb-2 uppercase tracking-wider">
                                <i class="far fa-clock mr-1"></i> Heure de début
                            </label>
                            <input type="time" id="heure_debut" name="heure_debut" required
                                   value="<?php 
                                        if (isset($emploiTemps)) echo date('H:i', strtotime($emploiTemps['heure_debut']));
                                        elseif (isset($prefilled['h_debut'])) echo $prefilled['h_debut'];
                                   ?>"
                                   class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        </div>

                        <!-- Heure fin -->
                        <div>
                            <label for="heure_fin" class="block text-sm font-bold text-blue-700 mb-2 uppercase tracking-wider">
                                <i class="far fa-clock mr-1"></i> Heure de fin
                            </label>
                            <input type="time" id="heure_fin" name="heure_fin" required
                                   value="<?php 
                                        if (isset($emploiTemps)) echo date('H:i', strtotime($emploiTemps['heure_fin']));
                                        elseif (isset($prefilled['h_fin'])) echo $prefilled['h_fin'];
                                   ?>"
                                   class="w-full px-4 py-2 bg-white border border-blue-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        </div>
                    </div>
                </div>

                <div class="mt-10">
                    <button type="submit" 
                            class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-blue-500/25 transform hover:-translate-y-1 flex items-center justify-center gap-3 text-lg">
                        <i class="fas fa-save"></i>
                        <span><?= isset($emploiTemps) ? 'Mettre à jour le créneau' : 'Enregistrer le créneau' ?></span>
                    </button>
                    <p class="mt-4 text-center text-xs text-gray-400">
                        L'emploi du temps sera instantanément mis à jour pour la classe concernée.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

