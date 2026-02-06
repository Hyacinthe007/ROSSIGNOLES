<div class="p-4 md:p-8">
    <!-- En-tÃªte -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-edit text-blue-600 mr-2"></i>
            Modifier le parent
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Modifiez les informations du parent</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('parents/edit/' . $parent['id']) ?>" class="space-y-6">
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
                           value="<?= e($parent['nom'] ?? '') ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- PrÃ©nom -->
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                        PrÃ©nom *
                    </label>
                    <input type="text"
                           id="prenom"
                           name="prenom"
                           value="<?= e($parent['prenom'] ?? '') ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Sexe -->
                <div>
                    <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-venus-mars mr-2 text-gray-500"></i>Sexe
                    </label>
                    <select id="sexe"
                            name="sexe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Non spÃ©cifiÃ©</option>
                        <option value="M" <?= ($parent['sexe'] ?? '') == 'M' ? 'selected' : '' ?>>Masculin</option>
                        <option value="F" <?= ($parent['sexe'] ?? '') == 'F' ? 'selected' : '' ?>>FÃ©minin</option>
                    </select>
                </div>

                <!-- Type de parent -->
                <div>
                    <label for="type_parent" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users mr-2 text-gray-500"></i>Type de parent
                    </label>
                    <select id="type_parent"
                            name="type_parent"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="pere" <?= ($parent['type_parent'] ?? '') == 'pere' ? 'selected' : '' ?>>PÃ¨re</option>
                        <option value="mere" <?= ($parent['type_parent'] ?? '') == 'mere' ? 'selected' : '' ?>>MÃ¨re</option>
                        <option value="tuteur" <?= ($parent['type_parent'] ?? '') == 'tuteur' ? 'selected' : '' ?>>Tuteur</option>
                        <option value="autre" <?= ($parent['type_parent'] ?? '') == 'autre' ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>

                <!-- TÃ©lÃ©phone -->
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2 text-gray-500"></i>TÃ©lÃ©phone *
                    </label>
                    <input type="tel"
                           id="telephone"
                           name="telephone"
                           value="<?= e($parent['telephone'] ?? '') ?>"
                           required
                           pattern="[0-9]{10}"
                           maxlength="10"
                           placeholder="0123456789"
                           title="Le numÃ©ro de tÃ©lÃ©phone doit contenir exactement 10 chiffres"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                           value="<?= e($parent['email'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Profession -->
                <div class="md:col-span-2">
                    <label for="profession" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-briefcase mr-2 text-gray-500"></i>Profession
                    </label>
                    <input type="text"
                           id="profession"
                           name="profession"
                           value="<?= e($parent['profession'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Adresse -->
                <div class="md:col-span-2">
                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-home mr-2 text-gray-500"></i>Adresse
                    </label>
                    <input type="text"
                           id="adresse"
                           name="adresse"
                           value="<?= e($parent['adresse'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer les modifications</span>
                </button>
                <a href="<?= url('parents/details/' . $parent['id']) ?>"
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
    document.getElementById('nom').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

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
