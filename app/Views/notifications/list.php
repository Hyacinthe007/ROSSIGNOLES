<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-bell text-blue-600 mr-2"></i>
                Liste des notifications
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Historique des notifications envoyées</p>
        </div>
        <a href="<?= url('notifications/add') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Nouvelle notification</span>
        </a>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-800"><?= count($notifications ?? []) ?></p>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <i class="fas fa-bell text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Envoyées</p>
                    <p class="text-2xl font-bold text-green-600">
                        <?= count(array_filter($notifications ?? [], fn($n) => ($n['statut'] ?? '') === 'envoye')) ?>
                    </p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">En attente</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        <?= count(array_filter($notifications ?? [], fn($n) => ($n['statut'] ?? '') === 'file_attente')) ?>
                    </p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Échecs</p>
                    <p class="text-2xl font-bold text-red-600">
                        <?= count(array_filter($notifications ?? [], fn($n) => ($n['statut'] ?? '') === 'echec')) ?>
                    </p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Canal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinataire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($notifications)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucune notification trouvée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($notifications as $notification): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= e($notification['titre'] ?? 'Sans titre') ?>
                                    </div>
                                    <?php if (!empty($notification['contenu'])): ?>
                                        <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                            <?= e(substr($notification['contenu'], 0, 60)) ?><?= strlen($notification['contenu']) > 60 ? '...' : '' ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $canal = $notification['canal'] ?? 'systeme';
                                    $canalIcons = [
                                        'email' => 'fa-envelope',
                                        'sms' => 'fa-sms',
                                        'push' => 'fa-mobile-alt',
                                        'systeme' => 'fa-bell'
                                    ];
                                    $canalColors = [
                                        'email' => 'blue',
                                        'sms' => 'green',
                                        'push' => 'purple',
                                        'systeme' => 'gray'
                                    ];
                                    $icon = $canalIcons[$canal] ?? 'fa-bell';
                                    $color = $canalColors[$canal] ?? 'gray';
                                    ?>
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                        <i class="fas <?= $icon ?>"></i>
                                        <?= ucfirst($canal) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if (isset($notification['user_id']) && $notification['user_id']): ?>
                                        Utilisateur #<?= e($notification['user_id']) ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">Tous</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= formatDate($notification['date_envoi'] ?? $notification['created_at'] ?? '', 'd/m/Y H:i') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statut = $notification['statut'] ?? 'file_attente';
                                    $statutLabels = [
                                        'envoye' => ['label' => 'Envoyée', 'color' => 'green'],
                                        'file_attente' => ['label' => 'En attente', 'color' => 'yellow'],
                                        'echec' => ['label' => 'Échec', 'color' => 'red']
                                    ];
                                    $statutInfo = $statutLabels[$statut] ?? ['label' => ucfirst($statut), 'color' => 'gray'];
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-<?= $statutInfo['color'] ?>-100 text-<?= $statutInfo['color'] ?>-800">
                                        <?= $statutInfo['label'] ?>
                                    </span>
                                    <?php if ($statut === 'echec' && !empty($notification['erreur'])): ?>
                                        <p class="text-xs text-red-600 mt-1"><?= e(substr($notification['erreur'], 0, 50)) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('notifications/details/' . $notification['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition">
                                            <i class="fas fa-eye"></i>
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


