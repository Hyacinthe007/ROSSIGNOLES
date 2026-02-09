<?php $periode = $periode ?? 'mois_ci'; ?>
<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                Tableau de bord financier
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Vue d'ensemble de la situation financière</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('finance/echeanciers') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-calendar-alt"></i>
                <span>Suivi Écolage</span>
            </a>
        </div>
    </div>

    <!-- Barre de filtrage par période -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-md p-2 flex justify-center gap-1 w-full">
            <a href="<?= url('finance/dashboard?periode=trois_mois') ?>" 
               class="<?= ($periode === 'trois_mois') ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all flex items-center justify-center gap-2">
                <i class="fas fa-calendar-alt"></i>
                <span>Trois derniers mois</span>
            </a>
            <a href="<?= url('finance/dashboard?periode=dernier_mois') ?>" 
               class="<?= ($periode === 'dernier_mois') ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all flex items-center justify-center gap-2">
                <i class="fas fa-calendar-minus"></i>
                <span>Dernier mois</span>
            </a>
            <a href="<?= url('finance/dashboard?periode=mois_ci') ?>" 
               class="<?= ($periode === 'mois_ci') ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all flex items-center justify-center gap-2">
                <i class="fas fa-calendar-week"></i>
                <span>Mois en cours</span>
            </a>
            <a href="<?= url('finance/dashboard?periode=aujourdhui') ?>" 
               class="<?= ($periode === 'aujourdhui') ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all flex items-center justify-center gap-2">
                <i class="fas fa-calendar-day"></i>
                <span>Aujourd'hui</span>
            </a>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!--Total Ecolage reçu-->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Ecolage reçu (MGA)</p>
                    <p class="text-3xl font-bold text-green-600"><?= formatMoney($stats['total_recu'] ?? 0) ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <!--Total Ecolage attendu-->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Ecolage attendu (MGA)</p>
                    <p class="text-3xl font-bold text-blue-600"><?= formatMoney($stats['total_attendu'] ?? 0) ?></p>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <!--Total Impayés-->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Impayés (MGA)</p>
                    <p class="text-3xl font-bold text-red-600"><?= formatMoney($stats['impayes'] ?? 0) ?></p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <!--Total Articles vendus-->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Articles vendus (MGA)</p>
                    <p class="text-3xl font-bold text-purple-600"><?= formatMoney($stats['total_articles_vendus'] ?? 0) ?></p>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg">
                    <i class="fas fa-shopping-bag text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques secondaires -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['eleves_total'] ?? 0 ?></p>
                    <p class="text-xs text-gray-600">Élèves inscrits</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['eleves_a_jour'] ?? 0 ?></p>
                    <p class="text-xs text-gray-600">À jour</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-user-clock text-orange-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-orange-600"><?= $stats['eleves_retard'] ?? 0 ?></p>
                    <p class="text-xs text-gray-600">En retard</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-file-invoice text-purple-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['inscriptions_droit_paye'] ?? 0 ?></p>
                    <p class="text-xs text-gray-600">Droits payés</p>
                </div>
            </div>
        </div>
    </div>

</div>
