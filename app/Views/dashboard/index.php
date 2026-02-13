<div class="p-0">
    <!-- En-tête -->
    <div class="mb-2 sm:mb-4 px-4 sm:px-8 md:px-8">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-1">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            Tableau de bord
        </h1>
        <p class="text-gray-600 text-xs sm:text-sm md:text-base">Vue d'ensemble de l'école Mandroso</p>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4 md:gap-6 mb-4 sm:mb-8 px-0 sm:px-8 md:px-8">
        <!-- Élèves -->
        <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow-lg">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <p class="text-blue-100 text-xs sm:text-sm mb-0.5 sm:mb-1">Élèves actifs</p>
                    <p class="text-2xl sm:text-3xl font-bold"><?= e($stats['total_eleves'] ?? 0) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-2 sm:p-4 rounded-lg">
                    <i class="fas fa-user-graduate text-xl sm:text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Classes -->
        <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow-lg">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <p class="text-purple-100 text-xs sm:text-sm mb-0.5 sm:mb-1">Classes</p>
                    <p class="text-2xl sm:text-3xl font-bold"><?= e($stats['total_classes'] ?? 0) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-2 sm:p-4 rounded-lg">
                    <i class="fas fa-door-open text-xl sm:text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Enseignants -->
        <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow-lg">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <p class="text-green-100 text-xs sm:text-sm mb-0.5 sm:mb-1">Enseignants</p>
                    <p class="text-2xl sm:text-3xl font-bold"><?= e($stats['total_enseignants'] ?? 0) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-2 sm:p-4 rounded-lg">
                    <i class="fas fa-chalkboard-teacher text-xl sm:text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Élèves en classe aujourd'hui -->
        <div class="stat-card bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow-lg">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <p class="text-orange-100 text-xs sm:text-sm mb-0.5 sm:mb-1">Élèves en classe aujourd'hui</p>
                    <p class="text-2xl sm:text-3xl font-bold"><?= e($stats['eleves_en_classe_aujourd_hui'] ?? 0) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-2 sm:p-4 rounded-lg">
                    <i class="fas fa-users text-xl sm:text-3xl"></i>
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
    <div class="mb-4 sm:mb-8 bg-white rounded-lg sm:rounded-xl shadow-lg py-3 sm:py-6 px-0 sm:px-8 md:px-8">
        <h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-800 mb-3 sm:mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Actions rapides
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
            <?php if ($canNewInscription): ?>
            <a href="<?= url('inscriptions/nouveau') ?>" class="flex flex-col items-center justify-center p-2 sm:p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition group">
                <i class="fas fa-user-plus text-xl sm:text-3xl text-blue-600 mb-1 sm:mb-2 group-hover:scale-110 transition"></i>
                <span class="text-xs font-medium text-gray-700 text-center">Nouvelle inscription</span>
            </a>
            <?php endif; ?>

            <?php if ($canTakeAttendance): ?>
            <a href="<?= url('presences/saisie') ?>" class="flex flex-col items-center justify-center p-2 sm:p-4 bg-green-50 hover:bg-green-100 rounded-lg transition group">
                <i class="fas fa-clipboard-check text-xl sm:text-3xl text-green-600 mb-1 sm:mb-2 group-hover:scale-110 transition"></i>
                <span class="text-xs font-medium text-gray-700 text-center">Faire l'appel</span>
            </a>
            <?php endif; ?>

            <?php if ($canEnterNotes): ?>
            <a href="<?= url('notes/saisie') ?>" class="flex flex-col items-center justify-center p-2 sm:p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition group">
                <i class="fas fa-pen text-xl sm:text-3xl text-purple-600 mb-1 sm:mb-2 group-hover:scale-110 transition"></i>
                <span class="text-xs font-medium text-gray-700 text-center">Saisir notes</span>
            </a>
            <?php endif; ?>

            <?php if ($canAddPayment): ?>
            <a href="<?= url('finance/paiement-mensuel') ?>" class="flex flex-col items-center justify-center p-2 sm:p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition group">
                <i class="fas fa-cash-register text-xl sm:text-3xl text-orange-600 mb-1 sm:mb-2 group-hover:scale-110 transition"></i>
                <span class="text-xs font-medium text-gray-700 text-center">Paiement</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
