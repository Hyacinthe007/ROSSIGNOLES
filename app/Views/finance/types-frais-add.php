<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-plus text-blue-600 mr-2"></i>
            Ajouter un Type de Frais
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Créer un nouveau type de frais scolaires</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-2xl">
        <form method="POST" action="<?= url('finance/types-frais/add') ?>">
            <?= csrf_field() ?>

            <!-- Libellé -->
            <div class="mb-4">
                <label for="libelle" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tag mr-2 text-gray-500"></i>Libellé *
                </label>
                <input type="text" id="libelle" name="libelle" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Ex: Frais d'inscription, Écolage, Cantine, etc.">
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2 text-gray-500"></i>Description (optionnel)
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Description détaillée du type de frais..."></textarea>
            </div>

            <!-- Boutons -->
            <div class="flex gap-3">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
                <a href="<?= url('finance/types-frais') ?>" 
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Annuler
                </a>
            </div>
        </form>
    </div>
</div>
