<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-user-plus text-indigo-600 mr-2"></i>
            Ajouter un enseignant
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Enregistrez un nouvel enseignant</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('enseignants/add') ?>" class="space-y-6" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <!-- Ligne 1 : Matricule et Photo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                           placeholder="Ex: ENS-2024-001">
                </div>
                
                <!-- Photo -->
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-camera mr-2 text-gray-500"></i>Photo
                    </label>
                    <input type="file" 
                           id="photo" 
                           name="photo"
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <!-- Ligne 2 : Nom, Prénom, Sexe -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-gray-500"></i>Nom *
                    </label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           required
                           oninput="this.value = this.value.toUpperCase()"
                           style="text-transform: uppercase"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- Sexe -->
                <div>
                    <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-venus-mars mr-2 text-gray-500"></i>Sexe *
                    </label>
                    <select id="sexe" 
                            name="sexe"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>
            </div>

            <!-- Ligne 3 : Date de naissance, Lieu de naissance, Numero CIN -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Date de naissance -->
                <div>
                    <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-birthday-cake mr-2 text-gray-500"></i>Date de naissance *
                    </label>
                    <input type="date" 
                           id="date_naissance" 
                           name="date_naissance"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- Lieu de naissance -->
                <div>
                    <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>Lieu de naissance *
                    </label>
                    <input type="text" 
                           id="lieu_naissance" 
                           name="lieu_naissance"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- CIN -->
                <div>
                    <label for="cin" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card-alt mr-2 text-gray-500"></i>Numéro CIN *
                    </label>
                    <input type="text" 
                           id="cin" 
                           name="cin"
                           required
                           maxlength="12"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="12 Chiffres">
                </div>
            </div>

            <!-- Ligne 4 : Téléphone, Email, Diplome -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- Diplôme -->
                <div>
                    <label for="diplome" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-certificate mr-2 text-gray-500"></i>Diplôme *
                    </label>
                    <select id="diplome" 
                           name="diplome"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Sélectionner...</option>
                        <option value="BAC+2">BAC+2</option>
                        <option value="Licence">Licence</option>
                        <option value="Maitrise">Maitrise</option>
                        <option value="CAPEN">CAPEN</option>
                    </select>
                </div>
            </div>

            <!-- Ligne 5 : Date d'embauche, Spécialité, Statut -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Date d'embauche -->
                <div>
                    <label for="date_embauche" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-2 text-gray-500"></i>Date d'embauche *
                    </label>
                    <input type="date" 
                           id="date_embauche" 
                           name="date_embauche"
                           required
                           value="<?= date('Y-m-d') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- Spécialité -->
                <div>
                    <label for="specialite" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap mr-2 text-gray-500"></i>Spécialité *
                    </label>
                    <input type="text" 
                           id="specialite" 
                           name="specialite"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Ex: Mathématiques">
                </div>

                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-2 text-gray-500"></i>Statut *
                    </label>
                    <select id="statut" 
                            name="statut"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
            </div>

            <!-- Ligne 6 : Adresse -->
            <div>
                <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-home mr-2 text-gray-500"></i>Adresse
                </label>
                <input type="text"
                       id="adresse" 
                       name="adresse"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t mt-6">
                <button type="submit" 
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer l'enseignant</span>
                </button>
                <a href="<?= url('liste-personnel') ?>" 
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
    document.getElementById('prenom').addEventListener('input', function() {
        this.value = this.value.toLowerCase().replace(/(?:^|[\s-])\w/g, match => match.toUpperCase());
    });
    
    const adresseField = document.getElementById('adresse');
    if (adresseField) {
        adresseField.addEventListener('blur', function() {
            this.value = this.value.toLowerCase().replace(/(?:^|[\s-])\w/g, match => match.toUpperCase());
        });
    }
    
    document.getElementById('telephone').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
</script>
