<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-edit text-indigo-600 mr-2"></i>
            Modifier l'enseignant
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Modifiez les informations de l'enseignant</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('enseignants/edit/' . $enseignant['id']) ?>" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Ligne 1: Matricule (full width) -->
                <div class="md:col-span-3">
                    <label for="matricule" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2 text-gray-500"></i>Matricule *
                    </label>
                    <input type="text" 
                           id="matricule" 
                           name="matricule" 
                           value="<?= e($enseignant['matricule']) ?>"
                           readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                </div>

                <!-- Ligne 2: Nom, Prénom, Sexe -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-gray-500"></i>Nom *
                    </label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           value="<?= e($enseignant['nom']) ?>"
                           required
                           oninput="this.value = this.value.toUpperCase()"
                           style="text-transform: uppercase"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                        Prénom *
                    </label>
                    <input type="text" 
                           id="prenom" 
                           name="prenom" 
                           value="<?= e($enseignant['prenom']) ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-venus-mars mr-2 text-gray-500"></i>Sexe *
                    </label>
                    <select id="sexe" 
                            name="sexe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="M" <?= ($enseignant['sexe'] == 'M') ? 'selected' : '' ?>>Masculin</option>
                        <option value="F" <?= ($enseignant['sexe'] == 'F') ? 'selected' : '' ?>>Féminin</option>
                    </select>
                </div>

                <!-- Ligne 3: Date de naissance, Lieu de naissance, CIN -->
                <div>
                    <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-birthday-cake mr-2 text-gray-500"></i>Date de naissance
                    </label>
                    <input type="date" 
                           id="date_naissance" 
                           name="date_naissance"
                           value="<?= e($enseignant['date_naissance'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>Lieu de naissance
                    </label>
                    <input type="text" 
                           id="lieu_naissance" 
                           name="lieu_naissance"
                           value="<?= e($enseignant['lieu_naissance'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="cin" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card-alt mr-2 text-gray-500"></i>CIN
                    </label>
                    <input type="text" 
                           id="cin" 
                           name="cin"
                           value="<?= e($enseignant['cin'] ?? '') ?>"
                           maxlength="12"
                           placeholder="12 Chiffres"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- Ligne 4: Date d'embauche, Diplôme, Spécialité -->
                <div>
                    <label for="date_embauche" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-2 text-gray-500"></i>Date d'embauche
                    </label>
                    <input type="date" 
                           id="date_embauche" 
                           name="date_embauche"
                           value="<?= e($enseignant['date_embauche'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="diplome" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-certificate mr-2 text-gray-500"></i>Diplôme
                    </label>
                    <input type="text" 
                           id="diplome" 
                           name="diplome"
                           value="<?= e($enseignant['diplome'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="specialite" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap mr-2 text-gray-500"></i>Spécialité
                    </label>
                    <input type="text" 
                           id="specialite" 
                           name="specialite"
                           value="<?= e($enseignant['specialite'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- Ligne 5: Téléphone, Email, Adresse -->
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2 text-gray-500"></i>Téléphone *
                    </label>
                    <input type="tel" 
                           id="telephone" 
                           name="telephone"
                           value="<?= e($enseignant['telephone'] ?? '') ?>"
                           required
                           pattern="[0-9]{10}"
                           maxlength="10"
                           placeholder="0123456789"
                           title="Le numéro de téléphone doit contenir exactement 10 chiffres"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>10 chiffres requis</p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-gray-500"></i>Email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email"
                           value="<?= e($enseignant['email'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-home mr-2 text-gray-500"></i>Adresse
                    </label>
                    <input type="text"
                           id="adresse" 
                           name="adresse"
                           value="<?= e($enseignant['adresse'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- Photo -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-camera mr-2 text-gray-500"></i>Photo
                    </label>
                    <div class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <?php if (!empty($enseignant['photo'])): ?>
                            <img src="<?= public_url($enseignant['photo']) ?>" alt="Photo actuelle" class="w-32 h-32 rounded-full object-cover mb-4">
                        <?php else: ?>
                            <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 mb-4">
                                <i class="fas fa-user text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        
                        <label for="photo" class="cursor-pointer bg-white border border-gray-300 rounded-md py-2 px-4 flex items-center gap-2 text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">
                            <i class="fas fa-upload text-gray-500"></i>
                            <span>Changer la photo</span>
                            <input type="file" id="photo" name="photo" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </label>
                        <p class="text-xs text-gray-500 mt-2">JPG, PNG max 2 Mo</p>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <button type="submit" 
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer les modifications</span>
                </button>
                <a href="<?= url('enseignants/details/' . $enseignant['id']) ?>" 
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

function previewImage(input) {
    if (input.files && input.files[0]) {
        if (input.files[0].size > 2 * 1024 * 1024) {
            alert("L'image est trop volumineuse. La taille maximum est de 2 Mo.");
            input.value = "";
            return;
        }
        
        var reader = new FileReader();
        reader.onload = function(e) {
            // Chercher l'image existante ou le placeholder
            var container = input.closest('.flex');
            var img = container.querySelector('img');
            var placeholder = container.querySelector('.bg-gray-200');
            
            if (img) {
                img.src = e.target.result;
            } else if (placeholder) {
                // Remplacer le placeholder par une image
                var newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.alt = "Nouvelle photo";
                newImg.className = "w-32 h-32 rounded-full object-cover mb-4";
                
                container.insertBefore(newImg, placeholder);
                placeholder.remove();
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

