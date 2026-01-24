<?php
/**
 * Vue : Liste des élèves en retard de paiement (Old Échéancier)
 */
require_once __DIR__ . '/../layout/header.php';

$totalRetardGlobal = 0;
foreach ($elevesEnRetard as $eleve) {
    $totalRetardGlobal += $eleve['total_retard'];
}
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="<?= url('echeancier/list') ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-600 p-2 rounded-lg transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
                    Élèves en Retard
                </h1>
            </div>
            <p class="text-gray-600 text-sm md:text-base">
                Année scolaire : <strong><?= htmlspecialchars($anneeScolaire['libelle'] ?? 'N/A') ?></strong>
                &nbsp;•&nbsp;
                <span class="text-red-600 font-semibold"><?= count($elevesEnRetard) ?></span> élèves en retard
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-print"></i>
                <span>Imprimer</span>
            </button>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-500">
            <div class="flex items-center gap-3">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-users text-orange-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800"><?= count($elevesEnRetard) ?></p>
                    <p class="text-xs text-gray-600">Élèves concernés</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-red-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600"><?= number_format($totalRetardGlobal, 0, ',', ' ') ?> Ar</p>
                    <p class="text-xs text-gray-600">Total des impayés</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800"><?= date('d F Y') ?></p>
                    <p class="text-xs text-gray-600">État au</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-xs text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-6 py-4">Matricule</th>
                        <th class="px-6 py-4">Élève</th>
                        <th class="px-6 py-4">Classe</th>
                        <th class="px-6 py-4 text-center">Échéances en retard</th>
                        <th class="px-6 py-4 text-right">Montant Total Retard</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (empty($elevesEnRetard)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 ">
                                Aucun élève en retard pour le moment.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($elevesEnRetard as $eleve): ?>
                            <tr class="hover:bg-gray-50 transition cursor-pointer" 
                                onclick="window.location.href='<?= url('echeancier/view?eleve_id=' . $eleve['eleve_id'] . '&annee_scolaire_id=' . $anneeScolaireId) ?>'">
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-blue-600 font-bold">
                                    <?= htmlspecialchars($eleve['matricule']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">
                                        <?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                        <?= htmlspecialchars($eleve['classe_nom']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-orange-100 text-orange-700 font-bold px-3 py-1 rounded-full text-sm">
                                        <?= $eleve['nb_echeances_retard'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-red-600">
                                    <?= number_format($eleve['total_retard'], 0, ',', ' ') ?> Ar
                                </td>
                                <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                    <a href="<?= url('echeancier/view?eleve_id=' . $eleve['eleve_id'] . '&annee_scolaire_id=' . $anneeScolaireId) ?>" 
                                       class="text-blue-600 hover:text-blue-800 font-bold text-sm bg-blue-50 px-3 py-2 rounded-lg transition inline-flex items-center gap-2">
                                        <i class="fas fa-eye"></i>
                                        <span>Détails</span>
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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

