<div class="p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="flex mb-6 text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="<?= url('dashboard') ?>" class="hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="<?= url('systeme/utilisateurs') ?>" class="hover:text-blue-600">Utilisateurs</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-900 font-medium"><?= isset($user) ? 'Modifier' : 'Ajouter' ?> un utilisateur</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 px-6 py-8 text-white">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas <?= isset($user) ? 'fa-user-edit' : 'fa-user-plus' ?> mr-3 text-blue-200"></i>
                    <?= isset($user) ? 'Modifier l\'utilisateur' : 'Créer un nouvel utilisateur' ?>
                </h1>
                <p class="text-blue-100 mt-2 opacity-90">
                    <?= isset($user) ? "Modification des informations de " . e($user['username']) : "Remplissez les informations ci-dessous pour créer un nouveau compte." ?>
                </p>
            </div>

            <form action="" method="POST" class="p-6 md:p-8 space-y-8" data-validate>
                <?php if (isset($error)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                            <p class="text-red-700 font-medium"><?= e($error) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informations de base -->
                    <div class="space-y-6">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center border-b pb-2">
                            <i class="fas fa-id-card mr-2 text-blue-600"></i> Identifiants
                        </h2>
                        
                        <div>
                            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Nom d'utilisateur <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="username" id="username" value="<?= e($user['username'] ?? '') ?>" required
                                    class="block w-full pl-10 pr-4 py-3 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 focus:bg-white shadow-sm"
                                    placeholder="ex: jdupond">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Adresse Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email" id="email" value="<?= e($user['email'] ?? '') ?>" required
                                    class="block w-full pl-10 pr-4 py-3 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 focus:bg-white shadow-sm"
                                    placeholder="ex: jean.dupond@ecole.com">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <?= isset($user) ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe' ?> 
                                <?= !isset($user) ? '<span class="text-red-500">*</span>' : '' ?>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password" id="password" <?= !isset($user) ? 'required' : '' ?>
                                    class="block w-full pl-10 pr-4 py-3 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 focus:bg-white shadow-sm"
                                    placeholder="••••••••">
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i> Utilisez au moins 8 caractères pour une sécurité optimale.
                            </p>
                        </div>
                    </div>

                        <div>
                            <label for="user_type" class="block text-sm font-semibold text-gray-700 mb-2">Type de compte <span class="text-red-500">*</span></label>
                            <select name="user_type" id="user_type" required
                                class="block w-full px-4 py-3 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 focus:bg-white shadow-sm font-medium">
                                <option value="" disabled <?= !isset($user) ? 'selected' : '' ?>>Sélectionnez un type...</option>
                                <?php foreach ($groupes as $g): ?>
                                    <option value="<?= e($g['code']) ?>" <?= (isset($user) && $user['user_type'] == $g['code']) ? 'selected' : '' ?>>
                                        <?= e($g['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-[10px] text-gray-400 mt-2  px-1">Définit la catégorie technique et les accès par défaut.</p>
                        </div>
                    </div>

                    <!-- Configuration et Groupes -->
                    <div class="space-y-6">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center border-b pb-2">
                            <i class="fas fa-users-cog mr-2 text-blue-600"></i> Appartenance aux Groupes
                        </h2>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Sélectionnez les groupes <span class="text-red-500">*</span></label>
                            <p class="text-[10px] text-gray-400 mb-2 ">L'utilisateur héritera de tous les rôles et permissions définis pour ces groupes.</p>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 grid grid-cols-1 gap-3 max-h-64 overflow-y-auto shadow-inner">
                                <?php foreach ($groupes as $group): ?>
                                    <label class="flex items-center p-3 hover:bg-white rounded-lg transition-colors cursor-pointer group border border-transparent hover:border-blue-100">
                                        <input type="checkbox" name="groups[]" value="<?= $group['id'] ?>" 
                                            <?= (isset($userGroups) && in_array($group['id'], $userGroups)) ? 'checked' : '' ?>
                                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition-all cursor-pointer">
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-700 group-hover:text-blue-700"><?= e($group['nom']) ?></span>
                                            <?php if (!empty($group['description'])): ?>
                                                <span class="block text-[11px] text-gray-500 leading-tight"><?= e($group['description']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="pt-4">
                            <label class="flex items-center justify-between cursor-pointer group p-4 bg-gray-50 rounded-2xl border border-gray-200 hover:border-blue-400 hover:bg-white transition-all shadow-sm">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-blue-700 transition-colors">Compte utilisateur actif</span>
                                    <span class="text-[10px] text-gray-500  mt-0.5">Désactivez pour suspendre l'accès</span>
                                </div>
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                                        <?= (!isset($user) || ($user['is_active'] ?? $user['actif'] ?? 1)) ? 'checked' : '' ?>
                                        class="sr-only peer">
                                    <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-end gap-4 pt-6 mt-8 border-t border-gray-100">
                    <a href="<?= url('systeme/utilisateurs') ?>" 
                        class="w-full md:w-auto px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 font-semibold rounded-xl transition-all flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i> Annuler
                    </a>
                    <button type="submit" 
                        class="w-full md:w-auto px-10 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center">
                        <i class="fas fa-save mr-2 text-blue-200"></i> 
                        <?= isset($user) ? 'Enregistrer' : 'Créer l\'utilisateur' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

