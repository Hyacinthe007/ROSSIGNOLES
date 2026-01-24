<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-tags text-blue-600 mr-2"></i>
                Types de Frais
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des types de frais scolaires</p>
        </div>
        <a href="<?= url('finance/types-frais/add') ?>" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nouveau type</span>
        </a>
    </div>

    <!-- Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?= $_SESSION['success'] ?>
        <?php unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <!-- Liste des types -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <?php if (empty($typesFrais)): ?>
        <div class="text-center py-12">
            <i class="fas fa-tags text-gray-400 text-6xl mb-4"></i>
            <p class="text-lg font-semibold text-gray-800 mb-2">Aucun type de frais</p>
            <p class="text-sm text-gray-500 mb-4">Commencez par ajouter un type de frais</p>
            <a href="<?= url('finance/types-frais/add') ?>" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus"></i>
                Ajouter un type
            </a>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Libellé</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($typesFrais as $type): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-800"><?= e($type['id']) ?></td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800"><?= e($type['libelle']) ?></p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <?= e($type['description'] ?? '-') ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= url('finance/types-frais/edit/' . $type['id']) ?>" 
                                   class="text-blue-600 hover:text-blue-800" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?= url('finance/types-frais/delete/' . $type['id']) ?>" 
                                      class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type de frais ?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
