<?php
$title = "Nouvelle Annonce";
$breadcrumbs = [
    ['label' => 'Communication', 'url' => '#'],
    ['label' => 'Annonces', 'url' => url('annonces/list')],
    ['label' => 'Nouvelle Annonce']
];
?>

<div class="p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-bullhorn text-indigo-600 mr-2"></i>
                Publier une annonce
            </h1>
            <p class="text-gray-600">Communiquez des informations importantes aux parents et au personnel.</p>
        </div>

        <form action="<?= url('annonces/add') ?>" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div class="space-y-2">
                    <label for="titre" class="block text-sm font-medium text-gray-700">Titre de l'annonce <span class="text-red-500">*</span></label>
                    <input type="text" name="titre" id="titre" required placeholder="Ex: Réunion de parents d'élèves"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="type" class="block text-sm font-medium text-gray-700">Type d'annonce</label>
                        <select name="type" id="type" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="generale">Générale</option>
                            <option value="urgente">Urgente</option>
                            <option value="administrative">Administrative</option>
                            <option value="pedagogique">Pédagogique</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="cible" class="block text-sm font-medium text-gray-700">Cible de la diffusion</label>
                        <select name="cible" id="cible" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="tous">Tout le monde</option>
                            <option value="parents">Parents uniquement</option>
                            <option value="enseignants">Enseignants uniquement</option>
                            <option value="eleves">Élèves uniquement</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="contenu" class="block text-sm font-medium text-gray-700">Contenu du message <span class="text-red-500">*</span></label>
                    <textarea name="contenu" id="contenu" rows="6" required placeholder="Écrivez votre message ici..."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" value="<?= date('Y-m-d') ?>"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div class="space-y-2">
                        <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                        <input type="date" name="date_fin" id="date_fin" value="<?= date('Y-m-d', strtotime('+7 days')) ?>"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="<?= url('annonces/list') ?>" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-xl transition font-semibold shadow-lg shadow-indigo-200 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Publier l'annonce
                </button>
            </div>
        </form>
    </div>
</div>
