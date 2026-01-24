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
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Ajouter une matière
            </h1>
            <p class="text-gray-600">Créez une nouvelle matière</p>
        </div>
        <a href="<?= url('matieres/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la configuration</span>
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('matieres/add') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" class="space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2 text-gray-500"></i>Code
                        <span class="text-xs text-gray-500 ml-2">(Généré automatiquement si vide)</span>
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="<?= e($code_auto ?? '') ?>"
                           readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                           placeholder="Ex: MATIERE-2024-001">
                </div>

                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-gray-500"></i>Nom *
                    </label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ex: Mathématiques">
                </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2 text-gray-500"></i>Description
                </label>
                <textarea id="description" 
                          name="description"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Description de la matière..."></textarea>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-8 border-t">
                <a href="<?= url('matieres/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition shadow-md flex items-center gap-2 font-bold justify-center flex-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour à la configuration</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer la matière</span>
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

