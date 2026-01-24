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
                Modifier l'année scolaire
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Modifier les informations de l'année scolaire</p>
        </div>
        <a href="<?= url('annees-scolaires/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la configuration</span>
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border">
        <form method="POST" action="<?= url('annees-scolaires/edit/' . $annee['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>">
            <?= csrf_field() ?>
            
            <div class="space-y-6">
                <!-- Statut actuel -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">
                            <i class="fas fa-info-circle mr-2"></i>Statut actuel :
                        </span>
                        <?php if ($annee['actif']): ?>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-circle mr-1"></i>Inactive
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Libellé -->
                <div>
                    <label for="libelle" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-gray-500"></i>Libellé *
                    </label>
                    <input type="text" id="libelle" name="libelle" required
                           value="<?= e($annee['libelle']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date de début -->
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-day mr-2 text-gray-500"></i>Date de début *
                        </label>
                        <input type="date" id="date_debut" name="date_debut" required
                               value="<?= e($annee['date_debut']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Date de fin -->
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-check mr-2 text-gray-500"></i>Date de fin *
                        </label>
                        <input type="date" id="date_fin" name="date_fin" required
                               value="<?= e($annee['date_fin']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-8 border-t">
                <a href="<?= url('annees-scolaires/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition shadow-md flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour à la configuration</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer les modifications</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    
    // Validation des dates
    dateFin.addEventListener('change', function() {
        if (dateDebut.value && dateFin.value && dateFin.value <= dateDebut.value) {
            alert('La date de fin doit être après la date de début');
            dateFin.value = '<?= e($annee['date_fin']) ?>';
        }
    });
    
    dateDebut.addEventListener('change', function() {
        if (dateDebut.value && dateFin.value && dateFin.value <= dateDebut.value) {
            alert('La date de fin doit être après la date de début');
            dateDebut.value = '<?= e($annee['date_debut']) ?>';
        }
    });
});
</script>

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
