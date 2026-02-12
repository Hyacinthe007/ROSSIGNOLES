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

        <!-- Élèves en classe aujourd'hui -->
        <div class="stat-card bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm mb-1">Élèves en classe aujourd'hui</p>
                    <p class="text-3xl font-bold"><?= e($stats['eleves_en_classe_aujourd_hui'] ?? 0) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-users text-3xl"></i>
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
</div>
