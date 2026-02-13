<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-graduate text-blue-600 mr-2"></i>
                Liste des élèves 
                <span class="text-blue-600 font-normal">
                    (<?= isset($anneeActive) ? e($anneeActive['libelle']) : 'Année en cours' ?>)
                </span>
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Consultez les élèves inscrits pour l'année scolaire en cours</p>
        </div>
    </div>
    <!-- Statistiques et Filtre -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total élèves</p>
                    <p class="text-2xl font-bold text-gray-800"><?= count($eleves ?? []) ?></p>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Classes</p>
                    <p class="text-2xl font-bold text-gray-800"><?= count($classes ?? []) ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <i class="fas fa-door-open text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-4">
                <div class="bg-purple-100 p-4 rounded-lg">
                    <i class="fas fa-filter text-purple-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-1">Filtrer par Classe</p>
                    <form method="GET" action="<?= url('classes/eleves') ?>">
                        <select name="classe_id" onchange="this.form.submit()" 
                                class="w-full border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5 text-sm font-medium">
                            <option value="">Toutes les classes</option>
                            <?php if (isset($classes)): 
                                $groups = ['Secondaire' => [], 'Lycée' => []];
                                foreach ($classes as $c) {
                                    $code = strtoupper($c['code'] ?? '');
                                    if (preg_match('/^[3456]/', $code)) {
                                        $groups['Secondaire'][] = $c;
                                    } else {
                                        $groups['Lycée'][] = $c;
                                    }
                                }
                                
                                // Sort 'Secondaire' group descending by the first digit (6, 5, 4, 3)
                                usort($groups['Secondaire'], function($a, $b) {
                                    $valA = (int)($a['code'][0] ?? 0);
                                    $valB = (int)($b['code'][0] ?? 0);
                                    if ($valA !== $valB) return $valB <=> $valA;
                                    return strcasecmp($a['code'], $b['code']);
                                });

                                // Sort 'Lycée' group (2nd -> 1ère -> Terminale)
                                usort($groups['Lycée'], function($a, $b) {
                                    $getLycéeWeight = function($code) {
                                        $code = strtoupper($code);
                                        if (str_starts_with($code, '2')) return 1;
                                        if (str_starts_with($code, '1')) return 2;
                                        if (str_contains($code, 'TER')) return 3;
                                        return 9;
                                    };
                                    $wa = $getLycéeWeight($a['code']);
                                    $wb = $getLycéeWeight($b['code']);
                                    if ($wa !== $wb) return $wa <=> $wb;
                                    return strcasecmp($a['code'], $b['code']);
                                });

                                foreach ($groups as $groupLabel => $items): 
                                    if (empty($items)) continue; 
                            ?>
                                    <optgroup label="<?= $groupLabel ?>">
                                        <?php foreach ($items as $classe): ?>
                                            <option value="<?= $classe['id'] ?>" <?= (isset($_GET['classe_id']) && $_GET['classe_id'] == $classe['id']) ? 'selected' : '' ?>>
                                                <?= e($classe['code']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des élèves -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list text-blue-600"></i>
                <?php if (isset($selectedClasse)): ?>
                    Élèves de la classe : <?= e($selectedClasse['nom']) ?>
                <?php else: ?>
                    Tous les élèves
                <?php endif; ?>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <?php if (empty($eleves)): ?>
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4 block"></i>
                    <p>Aucun élève trouvé</p>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-id-card mr-2"></i>Matricule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-user mr-2"></i>Nom - prénom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-chalkboard-teacher mr-2"></i>Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-calendar-alt mr-2"></i>Date d'inscription</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-credit-card mr-2"></i>Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-tools mr-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($eleves as $eleve): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= e($eleve['matricule'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-medium"><?= e(($eleve['nom'] ?? 'N/A') . ' ' . ($eleve['prenom'] ?? '')) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="<?= url('classes/details/' . ($eleve['classe_id'] ?? '')) ?>" 
                                       class="text-blue-600 hover:text-blue-800 hover:underline">
                                        <?= e($eleve['classe_code'] ?? 'N/A') ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= formatDate($eleve['date_inscription'] ?? '') ?>
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap">
                                     <?php 
                                     $typeInscription = $eleve['type_inscription'] ?? 'nouveau';
                                     $badgeClass = match($typeInscription) {
                                         'passant', 'passante' => 'bg-green-100 text-green-800',
                                         'redoublant', 'redoublante' => 'bg-orange-100 text-orange-800',
                                         'nouveau', 'nouvelle' => 'bg-blue-100 text-blue-800',
                                         default => 'bg-gray-100 text-gray-800'
                                     };
                                     $label = ucfirst($typeInscription);
                                     ?>
                                     <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badgeClass ?>">
                                         <?= e($label) ?>
                                     </span>
                                 </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                         <a href="<?= url('eleves/parcours/' . $eleve['id']) ?>" 
                                            class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition"
                                            title="Parcours Scolaire">
                                             <i class="fas fa-history"></i>
                                         </a>
                                     </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

