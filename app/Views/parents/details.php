<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                Détails du parent
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Informations complètes du parent</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('parents/edit/' . $parent['id']) ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Modifier</span>
            </a>
            <a href="<?= url('inscriptions/inscrire-enfant/' . $parent['id']) ?>" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-child"></i>
                <span>Inscrire un enfant</span>
            </a>
            <a href="<?= url('parents/list') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 mb-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <div class="w-32 h-32 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                    <?= strtoupper(substr($parent['prenom'], 0, 1) . substr($parent['nom'], 0, 1)) ?>
                </div>
            </div>

            <!-- Informations principales -->
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    <?= e($parent['prenom'] . ' ' . $parent['nom']) ?>
                </h2>
                <p class="text-gray-600 mb-4">
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                        <?= e($parent['type_parent'] ?? 'Parent') ?>
                    </span>
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-venus-mars text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Sexe</p>
                            <p class="font-semibold text-gray-800"><?= e($parent['sexe'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-phone text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Téléphone</p>
                            <p class="font-semibold text-gray-800"><?= e($parent['telephone'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="font-semibold text-gray-800"><?= e($parent['email'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-briefcase text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Profession</p>
                            <p class="font-semibold text-gray-800"><?= e($parent['profession'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>

                <?php if ($parent['adresse']): ?>
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-xs text-gray-500 mb-1">Adresse</p>
                        <p class="text-gray-800"><?= e($parent['adresse']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Enfants -->
    <?php if (!empty($enfants)): ?>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-user-graduate text-blue-600"></i>
            Enfants (<?= count($enfants) ?>)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($enfants as $enfant): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <p class="font-semibold text-gray-800"><?= e($enfant['nom'] . ' ' . $enfant['prenom']) ?></p>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-id-card mr-1"></i><?= e($enfant['matricule']) ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded"><?= e($enfant['type_lien'] ?? 'Enfant') ?></span>
                    </p>
                    <div class="flex gap-4 mt-2">
                        <a href="<?= url('eleves/details/' . $enfant['id']) ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm inline-block">
                            Voir détails <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        <a href="<?= url('eleves/parcours/' . $enfant['id']) ?>" 
                           class="text-purple-600 hover:text-purple-800 text-sm inline-block">
                            Parcours & Paiements <i class="fas fa-history ml-1"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

