<div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                <i class="fas fa-history text-indigo-600 mr-3"></i>
                Journal d'activité
            </h1>
            <p class="text-gray-500 mt-2 text-lg">Suivi en temps réel des actions effectuées sur le système</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-sync-alt mr-2 text-indigo-500"></i> Actualiser
            </button>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-8 shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                <p class="text-red-800 font-medium"><?= e($error) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-12">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-4 text-sm">
                <span class="font-bold text-gray-800">Dernières actions</span>
                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full font-bold text-[10px] uppercase">Temps réel</span>
            </div>
            <div class="relative w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" id="logSearch" placeholder="Filtrer les logs..." class="w-full pl-9 pr-4 py-2 text-xs border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-all">
            </div>
        </div>

        <?php if (empty($logs)): ?>
            <div class="text-center py-20 text-gray-400">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-terminal text-2xl"></i>
                </div>
                <p class="text-lg">Aucune activité enregistrée pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table id="logsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Horodatage</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Module</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Action</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Description</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Adresse IP</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php foreach ($logs as $log): 
                            $actionType = strtolower($log['action']);
                            $badgeClass = 'bg-gray-100 text-gray-600';
                            if (strpos($actionType, 'creat') !== false || strpos($actionType, 'add') !== false) $badgeClass = 'bg-green-100 text-green-700';
                            elseif (strpos($actionType, 'updat') !== false || strpos($actionType, 'edit') !== false) $badgeClass = 'bg-blue-100 text-blue-700';
                            elseif (strpos($actionType, 'delet') !== false || strpos($actionType, 'remov') !== false) $badgeClass = 'bg-red-100 text-red-700';
                            elseif (strpos($actionType, 'login') !== false) $badgeClass = 'bg-purple-100 text-purple-700';
                        ?>
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-[11px] font-bold text-gray-600">
                                        <i class="far fa-calendar-alt mr-1 text-gray-300"></i>
                                        <?= date('d/m/Y', strtotime($log['created_at'])) ?>
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        <i class="far fa-clock mr-1 text-gray-300"></i>
                                        <?= date('H:i:s', strtotime($log['created_at'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-7 w-7 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-[10px] font-bold mr-2 border border-indigo-100">
                                            <?= strtoupper(substr($log['username'] ?? 'S', 0, 1)) ?>
                                        </div>
                                        <div class="text-xs font-bold text-gray-700"><?= e($log['username'] ?? 'Système') ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ">
                                        <?= e($log['module'] ?: 'global') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider <?= $badgeClass ?>">
                                        <?= e($log['action']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-600 line-clamp-1 max-w-md" title="<?= e($log['description']) ?>">
                                        <?= e($log['description']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <code class="text-[10px] bg-gray-50 text-gray-400 px-1.5 py-0.5 rounded border border-gray-100">
                                        <?= e($log['ip_address'] ?: 'Inconnue') ?>
                                    </code>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof ERP !== 'undefined' && ERP.initSearch) {
        ERP.initSearch('logSearch', 'logsTable');
    }
});
</script>


