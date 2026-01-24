<?php
$title = "Modèles de Notifications";
$breadcrumbs = [
    ['label' => 'Communication', 'url' => '#'],
    ['label' => 'Modèles']
];
?>

    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-copy text-blue-600 mr-2"></i>
                Modèles de Notifications
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gérez vos messages types pour gagner du temps lors des envois.</p>
        </div>
        <div>
            <a href="<?= url('notifications/modeles/add') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg font-bold">
                <i class="fas fa-plus"></i>
                <span>Nouveau Modèle</span>
            </a>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Nom du Modèle</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Sujet</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($modeles)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 ">
                                <i class="fas fa-copy text-4xl mb-4 block opacity-20"></i>
                                Aucun modèle configuré.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($modeles as $modele): ?>
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="bg-blue-50 text-blue-600 p-2 rounded-lg mr-3">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <span class="text-sm font-bold text-gray-800"><?= e($modele['nom']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= e($modele['sujet']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-full <?= ($modele['type']??'info') === 'urgent' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' ?>">
                                        <?= e($modele['type'] ?? 'info') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?= url('notifications/modeles/edit/' . $modele['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('notifications/modeles/delete/' . $modele['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition" title="Supprimer"
                                           onclick="return confirm('Supprimer ce modèle ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.4s ease-out forwards;
}
</style>

