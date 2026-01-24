<?php
/**
 * Vue : Liste des échéanciers d'écolage - Interface cohérente avec Finance Dashboard
 */
require_once __DIR__ . '/../layout/header.php';

// Calculer les statistiques globales
$totalDu = 0;
$totalPaye = 0;
$totalRestant = 0;
$totalRetards = 0;
$nbElevesAJour = 0;
$nbElevesEnRetard = 0;
$nbElevesPartiel = 0;
$classesUniques = [];

foreach ($echeanciers as $ech) {
    $totalDu += $ech['total_du'];
    $totalPaye += $ech['total_paye'];
    $totalRestant += $ech['total_restant'];
    $totalRetards += $ech['nb_retards'];
    
    if (!in_array($ech['classe_nom'], $classesUniques)) {
        $classesUniques[] = $ech['classe_nom'];
    }
    
    if ($ech['nb_retards'] > 0) {
        $nbElevesEnRetard++;
    } else if ($ech['total_restant'] <= 0) {
        $nbElevesAJour++;
    } else {
        $nbElevesPartiel++;
    }
}
$tauxGlobal = $totalDu > 0 ? ($totalPaye / $totalDu) * 100 : 0;
sort($classesUniques);
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                Gestion des Échéanciers
            </h1>
            <p class="text-gray-600 text-sm md:text-base">
                Année scolaire : <strong><?= htmlspecialchars($anneeScolaire['libelle'] ?? 'N/A') ?></strong>
                &nbsp;•&nbsp;
                <span class="text-blue-600 font-semibold"><?= count($echeanciers) ?></span> élèves
            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('echeancier/retards') ?>" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Retards (<?= $totalRetards ?>)</span>
            </a>
            <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-print"></i>
            </button>
            <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-file-excel"></i>
            </button>
        </div>
    </div>



    <!-- Statistiques secondaires -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800"><?= count($echeanciers) ?></p>
                    <p class="text-xs text-gray-600">Élèves avec échéancier</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600"><?= $nbElevesAJour ?></p>
                    <p class="text-xs text-gray-600">À jour</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-user-clock text-orange-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-orange-600"><?= $nbElevesEnRetard ?></p>
                    <p class="text-xs text-gray-600">En retard</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-yellow-600"><?= $totalRetards ?></p>
                    <p class="text-xs text-gray-600">Échéances en retard</p>
                </div>
            </div>
        </div>
    </div>



    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" 
                       id="searchInput" 
                       placeholder="Rechercher un élève..."
                       onkeyup="filterTable()"
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>
            
            <select class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" 
                    id="filterClasse" onchange="filterTable()">
                <option value="">Toutes les classes</option>
                <?php foreach ($classesUniques as $classe): ?>
                    <option value="<?= htmlspecialchars($classe) ?>"><?= htmlspecialchars($classe) ?></option>
                <?php endforeach; ?>
            </select>
            
            <select class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" 
                    id="filterStatut" onchange="filterTable()">
                <option value="">Tous les statuts</option>
                <option value="a-jour">✓ À jour</option>
                <option value="en-retard">⚠ En retard</option>
                <option value="partiel">◐ Partiel</option>
            </select>
            
            <form method="GET" class="m-0">
                <select name="annee_scolaire_id" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" 
                        onchange="this.form.submit()">
                    <?php foreach ($annesScolaires as $as): ?>
                        <option value="<?= $as['id'] ?>" <?= $as['id'] == $anneeScolaireId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($as['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fas fa-list text-blue-500 mr-2"></i>
                Liste des échéanciers
            </h2>
            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-semibold" id="visibleCount">
                <?= count($echeanciers) ?> élèves
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-xs text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">Matricule</th>
                        <th class="px-4 py-3">Nom - Prénom</th>
                        <th class="px-4 py-3">Classe</th>
                        <th class="px-4 py-3 text-right">Total dû</th>
                        <th class="px-4 py-3 text-right">Payé</th>
                        <th class="px-4 py-3 text-right">Reste</th>
                        <th class="px-4 py-3 text-center">Progression</th>
                        <th class="px-4 py-3 text-center">Statut</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (empty($echeanciers)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-100 p-4 rounded-full mb-4">
                                        <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Aucun échéancier trouvé</p>
                                    <p class="text-gray-400 text-sm mt-1">Il n'y a pas d'échéancier pour cette année scolaire.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($echeanciers as $ech): ?>
                            <?php 
                                $tauxPaiement = $ech['total_du'] > 0 ? ($ech['total_paye'] / $ech['total_du']) * 100 : 0;
                                $statusClass = $ech['nb_retards'] > 0 ? 'red' : ($tauxPaiement >= 100 ? 'green' : 'yellow');
                                $statusText = $ech['nb_retards'] > 0 ? 'En retard' : ($tauxPaiement >= 100 ? 'À jour' : 'Partiel');
                                $dataStatut = $ech['nb_retards'] > 0 ? 'en-retard' : ($tauxPaiement >= 100 ? 'a-jour' : 'partiel');
                            ?>
                            <tr class="echeancier-row text-sm hover:bg-gray-50 cursor-pointer transition" 
                                data-statut="<?= $dataStatut ?>"
                                onclick="window.location.href='<?= url('echeancier/show?eleve_id=' . $ech['eleve_id'] . '&annee_scolaire_id=' . $anneeScolaireId) ?>'">
                                
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900 font-mono"><?= htmlspecialchars($ech['matricule']) ?></span>
                                </td>
                                
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-<?= $statusClass ?>-100 flex items-center justify-center">
                                            <span class="font-bold text-<?= $statusClass ?>-600 text-xs">
                                                <?= strtoupper(substr($ech['nom'], 0, 1) . substr($ech['prenom'], 0, 1)) ?>
                                            </span>
                                        </div>
                                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($ech['nom'] . ' ' . $ech['prenom']) ?></p>
                                    </div>
                                </td>
                                
                                <td class="px-4 py-3">
                                    <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded-lg text-xs font-medium">
                                        <?= htmlspecialchars($ech['classe_nom']) ?>
                                    </span>
                                </td>
                                
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                    <?= number_format($ech['total_du'], 0, ',', ' ') ?>
                                </td>
                                
                                <td class="px-4 py-3 text-right font-semibold text-green-600">
                                    <?= number_format($ech['total_paye'], 0, ',', ' ') ?>
                                </td>
                                
                                <td class="px-4 py-3 text-right font-semibold <?= $ech['total_restant'] > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                    <?= number_format($ech['total_restant'], 0, ',', ' ') ?>
                                </td>
                                
                                <td class="px-4 py-3">
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all bg-<?= $statusClass ?>-500" 
                                                 style="width: <?= min(100, $tauxPaiement) ?>%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-<?= $statusClass ?>-600">
                                            <?= number_format($tauxPaiement, 0) ?>%
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-<?= $statusClass ?>-100 text-<?= $statusClass ?>-700">
                                        <?= $statusText ?>
                                        <?php if ($ech['nb_retards'] > 0): ?>
                                            (<?= $ech['nb_retards'] ?>)
                                        <?php endif; ?>
                                    </span>
                                </td>
                                
                                <td class="px-4 py-3" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= url('echeancier/show?eleve_id=' . $ech['eleve_id'] . '&annee_scolaire_id=' . $anneeScolaireId) ?>" 
                                           class="p-2 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                                           title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('echeancier/export-pdf?eleve_id=' . $ech['eleve_id'] . '&annee_scolaire_id=' . $anneeScolaireId) ?>" 
                                           class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition"
                                           title="Télécharger PDF">
                                            <i class="fas fa-file-pdf"></i>
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

<script>
function filterTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const filterStatut = document.getElementById('filterStatut').value;
    const filterClasse = document.getElementById('filterClasse').value;
    const rows = document.querySelectorAll('.echeancier-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const statut = row.getAttribute('data-statut');
        const classe = row.getAttribute('data-classe');
        
        const matchSearch = text.includes(searchInput);
        const matchStatut = !filterStatut || statut === filterStatut;
        const matchClasse = !filterClasse || classe === filterClasse;
        
        const isVisible = matchSearch && matchStatut && matchClasse;
        row.style.display = isVisible ? '' : 'none';
        
        if (isVisible) visibleCount++;
    });
    
    document.getElementById('visibleCount').textContent = visibleCount + ' élève' + (visibleCount > 1 ? 's' : '');
}

function exportToExcel() {
    const rows = document.querySelectorAll('.echeancier-row');
    let csvContent = "Matricule,Nom,Prénom,Classe,Total Dû,Payé,Reste,Progression,Statut\n";
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cells = row.querySelectorAll('td');
            const studentInfo = cells[0].querySelector('p.font-semibold').textContent.trim();
            const nom = studentInfo.split(' ')[0];
            const prenom = studentInfo.split(' ').slice(1).join(' ');
            const matricule = cells[0].querySelector('.text-xs').textContent.replace(/[^\w]/g, '').trim();
            const classe = cells[1].textContent.trim();
            const totalDu = cells[2].textContent.trim();
            const paye = cells[3].textContent.trim();
            const reste = cells[4].textContent.trim();
            const progression = cells[5].querySelector('.text-xs').textContent.trim();
            const statut = cells[6].textContent.trim();
            
            csvContent += `"${matricule}","${nom}","${prenom}","${classe}","${totalDu}","${paye}","${reste}","${progression}","${statut}"\n`;
        }
    });
    
    const blob = new Blob(["\ufeff" + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'echeanciers_<?= date('Y-m-d') ?>.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
