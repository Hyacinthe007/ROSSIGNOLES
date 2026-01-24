<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-bell text-blue-600 mr-2"></i>
                Détails de la notification
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Informations complètes de la notification</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('notifications/list') ?>" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Titre -->
            <div class="md:col-span-2">
                <h2 class="text-xl font-bold text-gray-800 mb-2">
                    <?= e($notification['titre'] ?? 'Sans titre') ?>
                </h2>
            </div>

            <!-- Canal -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-broadcast-tower text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Canal</p>
                    <p class="font-semibold text-gray-800">
                        <?php
                        $canal = $notification['canal'] ?? 'systeme';
                        echo ucfirst($canal);
                        ?>
                    </p>
                </div>
            </div>

            <!-- Destinataire -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Destinataire</p>
                    <p class="font-semibold text-gray-800">
                        <?php if (isset($notification['username'])): ?>
                            <?= e($notification['username']) ?>
                            <?php if (isset($notification['email'])): ?>
                                <span class="text-sm text-gray-500">(<?= e($notification['email']) ?>)</span>
                            <?php endif; ?>
                        <?php elseif (isset($notification['user_id']) && $notification['user_id']): ?>
                            Utilisateur #<?= e($notification['user_id']) ?>
                        <?php else: ?>
                            <span class="text-gray-400">Tous les utilisateurs</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Date d'envoi -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Date d'envoi</p>
                    <p class="font-semibold text-gray-800">
                        <?= formatDate($notification['date_envoi'] ?? $notification['created_at'] ?? '', 'd/m/Y H:i') ?>
                    </p>
                </div>
            </div>

            <!-- Statut -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-info-circle text-gray-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Statut</p>
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
                </div>
            </div>
        </div>

        <!-- Contenu -->
        <div class="mt-6 pt-6 border-t">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Contenu</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-800 whitespace-pre-wrap"><?= e($notification['contenu'] ?? 'Aucun contenu') ?></p>
            </div>
        </div>

        <!-- Métadonnées -->
        <?php if (!empty($notification['meta_decoded']) || !empty($notification['meta'])): ?>
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Informations supplémentaires</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <?php
                    $meta = $notification['meta_decoded'] ?? (is_string($notification['meta']) ? json_decode($notification['meta'], true) : $notification['meta']);
                    if ($meta):
                    ?>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php if (isset($meta['lien'])): ?>
                                <div>
                                    <dt class="text-xs text-gray-500">Lien</dt>
                                    <dd class="text-sm text-gray-800">
                                        <a href="<?= e($meta['lien']) ?>" target="_blank" class="text-blue-600 hover:underline">
                                            <?= e($meta['lien']) ?>
                                        </a>
                                    </dd>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($meta['type'])): ?>
                                <div>
                                    <dt class="text-xs text-gray-500">Type</dt>
                                    <dd class="text-sm text-gray-800"><?= e(ucfirst($meta['type'])) ?></dd>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($meta['priorite'])): ?>
                                <div>
                                    <dt class="text-xs text-gray-500">Priorité</dt>
                                    <dd class="text-sm text-gray-800"><?= e(ucfirst($meta['priorite'])) ?></dd>
                                </div>
                            <?php endif; ?>
                        </dl>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">Aucune métadonnée</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Erreur (si échec) -->
        <?php if ($statut === 'echec' && !empty($notification['erreur'])): ?>
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-red-700 mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Erreur
                </h3>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-800"><?= e($notification['erreur']) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

