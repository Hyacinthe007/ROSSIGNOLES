<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            Tableau de bord
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Vue d'ensemble de l'école Mandroso</p>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Élèves -->
        <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Élèves actifs</p>
                    <p class="text-3xl font-bold"><?= e($stats['total_eleves'] ?? 0) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-user-graduate text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Classes -->
        <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1">Classes</p>
                    <p class="text-3xl font-bold"><?= e($stats['total_classes'] ?? 0) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-door-open text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Enseignants -->
        <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">Enseignants</p>
                    <p class="text-3xl font-bold"><?= e($stats['total_enseignants'] ?? 0) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-chalkboard-teacher text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Paiements du mois -->
        <div class="stat-card bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm mb-1">Paiements du mois</p>
                    <p class="text-3xl font-bold"><?= formatMoney($stats['paiements_du_mois'] ?? 0) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-money-bill-wave text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <?php 
    $canNewInscription = hasPermission('inscriptions.create');
    $canTakeAttendance = hasPermission('absences.create');
    $canEnterNotes = hasPermission('notes.create');
    $canAddPayment = hasPermission('finance.create');
    
    if ($canNewInscription || $canTakeAttendance || $canEnterNotes || $canAddPayment): 
    ?>
    <div class="mb-8 bg-white rounded-xl shadow-lg p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Actions rapides
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <?php if ($canNewInscription): ?>
            <a href="<?= url('inscriptions/nouveau') ?>" class="flex flex-col items-center justify-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition group">
                <i class="fas fa-user-plus text-2xl md:text-3xl text-blue-600 mb-2 group-hover:scale-110 transition"></i>
                <span class="text-xs md:text-sm font-medium text-gray-700 text-center">Nouvelle inscription</span>
            </a>
            <?php endif; ?>

            <?php if ($canTakeAttendance): ?>
            <a href="<?= url('presences/saisie') ?>" class="flex flex-col items-center justify-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition group">
                <i class="fas fa-clipboard-check text-2xl md:text-3xl text-green-600 mb-2 group-hover:scale-110 transition"></i>
                <span class="text-xs md:text-sm font-medium text-gray-700 text-center">Faire l'appel</span>
            </a>
            <?php endif; ?>

            <?php if ($canEnterNotes): ?>
            <a href="<?= url('notes/saisie') ?>" class="flex flex-col items-center justify-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition group">
                <i class="fas fa-pen text-2xl md:text-3xl text-purple-600 mb-2 group-hover:scale-110 transition"></i>
                <span class="text-xs md:text-sm font-medium text-gray-700 text-center">Saisir notes</span>
            </a>
            <?php endif; ?>

            <?php if ($canAddPayment): ?>
            <a href="<?= url('finance/paiement-mensuel') ?>" class="flex flex-col items-center justify-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition group">
                <i class="fas fa-cash-register text-2xl md:text-3xl text-orange-600 mb-2 group-hover:scale-110 transition"></i>
                <span class="text-xs md:text-sm font-medium text-gray-700 text-center">Paiement</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Activités Récentes -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fas fa-history text-gray-500 mr-2"></i>
                Activités récentes
            </h2>
            <a href="<?= url('systeme/logs') ?>" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Tout voir</a>
        </div>
        <div class="divide-y divide-gray-100">
            <?php if (!empty($recentLogs)): ?>
                <?php foreach ($recentLogs as $log): ?>
                    <div class="p-4 hover:bg-gray-50 transition flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i class="fas <?= $log['module'] === 'Authentification' ? 'fa-key' : 'fa-edit' ?> text-blue-500 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?= e($log['action']) ?></p>
                            <p class="text-xs text-gray-500"><?= e($log['description']) ?></p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded"><?= e($log['module']) ?></span>
                                <span class="text-[10px] text-gray-400">
                                    <i class="far fa-clock mr-1"></i><?= date('d/m à H:i', strtotime($log['created_at'])) ?>
                                </span>
                                <span class="text-[10px] text-gray-400">
                                    <i class="far fa-user mr-1"></i><?= e($log['user_name'] ?? 'Système') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-8 text-center text-gray-500 italic">
                    Aucune activité récente enregistrée.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
