<?php
$title = "Modifier l'Annonce";
$breadcrumbs = [
    ['label' => 'Communication', 'url' => '#'],
    ['label' => 'Annonces', 'url' => url('annonces/list')],
    ['label' => 'Modifier l\'annonce']
];
?>

<div class="p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-edit text-indigo-600 mr-2"></i>
                    Modifier l'annonce
                </h1>
                <p class="text-gray-600">Mettez à jour les informations de votre annonce.</p>
            </div>
            <a href="<?= url('annonces/delete/' . $annonce['id']) ?>" onclick="return confirm('Supprimer cette annonce ?')" class="bg-red-50 text-red-600 p-3 rounded-xl hover:bg-red-100 transition">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>

        <form action="<?= url('annonces/edit/' . $annonce['id']) ?>" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div class="space-y-2">
                    <label for="titre" class="block text-sm font-medium text-gray-700">Titre de l'annonce <span class="text-red-500">*</span></label>
                    <input type="text" name="titre" id="titre" required value="<?= e($annonce['titre']) ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="type" class="block text-sm font-medium text-gray-700">Type d'annonce</label>
                        <select name="type" id="type" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="generale" <?= $annonce['type'] == 'generale' ? 'selected' : '' ?>>Générale</option>
                            <option value="urgente" <?= $annonce['type'] == 'urgente' ? 'selected' : '' ?>>Urgente</option>
                            <option value="administrative" <?= $annonce['type'] == 'administrative' ? 'selected' : '' ?>>Administrative</option>
                            <option value="pedagogique" <?= $annonce['type'] == 'pedagogique' ? 'selected' : '' ?>>Pédagogique</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="cible" class="block text-sm font-medium text-gray-700">Cible de la diffusion</label>
                        <select name="cible" id="cible" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="tous" <?= $annonce['cible'] == 'tous' ? 'selected' : '' ?>>Tout le monde</option>
                            <option value="parents" <?= $annonce['cible'] == 'parents' ? 'selected' : '' ?>>Parents uniquement</option>
                            <option value="enseignants" <?= $annonce['cible'] == 'enseignants' ? 'selected' : '' ?>>Enseignants uniquement</option>
                            <option value="eleves" <?= $annonce['cible'] == 'eleves' ? 'selected' : '' ?>>Élèves uniquement</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="contenu" class="block text-sm font-medium text-gray-700">Contenu du message <span class="text-red-500">*</span></label>
                    <textarea name="contenu" id="contenu" rows="6" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"><?= e($annonce['contenu']) ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" value="<?= $annonce['date_debut'] ?>"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div class="space-y-2">
                        <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                        <input type="date" name="date_fin" id="date_fin" value="<?= $annonce['date_fin'] ?>"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-50 flex items-center gap-3">
                    <div class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="actif" value="1" id="actif" class="sr-only peer" <?= $annonce['actif'] ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Annonce active / visible</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="<?= url('annonces/list') ?>" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-xl transition font-semibold shadow-lg shadow-indigo-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
