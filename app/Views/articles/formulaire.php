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
    <!-- En-tête -->
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-shopping-bag text-blue-600 mr-2"></i>
                <?= isset($article) ? 'Modifier Article' : 'Nouvel Article' ?>
            </h1>
            <p class="text-gray-600">
                <?= isset($article) ? 'Modification de l\'article #' . $article['id'] : 'Création d\'un nouvel article scolaire' ?>
            </p>
        </div>
        <a href="<?= url('articles/liste') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Retour</span>
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-5xl mx-auto border border-gray-100">
        <form method="POST" action="<?= (isset($article) ? url('articles/mettre-a-jour/' . $article['id']) : url('articles/creer')) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>">
            <?= csrf_field() ?>

            <!-- Ligne 1 : Code et Année -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-barcode mr-2 text-gray-500"></i>Code Article (Généré) *
                    </label>
                    <input type="text" id="code" name="code" required readonly
                           value="<?= isset($article) ? e($article['code']) : '' ?>" 
                           placeholder="Généré automatiquement..."
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-0 cursor-not-allowed uppercase font-mono text-gray-500">
                </div>

                <!-- Année scolaire -->
                <div>
                    <label for="annee_scolaire_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Année Scolaire *
                    </label>
                    <select id="annee_scolaire_id" name="annee_scolaire_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <?php foreach ($annees as $annee): ?>
                            <option value="<?= $annee['id'] ?>" 
                                <?= (isset($tarif) && $tarif['annee_scolaire_id'] == $annee['id']) ? 'selected' : '' ?>
                                <?= (!isset($tarif) && $annee['actif']) ? 'selected' : '' ?>>
                                <?= e($annee['libelle']) ?> <?= $annee['actif'] ? '(Active)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Ligne 2 : Libellé, Type et Prix -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Libellé -->
                <div class="md:col-span-2">
                    <label for="libelle" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file-alt mr-2 text-gray-500"></i>Libellé de l'Article *
                    </label>
                    <input type="text" id="libelle" name="libelle" required
                           value="<?= isset($article) ? e($article['libelle']) : '' ?>" 
                           placeholder="Ex: Logo de l'école"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Type -->
                <div>
                    <label for="type_article" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-gray-500"></i>Type *
                    </label>
                    <select id="type_article" name="type_article" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="tenue_sport" <?= (isset($article) && $article['type_article'] == 'tenue_sport') ? 'selected' : '' ?>>Tenue Sport</option>
                        <option value="tenue_fete" <?= (isset($article) && $article['type_article'] == 'tenue_fete') ? 'selected' : '' ?>>Tenue Fête</option>
                        <option value="fourniture" <?= (isset($article) && $article['type_article'] == 'fourniture') ? 'selected' : '' ?>>Fourniture</option>
                        <option value="uniforme" <?= (isset($article) && $article['type_article'] == 'uniforme') ? 'selected' : '' ?>>Uniforme</option>
                        <option value="autre" <?= (isset($article) && $article['type_article'] == 'autre') ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>

                <!-- Prix unitaire -->
                <div>
                    <label for="prix_unitaire" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave mr-2 text-gray-500"></i>Prix Unit. *
                    </label>
                    <div class="relative">
                        <input type="text" id="prix_unitaire" name="prix_unitaire" required
                               value="<?= isset($tarif) ? $tarif['prix_unitaire'] : '0' ?>" 
                               class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 amount-format text-right font-bold">
                        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs font-bold">Ar</span>
                    </div>
                </div>
            </div>

            <!-- Ligne 3 : Options -->
            <div class="mb-8 flex flex-wrap gap-8 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" name="obligatoire" value="1" 
                               <?= (isset($article) && $article['obligatoire']) ? 'checked' : '' ?>
                               class="hidden peer">
                        <div class="w-6 h-6 border-2 border-gray-300 rounded peer-checked:bg-red-600 peer-checked:border-red-600 transition-all flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                        </div>
                    </div>
                    <span class="text-gray-700 font-medium group-hover:text-red-600 transition-colors">
                        <i class="fas fa-exclamation-circle text-red-500 mr-1"></i>
                        Article Obligatoire
                    </span>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" name="actif" value="1" 
                               <?= (!isset($article) || $article['actif']) ? 'checked' : '' ?>
                               class="hidden peer">
                        <div class="w-6 h-6 border-2 border-gray-300 rounded peer-checked:bg-green-600 peer-checked:border-green-600 transition-all flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                        </div>
                    </div>
                    <span class="text-gray-700 font-medium group-hover:text-green-600 transition-colors">
                        <i class="fas fa-toggle-on text-green-500 mr-1"></i>
                        Article Actif
                    </span>
                </label>
            </div>

            <!-- Boutons -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t">
                <a href="<?= url('articles/liste') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                   class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition">
                    Annuler
                </a>
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer l'Article</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Génération automatique du code à partir du libellé
    const libelleInput = document.getElementById('libelle');
    const codeInput = document.getElementById('code');
    const isEdit = <?= isset($article) ? 'true' : 'false' ?>;

    if (!isEdit && libelleInput && codeInput) {
        libelleInput.addEventListener('input', function() {
            let text = this.value.toUpperCase();
            // Supprimer les accents
            text = text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            // Remplacer tout ce qui n'est pas alphanumérique par des underscores
            text = text.replace(/[^A-Z0-9]/g, '_');
            // Éviter les doubles underscores
            text = text.replace(/_+/g, '_');
            // Supprimer les underscores au début et à la fin
            text = text.replace(/^_|_$/g, '');
            
            codeInput.value = text;
        });
    }

    // Formatage des montants
    function formatNumber(value) {
        let num = value.replace(/[^\d]/g, '');
        if (num === '') return '';
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
    
    function getNumericValue(value) {
        return value.replace(/\s/g, '');
    }
    
    const amountFields = document.querySelectorAll('.amount-format');
    amountFields.forEach(field => {
        if (field.value) {
            field.value = formatNumber(field.value);
        }
        
        field.addEventListener('input', function(e) {
            const cursorPosition = this.selectionStart;
            const oldValue = this.value;
            const oldLength = oldValue.length;
            
            this.value = formatNumber(this.value);
            
            const newLength = this.value.length;
            const diff = newLength - oldLength;
            this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        });
        
        field.form.addEventListener('submit', function(e) {
            amountFields.forEach(f => {
                f.value = getNumericValue(f.value);
            });
        });
    });
});
</script>

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
