<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-list text-blue-600 mr-2"></i>
                Inscriptions
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des inscriptions et réinscriptions</p>
        </div>
        <?php if (hasPermission('inscriptions_new.create')): ?>
        <div class="mt-4 md:mt-0">
            <a href="<?= url('inscriptions/nouveau') ?>" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle Inscription
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Statistiques -->
    <?php if ($statistiques): ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Inscriptions</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $statistiques['total_inscriptions'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Actives</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $statistiques['actives'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <i class="fas fa-money-bill-wave text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Frais</p>
                        <p class="text-lg font-bold text-gray-800"><?= number_format($statistiques['total_frais'], 0, ',', ' ') ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Impayés</p>
                        <p class="text-lg font-bold text-gray-800"><?= $statistiques['nb_impayes'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="<?= url('inscriptions/liste') ?>" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Recherche élève -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche (Nom, Matricule)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </span>
                    <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" 
                           class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nom, prénom ou matricule..."
                           oninput="document.getElementById('filterForm').submit()">
                </div>
            </div>

            <!-- Filtre Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                        onchange="document.getElementById('filterForm').submit()">
                    <option value="">Tous</option>
                    <option value="nouvelle" <?= ($filters['type_inscription'] ?? '') === 'nouvelle' ? 'selected' : '' ?>>Nouvelle</option>
                    <option value="reinscription" <?= ($filters['type_inscription'] ?? '') === 'reinscription' ? 'selected' : '' ?>>Réinscription</option>
                </select>
            </div>

            <!-- Filtre Statut -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        onchange="document.getElementById('filterForm').submit()">
                    <option value="">Tous</option>
                    <option value="validee" <?= ($filters['statut'] ?? '') === 'validee' ? 'selected' : '' ?>>Validée</option>
                    <option value="suspendue" <?= ($filters['statut'] ?? '') === 'suspendue' ? 'selected' : '' ?>>Suspendue</option>
                    <option value="terminee" <?= ($filters['statut'] ?? '') === 'terminee' ? 'selected' : '' ?>>Terminée</option>
                </select>
            </div>

            <!-- Filtre Classe (Codes) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                <select name="classe_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        onchange="document.getElementById('filterForm').submit()">
                    <option value="">Toutes</option>
                    
                    <optgroup label="Secondaire">
                        <?php 
                        // Tri manuel pour le Secondaire : 6ème, 5ème, 4ème, 3ème
                        $ordreSecondaire = ['6', '5', '4', '3'];
                        foreach ($ordreSecondaire as $prefixe) {
                            foreach ($classes as $classe) {
                                $code = e($classe['code']);
                                // On vérifie si le code commence par le chiffre (ex: "6ème 1")
                                if (strpos($code, $prefixe) === 0) {
                                    $selected = ($filters['classe_id'] ?? '') == $classe['id'] ? 'selected' : '';
                                    echo "<option value=\"{$classe['id']}\" $selected>{$code}</option>";
                                }
                            }
                        }
                        ?>
                    </optgroup>
                    
                    <optgroup label="Lycée">
                        <?php 
                        // Tri manuel pour le Lycée : 2nd, 1ère, Term
                        $ordreLycee = ['2nd', '1', 'T'];
                        foreach ($ordreLycee as $prefixe) {
                            foreach ($classes as $classe) {
                                $code = e($classe['code']);
                                // Pour 2nd et Term, vérification simple
                                // Pour 1ère, attention de ne pas confondre avec autre chose, mais ici les codes sont cleans
                                if (strpos($code, $prefixe) === 0) {
                                     $selected = ($filters['classe_id'] ?? '') == $classe['id'] ? 'selected' : '';
                                    echo "<option value=\"{$classe['id']}\" $selected>{$code}</option>";
                                }
                            }
                        }
                        ?>
                    </optgroup>
                </select>
            </div>
        </form>
    </div>

    <!-- Script pour délais de recherche (debounce) -->
    <script>
        let timeout;
        const input = document.querySelector('input[name="q"]');
        input.removeAttribute('oninput'); // Supprimer l'attribut inline pour gérer via JS propre
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 500); // Délai de 500ms pour éviter de submit à chaque touche
        });
        
        // Focus sur l'input après rechargement si une recherche est active
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('q')) {
            const val = urlParams.get('q');
            input.value = val;
            input.focus();
            // Placer le curseur à la fin
            const len = input.value.length;
            input.setSelectionRange(len, len);
        }
    </script>

    <!-- Liste des inscriptions -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Montant Payé</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($inscriptions)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Aucune inscription trouvée</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inscriptions as $inscription): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">
                                                <?= e($inscription['eleve_nom']) ?> <?= e($inscription['eleve_prenom']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500"><?= e($inscription['eleve_matricule']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900"><?= e($inscription['classe_nom']) ?></div>
                                    <div class="text-sm text-gray-500"><?= e($inscription['classe_code']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $inscription['type_inscription'] === 'nouvelle' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= $inscription['type_inscription'] === 'nouvelle' ? 'Nouvelle' : 'Réinscription' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($inscription['date_inscription'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $montantPaye = $inscription['montant_paye'] ?? 0;
                                    ?>
                                    <div class="text-sm font-bold text-green-700">
                                        <?= number_format($montantPaye, 0, ',', ' ') ?> MGA
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                         <?php
                                         switch ($inscription['statut']) {
                                             case 'validee': echo 'bg-green-100 text-green-800'; break;
                                             case 'active': echo 'bg-green-100 text-green-800'; break;
                                             case 'brouillon': echo 'bg-gray-100 text-gray-500 border border-gray-200'; break;
                                             case 'en_attente': echo 'bg-orange-100 text-orange-800'; break;
                                             case 'suspendue': echo 'bg-red-100 text-red-800'; break;
                                             case 'terminee': echo 'bg-gray-100 text-gray-800'; break;
                                             default: echo 'bg-blue-100 text-blue-800';
                                         }
                                         ?>">
                                         <?= ucfirst(str_replace('_', ' ', $inscription['statut'])) ?>
                                     </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?= url('inscriptions/details/' . $inscription['id']) ?>" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
