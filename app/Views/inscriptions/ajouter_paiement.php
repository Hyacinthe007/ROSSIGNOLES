<div class="max-w-2xl mx-auto p-4 md:p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-plus-circle text-green-600 mr-2"></i>Nouveau Paiement
        </h1>
        <p class="text-gray-600">
            Inscription de <strong><?= e($inscription['eleve_nom'] . ' ' . $inscription['eleve_prenom']) ?></strong>
        </p>
    </div>

    <!-- Info Solde -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-blue-700">Montant total des frais</p>
                <p class="font-bold text-blue-900"><?= number_format($inscription['frais_inscription_montant'] ?? 0, 0, ',', ' ') ?> MGA</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-red-700">Reste à payer</p>
                <p class="text-2xl font-bold text-red-600"><?= number_format($inscription['reste_a_payer'] ?? 0, 0, ',', ' ') ?> MGA</p>
            </div>
        </div>
    </div>

    <form action="" method="POST" class="bg-white rounded-xl shadow-lg p-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Montant -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Montant à payer *</label>
                <div class="relative">
                    <input type="text" name="montant" id="montant" required
                           value="<?= $inscription['reste_a_payer'] ?? '' ?>"
                           class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 font-bold text-lg amount-format">
                    <span class="absolute right-3 top-2 text-gray-400">Ar</span>
                </div>
            </div>

            <!-- Mode Paiement -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement *</label>
                <select name="mode_paiement" required
                        class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <?php foreach ($modesPaiement as $mode): ?>
                        <option value="<?= $mode['id'] ?>"><?= htmlspecialchars($mode['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date du paiement</label>
                <input type="date" name="date_paiement" value="<?= date('Y-m-d') ?>"
                       class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>

            <!-- Référence -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Référence (Chèque, Mobile, etc.)</label>
                <input type="text" name="reference_externe" 
                       placeholder="Ex: CH-12345 ou MP-98765"
                       class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
        </div>

        <!-- Commentaire -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire (facultatif)</label>
            <textarea name="commentaire" rows="2" 
                      class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t">
            <a href="<?= url('inscriptions/details/' . $inscription['id']) ?>" 
               class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow transition flex items-center">
                <i class="fas fa-save mr-2"></i> Enregistrer le paiement
            </button>
        </div>
    </form>
</div>
