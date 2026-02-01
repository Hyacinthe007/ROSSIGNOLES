<div class="p-4 md:p-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border-t-8 border-red-500">
            <div class="text-center mb-6">
                <i class="fas fa-exclamation-circle text-7xl text-red-500 mb-4 animate-pulse"></i>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Attention !</h1>
                <p class="text-gray-600">Vous allez supprimer définitivement cet enregistrement. Cette action est irréversible.</p>
            </div>

            <div class="bg-red-50 rounded-xl p-6 mb-8 border border-red-100">
                <h3 class="text-xs font-bold text-red-400 uppercase tracking-widest mb-3">Récapitulatif de l'élément à supprimer</h3>
                <div class="space-y-3">
                    <p class="text-lg text-gray-800 font-bold">
                        <i class="fas fa-calendar-day mr-2 text-red-600"></i>
                        <?= $absence['type'] === 'retard' ? 'Retard' : 'Absence' ?> du <?= formatDate($absence['date_absence']) ?>
                    </p>
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-clock mr-2 text-gray-400"></i>
                        Période : <span class="font-semibold"><?php 
                            if (!empty($absence['heure_debut'])) {
                                echo substr($absence['heure_debut'], 0, 5) . ' - ' . substr($absence['heure_fin'], 0, 5);
                            } else {
                                echo ucfirst($absence['periode']);
                            }
                        ?></span>
                    </p>
                    <?php if ($absence['motif']): ?>
                        <div class="mt-4 p-3 bg-white/50 rounded border border-red-50 italic text-sm text-gray-600">
                            "<?= e($absence['motif']) ?>"
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <form method="POST" action="<?= url('absences/delete/' . $absence['id']) ?>" class="flex flex-col sm:flex-row gap-4">
                <?= csrf_field() ?>
                <button type="submit" 
                        class="flex-[2] bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg hover:shadow-red-200 flex items-center justify-center gap-3">
                    <i class="fas fa-trash-alt"></i>
                    <span>Confirmer la suppression</span>
                </button>
                <a href="<?= url('absences/details/' . $absence['id']) ?>" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
            </form>
        </div>
    </div>
</div>
