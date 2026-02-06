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
                <i class="fas fa-link text-blue-600 mr-2"></i>
                Associations Classes
            </h1>
            <p class="text-gray-600">Associez vos classes aux niveaux et séries</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('classes/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à l'enseignement</span>
            </a>
        </div>
    </div>

    <!-- Panneau de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Classes</p>
                    <p class="text-3xl font-bold mt-1" id="stat-total"><?= $stats['total_classes'] ?? 0 ?></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-door-open text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Associées</p>
                    <p class="text-3xl font-bold mt-1" id="stat-associees"><?= $stats['classes_associees'] ?? 0 ?></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Non associées</p>
                    <p class="text-3xl font-bold mt-1" id="stat-non-associees"><?= $stats['classes_non_associees'] ?? 0 ?></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Taux</p>
                    <p class="text-3xl font-bold mt-1" id="stat-taux">
                        <?php 
                        $total = $stats['total_classes'] ?? 1;
                        $associees = $stats['classes_associees'] ?? 0;
                        $taux = $total > 0 ? round(($associees / $total) * 100) : 0;
                        echo $taux . '%';
                        ?>
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Panneau de filtres -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-filter mr-2 text-blue-600"></i>
            Filtres
        </h2>
        
        <form method="GET" action="<?= url('classes/associer') ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <?php if (isset($_GET['iframe'])): ?>
                <input type="hidden" name="iframe" value="1">
            <?php endif; ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Niveau</label>
                <select name="filter_niveau" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les niveaux</option>
                    <?php foreach ($niveaux as $niveau): ?>
                        <option value="<?= $niveau['id'] ?>" <?= ($filters['niveau'] == $niveau['id']) ? 'selected' : '' ?>>
                            <?= e($niveau['libelle'] ?? $niveau['nom'] ?? 'N/A') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Série</label>
                <select name="filter_section" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les séries</option>
                    <?php foreach ($series as $serie): ?>
                        <option value="<?= $serie['id'] ?>" <?= ($filters['section'] == $serie['id']) ? 'selected' : '' ?>>
                            <?= e($serie['libelle'] ?? $serie['nom'] ?? 'N/A') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Année scolaire</label>
                <select name="filter_annee" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les années</option>
                    <?php foreach ($annees as $annee): ?>
                        <option value="<?= $annee['id'] ?>" <?= ($filters['annee'] == $annee['id']) ? 'selected' : '' ?>>
                            <?= e($annee['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" 
                       placeholder="Nom ou code..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                <a href="<?= url('classes/associer') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>

        <div class="mt-4">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" name="show_unassociated" <?= $filters['show_unassociated'] ? 'checked' : '' ?> 
                       onchange="this.form.submit()" form="filter-form" class="rounded text-blue-600">
                <span class="ml-2 text-sm text-gray-700">Afficher uniquement les classes non associées</span>
            </label>
        </div>
    </div>

    <!-- Barre d'actions groupées (masquée par défaut) -->
    <div id="bulk-actions-bar" class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 mb-6 hidden">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-square text-blue-600 text-xl"></i>
                <span class="font-semibold text-gray-800">
                    <span id="selected-count">0</span> classe(s) sélectionnée(s)
                </span>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <select id="bulk-niveau" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Assigner un niveau...</option>
                    <?php foreach ($niveaux as $niveau): ?>
                        <option value="<?= $niveau['id'] ?>">
                            <?= e($niveau['libelle'] ?? $niveau['nom'] ?? 'N/A') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select id="bulk-section" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Assigner une série...</option>
                    <option value="null">Retirer la série</option>
                    <?php foreach ($series as $serie): ?>
                        <option value="<?= $serie['id'] ?>">
                            <?= e($serie['libelle'] ?? $serie['nom'] ?? 'N/A') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button onclick="clearSelection()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Annuler
                </button>
            </div>
        </div>
    </div>

    <!-- Tableau des associations avec édition inline -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-table mr-2 text-blue-600"></i>
                    Classes (<?= count($associations) ?>)
                </h2>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="select-all" class="rounded text-blue-600" onchange="toggleSelectAll(this)">
                    <span class="ml-2 text-sm font-medium text-gray-700">Tout sélectionner</span>
                </label>
            </div>
        </div>

        <?php if (empty($associations)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-inbox text-5xl mb-4 block text-gray-300"></i>
                <p class="text-lg font-medium">Aucune classe trouvée</p>
                <p class="text-sm">Essayez de modifier vos filtres</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <span class="sr-only">Sélection</span>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Classe
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Niveau
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Série
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Année scolaire
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($associations as $asso): ?>
                            <tr class="hover:bg-gray-50 transition classe-row" data-classe-id="<?= $asso['classe_id'] ?>">
                                <!-- Checkbox -->
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="classe-checkbox rounded text-blue-600" 
                                           value="<?= $asso['classe_id'] ?>" 
                                           onchange="updateBulkActionsBar()">
                                </td>

                                <!-- Classe -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800"><?= e($asso['classe_nom']) ?></div>
                                            <div class="text-xs text-gray-500"><?= e($asso['classe_code']) ?></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Niveau (éditable inline) -->
                                <td class="px-6 py-4">
                                    <div class="relative inline-edit-container">
                                        <select class="inline-edit-select px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                                data-classe-id="<?= $asso['classe_id'] ?>"
                                                data-field="niveau_id">
                                            <option value="">-- Aucun --</option>
                                            <?php foreach ($niveaux as $niveau): ?>
                                                <option value="<?= $niveau['id'] ?>" 
                                                        <?= ($asso['niveau_id'] == $niveau['id']) ? 'selected' : '' ?>>
                                                    <?= e($niveau['libelle'] ?? $niveau['nom'] ?? 'N/A') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="spinner hidden absolute right-2 top-2">
                                            <i class="fas fa-spinner fa-spin text-blue-600"></i>
                                        </div>
                                    </div>
                                </td>

                                <!-- Série (éditable inline) -->
                                <td class="px-6 py-4">
                                    <div class="relative inline-edit-container">
                                        <select class="inline-edit-select px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                                data-classe-id="<?= $asso['classe_id'] ?>"
                                                data-field="serie_id">
                                            <option value="">-- Aucune --</option>
                                            <?php foreach ($series as $serie): ?>
                                                <option value="<?= $serie['id'] ?>" 
                                                        <?= ($asso['serie_id'] == $serie['id']) ? 'selected' : '' ?>>
                                                    <?= e($serie['libelle'] ?? $serie['nom'] ?? 'N/A') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="spinner hidden absolute right-2 top-2">
                                            <i class="fas fa-spinner fa-spin text-blue-600"></i>
                                        </div>
                                    </div>
                                </td>

                                <!-- Année scolaire (éditable inline) -->
                                <td class="px-6 py-4">
                                    <div class="relative inline-edit-container">
                                        <select class="inline-edit-select px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                                data-classe-id="<?= $asso['classe_id'] ?>"
                                                data-field="annee_scolaire_id">
                                            <option value="">-- Aucune --</option>
                                            <?php foreach ($annees as $annee): ?>
                                                <option value="<?= $annee['id'] ?>" 
                                                        <?= ($asso['annee_id'] == $annee['id']) ? 'selected' : '' ?>>
                                                    <?= e($annee['libelle']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="spinner hidden absolute right-2 top-2">
                                            <i class="fas fa-spinner fa-spin text-blue-600"></i>
                                        </div>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <a href="<?= url('classes/edit/' . $asso['classe_id']) ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                       class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition"
                                       title="Modifier la classe">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script src="<?= url('public/assets/js/associer.js') ?>"></script>
<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
