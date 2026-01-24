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
    <body class="bg-gray-50">
<?php endif; ?>

<div class="p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas <?= isset($event) ? 'fa-edit text-blue-600' : 'fa-plus-circle text-orange-500' ?> mr-2"></i>
                    <?= isset($event) ? 'Modifier l\'événement' : 'Nouvel événement' ?>
                </h1>
                <p class="text-gray-500"><?= isset($event) ? "Mettre à jour les détails de l'événement" : "Ajouter un nouveau repos ou événement au calendrier" ?></p>
            </div>
            <a href="<?= url('calendrier/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?><?= isset($event) ? '&annee_id='.$event['annee_scolaire_id'] : (isset($_GET['annee_id']) ? '&annee_id='.$_GET['annee_id'] : '') ?>" class="text-gray-500 hover:text-gray-700 transition flex items-center gap-2 font-medium">
                <i class="fas fa-times"></i>
                <span>Annuler</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <form action="<?= isset($event) ? url('calendrier/edit/' . $event['id']) . (isset($_GET['iframe']) ? '?iframe=1' : '') : url('calendrier/add') . (isset($_GET['iframe']) ? '?iframe=1' : '') ?>" method="POST" class="p-8">
                
                <div class="space-y-6">
                    <!-- Sélection de l'année -->
                    <div>
                        <label for="annee_scolaire_id" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">
                            Année Scolaire
                        </label>
                        <select id="annee_scolaire_id" name="annee_scolaire_id" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            <?php foreach ($annees as $a): ?>
                                <option value="<?= $a['id'] ?>" <?= (isset($event) && $event['annee_scolaire_id'] == $a['id']) || (!isset($event) && isset($_GET['annee_id']) && $_GET['annee_id'] == $a['id']) || (!isset($event) && !isset($_GET['annee_id']) && $a['actif']) ? 'selected' : '' ?>>
                                    <?= e($a['libelle']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Type d'événement -->
                        <div>
                            <label for="type" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">
                                Type d'événement
                            </label>
                            <select id="type" name="type" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                                <option value="vacances" <?= (isset($event) && $event['type'] == 'vacances') ? 'selected' : '' ?>>Vacances</option>
                                <option value="ferie" <?= (isset($event) && $event['type'] == 'ferie') ? 'selected' : '' ?>>Jour Férié</option>
                                <option value="pont" <?= (isset($event) && $event['type'] == 'pont') ? 'selected' : '' ?>>Pont</option>
                                <option value="examen" <?= (isset($event) && $event['type'] == 'examen') ? 'selected' : '' ?>>Période d'examen</option>
                                <option value="autre" <?= (isset($event) && $event['type'] == 'autre') ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>

                        <!-- Libellé -->
                        <div>
                            <label for="libelle" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">
                                Nom de l'événement
                            </label>
                            <input type="text" id="libelle" name="libelle" placeholder="Ex: Vacances de Noël, Lundi de Pâques..."
                                   value="<?= e($event['libelle'] ?? '') ?>" required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date début -->
                        <div>
                            <label for="date_debut" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">
                                Date de début
                            </label>
                            <input type="date" id="date_debut" name="date_debut" required
                                   value="<?= $event['date_debut'] ?? '' ?>"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        </div>

                        <!-- Date fin -->
                        <div>
                            <label for="date_fin" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">
                                Date de fin
                            </label>
                            <input type="date" id="date_fin" name="date_fin" required
                                   value="<?= $event['date_fin'] ?? '' ?>"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">
                            Description / Notes
                        </label>
                        <textarea id="description" name="description" rows="2"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none"><?= e($event['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Bloque cours -->
                    <div class="p-4 bg-orange-50 rounded-xl border border-orange-100">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="bloque_cours" value="1" class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500" <?= (!isset($event) || $event['bloque_cours']) ? 'checked' : '' ?>>
                            <div class="ml-3">
                                <span class="block text-sm font-bold text-orange-900 group-hover:text-orange-700 transition-colors">Interrompre les cours</span>
                                <span class="block text-xs text-orange-700">Si coché, l'emploi du temps affichera un message de repos pour ces dates.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-10">
                    <button type="submit" 
                            class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-orange-500/25 transform hover:-translate-y-1 flex items-center justify-center gap-3 text-lg">
                        <i class="fas fa-save"></i>
                        <span><?= isset($event) ? 'Mettre à jour' : 'Enregistrer l\'événement' ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
