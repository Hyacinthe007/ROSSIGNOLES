

<div class="p-4 md:p-8">
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-stream text-purple-600 mr-2"></i>
                Gestion des Séries
            </h1>
            <p class="text-gray-600">Organisation des séries scolaires par niveau</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openAddModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-bold">
                <i class="fas fa-plus"></i>
                <span>Nouvelle Série</span>
            </button>
            <a href="<?= url('classes/list') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à l'enseignement</span>
            </a>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Série</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Niveau</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Cycle</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($series)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-5xl mb-4 block text-gray-300"></i>
                                <p class="text-lg font-medium">Aucune série trouvée</p>
                                <p class="text-sm mt-1">Commencez par ajouter une nouvelle série</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($series as $serie): ?>
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">
                                    <?= e($serie['code'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    <i class="fas fa-graduation-cap text-green-600 mr-2"></i>
                                    <?= e($serie['libelle'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php if (!empty($serie['niveau_libelle'])): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            <i class="fas fa-layer-group mr-1.5 opacity-70"></i>
                                            <?= e($serie['niveau_libelle']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php if (!empty($serie['cycle'])): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                            <i class="fas fa-circle-notch mr-1.5 opacity-70"></i>
                                            <?= e($serie['cycle']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    <?= e($serie['description'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="<?= url('pedagogie/series/toggle/' . $serie['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="px-2 py-1 text-xs font-semibold rounded-full <?= ($serie['actif'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?> hover:opacity-80 transition">
                                        <?= ($serie['actif'] ?? 0) ? 'Actif' : 'Inactif' ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?= url('pedagogie/series/coefficients/' . $serie['id']) ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" 
                                           class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Coefficients">
                                            <i class="fas fa-percentage"></i>
                                        </a>
                                        <button onclick='openEditModal(<?= json_encode($serie) ?>)' 
                                                class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete(<?= $serie['id'] ?>)" 
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Modal Ajout/Modification -->
<div id="serieModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeModal()"></div>
        
        <!-- Modal content -->
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Nouvelle Série</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="serieForm" method="POST" action="<?= url('pedagogie/series/add') ?><?= !empty($_GET['iframe']) ? '?iframe=1' : '' ?>" class="p-6 space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="serieId">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="serieCode" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none"
                           placeholder="Ex: S, L, OSE...">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Libellé <span class="text-red-500">*</span></label>
                    <input type="text" name="libelle" id="serieLibelle" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none"
                           placeholder="Ex: Scientifique, Littéraire...">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Niveau <span class="text-red-500">*</span></label>
                    <select name="niveau_id" id="serieNiveau" required 
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none bg-white">
                        <option value="">Sélectionner un niveau</option>
                        <?php foreach ($niveaux as $niveau): ?>
                            <option value="<?= $niveau['id'] ?>"><?= e($niveau['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="serieDescription" rows="3" 
                              class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none resize-none"
                              placeholder="Description facultative..."></textarea>
                </div>
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="actif" id="serieActif" value="1" checked 
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label for="serieActif" class="text-sm font-medium text-gray-700">Série active</label>
                </div>
                
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal()" 
                            class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold shadow-lg shadow-purple-200 transition-all">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById('serieModal');
const form = document.getElementById('serieForm');
const modalTitle = document.getElementById('modalTitle');

const isIframe = <?= !empty($_GET['iframe']) ? 'true' : 'false' ?>;
const iframeParam = isIframe ? '?iframe=1' : '';

function openAddModal() {
    modalTitle.innerText = "Nouvelle Série";
    form.action = "<?= url('pedagogie/series/add') ?>" + iframeParam;
    form.reset();
    document.getElementById('serieId').value = "";
    document.getElementById('serieActif').checked = true;
    modal.classList.remove('hidden');
}

function openEditModal(serie) {
    modalTitle.innerText = "Modifier la Série";
    form.action = "<?= url('pedagogie/series/edit/') ?>" + serie.id + iframeParam;
    
    document.getElementById('serieId').value = serie.id;
    document.getElementById('serieCode').value = serie.code || '';
    document.getElementById('serieLibelle').value = serie.libelle || '';
    document.getElementById('serieNiveau').value = serie.niveau_id || '';
    document.getElementById('serieDescription').value = serie.description || '';
    document.getElementById('serieActif').checked = parseInt(serie.actif) === 1;
    
    modal.classList.remove('hidden');
}

function closeModal() {
    modal.classList.add('hidden');
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette série ? Si elle est utilisée par des classes, elle sera simplement désactivée.')) {
        window.location.href = "<?= url('pedagogie/series/delete/') ?>" + id + iframeParam;
    }
}
</script>



