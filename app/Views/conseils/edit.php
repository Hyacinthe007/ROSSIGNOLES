<?php
$title = "Modifier le Conseil";
$breadcrumbs = [
    ['label' => 'Évaluations', 'url' => '#'],
    ['label' => 'Conseils de classe', 'url' => url('conseils/list')],
    ['label' => 'Saisie des résultats']
];
?>

<div class="p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-edit text-indigo-600 mr-2"></i>
                    Résultats du Conseil
                </h1>
                <p class="text-gray-600">Saisissez le compte-rendu et les statistiques du conseil de classe.</p>
            </div>
            <a href="<?= url('conseils/details/' . $conseil['id']) ?>" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl hover:bg-gray-200 transition flex items-center gap-2">
                <i class="fas fa-eye"></i>
                Vue détails
            </a>
        </div>

        <form action="<?= url('conseils/edit/' . $conseil['id']) ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sidebar info -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-indigo-500"></i>
                            Contexte
                        </h3>
                        <div class="space-y-4 text-sm">
                            <div>
                                <span class="text-gray-400 block">Classe</span>
                                <span class="font-semibold text-gray-700">
                                    <?php 
                                    foreach ($classes as $c) {
                                        if ($c['id'] == $conseil['classe_id']) echo e($c['nom']);
                                    }
                                    ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Période</span>
                                <span class="font-semibold text-gray-700">
                                    <?php 
                                    foreach ($periodes as $p) {
                                        if ($p['id'] == $conseil['periode_id']) echo e($p['nom']);
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="space-y-2">
                                <label for="date_conseil" class="text-gray-400 block">Date du conseil</label>
                                <input type="date" name="date_conseil" id="date_conseil" value="<?= $conseil['date_conseil'] ?>"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label for="heure_debut" class="text-xs text-gray-400">Début</label>
                                    <input type="time" name="heure_debut" id="heure_debut" value="<?= $conseil['heure_debut'] ?>"
                                        class="w-full px-2 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-700">
                                </div>
                                <div class="space-y-1">
                                    <label for="heure_fin" class="text-xs text-gray-400">Fin</label>
                                    <input type="time" name="heure_fin" id="heure_fin" value="<?= $conseil['heure_fin'] ?>"
                                        class="w-full px-2 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-700">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-flag text-indigo-500"></i>
                            Statut
                        </h3>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="statut" value="planifie" <?= $conseil['statut'] == 'planifie' ? 'checked' : '' ?> class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Planifié</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="statut" value="en_cours" <?= $conseil['statut'] == 'en_cours' ? 'checked' : '' ?> class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                <span class="text-sm font-medium text-gray-700">En cours</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="statut" value="termine" <?= $conseil['statut'] == 'termine' ? 'checked' : '' ?> class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Terminé</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Main content -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        <h3 class="font-bold text-gray-800 border-b border-gray-50 pb-4">Résumé & Statistiques</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="moyenne_classe" class="block text-sm font-medium text-gray-700">Moyenne de classe</label>
                                <input type="number" step="0.01" name="moyenne_classe" id="moyenne_classe" value="<?= $conseil['moyenne_classe'] ?>"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div class="space-y-2">
                                <label for="taux_reussite" class="block text-sm font-medium text-gray-700">Taux de réussite (%)</label>
                                <input type="number" step="0.1" name="taux_reussite" id="taux_reussite" value="<?= $conseil['taux_reussite'] ?>"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="nb_felicitations" class="block text-sm font-medium text-gray-700">Félicitations</label>
                                <input type="number" name="nb_felicitations" id="nb_felicitations" value="<?= $conseil['nb_felicitations'] ?>"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div class="space-y-2">
                                <label for="nb_encouragements" class="block text-sm font-medium text-gray-700">Encouragements</label>
                                <input type="number" name="nb_encouragements" id="nb_encouragements" value="<?= $conseil['nb_encouragements'] ?>"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="appreciation_generale" class="block text-sm font-medium text-gray-700">Appréciation générale du conseil</label>
                            <textarea name="appreciation_generale" id="appreciation_generale" rows="5"
                                placeholder="Synthèse globale du climat et des résultats de la classe..."
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 transition"><?= e($conseil['appreciation_generale']) ?></textarea>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        <h3 class="font-bold text-gray-800 border-b border-gray-50 pb-4">Intervenants</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="president_conseil" class="block text-sm font-medium text-gray-700">Président du conseil</label>
                                <select name="president_conseil" id="president_conseil" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 transition">
                                    <option value="">Sélectionner...</option>
                                    <?php foreach ($personnels as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= $p['id'] == $conseil['president_conseil'] ? 'selected' : '' ?>><?= e($p['nom'] . ' ' . $p['prenom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="secretaire" class="block text-sm font-medium text-gray-700">Secrétaire</label>
                                <select name="secretaire" id="secretaire" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 transition">
                                    <option value="">Sélectionner...</option>
                                    <?php foreach ($personnels as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= $p['id'] == $conseil['secretaire'] ? 'selected' : '' ?>><?= e($p['nom'] . ' ' . $p['prenom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="<?= url('conseils/list') ?>" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-xl transition font-semibold shadow-lg shadow-indigo-200 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    Enregistrer les résultats
                </button>
            </div>
        </form>
    </div>
</div>
