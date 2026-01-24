<div class="p-4 md:p-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
            <div class="text-center mb-6">
                <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Désactiver le membre</h1>
                <p class="text-gray-600">Êtes-vous sûr de vouloir désactiver ce membre du personnel ?</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="font-semibold text-gray-800"><?= e($personnel['prenom'] . ' ' . $personnel['nom']) ?></p>
                <p class="text-sm text-gray-600">Matricule: <?= e($personnel['matricule']) ?></p>
                <p class="text-sm text-gray-600">Poste: <?= e($personnel['poste'] ?: 'N/A') ?></p>
            </div>

            <form method="POST" action="<?= url('personnel/delete/' . $personnel['id']) ?>" class="flex flex-col sm:flex-row gap-4">
                <?= csrf_field() ?>
                <button type="submit" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-ban"></i>
                    <span>Oui, désactiver</span>
                </button>
                <a href="<?= url('personnel/details/' . $personnel['id']) ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
            </form>
        </div>
    </div>
</div>

