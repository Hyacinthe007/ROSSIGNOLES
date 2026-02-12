<div class="p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="<?= url('systeme/utilisateurs') ?>" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:text-blue-600 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?= isset($group) ? 'Modifier' : 'Créer' ?> un groupe d'utilisateurs</h1>
                <p class="text-gray-500 text-sm">Les rôles assignés à ce groupe seront hérités par tous ses membres.</p>
            </div>
        </div>

        <form action="<?= isset($group) ? url('systeme/groupes/edit/' . $group['id']) : url('systeme/groupes/add') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Rôles hérités</h2>
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Sélectionnez les rôles applicables au groupe</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                        <?php foreach ($roles as $role): ?>
                            <label class="relative flex items-center p-3 border rounded-xl hover:bg-blue-50 transition-all cursor-pointer group <?= in_array($role['id'], $groupRoles ?? []) ? 'border-blue-500 bg-blue-50/50' : 'border-gray-100' ?>">
                                <input type="checkbox" name="roles[]" value="<?= $role['id'] ?>" class="hidden peer" <?= in_array($role['id'], $groupRoles ?? []) ? 'checked' : '' ?> onchange="this.parentElement.classList.toggle('border-blue-500'); this.parentElement.classList.toggle('bg-blue-50/50');">
                                <div class="flex-1">
                                    <div class="font-bold text-sm text-gray-800"><?= e($role['nom']) ?></div>
                                    <div class="text-[10px] text-gray-500"><?= e($role['code']) ?></div>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 border-gray-200 flex items-center justify-center transition-all peer-checked:border-blue-600 peer-checked:bg-blue-600">
                                    <div class="w-2 h-2 rounded-full bg-white opacity-0 scale-0 transition-all duration-300 peer-checked:opacity-100 peer-checked:scale-100"></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if (isset($members) && !empty($members)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h2 class="font-bold text-gray-800">Membres du groupe (<?= count($members) ?>)</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-3">
                            <?php foreach ($members as $member): ?>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black">
                                        <?= strtoupper(substr($member['username'], 0, 2)) ?>
                                    </div>
                                    <div class="flex-1 overflow-hidden">
                                        <div class="text-sm font-bold text-gray-800 truncate"><?= e($member['username']) ?></div>
                                        <div class="text-[9px] text-gray-400 ">Ajouté le <?= date('d/m/Y', strtotime($member['created_at'])) ?></div>
                                    </div>
                                    <a href="<?= url('systeme/utilisateurs/edit/' . $member['id']) ?>" class="text-gray-300 hover:text-blue-500 transition-colors">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex justify-end gap-3">
                <a href="<?= url('systeme/utilisateurs') ?>" class="px-6 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all transform active:scale-95">
                    <?= isset($group) ? 'Enregistrer' : 'Créer le groupe' ?>
                </button>
            </div>
        </form>
    </div>
</div>

