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
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                <?= e($annee['libelle']) ?>
            </h1>
            <p class="text-gray-600 text-sm md:text-base">
                <?= formatDate($annee['date_debut']) ?> - <?= formatDate($annee['date_fin']) ?>
            </p>
        </div>
        <a href="<?= url('annees-scolaires/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la configuration</span>
        </a>
    </div>
        <div class="flex gap-2">
            <?php if (!$annee['actif']): ?>
                <a href="<?= url('annees-scolaires/activate/' . $annee['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2"
                   onclick="return confirm('Activer cette année scolaire ? Cela désactivera l\'année actuellement active.')">
                    <i class="fas fa-toggle-on"></i>
                    <span>Activer</span>
                </a>
            <?php endif; ?>
            <a href="<?= url('periodes/list?annee_id=' . $annee['id']) ?><?= !empty($_GET['iframe']) ? '&iframe=1' : '' ?>" 
               class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-clock"></i>
                <span>Gérer les périodes</span>
            </a>
            <a href="<?= url('annees-scolaires/edit/' . $annee['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Modifier</span>
            </a>
        </div>
    </div>

    <!-- Statut -->
    <div class="mb-6">
        <?php if ($annee['actif']): ?>
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                    <div>
                        <p class="text-green-800 font-semibold">Année Scolaire Active</p>
                        <p class="text-green-700 text-sm mt-1">
                            Cette année est actuellement utilisée pour les nouvelles inscriptions
                        </p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-circle text-gray-600 text-2xl mr-3"></i>
                    <div>
                        <p class="text-gray-800 font-semibold">Année Scolaire Inactive</p>
                        <p class="text-gray-700 text-sm mt-1">
                            Cette année n'est pas utilisée pour les nouvelles inscriptions
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Inscriptions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Inscriptions</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $statistiques['total_inscriptions'] ?></p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Élèves Actifs -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Élèves Actifs</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $statistiques['total_eleves_actifs'] ?></p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-user-graduate text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Classes -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Classes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $statistiques['total_classes'] ?></p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-door-open text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Finances -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Frais</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2"><?= number_format($statistiques['total_frais'], 0, ',', ' ') ?> MGA</p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-money-bill-wave text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails Financiers -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-green-600 mr-2"></i>
            Situation Financière
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="border-l-4 border-green-500 pl-4">
                <p class="text-sm text-gray-600">Total Payé</p>
                <p class="text-2xl font-bold text-green-600"><?= number_format($statistiques['total_paye'], 0, ',', ' ') ?> MGA</p>
            </div>
            <div class="border-l-4 border-orange-500 pl-4">
                <p class="text-sm text-gray-600">Reste à Payer</p>
                <p class="text-2xl font-bold text-orange-600"><?= number_format($statistiques['reste_a_payer'], 0, ',', ' ') ?> MGA</p>
            </div>
            <div class="border-l-4 border-blue-500 pl-4">
                <p class="text-sm text-gray-600">Taux de Recouvrement</p>
                <?php 
                $taux = $statistiques['total_frais'] > 0 
                    ? round(($statistiques['total_paye'] / $statistiques['total_frais']) * 100, 1) 
                    : 0;
                ?>
                <p class="text-2xl font-bold text-blue-600"><?= $taux ?>%</p>
            </div>
        </div>
    </div>

    <!-- Liste des Classes -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-door-open text-purple-600 mr-2"></i>
                Classes de l'année <?= e($annee['libelle']) ?>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Classe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Code
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre d'élèves
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucune classe pour cette année scolaire
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $classe): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900"><?= e($classe['nom']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-500"><?= e($classe['code'] ?? 'N/A') ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <?= $classe['nb_eleves'] ?> élèves
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= url('classes/details/' . $classe['id']) ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bouton Retour -->
    <div class="mt-6">
        <a href="<?= url('annees-scolaires/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition shadow-md">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la configuration</span>
        </a>
    </div>
</div>

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
