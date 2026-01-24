<?php
/**
 * Vue : Ajouter/Modifier une interrogation
 */
if (empty($_GET['iframe'])): ?>
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

<?php
$isEdit = isset($interrogation);
$title = $isEdit ? 'Modifier interrogation' : 'Nouvelle interrogation';
$action = $isEdit ? url('interrogations/edit/' . $interrogation['id']) : url('interrogations/add');
if (!empty($_GET['iframe'])) {
    $action .= '?iframe=1';
}
?>

<div class="p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-<?= $isEdit ? 'edit' : 'plus-circle' ?> text-purple-600 mr-2"></i>
                <?= $title ?>
            </h1>
            <a href="<?= url('interrogations/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Ligne 1 : Classe et Matière -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Classe <span class="text-red-500">*</span></label>
                        <select name="classe_id" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Sélectionner...</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($isEdit && $interrogation['classe_id'] == $c['id']) ? 'selected' : '' ?>>
                                    <?= e($c['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Matière <span class="text-red-500">*</span></label>
                        <select name="matiere_id" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Sélectionner...</option>
                            <?php foreach ($matieres as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= ($isEdit && $interrogation['matiere_id'] == $m['id']) ? 'selected' : '' ?>>
                                    <?= e($m['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Ligne 2 : Période et Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Période <span class="text-red-500">*</span></label>
                        <select name="periode_id" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Sélectionner...</option>
                            <?php foreach ($periodes as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= ($isEdit && $interrogation['periode_id'] == $p['id']) ? 'selected' : '' ?>>
                                    <?= e($p['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date_interrogation" required 
                               value="<?= $isEdit ? $interrogation['date_interrogation'] : date('Y-m-d') ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>

                <!-- Ligne 3 : Informations -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom / Intitulé <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" required 
                           value="<?= $isEdit ? e($interrogation['nom']) : '' ?>"
                           placeholder="Ex: Contrôle sur les fractions"
                           class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Ligne 4 : Notation -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note sur</label>
                        <input type="number" name="note_sur" step="0.5" 
                               value="<?= $isEdit ? $interrogation['note_sur'] : '20' ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durée (min)</label>
                        <input type="number" name="duree" 
                               value="<?= $isEdit ? $interrogation['duree'] : '60' ?>"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="planifiee" <?= ($isEdit && $interrogation['statut'] == 'planifiee') ? 'selected' : '' ?>>Planifiée</option>
                            <option value="en_cours" <?= ($isEdit && $interrogation['statut'] == 'en_cours') ? 'selected' : '' ?>>En cours</option>
                            <option value="terminee" <?= ($isEdit && $interrogation['statut'] == 'terminee') ? 'selected' : '' ?>>Terminée</option>
                            <option value="annulee" <?= ($isEdit && $interrogation['statut'] == 'annulee') ? 'selected' : '' ?>>Annulée</option>
                        </select>
                    </div>
                </div>

                <!-- Fichier Sujet (Nouveau Champ Obligatoire) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fichier de l'épreuve (PDF ou Image) <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition-colors">
                        <div class="space-y-1 text-center">
                            <div class="flex text-sm text-gray-600">
                                <label for="fichier_sujet" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                    <span>Télécharger un fichier</span>
                                    <input id="fichier_sujet" name="fichier_sujet" type="file" required class="sr-only" accept=".pdf,image/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, PNG, JPG jusqu'à 10MB</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description / Consignes</label>
                    <textarea name="description" rows="3" 
                              class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"><?= $isEdit ? e($interrogation['description']) : '' ?></textarea>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                    <a href="<?= url('interrogations/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition shadow-md flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la liste
                    </a>
                    <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition shadow-md">
                        <i class="fas fa-save mr-2"></i> Enregistrer
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
