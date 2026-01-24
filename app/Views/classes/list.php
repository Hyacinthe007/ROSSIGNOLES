<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-door-open text-blue-600 mr-2"></i>
                Liste des classes
            </h1>
            <p class="text-gray-500 text-xs md:text-sm">Gestion des classes de l'école</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('classes/associer') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 font-bold shadow-md">
                <i class="fas fa-link"></i>
                <span>Associer</span>
            </a>
            <a href="<?= url('classes/add') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg font-bold">
                <i class="fas fa-plus"></i>
                <span>Ajouter une classe</span>
            </a>
        </div>
    </div>

    <!-- Alerte si aucune année scolaire active -->
    <?php if (empty($anneeActive)): ?>
        <div class="mb-6 bg-amber-50 border-l-4 border-amber-400 text-amber-800 px-4 py-3 rounded-r-lg shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle mt-1"></i>
                <div>
                    <p class="font-medium">Aucune année scolaire active</p>
                    <p class="text-sm">
                        Pour afficher les classes, vous devez d'abord créer et <strong>activer</strong> une année scolaire
                        dans le menu <strong>Années scolaires</strong> (menu Système &gt; Années scolaires).
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Liste avec regroupement par année -->
    <div class="space-y-8">
        <?php if (empty($classes)): ?>
            <div class="bg-white rounded-xl shadow-lg p-8 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                Aucune classe trouvée
            </div>
        <?php else: ?>
            <?php 
            $groupedClasses = [];
            foreach ($classes as $classe) {
                // Définir une valeur par défaut pour le groupe si vide
                $annee = !empty($classe['annee_scolaire_libelle']) ? $classe['annee_scolaire_libelle'] : 'Année non définie';
                $groupedClasses[$annee][] = $classe;
            }
            // (L'ordre est déjà géré par la requête SQL: date_debut DESC)
            ?>

            <?php foreach ($groupedClasses as $annee => $classesAnnee): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg md:text-xl font-bold text-gray-700">
                            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i><?= e($annee) ?>
                        </h2>
                        <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full">
                            <?= count($classesAnnee) ?> classe(s)
                        </span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse table-responsive-text">
                            <thead>
                                <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white text-xs uppercase font-semibold">
                                    <th class="px-6 py-4">Classe</th>
                                    <th class="px-6 py-4">Code</th>
                                    <th class="px-6 py-4">Niveau</th>
                                    <th class="px-6 py-4">Série</th>
                                    <th class="px-6 py-4 text-center">Effectif</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($classesAnnee as $classe): ?>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="bg-blue-50 text-blue-600 p-2 rounded-lg mr-3 group-hover:bg-blue-100 transition">
                                                <i class="fas fa-chalkboard"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-800"><?= e($classe['nom']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                        <?= e($classe['code'] ?: '-') ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?= e($classe['niveau_nom'] ?? '-') ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?= e($classe['serie_nom'] ?? '-') ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                            <?= $classe['effectif'] ?? 0 ?> élèves
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                                        <a href="<?= url('classes/edit/' . $classe['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-green-600 hover:text-green-800 font-medium text-sm inline-flex items-center gap-1">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
