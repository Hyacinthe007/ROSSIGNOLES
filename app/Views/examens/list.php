<?php
$title = "Examens & Compositions";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => url('dashboard')],
    ['label' => 'Évaluations', 'url' => url('evaluations')],
    ['label' => 'Examens & Compositions']
];
?>

<div class="<?= empty($_GET['iframe']) ? 'p-4 md:p-8' : 'p-2 md:p-3' ?>">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <?php if (empty($_GET['iframe'])): ?>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-graduation-cap text-indigo-600 mr-2"></i>
                    Examens & Compositions
                </h1>
                <p class="text-gray-600 text-sm md:text-base">Gestion des examens finaux</p>
            </div>
        <?php else: ?>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Examens & Compositions</h2>
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

            <a href="<?= url('examens/add') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg font-semibold">
                <i class="fas fa-plus"></i>
                <span>Nouvel examen</span>
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:w-1/3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                <select name="classe_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Toutes les classes</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($filters['classe_id'] == $c['id']) ? 'selected' : '' ?>>
                            <?= e($c['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full sm:w-1/3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                <select name="periode_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Toutes les périodes</option>
                    <?php foreach ($periodes as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($filters['periode_id'] == $p['id']) ? 'selected' : '' ?>>
                            <?= e($p['nom']) ?>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Examen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note sur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($examens)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-graduation-cap text-4xl mb-4 block"></i>
                                Aucun examen trouvé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($examens as $examen): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d/m/Y', strtotime($examen['date_examen'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                    <?= e($examen['nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e($examen['matiere_nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <?= e($examen['classe_nom']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= e($examen['periode_nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    /<?= e($examen['note_sur']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'planifie' => 'bg-yellow-100 text-yellow-800',
                                        'en_cours' => 'bg-blue-100 text-blue-800',
                                        'termine' => 'bg-green-100 text-green-800',
                                        'annule' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabel = [
                                        'planifie' => 'Planifié',
                                        'en_cours' => 'En cours',
                                        'termine' => 'Terminé',
                                        'annule' => 'Annulé'
                                    ];
                                    ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColors[$examen['statut']] ?? 'bg-gray-100' ?>">
                                        <?= $statusLabel[$examen['statut']] ?? $examen['statut'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <?php if (empty($_GET['iframe'])): ?>
                                        <a href="<?= url('notes/saisie?examen_id=' . $examen['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 p-2 hover:bg-indigo-50 rounded transition"
                                           title="Saisir les notes">
                                            <i class="fas fa-pen-nib"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="<?= url('examens/edit/' . $examen['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('examens/delete/' . $examen['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
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
