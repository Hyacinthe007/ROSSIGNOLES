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

        <!-- Filtres et Recherche -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                       id="receiptSearchInput"
                       placeholder="Rechercher par élève, matricule, n° facture, classe..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
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
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group" data-sort="date" data-order="desc">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-gray-400 group-hover:text-green-600 transition-colors"></i>
                                    <span>Date</span>
                                    <i class="fas fa-sort text-gray-300 ml-auto"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group" data-sort="text">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-gray-400 group-hover:text-green-600 transition-colors"></i>
                                    <span>Élève</span>
                                    <i class="fas fa-sort text-gray-300 ml-auto"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group" data-sort="text">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-chalkboard-teacher text-gray-400 group-hover:text-green-600 transition-colors"></i>
                                    <span>Mois Écolage</span>
                                    <i class="fas fa-sort text-gray-300 ml-auto"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group" data-sort="text">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-invoice text-gray-400 group-hover:text-green-600 transition-colors"></i>
                                    <span>Facture</span>
                                    <i class="fas fa-sort text-gray-300 ml-auto"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group" data-sort="text">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-money-bill-wave text-gray-400 group-hover:text-green-600 transition-colors"></i>
                                    <span>Mode</span>
                                    <i class="fas fa-sort text-gray-300 ml-auto"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group" data-sort="number">
                                <div class="flex items-center justify-end gap-2">
                                    <i class="fas fa-money-bill-wave text-gray-400 group-hover:text-green-600 transition-colors"></i>
                                    <span>Montant</span>
                                    <i class="fas fa-sort text-gray-300 ml-2"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">
                                <div class="flex items-center justify-end gap-2">
                                    <i class="fas fa-tools text-gray-400"></i>
                                    <span>Actions</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($paiements as $paiement): 
                            $mois = !empty($paiement['mois_ecolage']) ? explode(', ', $paiement['mois_ecolage']) : [null];
                            foreach ($mois as $index => $m):
                        ?>
                            <tr class="receipt-row hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-val="<?= strtotime($paiement['date_paiement']) ?>">
                                    <?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-val="<?= e($paiement['eleve_nom'] . ' ' . $paiement['eleve_prenom']) ?>">
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" data-val="<?= e($m ?: '') ?>">
                                    <?php if ($m): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            <?= e($m) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400  text-xs">Autre / Frais divers</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" data-val="<?= e($paiement['numero_facture'] ?? '') ?>">
                                    <?= e($paiement['numero_facture'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" data-val="<?= e($paiement['mode_paiement_libelle'] ?? '') ?>">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= e($paiement['mode_paiement_libelle'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-green-600" data-val="<?php 
                                    $nombreMois = count($mois);
                                    $mParMois = $nombreMois > 0 ? $paiement['montant'] / $nombreMois : $paiement['montant'];
                                    echo $mParMois;
                                ?>">
                                    <?= number_format($mParMois, 0, ',', ' ') ?> Ar
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
    const tableBody = document.querySelector('tbody');
    const rows = Array.from(tableBody.getElementsByClassName('receipt-row'));
    const headers = document.querySelectorAll('th[data-sort]');

    // --- LOGIQUE DE RECHERCHE ---
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
        searchInput.focus();
    }

    // --- LOGIQUE DE TRI ---
    headers.forEach(header => {
        header.addEventListener('click', () => {
            const sortType = header.getAttribute('data-sort');
            const currentOrder = header.getAttribute('data-order') || 'asc';
            const nextOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            const colIndex = Array.from(header.parentElement.children).indexOf(header);

            // Reset other headers
            headers.forEach(h => {
                h.removeAttribute('data-order');
                const icon = h.querySelector('.fa-sort, .fa-sort-up, .fa-sort-down');
                if (icon) {
                    icon.className = 'fas fa-sort text-gray-300 ml-auto';
                    if (h.classList.contains('text-right')) icon.className = 'fas fa-sort text-gray-300 ml-2';
                }
            });

            // Update current header
            header.setAttribute('data-order', nextOrder);
            const statusIcon = header.querySelector('.fa-sort');
            if (statusIcon) {
                statusIcon.className = `fas fa-sort-${nextOrder === 'asc' ? 'up' : 'down'} text-green-600 ${header.classList.contains('text-right') ? 'ml-2' : 'ml-auto'}`;
            }

            // Sort rows
            const sortedRows = rows.sort((a, b) => {
                const aVal = a.children[colIndex].getAttribute('data-val');
                const bVal = b.children[colIndex].getAttribute('data-val');

                if (sortType === 'number') {
                    return nextOrder === 'asc' 
                        ? parseFloat(aVal) - parseFloat(bVal)
                        : parseFloat(bVal) - parseFloat(aVal);
                } else if (sortType === 'date') {
                    return nextOrder === 'asc'
                        ? parseInt(aVal) - parseInt(bVal)
                        : parseInt(bVal) - parseInt(aVal);
                } else {
                    return nextOrder === 'asc'
                        ? aVal.localeCompare(bVal)
                        : bVal.localeCompare(aVal);
                }
            });

            // Re-render table body
            sortedRows.forEach(row => tableBody.appendChild(row));
        });
    });
});
</script>

