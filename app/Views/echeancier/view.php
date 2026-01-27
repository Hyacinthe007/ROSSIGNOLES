<?php
/**
 * Vue : Détails de l'échéancier d'un élève
 */
require_once __DIR__ . '/../layout/header.php';

// Extraire les données
$echeances = $data['echeances'] ?? [];
$statistiques = $data['statistiques'] ?? [];
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="<?= url('eleves/list?annee_scolaire_id=' . $anneeScolaireId) ?>" class="text-gray-500 hover:text-gray-700 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                    Échéancier d'Écolage
                </h1>
            </div>
            <p class="text-gray-600">
                Détails pour <span class="font-semibold text-gray-900"><?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) ?></span>
                <span class="text-gray-400 mx-2">•</span>
                <span class="text-sm text-gray-500">Matricule: <?= htmlspecialchars($eleve['matricule']) ?></span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-graduation-cap mr-2"></i><?= htmlspecialchars($anneeScolaire['libelle'] ?? 'N/A') ?>
            </div>
            <a href="<?= url('echeancier/export-pdf?eleve_id=' . $eleveId . '&annee_scolaire_id=' . $anneeScolaireId) ?>" 
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-sm flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Télécharger PDF
            </a>
        </div>
    </div>

    <!-- Alertes Statut -->
    <?php 
    $nbRetards = $statistiques['nb_echeances_en_retard'] ?? 0;
    $nbExclusions = 0;
    foreach ($echeances as $ech) {
        if (in_array($ech['statut'], ['exclusion'])) $nbExclusions++;
    }
    ?>

    <?php if ($nbExclusions > 0): ?>
        <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-r-lg shadow-sm mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-ban text-red-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">Exclusion en cours</h3>
                    <p class="text-sm text-red-700 mt-1">
                        Cet élève a <?= $nbExclusions ?> mensualité(s) en statut d'exclusion. L'accès aux cours doit être refusé jusqu'à régularisation.
                    </p>
                </div>
            </div>
        </div>
    <?php elseif ($nbRetards > 0): ?>
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg shadow-sm mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-orange-800 uppercase tracking-wide">Paiements en retard</h3>
                    <p class="text-sm text-orange-700 mt-1">
                        Cet élève a <?= $nbRetards ?> échéance(s) en retard. Merci de relancer les parents.
                    </p>
                </div>
            </div>
        </div>
    <?php elseif (($statistiques['total_restant'] ?? 0) <= 0): ?>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-green-800 uppercase tracking-wide">Paiements à jour</h3>
                    <p class="text-sm text-green-700 mt-1">
                        Tous les paiements sont à jour. Excellent suivi !
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-wallet text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800"><?= number_format($statistiques['total_du'] ?? 0, 0, ',', ' ') ?></p>
                    <p class="text-xs text-gray-600">Total dû (AR)</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600"><?= number_format($statistiques['total_paye'] ?? 0, 0, ',', ' ') ?></p>
                    <p class="text-xs text-gray-600">Payé (AR)</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600"><?= number_format($statistiques['total_restant'] ?? 0, 0, ',', ' ') ?></p>
                    <p class="text-xs text-gray-600">Reste (AR)</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-chart-pie text-purple-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-purple-600"><?= number_format($statistiques['taux_paiement'] ?? 0, 1) ?>%</p>
                    <p class="text-xs text-gray-600">Progression</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des échéances -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list-ol text-blue-600"></i> 
                Échéancier détaillé
            </h2>
            <span class="text-xs font-medium text-gray-500 bg-white px-3 py-1 rounded-lg border border-gray-200">
                <?= $statistiques['nb_echeances_payees'] ?? 0 ?> / <?= $statistiques['nb_echeances_total'] ?? 0 ?> payées
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">Mois</th>
                        <th class="px-6 py-3 text-right">Montant dû</th>
                        <th class="px-6 py-3 text-right">Payé</th>
                        <th class="px-6 py-3 text-right">Reste</th>
                        <th class="px-6 py-3 text-center">Date limite</th>
                        <th class="px-6 py-3 text-center">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($echeances)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-100 p-4 rounded-full mb-4">
                                        <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Aucune échéance trouvée</p>
                                    <p class="text-gray-400 text-sm mt-1">L'échéancier n'a pas encore été généré.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($echeances as $ech): ?>
                            <?php
                            $badgeColor = match($ech['statut']) {
                                'paye' => 'bg-green-100 text-green-700 ring-green-600/20',
                                'partiel' => 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
                                'retard' => 'bg-orange-100 text-orange-700 ring-orange-600/20',
                                'retard_grave' => 'bg-orange-100 text-orange-800 font-bold ring-orange-600/20',
                                'exclusion' => 'bg-red-100 text-red-700 font-bold ring-red-600/20',
                                default => 'bg-gray-100 text-gray-600 ring-gray-500/10'
                            };
                            $statutLabel = match($ech['statut']) {
                                'paye' => 'Payé',
                                'partiel' => 'Partiel',
                                'retard' => 'Retard',
                                'retard_grave' => 'Retard grave',
                                'exclusion' => 'Exclusion',
                                'en_attente' => 'À venir',
                                default => ucfirst($ech['statut'] ?? 'Impayé')
                            };
                            $rowBg = in_array($ech['statut'], ['exclusion', 'retard_grave']) ? 'bg-red-50/30' : '';
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors <?= $rowBg ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= ucfirst($ech['mois_libelle'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600">
                                    <?= number_format($ech['montant_du'] ?? 0, 0, ',', ' ') ?> AR
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-medium">
                                    <?= number_format($ech['montant_paye'] ?? 0, 0, ',', ' ') ?> AR
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-bold">
                                    <?= number_format($ech['montant_restant'] ?? 0, 0, ',', ' ') ?> AR
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                    <?= date('d/m/Y', strtotime($ech['date_limite'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset <?= $badgeColor ?>">
                                        <?= $statutLabel ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <!-- Total Row -->
                        <tr class="bg-gray-100 font-bold">
                            <td class="px-6 py-4 text-sm text-gray-900">TOTAL</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">
                                <?= number_format($statistiques['total_du'] ?? 0, 0, ',', ' ') ?> AR
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-green-600">
                                <?= number_format($statistiques['total_paye'] ?? 0, 0, ',', ' ') ?> AR
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-red-600">
                                <?= number_format($statistiques['total_restant'] ?? 0, 0, ',', ' ') ?> AR
                            </td>
                            <td colspan="2" class="px-6 py-4"></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
