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
                <i class="fas fa-file-invoice-dollar text-green-600 mr-2"></i>
                <?= isset($tarif) ? 'Modifier Tarif' : 'Nouveau Tarif' ?>
            </h1>
            <p class="text-gray-600">
                <?= isset($tarif) ? 'Modification du tarif #' . $tarif['id'] : 'Création d\'un nouveau tarif scolaire' ?>
            </p>
        </div>
        <a href="<?= url('tarifs/liste') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la configuration</span>
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-2xl border border-gray-100 mx-auto">
        <form method="POST" action="<?= (isset($tarif) ? url('tarifs/mettre-a-jour/' . $tarif['id']) : url('tarifs/creer')) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>">
            <?= csrf_field() ?>

            <!-- Année Scolaire -->
            <div class="mb-6">
                <label for="annee_scolaire_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2 text-gray-500"></i>Année Scolaire *
                </label>
                <select id="annee_scolaire_id" name="annee_scolaire_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Sélectionner une année</option>
                    <?php foreach ($annees as $annee): ?>
                        <option value="<?= $annee['id'] ?>" 
                            <?= (isset($tarif) && $tarif['annee_scolaire_id'] == $annee['id']) ? 'selected' : '' ?>
                            <?= (!isset($tarif) && isset($annee['actif']) && $annee['actif']) ? 'selected' : '' ?>>
                            <?= e($annee['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Niveau -->
            <div class="mb-6">
                <label for="niveau_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-graduation-cap mr-2 text-gray-500"></i>Niveau *
                </label>
                <select id="niveau_id" name="niveau_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Sélectionner un niveau</option>
                    <?php 
                    // Group levels by Cycle for display if possible, otherwise just list
                    $currentCycle = '';
                    // Sort levels by cycle momentarily if needed, but assuming simple list for now
                    ?>
                    <?php foreach ($niveaux as $niveau): ?>
                        <option value="<?= $niveau['id'] ?>" 
                            <?= (isset($tarif) && $tarif['niveau_id'] == $niveau['id']) ? 'selected' : '' ?>>
                            <?= e($niveau['libelle']) ?> (<?= e($niveau['cycle_libelle'] ?? 'Cycle non défini') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Sélectionnez le niveau pour lequel appliquer ce tarif.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Frais Inscription -->
                <div>
                    <label for="frais_inscription" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file-contract mr-2 text-gray-500"></i>Frais Inscription *
                    </label>
                    <div class="relative">
                        <input type="text" id="frais_inscription" name="frais_inscription" required
                               value="<?= isset($tarif) ? $tarif['frais_inscription'] : '0' ?>" 
                               class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent font-semibold amount-format text-right">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">Ar</span>
                    </div>
                </div>

                <!-- Écolage Mensuel -->
                <div>
                     <label for="ecolage_mensuel" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave mr-2 text-gray-500"></i>Écolage Mensuel *
                    </label>
                    <div class="relative">
                        <input type="text" id="ecolage_mensuel" name="ecolage_mensuel" required
                               value="<?= isset($tarif) ? $tarif['ecolage_mensuel'] : '0' ?>" 
                               class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent font-semibold amount-format text-right">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">Ar</span>
                    </div>
                </div>
            </div>



            <!-- Actif -->
            <div class="mb-8">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="actif" value="1" 
                           <?= (!isset($tarif) || $tarif['actif']) ? 'checked' : '' ?>
                           class="form-checkbox h-5 w-5 text-green-600 rounded focus:ring-green-500 border-gray-300">
                    <span class="text-gray-700 font-medium">Tarif Actif</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-4 pt-6 border-t">
                <a href="<?= url('tarifs/liste') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour à la configuration</span>
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg transition flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour formater un nombre avec des espaces comme séparateurs de milliers
    function formatNumber(value) {
        // Enlever tous les espaces et caractères non numériques sauf le point
        let num = value.replace(/[^\d]/g, '');
        
        // Convertir en nombre puis formater avec des espaces
        if (num === '') return '';
        
        // Ajouter des espaces tous les 3 chiffres depuis la droite
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
    
    // Fonction pour obtenir la valeur numérique (sans espaces)
    function getNumericValue(value) {
        return value.replace(/\s/g, '');
    }
    
    // Appliquer le formatage à tous les champs avec la classe amount-format
    const amountFields = document.querySelectorAll('.amount-format');
    
    amountFields.forEach(field => {
        // Formater la valeur initiale
        if (field.value) {
            field.value = formatNumber(field.value);
        }
        
        // Formater lors de la saisie
        field.addEventListener('input', function(e) {
            const cursorPosition = this.selectionStart;
            const oldValue = this.value;
            const oldLength = oldValue.length;
            
            // Formater la valeur
            this.value = formatNumber(this.value);
            
            // Ajuster la position du curseur
            const newLength = this.value.length;
            const diff = newLength - oldLength;
            this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        });
        
        // Avant la soumission du formulaire, enlever les espaces
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
