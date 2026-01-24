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
                <i class="fas fa-layer-group text-green-600 mr-2"></i>
                Gestion des niveaux
            </h1>
            <p class="text-gray-600">Organisation des niveaux scolaires</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('pedagogie/cycles') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md">
                <i class="fas fa-circle-notch"></i>
                <span>Gérer les Cycles</span>
            </a>
            <a href="<?= url('classes/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à l'enseignement</span>
            </a>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Ordre</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Libellé</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($niveaux)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucun niveau trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($niveaux as $niveau): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= $niveau['ordre'] ?? '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    <?= e($niveau['libelle'] ?? 'N/A') ?>
                                    <span class="ml-2 px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-500 rounded uppercase tracking-wider">
                                        <?= e($niveau['cycle_nom'] ?? 'Cycle inconnu') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= e($niveau['code'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= e($niveau['description'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= ($niveau['actif'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= ($niveau['actif'] ?? 0) ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= url('pedagogie/niveaux/coefficients/' . $niveau['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                       class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Coefficients">
                                        <i class="fas fa-percentage"></i>
                                    </a>
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
