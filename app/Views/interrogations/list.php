<?php
$title = "Interrogations & Devoirs";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => url('dashboard')],
    ['label' => 'Évaluations', 'url' => url('evaluations')],
    ['label' => 'Interrogations & Devoirs']
];
?>

<div class="<?= empty($_GET['iframe']) ? 'p-4 md:p-8' : 'p-2 md:p-3' ?>">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <?php if (empty($_GET['iframe'])): ?>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-edit text-purple-600 mr-2"></i>
                    Interrogations & Devoirs
                </h1>
                <p class="text-gray-600 text-sm md:text-base">Gestion des évaluations continues</p>
            </div>
        <?php else: ?>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Interrogations & Devoirs</h2>
            </div>
        <?php endif; ?>

        <div class="flex items-center gap-3">
            <?php if (empty($_GET['iframe'])): ?>
                <a href="<?= url('evaluations') ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour aux évaluations</span>
                </a>
            <?php endif; ?>

            <a href="<?= url('interrogations/add') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg font-semibold">
                <i class="fas fa-plus"></i>
                <span>Nouvelle interrogation</span>
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1 w-full md:w-auto">
                <label for="classe_id" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest text-[10px]">
                    <i class="fas fa-door-open mr-1 text-purple-500"></i>Classe
                </label>
                <?php
                if (isset($classes) && is_array($classes)) {
                    usort($classes, function($a, $b) {
                        $getKey = function($c) {
                            $code = mb_strtolower($c['code'] ?? ($c['nom'] ?? ''));
                            $clean = preg_replace('/[^a-z0-9]+/u', ' ', $code);
                            preg_match_all('/\d+/', $clean, $nums);
                            $nums = $nums[0];
                            if (count($nums) >= 1) {
                                $grade = intval($nums[0]);
                                $section = isset($nums[1]) ? intval($nums[1]) : 0;
                                $weight = 6 - min(6, max(1, $grade));
                            } else {
                                if (strpos($clean, 'ter') !== false || strpos($clean, 'term') !== false) {
                                    $weight = 6; // Terminale last
                                    $section = 0;
                                } else {
                                    $weight = 999; // unknowns at end
                                    $section = 0;
                                }
                            }
                            return ($weight * 10) + $section;
                        };
                        $ka = $getKey($a);
                        $kb = $getKey($b);
                        if ($ka == $kb) return 0;
                        return ($ka < $kb) ? -1 : 1;
                    });
                }
                ?>

                <select id="classe_id" 
                        name="classe_id"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 transition-all bg-gray-50 outline-none text-sm">
                    <option value="">Toutes les classes</option>
                    <?php if (isset($classes)): 
                        foreach ($classes as $classe): 
                    ?>
                            <option value="<?= $classe['id'] ?>" <?= (isset($_GET['classe_id']) && $_GET['classe_id'] == $classe['id']) ? 'selected' : '' ?>>
                                <?= e($classe['code']) ?>
                            </option>
                        <?php endforeach; 
                    endif; ?>
                </select>
            </div>
            <div class="flex-1 w-full md:w-auto">
                <label for="matiere_id" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest text-[10px]">
                    <i class="fas fa-book mr-1 text-purple-500"></i>Matière
                </label>
                <select id="matiere_id" name="matiere_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 transition-all bg-gray-50 outline-none text-sm">
                    <option value="">Toutes les matières</option>
                    <?php foreach ($matieres as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= ($filters['matiere_id'] == $m['id']) ? 'selected' : '' ?>>
                            <?= e($m['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition">
                <i class="fas fa-filter mr-1"></i> Filtrer
            </button>
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intitulé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note sur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($interrogations)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-edit text-4xl mb-4 block"></i>
                                Aucune interrogation trouvée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($interrogations as $interro): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d/m/Y', strtotime($interro['date_interrogation'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                    <?= e($interro['nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e($interro['matiere_nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= e($interro['classe_code']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= e($interro['periode_nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    /<?= e($interro['note_sur']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'planifiee' => 'bg-yellow-100 text-yellow-800',
                                        'en_cours' => 'bg-blue-100 text-blue-800',
                                        'terminee' => 'bg-green-100 text-green-800',
                                        'annulee' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabel = [
                                        'planifiee' => 'Planifiée',
                                        'en_cours' => 'En cours',
                                        'terminee' => 'Terminée',
                                        'annulee' => 'Annulée'
                                    ];
                                    ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColors[$interro['statut']] ?? 'bg-gray-100' ?>">
                                        <?= $statusLabel[$interro['statut']] ?? $interro['statut'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <?php if (empty($_GET['iframe'])): ?>
                                        <a href="<?= url('notes/saisie?interrogation_id=' . $interro['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 p-2 hover:bg-indigo-50 rounded transition"
                                           title="Saisir les notes">
                                            <i class="fas fa-pen-nib"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="<?= url('interrogations/edit/' . $interro['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('interrogations/delete/' . $interro['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded transition"
                                           onclick="return confirm('Confirmer la suppression ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
