<?php
$title = "Modifier le Modèle";
$breadcrumbs = [
    ['label' => 'Communication', 'url' => '#'],
    ['label' => 'Modèles', 'url' => url('notifications/modeles')],
    ['label' => 'Modifier']
];
?>

<div class="p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-edit text-indigo-600 mr-2"></i>
                    Modifier le Modèle
                </h1>
                <p class="text-gray-600">Mettez à jour votre message type.</p>
            </div>
            <div class="flex gap-3">
                <a href="<?= url('notifications/modeles/delete/' . $modele['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" onclick="return confirm('Supprimer ce modèle ?')" class="bg-red-50 text-red-600 p-4 rounded-2xl hover:bg-red-600 hover:text-white transition shadow-sm group">
                    <i class="fas fa-trash-alt group-hover:animate-bounce"></i>
                </a>
                <?php if (!empty($_GET['iframe'])): ?>
                <a href="<?= url('notifications/modeles') ?>?iframe=1" class="bg-blue-50 text-blue-600 p-4 rounded-2xl hover:bg-blue-600 hover:text-white transition shadow-sm group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <form action="<?= url('notifications/modeles/edit/' . $modele['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" method="POST" class="space-y-6 animate-fade-in">
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest px-1">Nom du modèle</label>
                            <input type="text" name="nom" required value="<?= e($modele['nom']) ?>"
                                class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-50/50 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest px-1">Type / Catégorie</label>
                            <select name="type" class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-50/50 focus:border-indigo-500 outline-none transition-all appearance-none cursor-pointer">
                                <option value="info" <?= ($modele['type']??'') === 'info' ? 'selected' : '' ?>>Information Standard</option>
                                <option value="urgent" <?= ($modele['type']??'') === 'urgent' ? 'selected' : '' ?>>Urgent / Alerte</option>
                                <option value="pedagogique" <?= ($modele['type']??'') === 'pedagogique' ? 'selected' : '' ?>>Pédagogique</option>
                                <option value="administratif" <?= ($modele['type']??'') === 'administratif' ? 'selected' : '' ?>>Administratif</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-widest px-1">Sujet par défaut</label>
                        <input type="text" name="sujet" required value="<?= e($modele['sujet']) ?>"
                            class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-50/50 focus:border-indigo-500 outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between px-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">Corps du message</label>
                            <div class="flex gap-2">
                                <span class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-1 rounded-md font-bold cursor-help" title="Sera remplacé par le nom de l'élève">{eleve_nom}</span>
                                <span class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-1 rounded-md font-bold cursor-help" title="Sera remplacé par le nom du parent">{parent_nom}</span>
                            </div>
                        </div>
                        <textarea name="contenu" required rows="8"
                            class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-50/50 focus:border-indigo-500 outline-none transition-all resize-none"><?= e($modele['contenu']) ?></textarea>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-4">
                    <a href="<?= url('notifications/modeles') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="text-gray-500 font-bold hover:text-gray-700 transition px-4">Annuler</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-100 transition-all flex items-center gap-2">
                        <i class="fas fa-save font-normal"></i>
                        <span>Enregistrer les modifications</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
