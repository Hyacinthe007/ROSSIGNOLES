<div class="max-w-2xl mx-auto p-4 md:p-8">
    <div class="bg-white rounded-xl shadow-lg border-t-4 border-red-500 overflow-hidden">
        <div class="p-6 md:p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                <i class="fas fa-ban text-red-500 mr-2"></i>Terminer l'inscription
            </h1>
            <p class="text-gray-600 mb-6">
                Vous êtes sur le point de terminer (désactiver) une inscription. Cette action libérera la place dans la classe.
            </p>

            <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                <h2 class="font-semibold text-gray-800 mb-2">Inscription concernée :</h2>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li><span class="font-medium">Élève :</span> <?= e($inscription['eleve_nom']) ?> <?= e($inscription['eleve_prenom']) ?> (<?= e($inscription['eleve_matricule']) ?>)</li>
                    <li><span class="font-medium">Classe :</span> <?= e($inscription['classe_nom']) ?></li>
                    <li><span class="font-medium">Année scolaire :</span> <?= e($inscription['annee_scolaire'] ?? $inscription['annee_scolaire_libelle'] ?? 'N/A') ?></li>
                </ul>
            </div>

            <form method="POST" action="<?= url('inscriptions/terminer/' . $inscription['id']) ?>">
                <?= csrf_field() ?>
                
                <div class="mb-6">
                    <label for="motif" class="block text-sm font-medium text-gray-700 mb-2">
                        Motif de la fin d'inscription (Optionnel)
                    </label>
                    <textarea id="motif" name="motif" rows="3" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="Ex: Départ de l'établissement, abandon, etc."></textarea>
                </div>

                <div class="flex items-center gap-4">
                    <a href="<?= url('inscriptions/details/' . $inscription['id']) ?>" 
                       class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg text-center transition">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>Confirmer</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
