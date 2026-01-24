<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-history text-purple-600 mr-2"></i>
                Parcours Scolaire
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Historique complet de l'élève : <?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('eleves/list') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à la liste</span>
            </a>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow">
                <i class="fas fa-print"></i>
                <span>Imprimer</span>
            </button>
            <a href="<?= url('eleves/parcours/pdf/' . $eleve['id']) ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow">
                <i class="fas fa-file-pdf"></i>
                <span>Télécharger PDF</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne Gauche : Profil -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 h-24"></div>
                <div class="px-6 pb-6">
                    <div class="relative -mt-12 mb-4 text-center">
                        <?php if (!empty($eleve['photo'])): ?>
                            <img src="<?= public_url($eleve['photo']) ?>" 
                                 class="w-24 h-24 rounded-2xl mx-auto object-cover border-4 border-white shadow-lg">
                        <?php else: ?>
                            <div class="w-24 h-24 rounded-2xl mx-auto bg-gray-200 border-4 border-white shadow-lg flex items-center justify-center text-gray-400">
                                <i class="fas fa-user text-4xl"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900"><?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?></h2>
                        <p class="text-sm font-mono text-blue-600 font-bold"><?= e($eleve['matricule']) ?></p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <i class="fas fa-venus-mars text-purple-500 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Sexe</p>
                                <p class="text-sm text-gray-900"><?= $eleve['sexe'] == 'M' ? 'Masculin' : 'Féminin' ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <i class="fas fa-birthday-cake text-purple-500 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Né(e) le</p>
                                <p class="text-sm text-gray-900"><?= formatDate($eleve['date_naissance']) ?> à <?= e($eleve['lieu_naissance']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne Droite : Cursus Scolaire -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b flex items-center justify-between bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-graduation-cap text-purple-600"></i>
                        Cursus Scolaire
                    </h3>
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">
                        <?= count($inscriptions) ?> Année(s)
                    </span>
                </div>

                <div class="p-6">
                    <?php if (empty($inscriptions)): ?>
                        <div class="p-12 text-center">
                            <i class="fas fa-graduation-cap text-4xl text-gray-300 mb-4 block"></i>
                            <p class="text-gray-500 ">Aucune inscription trouvée.</p>
                        </div>
                    <?php else: ?>
                        <div class="relative pl-8 border-l-2 border-purple-100 space-y-8">
                            <?php foreach ($inscriptions as $ins): ?>
                                <div class="relative">
                                    <div class="absolute -left-[2.625rem] top-6 w-5 h-5 rounded-full bg-white border-4 border-purple-600 shadow-sm z-10"></div>
                                    <div class="bg-gray-50 rounded-xl p-4 hover:bg-white hover:shadow-md transition border border-transparent hover:border-purple-100">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                            <div>
                                                <p class="text-sm font-bold text-purple-600"><?= e($ins['annee_scolaire']) ?></p>
                                                <h4 class="text-lg font-bold text-gray-900"><?= e($ins['classe_nom']) ?></h4>
                                            </div>
                                            <div class="text-left md:text-right">
                                                <p class="text-xs text-gray-500 uppercase font-bold">Date d'inscription</p>
                                                <p class="text-sm text-gray-600"><?= formatDate($ins['created_at']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

