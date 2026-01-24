<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-door-open text-purple-600 mr-2"></i>
            Choix de la Classe
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Étape 3 sur 7 : Sélectionner la classe</p>
    </div>

    <div class="mb-8">
        <?php 
        $stepNames = [1 => 'Type', 2 => 'Élève', 3 => 'Classe', 4 => 'Documents', 5 => 'Articles', 6 => 'Paiement', 7 => 'Confirmation'];
        ?>
        <div class="flex items-center justify-between mb-2">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?> text-center">
                    <span class="text-[10px] md:text-xs font-semibold <?= $i <= 3 ? 'text-blue-600' : 'text-gray-400' ?>">
                        Étape <?= $i ?>: <?= $stepNames[$i] ?>
                    </span>
                </div>
            <?php endfor; ?>
        </div>
        <div class="flex items-center justify-between">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?>">
                    <div class="h-2 <?= $i <= 3 ? 'bg-blue-600' : 'bg-gray-200' ?> rounded"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Messages Flash -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
            <p class="font-bold">Erreur</p>
            <p><?= $_SESSION['error'] ?></p>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
            <p class="font-bold">Succès</p>
            <p><?= $_SESSION['success'] ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" id="inscriptionForm" action="<?= url('inscriptions/nouveau?etape=3') ?>">
            <?= csrf_field() ?>
            
            
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Sélectionner la classe d'inscription</h2>
            
            <?php if (isset($typeInscription) && $typeInscription === 'reinscription' && $classePrecedente): ?>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mr-3 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-blue-900 font-semibold mb-1">📚 Classe précédente</p>
                            <p class="text-blue-800 text-sm">
                                L'élève était en <strong><?= e($classePrecedente['niveau_nom']) ?> (<?= e($classePrecedente['nom']) ?>)</strong> l'année dernière.
                            </p>
                            <p class="text-blue-700 text-xs mt-2">
                                ⚠️ <strong>Important :</strong> L'élève doit être inscrit dans une classe de niveau égal ou supérieur. 
                                La rétrogradation n'est pas autorisée.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($classeSuggeree): ?>
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-lightbulb text-green-600 mr-2"></i>
                        <p class="text-green-800 font-semibold">💡 Suggestion automatique</p>
                    </div>
                    <p class="text-green-700 text-sm mt-1">
                        Nous vous suggérons la classe <strong><?= e($classeSuggeree['nom']) ?></strong> 
                        (<?= e($classeSuggeree['niveau_nom']) ?>) pour la progression naturelle de l'élève.
                    </p>
                </div>
            <?php endif; ?>
            
            <div>
                <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-door-open mr-2 text-gray-500"></i>Classe *
                </label>
                <select id="classe_id" name="classe_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Sélectionner une classe</option>
                    <?php 
                    $selectedId = $savedClasseId ?? ($classeSuggeree['id'] ?? '');
                    $currentCycle = '';
                    foreach ($classes as $classe): 
                        // Grouper par cycle
                        if ($currentCycle !== ($classe['cycle_nom'] ?? '')) {
                            $currentCycle = $classe['cycle_nom'] ?? '';
                            if ($currentCycle) {
                                echo '<optgroup label="' . e($currentCycle) . '">';
                            }
                        }
                    ?>
                        <option value="<?= $classe['id'] ?>" <?= ($selectedId == $classe['id']) ? 'selected' : '' ?>>
                            <?= e($classe['code'] ?? $classe['nom']) ?> - <?= (int)($classe['nb_eleves'] ?? 0) ?> élève<?= ($classe['nb_eleves'] ?? 0) > 1 ? 's' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Choisissez la classe dans laquelle l'élève sera inscrit pour cette année scolaire
                </p>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                let formSubmitted = false;
                const form = document.getElementById('inscriptionForm');
                
                form.addEventListener('submit', function() {
                    formSubmitted = true;
                });
                
                window.addEventListener('beforeunload', function(e) {
                     if (!formSubmitted) {
                         const message = "Attention, vos données ne sont pas encore enregistrées. Si vous quittez cette page maintenant, les informations saisies seront perdues.";
                         e.returnValue = message;
                         return message;
                     }
                });
            });
            </script>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t">
                <a href="<?= url('inscriptions/nouveau?etape=2') ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <span>Continuer</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>
