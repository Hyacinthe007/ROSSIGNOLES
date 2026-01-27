<!-- ====== SIDEBAR ====== -->
<?php $isCollapsed = ($_COOKIE['sidebarCollapsed'] ?? 'false') === 'true'; ?>
<aside id="sidebar" class="sidebar bg-white border-r no-print <?= $isCollapsed ? 'collapsed' : '' ?>">
    <div class="p-4">
        <!-- Logo (caché en mode collapsed) -->
        <div class="logo-text mb-6 px-3">
            <h2 class="text-lg font-bold text-gray-800">Menu Principal</h2>
        </div>

        <nav class="space-y-1">
            <!-- Dashboard -->
            <a href="<?= url('dashboard') ?>" 
               class="menu-item group flex items-center justify-between p-3 <?= isExactActiveRoute('dashboard') ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?> rounded-lg transition-all duration-200"
               title="Tableau de bord">
                <div class="flex items-center gap-3">
                    <i class="fas fa-home text-center flex-shrink-0 <?= isExactActiveRoute('dashboard') ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600' ?> transition-colors duration-200" style="width: 20px;"></i>
                    <span class="menu-text">Tableau de bord</span>
                </div>
            </a>

            <!-- MODULE 1: Scolarité -->
            <?php if (hasPermission('scolarite.view')): ?>
            <div class="menu-group">
                <div class="menu-item-header group flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 <?= isActiveRoute('inscriptions') || isActiveRoute('parents') || isActiveRoute('classes') ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?>"
                     title="Scolarité">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-graduate text-center flex-shrink-0 transition-colors duration-200 <?= isActiveRoute('inscriptions') || isActiveRoute('parents') || isActiveRoute('classes') ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600' ?>" style="width: 20px;"></i>
                        <span class="menu-text font-medium">Scolarité</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200"></i>
                </div>
                <div class="menu-sub-items hidden overflow-hidden transition-all duration-300">
                    <?php if (hasPermission('inscriptions_new.create')): ?>
                    <a href="<?= url('inscriptions/nouveau') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('inscriptions/nouveau') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-plus-circle w-4 h-4 mr-2"></i><span>Nouvelle inscription</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('inscriptions_list.view')): ?>
                    <a href="<?= url('inscriptions/liste') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('inscriptions/liste') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-tasks w-4 h-4 mr-2"></i><span>Gestion inscriptions</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('eleves.view')): ?>
                    <a href="<?= url('classes/eleves') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('classes/eleves') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-chalkboard-user w-4 h-4 mr-2"></i><span>Élèves par classe</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('parents.view')): ?>
                    <a href="<?= url('parents/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('parents/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-users w-4 h-4 mr-2"></i><span>Parents / Tuteurs</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- MODULE 2: Finance -->
            <?php if (hasPermission('finance.view')): ?>
            <div class="menu-group">
                <div class="menu-item-header group flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 <?= isActiveRoute('finance') || isActiveRoute('echeancier') || isActiveRoute('eleves') ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?>"
                     title="Finance">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-money-bill-wave text-center flex-shrink-0 transition-colors duration-200 <?= isActiveRoute('finance') || isActiveRoute('echeancier') || isActiveRoute('eleves') ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600' ?>" style="width: 20px;"></i>
                        <span class="menu-text font-medium">Finance</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200"></i>
                </div>
                <div class="menu-sub-items hidden overflow-hidden transition-all duration-300">
                    <!-- Dashboard & Suivi Écolage -->
                    <?php if (hasPermission('finance.dashboard')): ?>
                    <a href="<?= url('finance/dashboard') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('finance/dashboard') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-chart-line w-4 h-4 mr-2"></i><span>Tableau de bord</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('finance_mensuel.create')): ?>
                    <a href="<?= url('eleves/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('eleves/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-address-book w-4 h-4 mr-2"></i><span>Liste des élèves</span>
                    </a>
                    <a href="<?= url('finance/paiement-mensuel') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('finance/paiement-mensuel') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-hand-holding-usd w-4 h-4 mr-2"></i><span>Paiement Mensuel</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('echeanciers.view')): ?>
                    <a href="<?= url('finance/echeanciers') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= (isExactActiveRoute('finance/echeanciers') && (!isset($_GET['statut']) || $_GET['statut'] === 'retard' || $_GET['statut'] === 'retard_10')) ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-file-invoice-dollar w-4 h-4 mr-2"></i><span>Recouvrement</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('recus.view')): ?>
                    <a href="<?= url('finance/recus') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('finance/recus') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-file-invoice w-4 h-4 mr-2"></i><span>Reçus de paiement</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- MODULE 3: Pédagogie -->
            <?php if (hasPermission('pedagogie.view')): ?>
            <div class="menu-group">
                <div class="menu-item-header group flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 <?= isActiveRoute('pedagogie') || isActiveRoute('conseils') ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?>"
                     title="Pédagogie">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-door-open text-center flex-shrink-0 transition-colors duration-200 <?= isActiveRoute('pedagogie') || isActiveRoute('conseils') ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600' ?>" style="width: 20px;"></i>
                        <span class="menu-text font-medium">Pédagogie</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200"></i>
                </div>
                <div class="menu-sub-items hidden overflow-hidden transition-all duration-300">
                    <?php if (hasPermission('calendrier.view')): ?>
                    <a href="<?= url('pedagogie/emplois-temps') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('pedagogie/emplois-temps') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-calendar-alt w-4 h-4 mr-2"></i><span>Emplois du temps</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('parcours.view')): ?>
                    <a href="<?= url('eleves/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('eleves/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-history w-4 h-4 mr-2"></i><span>Parcours Scolaires</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('conseils.view')): ?>
                    <a href="<?= url('conseils/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('conseils/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-comments w-4 h-4 mr-2"></i><span>Conseils de classe</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- MODULE 4: Ressources Humaines -->
            <?php if (hasPermission('personnel.view')): ?>
            <div class="menu-group">
                <div class="menu-item-header group flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 <?= isActiveRoute('personnel') || isActiveRoute('liste-personnel') || isActiveRoute('absences_personnel') ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?>"
                     title="Ressources Humaines">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users text-center flex-shrink-0 transition-colors duration-200 <?= isActiveRoute('personnel') || isActiveRoute('liste-personnel') || isActiveRoute('absences_personnel') ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600' ?>" style="width: 20px;"></i>
                        <span class="menu-text font-medium">Ressources Humaines</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200"></i>
                </div>
                <div class="menu-sub-items hidden overflow-hidden transition-all duration-300">
                    <?php if (hasPermission('personnel_new.create')): ?>
                    <a href="<?= url('personnel/nouveau') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('personnel/nouveau') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-user-plus w-4 h-4 mr-2"></i><span>Nouveau Personnel</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('personnel_list.view')): ?>
                    <a href="<?= url('liste-personnel') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('liste-personnel') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-users w-4 h-4 mr-2"></i><span>Liste du personnel</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('absences_personnel.view')): ?>
                    <a href="<?= url('absences_personnel/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('absences_personnel/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-calendar-times w-4 h-4 mr-2"></i><span>Absences du personnel</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- MODULE 5: Évaluations -->
            <?php if (hasPermission('notes.view')): ?>
            <div class="menu-group">
                <div class="menu-item-header group flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 <?= isActiveRoute('notes') || isActiveRoute('bulletins') || isActiveRoute('interrogations') || isActiveRoute('examens') ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?>"
                     title="Évaluations">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-clipboard-list text-center flex-shrink-0 transition-colors duration-200 <?= isActiveRoute('notes') || isActiveRoute('bulletins') || isActiveRoute('interrogations') || isActiveRoute('examens') ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600' ?>" style="width: 20px;"></i>
                        <span class="menu-text font-medium">Évaluations</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200"></i>
                </div>
                <div class="menu-sub-items hidden overflow-hidden transition-all duration-300">
                    <a href="<?= url('evaluations') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('evaluations') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-clipboard-list w-4 h-4 mr-2"></i><span>Gestion évaluations</span>
                    </a>
                    <a href="<?= url('notes/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('notes/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-star w-4 h-4 mr-2"></i><span>Notes</span>
                    </a>
                    <a href="<?= url('notes/moyennes') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('notes/moyennes') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-chart-bar w-4 h-4 mr-2"></i><span>Résultats</span>
                    </a>
                    <a href="<?= url('bulletins/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('bulletins/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-file-alt w-4 h-4 mr-2"></i><span>Bulletins</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- MODULE 6: Vie scolaire -->
            <?php if (hasPermission('viescolaire.view')): ?>
            <div class="menu-group">
                <div class="menu-item-header group flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 <?= isActiveRoute('absences') || isActiveRoute('sanctions') ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?>"
                     title="Vie scolaire">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-calendar-check text-center flex-shrink-0 transition-colors duration-200 <?= isActiveRoute('absences') || isActiveRoute('sanctions') ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600' ?>" style="width: 20px;"></i>
                        <span class="menu-text font-medium">Vie scolaire</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200"></i>
                </div>
                <div class="menu-sub-items hidden overflow-hidden transition-all duration-300">
                    <?php if (hasPermission('absences.view')): ?>
                    <a href="<?= url('absences/list?type=absence') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= (isExactActiveRoute('absences/list') && (!isset($_GET['type']) || $_GET['type'] === 'absence')) ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-user-times w-4 h-4 mr-2"></i><span>Absences</span>
                    </a>
                    <a href="<?= url('absences/list?type=retard') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= (isExactActiveRoute('absences/list') && isset($_GET['type']) && $_GET['type'] === 'retard') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-clock w-4 h-4 mr-2"></i><span>Retards</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('sanctions.view')): ?>
                    <a href="<?= url('sanctions/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('sanctions/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-gavel w-4 h-4 mr-2"></i><span>Sanctions</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- MODULE 7: Communication -->
            <?php if (hasPermission('communication.view')): ?>
            <div class="menu-group">
                <div class="menu-item-header group flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 <?= isActiveRoute('annonces') || isActiveRoute('notifications') ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?>"
                     title="Communication">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-bell text-center flex-shrink-0 transition-colors duration-200 <?= isActiveRoute('annonces') || isActiveRoute('notifications') ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600' ?>" style="width: 20px;"></i>
                        <span class="menu-text font-medium">Communication</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200"></i>
                </div>
                <div class="menu-sub-items hidden overflow-hidden transition-all duration-300">
                    <a href="<?= url('annonces/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('annonces/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-bullhorn w-4 h-4 mr-2"></i><span>Annonces</span>
                    </a>
                    <a href="<?= url('notifications/messagerie') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('notifications/messagerie') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-envelope w-4 h-4 mr-2"></i><span>Messagerie</span>
                    </a>
                    <a href="<?= url('notifications/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('notifications/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-bell w-4 h-4 mr-2"></i><span>Notifications</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- MODULE 8: Paramètres -->
            <?php if (hasPermission('systeme.config')): ?>
            <div class="menu-group">
                <div class="menu-item-header group flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 <?= isActiveRoute('systeme') || isActiveRoute('roles') ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?>"
                     title="Paramètres">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-cog text-center flex-shrink-0 transition-colors duration-200 <?= isActiveRoute('systeme') || isActiveRoute('roles') ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600' ?>" style="width: 20px;"></i>
                        <span class="menu-text font-medium">Paramètres</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform duration-200"></i>
                </div>
                <div class="menu-sub-items hidden overflow-hidden transition-all duration-300">
                    <?php if (hasPermission('systeme.config')): ?>
                    <a href="<?= url('systeme/config') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('systeme/config') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-tools w-4 h-4 mr-2"></i><span>Configurations</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('users.view')): ?>
                    <a href="<?= url('systeme/utilisateurs') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('systeme/utilisateurs') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-users-cog w-4 h-4 mr-2"></i><span>Utilisateurs</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('roles.view')): ?>
                    <a href="<?= url('roles/list') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('roles/list') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-user-shield w-4 h-4 mr-2"></i><span>Rôles</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission('logs.view')): ?>
                    <a href="<?= url('systeme/logs') ?>" class="menu-item flex items-center p-2 pl-11 text-sm <?= isExactActiveRoute('systeme/logs') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
                        <i class="fas fa-history w-4 h-4 mr-2"></i><span>Logs</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </nav>
    </div>
</aside>
