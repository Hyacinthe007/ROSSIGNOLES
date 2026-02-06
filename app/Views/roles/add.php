<?php
$title = "Nouveau RÃ´le";
$breadcrumbs = [
    ['label' => 'SystÃ¨me', 'url' => '#'],
    ['label' => 'RÃ´les & Permissions', 'url' => url('roles/list')],
    ['label' => 'Nouveau RÃ´le']
];
?>

<div class="p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-user-shield text-blue-600 mr-2"></i>
                    CrÃ©er un nouveau rÃ´le
                </h1>
                <p class="text-gray-600 text-sm md:text-base">DÃ©finissez les caractÃ©ristiques et les accÃ¨s de ce nouveau rÃ´le pour le systÃ¨me.</p>
            </div>
            <div>
                <a href="<?= url('roles/list') ?>" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 p-2.5 rounded-xl bg-white border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <i class="fas fa-arrow-left"></i>
                    <span class="text-sm font-medium">Retour Ã  la liste</span>
                </a>
            </div>
        </div>

        <form action="<?= url('roles/add') ?>" method="POST" class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Informations gÃ©nÃ©rales
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom du rÃ´le <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="nom" required placeholder="Ex: Surveillant GÃ©nÃ©ral"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div class="space-y-2">
                        <label for="niveau" class="block text-sm font-medium text-gray-700">Niveau d'accÃ¨s (HiÃ©rarchie) <span class="text-red-500">*</span></label>
                        <select name="niveau" id="niveau" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="10">Ã‰lÃ¨ve (10)</option>
                            <option value="20">Parent (20)</option>
                            <option value="50" selected>Personnel / Enseignant (50)</option>
                            <option value="80">Administration (80)</option>
                            <option value="90">Direction (90)</option>
                            <option value="100">Super Admin (100)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Plus le niveau est haut, plus le rÃ´le est important.</p>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="DÃ©crivez les responsabilitÃ©s liÃ©es Ã  ce rÃ´le..."
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></textarea>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="actif" value="1" id="actif" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">RÃ´le actif</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-key text-blue-500"></i>
                        Permissions & AccÃ¨s
                    </h2>
                    <div class="flex gap-2">
                        <button type="button" onclick="selectAllPermissions(true)" class="text-xs text-blue-600 hover:font-bold">Tout cocher</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" onclick="selectAllPermissions(false)" class="text-xs text-gray-500 hover:font-bold">Tout dÃ©cocher</button>
                    </div>
                </div>
                <div class="p-0"> <!-- Removed padding for full-height side layout -->
                    <?php
                    $targetLabels = [
                        'inscriptions' => 'Inscriptions (Gestion)',
                        'eleves' => 'Liste des Ã©lÃ¨ves',
                        'parents' => 'Parents / Tuteurs',
                        'scolarite' => 'AccÃ¨s au module ScolaritÃ©',
                        'finance' => 'Finance / Caisse',
                        'echeancier' => 'Ã‰chÃ©anciers',
                        'pedagogie' => 'Enseignement / Classes',
                        'parcours' => 'Parcours Scolaires',
                        'conseils' => 'Conseils de classe',
                        'personnel' => 'Personnel (Gestion)',
                        'absences_personnel' => 'Absences Personnel',
                        'notes' => 'Notes / Ã‰valuations',
                        'bulletins' => 'Bulletins',
                        'absences' => 'Absences & Retards',
                        'sanctions' => 'Sanctions',
                        'viescolaire' => 'AccÃ¨s au module Vie Scolaire',
                        'annonces' => 'Annonces',
                        'notifications' => 'Notifications / SMS',
                        'communication' => 'AccÃ¨s au module Communication',
                        'systeme' => 'Configurations',
                        'roles' => 'RÃ´les & Permissions',
                        'users' => 'Utilisateurs',
                        'logs' => 'Logs / Historique'
                    ];

                    $groupedPermissions = [];
                    foreach ($permissions as $p) {
                        $parts = explode('.', $p['code']);
                        $target = $parts[0] ?? $p['module'];
                        $action = $parts[1] ?? $p['action'];
                        $groupedPermissions[$p['module']][$target][$action] = $p;
                    }

                    // DÃ©finir l'ordre des modules pour correspondre au sidebar
                    $orderedModules = [
                        'ScolaritÃ©',
                        'Finance',
                        'Enseignement',
                        'Ressources Humaines',
                        'Ã‰valuations',
                        'Vie scolaire',
                        'Communication',
                        'ParamÃ¨tres'
                    ];

                    // Filtrer pour ne garder que ceux qui existent dans les donnÃ©es
                    $modules = array_values(array_intersect($orderedModules, array_keys($groupedPermissions)));
                    $firstModule = $modules[0] ?? null;
                    ?>

                    <div class="flex flex-col md:flex-row min-h-[500px]">
                        <!-- Colonne de Gauche : Liste des Modules -->
                        <div class="w-full md:w-64 border-r border-gray-100 bg-gray-50/30">
                            <div class="p-4 space-y-2">
                                <?php foreach ($modules as $module):
                                    $targetCount = isset($groupedPermissions[$module]) ? count($groupedPermissions[$module]) : 0;
                                ?>
                                    <button type="button"
                                        onclick="showModulePermissions('<?= md5($module) ?>')"
                                        id="btn-module-<?= md5($module) ?>"
                                        class="module-tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center justify-between group <?= $module === $firstModule ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-gray-600 hover:bg-white hover:text-blue-600' ?>">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-folder text-xs <?= $module === $firstModule ? 'text-blue-200' : 'text-gray-400 group-hover:text-blue-500' ?>"></i>
                                            <?= e($module) ?>
                                        </div>
                                        <span class="text-[10px] <?= $module === $firstModule ? 'bg-blue-500 text-blue-100' : 'bg-gray-100 text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600' ?> px-2 py-0.5 rounded-full transition-colors">
                                            <?= $targetCount ?>
                                        </span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Colonne de Droite : Permissions du Module -->
                        <div class="flex-1 bg-white">
                            <?php foreach ($groupedPermissions as $module => $targets): ?>
                                <div id="module-content-<?= md5($module) ?>"
                                    class="module-permission-content p-6 <?= $module === $firstModule ? '' : 'hidden' ?>">

                                    <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800"><?= e($module) ?></h3>
                                            <p class="text-xs text-gray-500">Configurez les accÃ¨s dÃ©taillÃ©s pour ce module.</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" onclick="selectModulePermissions('<?= md5($module) ?>', true)" class="text-xs font-bold text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">Tout cocher</button>
                                            <button type="button" onclick="selectModulePermissions('<?= md5($module) ?>', false)" class="text-xs font-bold text-gray-400 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition">Tout dÃ©cocher</button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <?php foreach ($targets as $targetName => $actions): ?>
                                            <div class="mb-6 group">
                                                <div class="flex items-center justify-between mb-3 border-b border-gray-200 pb-1.5">
                                                    <h6 class="text-[9px] font-black text-gray-500 lowercase first-letter:uppercase tracking-wider"><?= e($targetLabels[$targetName] ?? ucfirst($targetName)) ?></h6>
                                                    <div class="flex gap-2">
                                                        <button type="button" onclick="selectTargetPermissions('<?= $targetName ?>', true)" class="text-[8px] font-bold text-blue-500 hover:text-blue-700 uppercase">Tous</button>
                                                        <span class="text-gray-300 text-[8px]">|</span>
                                                        <button type="button" onclick="selectTargetPermissions('<?= $targetName ?>', false)" class="text-[8px] font-bold text-gray-400 hover:text-gray-600 uppercase">Aucun</button>
                                                     </div>
                                                 </div>
                                                 <div class="flex flex-col gap-2 px-1">
                                                    <?php
                                                    $order = ['view' => 1, 'create' => 2, 'update' => 3, 'delete' => 4];
                                                    uksort($actions, function($a, $b) use ($order) {
                                                        return ($order[$a] ?? 99) <=> ($order[$b] ?? 99);
                                                    });
                                                    ?>
                                                    <?php foreach ($actions as $action => $perm): ?>
                                                        <label class="flex items-center gap-2 group/label cursor-pointer">
                                                            <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>"
                                                                data-target="<?= e($targetName) ?>"
                                                                data-module-hash="<?= md5($module) ?>"
                                                                class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer transition-all">
                                                            <span class="text-[11px] font-semibold text-gray-700 group-hover/label:text-blue-600 transition-colors">
                                                                <?php
                                                                $actionLabels = ['view' => 'Lire', 'create' => 'CrÃ©er', 'update' => 'Modifier', 'delete' => 'Supprimer'];
                                                                echo $actionLabels[$action] ?? ucfirst($action);
                                                                ?>
                                                            </span>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="<?= url('roles/list') ?>" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl transition font-semibold shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Enregistrer le rÃ´le
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showModulePermissions(moduleHash) {
    // Masquer tous les contenus
    document.querySelectorAll('.module-permission-content').forEach(el => el.classList.add('hidden'));
    // Afficher le contenu sÃ©lectionnÃ©
    document.getElementById('module-content-' + moduleHash).classList.remove('hidden');

    // Mettre Ã  jour les boutons d'onglets
    document.querySelectorAll('.module-tab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-100');
        btn.classList.add('text-gray-600', 'hover:bg-white', 'hover:text-blue-600');
        const icon = btn.querySelector('i.fas.fa-folder');
        if(icon) {
            icon.classList.remove('text-blue-200');
            icon.classList.add('text-gray-400', 'group-hover:text-blue-500');
        }
    });

    const activeBtn = document.getElementById('btn-module-' + moduleHash);
    activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-100');
    activeBtn.classList.remove('text-gray-600', 'hover:bg-white', 'hover:text-blue-600');
    const activeIcon = activeBtn.querySelector('i.fas.fa-folder');
    if(activeIcon) {
        activeIcon.classList.add('text-blue-200');
        activeIcon.classList.remove('text-gray-400', 'group-hover:text-blue-500');
    }
}

function selectModulePermissions(moduleHash, checked) {
    document.querySelectorAll(`input[data-module-hash="${moduleHash}"]`).forEach(cb => cb.checked = checked);
}

function selectTargetPermissions(target, checked) {
    document.querySelectorAll(`input[data-target="${target}"]`).forEach(cb => cb.checked = checked);
}

function selectAllPermissions(checked) {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = checked);
}
</script>

