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
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-shopping-bag text-blue-600 mr-2"></i>
                Articles Scolaires
            </h1>
            <p class="text-gray-600">
                Gestion des articles optionnels (Logo, Tee-shirt, Carnet, Bus, etc.)
            </p>
        </div>
        <a href="<?= url('articles/nouveau') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
            <i class="fas fa-plus"></i>
            <span>Nouvel Article</span>
        </a>
    </div>

    <!-- Filtre par année -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="<?= url('articles/liste') ?>" class="flex items-center gap-4">
            <?php if (!empty($_GET['iframe'])): ?>
                <input type="hidden" name="iframe" value="1">
            <?php endif; ?>
            
            <label class="text-sm font-medium text-gray-700">Année scolaire :</label>
            <select name="annee_id" onchange="this.form.submit()" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Toutes les années</option>
                <?php foreach ($annees as $annee): ?>
                    <option value="<?= $annee['id'] ?>" <?= ($selectedAnnee == $annee['id']) ? 'selected' : '' ?>>
                        <?= e($annee['libelle']) ?>
                        <?= $annee['actif'] ? ' (Active)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Liste des articles -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <?php if (empty($articles)): ?>
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-inbox text-6xl mb-4 text-gray-300"></i>
                <p class="text-lg">Aucun article trouvé</p>
                <p class="text-sm mt-2">Commencez par créer votre premier article scolaire</p>
            </div>
        <?php else: ?>
            <table class="w-full">
                <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left font-medium">Code</th>
                        <th class="px-6 py-4 text-left font-medium">Libellé</th>
                        <th class="px-6 py-4 text-left font-medium">Type</th>
                        <th class="px-6 py-4 text-right font-medium">Prix Unitaire</th>
                        <th class="px-6 py-4 text-center font-medium">Obligatoire</th>
                        <th class="px-6 py-4 text-center font-medium">Statut</th>
                        <th class="px-6 py-4 text-center font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($articles as $article): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
                                    <?= e($article['code']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <?= e($article['libelle']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $typeLabels = [
                                    'tenue_sport' => 'Tenue Sport',
                                    'tenue_fete' => 'Tenue Fête',
                                    'fourniture' => 'Fourniture',
                                    'uniforme' => 'Uniforme',
                                    'autre' => 'Autre'
                                ];
                                $typeColors = [
                                    'tenue_sport' => 'bg-green-100 text-green-800',
                                    'tenue_fete' => 'bg-purple-100 text-purple-800',
                                    'fourniture' => 'bg-blue-100 text-blue-800',
                                    'uniforme' => 'bg-indigo-100 text-indigo-800',
                                    'autre' => 'bg-gray-100 text-gray-800'
                                ];
                                $type = $article['type_article'] ?? 'autre';
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-medium <?= $typeColors[$type] ?>">
                                    <?= $typeLabels[$type] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <?php if (!empty($article['prix_unitaire'])): ?>
                                    <span class="font-semibold text-gray-900">
                                        <?= number_format($article['prix_unitaire'], 0, ',', ' ') ?> Ar
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">Non défini</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($article['obligatoire']): ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Obligatoire
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                        Optionnel
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($article['actif']): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1"></i>Actif
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                        <i class="fas fa-times-circle mr-1"></i>Inactif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?= url('articles/modifier/' . $article['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                       class="text-blue-600 hover:text-blue-800 transition" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmerSuppression(<?= $article['id'] ?>)" 
                                            class="text-red-600 hover:text-red-800 transition" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmerSuppression(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        window.location.href = '<?= url('articles/supprimer/') ?>' + id + '<?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>';
    }
}
</script>

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
