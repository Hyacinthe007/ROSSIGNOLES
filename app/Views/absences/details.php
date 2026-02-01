<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-times text-red-600 mr-2"></i>
                Détails de <?= $absence['type'] === 'retard' ? "du retard" : "de l'absence" ?>
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Informations complètes enregistrées</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('absences/edit/' . $absence['id']) ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Modifier</span>
            </a>
            <a href="<?= url('absences/list') ?>" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne Gauche: Infos Élève -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg border-t-4 border-blue-600 p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-3">
                        <?= strtoupper(substr($absence['eleve_nom'], 0, 1) . substr($absence['eleve_prenom'], 0, 1)) ?>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900"><?= e($absence['eleve_nom'] . ' ' . $absence['eleve_prenom']) ?></h2>
                    <p class="text-gray-500"><?= e($absence['matricule']) ?></p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Classe</span>
                        <span class="font-semibold text-gray-800"><?= e($absence['classe_nom']) ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Type</span>
                        <span class="px-2 py-1 text-xs font-bold rounded-full <?= $absence['type'] === 'retard' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800' ?>">
                            <?= strtoupper($absence['type']) ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-500 text-sm">Justifiée</span>
                        <?php if ($absence['justifiee']): ?>
                            <span class="text-green-600 font-bold"><i class="fas fa-check-circle mr-1"></i>Oui</span>
                        <?php else: ?>
                            <span class="text-red-600 font-bold"><i class="fas fa-times-circle mr-1"></i>Non</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne Droite: Détails Absence -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6 h-full text-sm">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>Détails du créneau
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Date et Heure -->
                    <div class="space-y-6">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-calendar-alt text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Date</p>
                                <p class="text-base font-semibold text-gray-800"><?= formatDate($absence['date_absence']) ?></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Horaire / Période</p>
                                <p class="text-base font-semibold text-gray-800">
                                    <?php if (!empty($absence['heure_debut'])): ?>
                                        <?= substr($absence['heure_debut'], 0, 5) ?> - <?= substr($absence['heure_fin'], 0, 5) ?>
                                    <?php else: ?>
                                        <?= ucfirst($absence['periode']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Motif et Infos Saisie -->
                    <div class="space-y-6">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-comment-dots text-gray-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Motif</p>
                                <p class="text-base text-gray-800"><?= e($absence['motif'] ?: 'Aucun motif renseigné') ?></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-user-edit text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Saisie par</p>
                                <p class="text-base font-semibold text-gray-800"><?= e($absence['saisi_par_username'] ?: 'Système') ?></p>
                                <p class="text-xs text-gray-400 mt-1">Le <?= formatDate($absence['created_at'], true) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Autres infos si validé -->
                <?php if ($absence['valide_par']): ?>
                <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-2 text-gray-700 font-bold mb-2">
                        <i class="fas fa-check-double text-green-500"></i>
                        Validation
                    </div>
                    <p class="text-sm">Validé par <span class="font-bold"><?= e($absence['valide_par_username']) ?></span> le <?= formatDate($absence['date_validation'], true) ?></p>
                    <?php if ($absence['commentaire_validation']): ?>
                        <div class="mt-2 text-xs text-gray-600 italic">
                            "<?= e($absence['commentaire_validation']) ?>"
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
