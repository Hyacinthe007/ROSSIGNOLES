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
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-clock text-teal-600 mr-2"></i>
                Gestion des Périodes
            </h1>
            <p class="text-gray-600">Trimestres ou Semestres pour l'année <?= e($annee['libelle']) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('periodes/add?annee_id=' . $annee['id']) ?><?= !empty($_GET['iframe']) ? '&iframe=1' : '' ?>" 
               class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-lg font-bold">
                <i class="fas fa-plus"></i>
                <span>Ajouter une période</span>
            </a>
            <a href="<?= url('annees-scolaires/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à la configuration</span>
            </a>
        </div>
    </div>

    <!-- Sélecteur d'année -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <form method="GET" action="<?= url('periodes/list') ?>" class="flex items-center gap-4">
            <?php if (isset($_GET['iframe']) && $_GET['iframe'] == '1'): ?>
                <input type="hidden" name="iframe" value="1">
            <?php endif; ?>
            <label class="text-sm font-medium text-gray-700">Changer d'année :</label>
            <select name="annee_id" onchange="this.form.submit()" class="border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                <?php foreach($annees as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= $a['id'] == $annee['id'] ? 'selected' : '' ?>>
                        <?= e($a['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Liste des périodes -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-teal-600 to-teal-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Ordre</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Période</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Date début</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Date fin</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($periodes)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 ">
                                <i class="fas fa-calendar-times text-4xl mb-4 block opacity-20"></i>
                                Aucune période configurée pour cette année scolaire.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($periodes as $p): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                                    #<?= e($p['numero']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900"><?= e($p['nom']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= formatDate($p['date_debut']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= formatDate($p['date_fin']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($p['actif']): ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-400">
                                            Fermé
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('periodes/edit/' . $p['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-teal-600 hover:bg-teal-50 p-2 rounded-lg transition" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('periodes/delete/' . $p['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition" 
                                           title="Supprimer"
                                           onclick="return confirm('Supprimer cette période ?')">
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
</div>

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>

