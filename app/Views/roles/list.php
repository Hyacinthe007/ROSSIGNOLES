<?php
$title = "Rôles et Permissions";
$breadcrumbs = [
    ['label' => 'Système', 'url' => '#'],
    ['label' => 'Rôles & Permissions']
];
?>

<div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-shield text-blue-600 mr-2"></i>
                Gestion des Rôles
            </h1>
            <p class="text-gray-600">Définissez les niveaux d'accès et les permissions du personnel</p>
        </div>
        <a href="<?= url('roles/add') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nouveau Rôle</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($roles)): ?>
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users-cog text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Aucun rôle défini</h3>
                <p class="text-gray-500 mb-6">Commencez par créer des rôles pour votre établissement.</p>
                <a href="<?= url('roles/add') ?>" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:underline">
                    Créer le premier rôle <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($roles as $role): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition group relative">
                    <!-- Actions en haut à droite (permanentes) -->
                    <div class="absolute top-4 right-4 flex gap-1">
                        <a href="<?= url('roles/edit/' . $role['id']) ?>" 
                           class="text-gray-400 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition-colors"
                           title="Modifier le rôle">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <a href="<?= url('roles/delete/' . $role['id']) ?>" 
                           class="text-gray-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-colors" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')"
                           title="Supprimer le rôle">
                            <i class="fas fa-trash text-sm"></i>
                        </a>
                    </div>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 <?= $role['actif'] ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' ?> rounded-lg flex items-center justify-center text-lg shrink-0">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <div class="pr-16"> <!-- Padding pour éviter le chevauchement avec les boutons -->
                            <h3 class="text-base font-bold text-gray-800 leading-tight"><?= e($role['nom']) ?></h3>
                            <code class="text-[10px] text-gray-400 uppercase tracking-wider"><?= e($role['code']) ?></code>
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500 mb-4 line-clamp-2 h-8"><?= e($role['description'] ?: 'Aucune description.') ?></p>
                    
                    <div class="flex items-center justify-between py-3 border-t border-gray-50">
                        <div class="flex items-center gap-1.5 text-gray-600">
                            <i class="fas fa-users text-xs opacity-70"></i>
                            <span class="text-xs font-bold"><?= $role['users_count'] ?? 0 ?></span>
                            <span class="text-[11px] text-gray-400">utilisateurs actifs</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide <?= $role['actif'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $role['actif'] ? 'Actif' : 'Inactif' ?>
                        </span>
                    </div>
                    
                    <div class="mt-2">
                        <a href="<?= url('roles/edit/' . $role['id']) ?>" class="block w-full text-center py-2 rounded-lg bg-gray-50 text-blue-600 text-xs font-bold hover:bg-blue-600 hover:text-white transition-all">
                            Permissions & Configuration
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
