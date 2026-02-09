<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-receipt text-green-600 mr-2"></i>
                    Reçus de paiement
                </h1>
                <p class="text-gray-600 text-sm md:text-base">Consultez et imprimez les reçus de paiement</p>
            </div>
            <div class="flex gap-2">
                <?php if (hasPermission('finance_recus_export')): ?>
                    <a href="<?= url('finance/recus/export-excel' . (!empty($search) ? '?search=' . urlencode($search) : '')) ?>" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow-md font-medium">
                        <i class="fas fa-file-excel"></i>
                        Export Excel
                    </a>
                <?php endif; ?>
                <a href="<?= url('finance/paiement-mensuel') ?>" 
                   class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-md font-medium">
                    <i class="fas fa-arrow-left"></i>
                    Retour au paiement
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <form method="GET" action="<?= url('finance/recus') ?>" id="receiptSearchForm" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               id="receiptSearchInput"
                               name="search" 
                               value="<?= e($search ?? '') ?>"
                               placeholder="Filtrer par élève, matricule, n° facture, classe..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div id="searchLoader" class="hidden flex items-center px-2">
                    <i class="fas fa-spinner fa-spin text-green-600"></i>
                </div>
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition flex items-center justify-center gap-2 shadow font-medium">
                    <i class="fas fa-filter"></i>
                    <span>Filtrer</span>
                </button>
                <?php if (!empty($search)): ?>
                    <a href="<?= url('finance/recus') ?>" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Réinitialiser</span>
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <?php if (empty($paiements)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                <?php if (!empty($search)): ?>
                    <p class="font-medium">Aucun reçu trouvé pour "<?= e($search) ?>"</p>
                    <p class="text-sm mt-2">Essayez avec d'autres termes de recherche</p>
                <?php else: ?>
                    <p>Aucun reçu de paiement enregistré</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-calendar-alt mr-2"></i>Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-user mr-2"></i>Élève</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-chalkboard-teacher mr-2"></i>Mois Écolage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-file-invoice mr-2"></i>Facture</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-money-bill-wave mr-2"></i>Mode</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-money-bill-wave mr-2"></i>Montant</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-900 uppercase tracking-wider"><i class="fas fa-tools mr-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($paiements as $paiement): 
                            $mois = !empty($paiement['mois_ecolage']) ? explode(', ', $paiement['mois_ecolage']) : [null];
                            foreach ($mois as $index => $m):
                        ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($paiement['eleve_nom']): ?>
                                        <div>
                                            <div class="font-medium"><?= e($paiement['eleve_nom'] . ' ' . $paiement['eleve_prenom']) ?></div>
                                            <div class="text-xs text-gray-500">
                                                <?= e($paiement['matricule']) ?> - <?= e($paiement['classe_code'] ?? 'N/A') ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php if ($m): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            <?= e($m) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400  text-xs">Autre / Frais divers</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= e($paiement['numero_facture'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= e($paiement['mode_paiement_libelle'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-green-600">
                                    <?php 
                                    // Calculer le montant par mois
                                    $nombreMois = count($mois);
                                    $montantParMois = $nombreMois > 0 ? $paiement['montant'] / $nombreMois : $paiement['montant'];
                                    ?>
                                    <?= number_format($montantParMois, 0, ',', ' ') ?> Ar
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('finance/recus?id=' . $paiement['id']) ?>" 
                                           target="_blank"
                                           class="text-indigo-600 hover:text-indigo-900 p-2 hover:bg-indigo-50 rounded transition"
                                           title="Aperçu">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('finance/export-recu/' . $paiement['id']) ?>" 
                                           class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded transition"
                                           title="Télécharger PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('receiptSearchInput');
    const loader = document.getElementById('searchLoader');
    let timeout = null;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value.trim();
            
            // Afficher le loader
            if (loader) loader.classList.remove('hidden');

            timeout = setTimeout(() => {
                // Rediriger vers l'URL avec le paramètre de recherche
                // On utilise window.location pour recharger la page avec les nouveaux résultats de la DB
                // C'est le moyen le plus simple d'interroger la base sans refaire toute l'API AJAX
                const baseUrl = "<?= url('finance/recus') ?>";
                const url = query ? `${baseUrl}?search=${encodeURIComponent(query)}` : baseUrl;
                
                window.location.href = url;
            }, 800); // Délai de 800ms pour éviter trop de rechargements pendant la frappe
        });

        // Placer le curseur à la fin du texte si déjà présent (après rechargement)
        if (searchInput.value) {
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }
    }
});
</script>

