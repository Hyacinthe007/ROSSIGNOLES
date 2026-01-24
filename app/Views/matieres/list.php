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
                <i class="fas fa-book text-pink-600 mr-2"></i>
                Liste des matières
            </h1>
            <p class="text-gray-600">Gestion des matières enseignées</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('matieres/add') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-bold">
                <i class="fas fa-plus"></i>
                <span>Ajouter une matière</span>
            </a>
            <a href="<?= url('classes/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à l'enseignement</span>
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <div class="flex">
                <div class="py-1"><i class="fas fa-check-circle mr-3"></i></div>
                <div>
                    <p class="font-bold">Succès</p>
                    <p class="text-sm"><?= $_SESSION['success_message'] ?></p>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <div class="flex">
                <div class="py-1"><i class="fas fa-exclamation-circle mr-3"></i></div>
                <div>
                    <p class="font-bold">Erreur</p>
                    <p class="text-sm"><?= $_SESSION['error_message'] ?></p>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-pink-600 to-pink-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($matieres)): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucune matière trouvée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($matieres as $matiere): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= e($matiere['code']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    <?= e($matiere['nom']) ?>
                                </td>
                                <!--
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                                        <?php // e($matiere['coefficient']) ?>
                                    </span>
                                </td>
                                -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('matieres/edit/' . $matiere['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-pink-600 hover:text-pink-900 p-2 hover:bg-pink-50 rounded transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('matieres/delete/' . $matiere['id']) ?><?= !empty($_GET['iframe']) ? '&iframe=1' : '' ?>" 
                                           class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded transition">
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

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
