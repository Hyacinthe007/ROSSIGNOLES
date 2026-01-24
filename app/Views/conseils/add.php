<?php
$title = "Planifier un Conseil";
$breadcrumbs = [
    ['label' => 'Évaluations', 'url' => '#'],
    ['label' => 'Conseils de classe', 'url' => url('conseils/list')],
    ['label' => 'Nouveau Conseil']
];
?>

<div class="p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-calendar-plus text-indigo-600 mr-2"></i>
                Planifier un Conseil de Classe
            </h1>
            <p class="text-gray-600">Planifiez la tenue d'un conseil de classe pour une classe et une période données.</p>
        </div>

        <form action="<?= url('conseils/add') ?>" method="POST" class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="classe_id" class="block text-sm font-medium text-gray-700">Classe <span class="text-red-500">*</span></label>
                        <select name="classe_id" id="classe_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Sélectionner une classe</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="annee_scolaire_id" class="block text-sm font-medium text-gray-700">Année Scolaire <span class="text-red-500">*</span></label>
                        <select name="annee_scolaire_id" id="annee_scolaire_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <?php foreach ($annees as $a): ?>
                                <option value="<?= $a['id'] ?>" <?= $a['id'] == ($_SESSION['active_annee_id'] ?? '') ? 'selected' : '' ?>><?= e($a['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="periode_id" class="block text-sm font-medium text-gray-700">Période <span class="text-red-500">*</span></label>
                        <select name="periode_id" id="periode_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <?php foreach ($periodes as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= e($p['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="date_conseil" class="block text-sm font-medium text-gray-700">Date prévue <span class="text-red-500">*</span></label>
                        <input type="date" name="date_conseil" id="date_conseil" required value="<?= date('Y-m-d') ?>"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="president_conseil" class="block text-sm font-medium text-gray-700">Président du conseil</label>
                        <select name="president_conseil" id="president_conseil" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Sélectionner...</option>
                            <?php foreach ($personnels as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= e($p['nom'] . ' ' . $p['prenom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="secretaire" class="block text-sm font-medium text-gray-700">Secrétaire</label>
                        <select name="secretaire" id="secretaire" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Sélectionner...</option>
                            <?php foreach ($personnels as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= e($p['nom'] . ' ' . $p['prenom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="ordre_du_jour" class="block text-sm font-medium text-gray-700">Ordre du jour</label>
                    <textarea name="ordre_du_jour" id="ordre_du_jour" rows="4" placeholder="Points à aborder lors du conseil..."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="<?= url('conseils/list') ?>" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-xl transition font-semibold shadow-lg shadow-indigo-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Planifier le conseil
                </button>
            </div>
        </form>
    </div>
</div>
