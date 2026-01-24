<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/header.php'; ?>
<?php else: ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>
    <body class="bg-white">
<?php endif; ?>

<div class="p-4 md:p-8">
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-circle-notch text-orange-600 mr-2"></i>
                Gestion des cycles
            </h1>
            <p class="text-gray-600">Organisation des cycles scolaires</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('pedagogie/niveaux') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md">
                <i class="fas fa-layer-group"></i>
                <span>Gérer les Niveaux</span>
            </a>
            <a href="<?= url('classes/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à l'enseignement</span>
            </a>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-orange-600 to-orange-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Ordre</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Libellé</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($cycles)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucun cycle trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cycles as $cycle): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= $cycle['ordre'] ?? '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    <?= e($cycle['libelle'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= e($cycle['code'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= e($cycle['description'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= ($cycle['actif'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= ($cycle['actif'] ?? 0) ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
