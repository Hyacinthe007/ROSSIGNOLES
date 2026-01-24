<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/header.php'; ?>
<?php else: ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>body { font-family: 'Outfit', sans-serif; }</style>
    </head>
    <body class="bg-white">
<?php endif; ?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-umbrella-beach text-orange-500 mr-2"></i>
                Vacances & Jours Fériés
            </h1>
            <p class="text-gray-600">Planification des interruptions de cours pour l'année <?= e($annee['libelle'] ?? 'N/A') ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('calendrier/add') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?><?= isset($annee) ? '&annee_id='.$annee['id'] : '' ?>" 
               class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-lg font-bold">
                <i class="fas fa-plus"></i>
                <span>Ajouter un événement</span>
            </a>
        </div>
    </div>

    <!-- Sélecteur d'année -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100 flex items-center justify-between">
        <form method="GET" action="<?= url('calendrier/list') ?>" class="flex items-center gap-4">
            <?php if (isset($_GET['iframe']) && $_GET['iframe'] == '1'): ?>
                <input type="hidden" name="iframe" value="1">
            <?php endif; ?>
            <label class="text-sm font-medium text-gray-700">Année scolaire :</label>
            <select name="annee_id" onchange="this.form.submit()" class="border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <?php foreach($annees as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= (isset($annee) && $a['id'] == $annee['id']) ? 'selected' : '' ?>>
                        <?= e($a['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Messages Flash -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-700 font-medium"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Liste des événements -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-orange-500 to-orange-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Événement</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Période</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Bloque les cours</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 ">
                                <i class="fas fa-calendar-day text-4xl mb-4 block opacity-20"></i>
                                Aucun événement planifié pour cette année scolaire.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                    if ($event['type'] == 'vacances') $badgeClass = 'bg-blue-100 text-blue-800';
                                    if ($event['type'] == 'ferie') $badgeClass = 'bg-red-100 text-red-800';
                                    ?>
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-full <?= $badgeClass ?>">
                                        <?= e($event['type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900"><?= e($event['libelle']) ?></div>
                                    <?php if ($event['description']): ?>
                                        <div class="text-xs text-gray-500"><?= e($event['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="far fa-calendar-alt text-gray-400"></i>
                                        <span>Du <?= date('d/m/Y', strtotime($event['date_debut'])) ?> au <?= date('d/m/Y', strtotime($event['date_fin'])) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($event['bloque_cours']): ?>
                                        <span class="text-red-500 flex items-center gap-1 text-xs font-bold">
                                            <i class="fas fa-lock"></i> OUI
                                        </span>
                                    <?php else: ?>
                                        <span class="text-green-500 flex items-center gap-1 text-xs font-bold">
                                            <i class="fas fa-unlock"></i> NON
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('calendrier/edit/' . $event['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('calendrier/delete/' . $event['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition" 
                                           title="Supprimer"
                                           onclick="return confirm('Supprimer cet événement ?')">
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
