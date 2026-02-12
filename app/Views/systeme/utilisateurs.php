<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                <i class="fas fa-users-cog text-blue-600 mr-3"></i>
                Gestion des Accès
            </h1>
            <p class="text-gray-500 mt-2 text-lg">Centralisez la gestion des comptes et les niveaux de sécurité</p>
        </div>
    </div>

    <!-- Statistiques / Groupes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <?php 
        $groups = [
            'Admins' => ['icon' => 'fa-user-shield', 'color' => 'blue', 'type' => 'admin'],
            'Enseignants' => ['icon' => 'fa-chalkboard-teacher', 'color' => 'blue', 'type' => 'enseignant'],
            'Parents' => ['icon' => 'fa-users', 'color' => 'purple', 'type' => 'parent'],
            'Élèves' => ['icon' => 'fa-user-graduate', 'color' => 'orange', 'type' => 'eleve'],
        ];
        
        $counts = [];
        foreach ($utilisateurs as $u) {
            $type = $u['user_type'] ?? 'admin';
            $counts[$type] = ($counts[$type] ?? 0) + 1;
        }
        
        foreach ($groups as $label => $info): 
            $count = $counts[$info['type']] ?? 0;
        ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-all cursor-pointer group" 
                 onclick="switchTab('users'); document.getElementById('userSearch').value = '<?= $info['type'] ?>'; document.getElementById('userSearch').dispatchEvent(new Event('input'));">
                <div class="w-12 h-12 bg-<?= $info['color'] ?>-50 text-<?= $info['color'] ?>-600 rounded-lg flex items-center justify-center text-xl group-hover:bg-<?= $info['color'] ?>-600 group-hover:text-white transition-all">
                    <i class="fas <?= $info['icon'] ?>"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500"><?= $label ?></h3>
                    <p class="text-2xl font-bold text-gray-900"><?= $count ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Onglets Navigation -->
    <div class="flex border-b border-gray-200 mb-8 space-x-8">
        <button onclick="switchTab('users')" id="tab-users" class="tab-btn py-4 px-1 border-b-2 font-bold text-sm transition-all border-blue-600 text-blue-600">
            <i class="fas fa-users mr-2"></i>Liste des utilisateurs
        </button>
        <button onclick="switchTab('groups')" id="tab-groups" class="tab-btn py-4 px-1 border-b-2 font-bold text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
            <i class="fas fa-layer-group mr-2"></i>Groupes d'utilisateurs
        </button>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl mb-8 shadow-sm">
            <p class="text-green-800 font-medium"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
        </div>
    <?php endif; ?>

    <!-- Contenu Onglet: Utilisateurs -->
    <div id="content-users" class="tab-content transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-12 border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold text-gray-800">Utilisateurs</h2>
                    <a href="<?= url('systeme/utilisateurs/add') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-md transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-plus-circle mr-2"></i> Nouvel Utilisateur
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative w-64">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-search text-sm"></i>
                        </span>
                        <input type="text" id="userSearch" placeholder="Rechercher..." class="w-full pl-9 pr-4 py-2 text-sm border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
            
            <?php if (empty($utilisateurs)): ?>
                <div class="text-center py-20 text-gray-400">
                    <i class="fas fa-users-slash text-4xl mb-4"></i>
                    <p class="text-lg">Aucun utilisateur trouvé</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table id="usersTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Utilisateur</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Groupes</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Dernière connexion</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php foreach ($utilisateurs as $user): ?>
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 shrink-0 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border-2 border-white shadow-sm">
                                                <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900"><?= e($user['username']) ?></div>
                                                <div class="text-[10px] text-gray-500 uppercase font-bold"><?= e($user['user_type']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <?php if (!empty($user['groupes_noms'])): ?>
                                                <?php foreach (explode(', ', $user['groupes_noms']) as $groupName): ?>
                                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded border border-blue-100"><?= e($groupName) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400 ">Aucun groupe</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        <?= $user['last_login_at'] ? date('d/m/Y H:i', strtotime($user['last_login_at'])) : '— Jamais' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold <?= ($user['is_active'] ?? 1) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                            <?= ($user['is_active'] ?? 1) ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="<?= url('systeme/utilisateurs/edit/' . $user['id']) ?>" 
                                               class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg"
                                               data-tooltip="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <form action="<?= url('systeme/utilisateurs/toggle-status/' . $user['id']) ?>" method="POST" class="inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" 
                                                            class="p-2 <?= ($user['is_active'] ?? 1) ? 'text-orange-500 hover:bg-orange-100' : 'text-green-500 hover:bg-green-100' ?> rounded-lg"
                                                            data-tooltip="<?= ($user['is_active'] ?? 1) ? 'Désactiver' : 'Activer' ?>">
                                                        <i class="fas <?= ($user['is_active'] ?? 1) ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                                    </button>
                                                </form>
                                                <form action="<?= url('systeme/utilisateurs/delete/' . $user['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" 
                                                            class="p-2 text-red-500 hover:bg-red-100 rounded-lg"
                                                            data-tooltip="Supprimer">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contenu Onglet: Groupes -->
    <div id="content-groups" class="tab-content hidden transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold text-gray-800">Groupes d'utilisateurs</h2>
                    <a href="<?= url('systeme/groupes/add') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-plus-circle mr-2"></i> Créer un groupe
                    </a>
                </div>
                <form action="<?= url('systeme/utilisateurs/sync-parents') ?>" method="POST" onsubmit="return confirm('Désactiver les parents sans enfants actifs ?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="text-sm font-bold text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-all">
                        <i class="fas fa-sync-alt mr-2"></i>Nettoyer le groupe Parents
                    </button>
                </form>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($groupes as $group): ?>
                    <div class="bg-white border-2 border-gray-50 rounded-2xl p-5 hover:border-blue-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="flex gap-1">
                                <a href="<?= url('systeme/groupes/edit/' . $group['id']) ?>" 
                                   class="p-2 text-blue-700 hover:text-green-700 rounded-lg hover:bg-blue-50"
                                   data-tooltip="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= url('systeme/groupes/delete/' . $group['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Supprimer ce groupe ?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" 
                                            class="p-2 text-red-700 hover:text-red-700 rounded-lg hover:bg-red-50"
                                            data-tooltip="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1"><?= e($group['nom']) ?></h3>
                        <p class="text-xs text-gray-500 mb-4 line-clamp-2 h-8"><?= e($group['description'] ?: 'Aucune description.') ?></p>
                        <div class="pt-4 border-t border-gray-50 flex items-center justify-between mt-auto">
                            <span class="text-[10px] font-mono bg-gray-50 text-gray-500 px-2 py-1 rounded">Code: <?= e($group['code']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabId) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
    // Deactivate all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected content
    document.getElementById('content-' + tabId).classList.remove('hidden');
    // Activate selected button
    const activeBtn = document.getElementById('tab-' + tabId);
    activeBtn.classList.add('border-blue-600', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof ERP !== 'undefined' && ERP.initSearch) {
        ERP.initSearch('userSearch', 'usersTable');
    }
});
</script>




