<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-shopping-bag text-blue-600 mr-2"></i>
            Articles et Frais Annexes (Optionnels)
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Étape 5 sur 7 : Sélectionnez les articles que vous souhaitez acquérir maintenant.</p>
    </div>

    <div class="mb-8">
        <?php 
        $stepNames = [1 => 'Type', 2 => 'Élève', 3 => 'Classe', 4 => 'Documents', 5 => 'Articles', 6 => 'Paiement', 7 => 'Confirmation'];
        ?>
        <div class="flex items-center justify-between mb-2">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?> text-center">
                    <span class="text-[10px] md:text-xs font-semibold <?= $i <= 5 ? 'text-blue-600' : 'text-gray-400' ?>">
                        Étape <?= $i ?>: <?= $stepNames[$i] ?>
                    </span>
                </div>
            <?php endfor; ?>
        </div>
        <div class="flex items-center justify-between">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?>">
                    <div class="h-2 <?= $i <= 5 ? 'bg-blue-600' : 'bg-gray-200' ?> rounded"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <form method="POST" action="<?= url('inscriptions/nouveau?etape=5') ?>">
        <?= csrf_field() ?>
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Ces articles ne sont pas obligatoires pour finaliser l'inscription. Vous pouvez les choisir maintenant pour les inclure dans votre premier paiement, ou les acheter plus tard depuis votre espace parent.
                    </p>
                </div>
            </div>
        </div>

        <?php if (empty($articles)): ?>
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center border-2 border-dashed border-gray-200">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <i class="fas fa-box-open text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Aucun article disponible</h3>
                <p class="text-gray-500 max-w-md mx-auto">Il n'y a pas d'articles optionnels configurés pour le niveau <span class="font-bold text-blue-600"><?= e($classe['niveau_nom'] ?? 'ce niveau') ?></span> pour le moment.</p>
                <div class="mt-8">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg flex items-center justify-center mx-auto">
                        <span>Continuer vers le paiement</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <?php foreach ($articles as $article): 
                    $isSelected = in_array($article['id'], $articlesSelectionnes);
                ?>
                    <label class="relative group cursor-pointer">
                        <input type="checkbox" name="articles[]" value="<?= $article['id'] ?>" class="hidden peer" <?= $isSelected ? 'checked' : '' ?>>
                        
                        <div class="h-full bg-white border-2 border-gray-100 rounded-2xl p-6 transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-50/50 shadow-sm hover:shadow-md group-hover:border-blue-200 relative overflow-hidden">
                            <!-- Badge Type -->
                            <?php
                            $typeLabels = [
                                'tenue_sport' => 'Sport',
                                'tenue_fete' => 'Tenue Fête',
                                'fourniture' => 'Fourniture',
                                'uniforme' => 'Uniforme',
                                'autre' => 'Autre'
                            ];
                            $typeColors = [
                                'tenue_sport' => 'bg-green-100 text-green-700',
                                'tenue_fete' => 'bg-purple-100 text-purple-700',
                                'fourniture' => 'bg-blue-100 text-blue-700',
                                'uniforme' => 'bg-indigo-100 text-indigo-700',
                                'autre' => 'bg-gray-100 text-gray-700'
                            ];
                            $type = $article['type_article'] ?? 'autre';
                            ?>
                            <div class="mb-4">
                                <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-md <?= $typeColors[$type] ?>">
                                    <?= $typeLabels[$type] ?>
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition-colors"><?= e($article['libelle']) ?></h3>
                            <p class="text-xs text-gray-400 mb-4 font-mono"><?= e($article['code']) ?></p>
                            
                            <div class="flex items-end justify-between mt-auto pt-4 border-t border-gray-50 group-hover:border-blue-100 transition-colors">
                                <div>
                                    <span class="text-2xl font-black text-blue-600"><?= number_format($article['prix_unitaire'], 0, ',', ' ') ?></span>
                                    <span class="text-sm font-bold text-gray-400 ml-1">Ar</span>
                                </div>
                                
                                <div class="w-8 h-8 rounded-full border-2 border-gray-200 flex items-center justify-center transition-all peer-checked:bg-blue-500 peer-checked:border-blue-500">
                                    <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                </div>
                            </div>

                            <!-- Indicator Checkbox Fake (Visual Only) -->
                            <div class="absolute top-4 right-4 text-blue-500 opacity-0 peer-checked:opacity-100 transition-opacity">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>

            <!-- Boutons Navigation -->
            <div class="flex flex-col sm:flex-row gap-4 pt-8 mt-8 border-t">
                <?php 
                    $typeInscription = $_SESSION['inscription_data']['type_inscription'] ?? 'nouvelle';
                    $backStep = ($typeInscription === 'reinscription') ? 3 : 4;
                ?>
                <a href="<?= url('inscriptions/nouveau?etape=' . $backStep) ?>" class="px-8 py-4 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition text-center min-w-[160px]">
                    <i class="fas fa-arrow-left mr-2"></i>Précédent
                </a>
                
                <button type="submit" class="flex-1 px-8 py-4 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 transition shadow-xl flex items-center justify-center group">
                    <span>Valider et passer au paiement</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<style>
/* Custom peers because Tailwind peer-checked works on siblings but we wrapped in label */
input:checked + div {
    border-color: #3b82f6 !important;
    background-color: #eff6ff !important;
}
input:checked + div .w-8.h-8 {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
}
input:checked + div .fa-check {
    opacity: 1 !important;
}
</style>
