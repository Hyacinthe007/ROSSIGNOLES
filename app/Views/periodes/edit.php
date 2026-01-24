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
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Modifier la Période
            </h1>
            <p class="text-gray-600">Modification des dates et paramètres de la période</p>
        </div>
        <a href="<?= url('periodes/list?annee_id=' . $periode['annee_scolaire_id']) ?><?= !empty($_GET['iframe']) ? '&iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la configuration</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
        <form method="POST" action="<?= url('periodes/edit/' . $periode['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Libellé de la période</label>
                <input type="text" name="nom" required 
                       value="<?= e($periode['nom']) ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Numéro d'ordre</label>
                    <input type="number" name="numero" required min="1" max="10" 
                           value="<?= e($periode['numero']) ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="flex items-end pb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="actif" value="1" <?= $periode['actif'] ? 'checked' : '' ?>
                               class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-semibold text-gray-700">Période active</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date de début</label>
                    <input type="date" name="date_debut" required 
                           value="<?= e($periode['date_debut']) ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
                    <input type="date" name="date_fin" required 
                           value="<?= e($periode['date_fin']) ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="pt-6 border-t flex gap-3">
                <a href="<?= url('periodes/list?annee_id=' . $periode['annee_scolaire_id']) ?><?= !empty($_GET['iframe']) ? '&iframe=1' : '' ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition shadow-md flex items-center gap-2 font-bold justify-center flex-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour à la configuration</span>
                </a>
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
