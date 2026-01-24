<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-user-plus text-blue-600 mr-2"></i>
            Ajouter un élève
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Remplissez les informations de l'élève</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('eleves/add') ?>" class="space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Matricule -->
                <div>
                    <label for="matricule" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2 text-gray-500"></i>Matricule
                        <span class="text-xs text-gray-500 ml-2">(Généré automatiquement si vide)</span>
                    </label>
                    <input type="text" 
                           id="matricule" 
                           name="matricule" 
                           value="<?= e($matricule_auto ?? '') ?>"
                           readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                           placeholder="Ex: ELEV-2024-001">
                </div>

                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-gray-500"></i>Nom *
                    </label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nom de famille">
                </div>

                <!-- Prénom -->
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                        Prénom *
                    </label>
                    <input type="text" 
                           id="prenom" 
                           name="prenom" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Prénom">
                </div>

                <!-- Sexe -->
                <div>
                    <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-venus-mars mr-2 text-gray-500"></i>Sexe *
                    </label>
                    <select id="sexe" 
                            name="sexe" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>

                <!-- Date de naissance -->
                <div>
                    <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-birthday-cake mr-2 text-gray-500"></i>Date de naissance
                    </label>
                    <input type="date" 
                           id="date_naissance" 
                           name="date_naissance"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Lieu de naissance -->
                <div>
                    <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>Lieu de naissance
                    </label>
                    <input type="text" 
                           id="lieu_naissance" 
                           name="lieu_naissance"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ville de naissance">
                </div>

                <!-- Nationalité -->
                <div>
                    <label for="nationalite" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag mr-2 text-gray-500"></i>Nationalité
                    </label>
                    <input type="text" 
                           id="nationalite" 
                           name="nationalite" 
                           value="Malagasy"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>


                <!-- Téléphone -->
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2 text-gray-500"></i>Téléphone *
                    </label>
                    <input type="tel" 
                           id="telephone" 
                           name="telephone"
                           required
                           pattern="[0-9]{10}"
                           maxlength="10"
                           placeholder="0123456789"
                           title="Le numéro de téléphone doit contenir exactement 10 chiffres"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>10 chiffres requis</p>
                </div>
            </div>


            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2 text-gray-500"></i>Email
                </label>
                <input type="email" 
                       id="email" 
                       name="email"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="eleve@example.com">
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer</span>
                </button>
                <a href="<?= url('eleves/list') ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
            </div>
        </form>
    </div>
</div>
