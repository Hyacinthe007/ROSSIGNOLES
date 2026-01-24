<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-user-plus text-purple-600 mr-2"></i>
            Ajouter un parent
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Remplissez les informations du parent/tuteur</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('parents/add') ?>" class="space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-gray-500"></i>Nom *
                    </label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Prénom">
                </div>

                <!-- Sexe -->
                <div>
                    <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-venus-mars mr-2 text-gray-500"></i>Sexe
                    </label>
                    <select id="sexe" 
                            name="sexe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Non spécifié</option>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>

                <!-- Type de parent -->
                <div>
                    <label for="type_parent" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users mr-2 text-gray-500"></i>Type de parent *
                    </label>
                    <select id="type_parent" 
                            name="type_parent"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="pere">Père</option>
                        <option value="mere">Mère</option>
                        <option value="tuteur">Tuteur</option>
                        <option value="autre">Autre</option>
                    </select>
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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>10 chiffres requis</p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-gray-500"></i>Email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="parent@example.com">
                </div>

                <!-- Profession -->
                <div class="md:col-span-2">
                    <label for="profession" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-briefcase mr-2 text-gray-500"></i>Profession
                    </label>
                    <input type="text" 
                           id="profession" 
                           name="profession"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Ex: Enseignant, Médecin, Commerçant...">
                </div>

                <!-- Adresse -->
                <div class="md:col-span-2">
                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-home mr-2 text-gray-500"></i>Adresse
                    </label>
                    <input type="text" 
                           id="adresse" 
                           name="adresse"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <button type="submit" 
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer le parent</span>
                </button>
                <a href="<?= url('parents/list') ?>" 
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
    // Formater nom en majuscules
    document.getElementById('nom').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Formater prénom en title case
    document.getElementById('prenom').addEventListener('input', function() {
        this.value = this.value.toLowerCase().replace(/(?:^|[\s-])\w/g, match => match.toUpperCase());
    });
    
    // Formater adresse en title case
    const adresseField = document.getElementById('adresse');
    if (adresseField) {
        adresseField.addEventListener('blur', function() {
            this.value = this.value.toLowerCase().replace(/(?:^|[\s-])\w/g, match => match.toUpperCase());
        });
    }
    
    // Validation téléphone : accepter uniquement les chiffres
    document.getElementById('telephone').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
</script>
