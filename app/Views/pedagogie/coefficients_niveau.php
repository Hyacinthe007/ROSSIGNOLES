<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/header.php'; ?>
<?php else: ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>
    <body class="bg-white">
<?php endif; ?>

<div class="p-4 md:p-8">

    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                <a href="<?= url('pedagogie/niveaux') ?>?iframe=1" class="hover:text-blue-600 transition">Niveaux</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span>Coefficients par Niveau</span>
            </div>
            <h1 class="text-lg md:text-xl font-semibold text-gray-800">
                <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                Coefficients : <?= e($niveau['libelle']) ?>
            </h1>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('pedagogie/niveaux') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à l'enseignement</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Configuration des coefficients -->
        <div class="xl:col-span-2">
            <form action="<?= url('pedagogie/niveaux/update-coefficients') ?><?= isset($_GET['iframe']) ? '?iframe=1' : '' ?>" method="POST" class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <?= csrf_field() ?>
                <input type="hidden" name="niveau_id" value="<?= $niveau['id'] ?>">

                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="font-bold text-gray-700">Matières du niveau</h2>
                    <span class="text-sm text-gray-500"><?= count($matieresAssociees) ?> matière(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Coefficient</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">H/Semaine</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Obligatoire</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase w-16">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($matieresAssociees)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        <i class="fas fa-book-open text-4xl mb-4 block opacity-20"></i>
                                        Aucune matière configurée pour ce niveau.<br>
                                        Utilisez le panneau de droite pour en ajouter.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($matieresAssociees as $ma): ?>
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-800"><?= e($ma['matiere_nom']) ?></div>
                                            <div class="text-xs text-gray-500"><?= e($ma['code']) ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="number" 
                                                   name="matieres[<?= $ma['matiere_id'] ?>][coefficient]" 
                                                   value="<?= $ma['coefficient'] ?>" 
                                                   step="0.25" min="0.25"
                                                   class="w-20 text-center px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="number" 
                                                   name="matieres[<?= $ma['matiere_id'] ?>][heures_semaine]" 
                                                   value="<?= $ma['heures_semaine'] ?>" 
                                                   step="0.5" min="0"
                                                   class="w-20 text-center px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" 
                                                       name="matieres[<?= $ma['matiere_id'] ?>][obligatoire]" 
                                                       value="1" 
                                                       <?= $ma['obligatoire'] ? 'checked' : '' ?>
                                                       class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </label>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button type="button" 
                                                    onclick="removeMatiere(this)"
                                                    class="text-red-400 hover:text-red-600 p-2 transition">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-lg transition flex items-center gap-2">
                        <i class="fas fa-save font-normal"></i>
                        Enregistrer les coefficients
                    </button>
                </div>
            </form>
        </div>

        <!-- Panel d'ajout de matières -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-green-500"></i>
                    Ajouter une matière
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher une matière</label>
                        <div class="relative">
                            <input type="text" id="matiereSearch" placeholder="Nom ou code..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>

                    <div class="max-h-[50vh] overflow-y-auto pr-2 space-y-2" id="matiereList">
                        <?php 
                        $assocIds = array_column($matieresAssociees, 'matiere_id');
                        foreach ($toutesMatieres as $m): 
                            $isAssoc = in_array($m['id'], $assocIds);
                        ?>
                            <div class="matiere-item p-3 border rounded-lg flex items-center justify-between hover:bg-gray-50 transition <?= $isAssoc ? 'opacity-50 pointer-events-none' : '' ?>" 
                                 data-id="<?= $m['id'] ?>"
                                 data-nom="<?= e($m['nom']) ?>"
                                 data-code="<?= e($m['code']) ?>">
                                <div>
                                    <div class="font-medium text-sm text-gray-800"><?= e($m['nom']) ?></div>
                                    <div class="text-[10px] text-gray-500 uppercase font-mono"><?= e($m['code']) ?></div>
                                </div>
                                <?php if (!$isAssoc): ?>
                                    <button type="button" 
                                            onclick="addMatiereToTable(<?= $m['id'] ?>, '<?= e($m['nom']) ?>', '<?= e($m['code']) ?>')"
                                            class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold uppercase">Déjà ajoutée</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="bg-blue-600 rounded-xl shadow-lg p-6 text-white overflow-hidden relative">
                <i class="fas fa-info-circle absolute -right-4 -bottom-4 text-8xl opacity-10"></i>
                <h3 class="font-bold mb-2">Note</h3>
                <p class="text-sm text-blue-100 leading-relaxed">
                    Les coefficients définis ici s'appliquent à toutes les classes de ce niveau, sauf si une série spécifique ou un override classe est défini.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function addMatiereToTable(id, nom, code) {
    const tbody = document.querySelector('tbody');
    const emptyRow = tbody.querySelector('td[colspan="5"]');
    if (emptyRow) tbody.innerHTML = '';
    if (document.querySelector(`input[name="matieres[${id}][coefficient]"]`)) return;

    const row = document.createElement('tr');
    row.className = 'hover:bg-blue-50/30 transition-colors animate-fadeIn';
    row.innerHTML = `
        <td class="px-6 py-4">
            <div class="font-semibold text-gray-800">${nom}</div>
            <div class="text-xs text-gray-500">${code}</div>
        </td>
        <td class="px-6 py-4">
            <input type="number" name="matieres[${id}][coefficient]" value="1" step="0.25" min="0.25" class="w-20 text-center px-2 py-1 border border-blue-300 bg-blue-50 rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </td>
        <td class="px-6 py-4">
            <input type="number" name="matieres[${id}][heures_semaine]" value="" step="0.5" min="0" placeholder="0" class="w-20 text-center px-2 py-1 border border-blue-300 bg-blue-50 rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </td>
        <td class="px-6 py-4 text-center">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="matieres[${id}][obligatoire]" value="1" checked class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
        </td>
        <td class="px-6 py-4 text-right">
            <button type="button" onclick="removeMatiere(this)" class="text-red-400 hover:text-red-600 p-2 transition">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);

    const listItem = document.querySelector(`.matiere-item[data-id="${id}"]`);
    if (listItem) {
        listItem.classList.add('opacity-50', 'pointer-events-none');
        const btn = listItem.querySelector('button');
        if (btn) {
            btn.remove();
            const badge = document.createElement('span');
            badge.className = 'text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold uppercase';
            badge.textContent = 'Déjà ajoutée';
            listItem.appendChild(badge);
        }
    }
}

function removeMatiere(btn) {
    if (!confirm('Voulez-vous retirer cette matière du niveau ?')) return;
    const row = btn.closest('tr');
    const input = row.querySelector('input[name^="matieres"]');
    const id = input.name.match(/\[(\d+)\]/)[1];
    row.classList.add('opacity-0', '-translate-x-4');
    setTimeout(() => {
        row.remove();
        const listItem = document.querySelector(`.matiere-item[data-id="${id}"]`);
        if (listItem) {
            listItem.classList.remove('opacity-50', 'pointer-events-none');
            const badge = listItem.querySelector('span');
            if (badge) badge.remove();
            const addBtn = document.createElement('button');
            addBtn.type = 'button';
            addBtn.onclick = () => addMatiereToTable(id, listItem.dataset.nom, listItem.dataset.code);
            addBtn.className = 'p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition';
            addBtn.innerHTML = '<i class="fas fa-plus"></i>';
            listItem.appendChild(addBtn);
        }
        const tbody = document.querySelector('tbody');
        if (tbody.children.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-gray-400"><i class="fas fa-book-open text-4xl mb-4 block opacity-20"></i>Aucune matière configurée pour ce niveau.<br>Utilisez le panneau de droite pour en ajouter.</td></tr>';
        }
    }, 300);
}

document.getElementById('matiereSearch').addEventListener('input', function(e) {
    const text = e.target.value.toLowerCase();
    document.querySelectorAll('.matiere-item').forEach(item => {
        const nom = item.dataset.nom.toLowerCase();
        const code = item.dataset.code.toLowerCase();
        item.style.display = (nom.includes(text) || code.includes(text)) ? 'flex' : 'none';
    });
});
</script>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
</style>
</div>

<?php if (empty($_GET['iframe'])): ?>
    <?php require_once APP_PATH . '/Views/layout/footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>
