<?php
$title = "Annonces";
$breadcrumbs = [
    ['label' => 'Communication', 'url' => '#'],
    ['label' => 'Annonces']
];
?>

<div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-bullhorn text-orange-600 mr-2"></i>
                Tableau d'Affichage
            </h1>
            <p class="text-gray-600">Communiquez les informations importantes à la communauté scolaire</p>
        </div>
        <a href="<?= url('annonces/add') ?>" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-orange-200">
            <i class="fas fa-plus"></i>
            <span>Créer une annonce</span>
        </a>
    </div>

    <div class="space-y-4">
        <?php if (empty($annonces)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bullhorn text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Aucune annonce</h3>
                <p class="text-gray-500">Diffusez votre première annonce dès maintenant.</p>
            </div>
        <?php else: ?>
            <?php foreach ($annonces as $annonce): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 flex-shrink-0 bg-<?= $annonce['type'] === 'urgente' ? 'red' : ($annonce['type'] === 'administrative' ? 'blue' : 'orange') ?>-100 text-<?= $annonce['type'] === 'urgente' ? 'red' : ($annonce['type'] === 'administrative' ? 'blue' : 'orange') ?>-600 rounded-xl flex items-center justify-center text-xl">
                            <i class="fas <?= $annonce['type'] === 'urgente' ? 'fa-exclamation-circle' : 'fa-info-circle' ?>"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-2">
                                <h3 class="text-lg font-bold text-gray-800"><?= e($annonce['titre']) ?></h3>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400"><?= date('d/m/Y H:i', strtotime($annonce['created_at'])) ?></span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-gray-100 text-gray-500">
                                        <?= e($annonce['type']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="text-gray-600 mb-4 prose max-w-none">
                                <?= nl2br(e($annonce['contenu'])) ?>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-xs">
                                <span class="text-gray-400">Diffusion : </span>
                                <div class="flex items-center gap-3">
                                    <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded flex items-center gap-1">
                                        <i class="fas fa-bullseye text-[10px]"></i> Cible : <?= ucfirst(e($annonce['cible'])) ?>
                                    </span>
                                    <span class="text-gray-400">|</span>
                                    <span class="text-gray-500">Du <?= date('d/m/Y', strtotime($annonce['date_debut'])) ?> au <?= date('d/m/Y', strtotime($annonce['date_fin'])) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="<?= url('annonces/edit/' . $annonce['id']) ?>" class="text-gray-400 hover:text-blue-600"><i class="fas fa-edit"></i></a>
                            <a href="<?= url('annonces/delete/' . $annonce['id']) ?>" onclick="return confirm('Supprimer cette annonce ?')" class="text-gray-400 hover:text-red-600"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
