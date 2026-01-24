<div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-file-invoice text-blue-600 mr-2"></i>
                Détails de l'inscription
            </h1>
            <p class="text-gray-600 text-sm md:text-base">
                Inscription #<?= e($inscription['id']) ?> - <?= e($inscription['annee_scolaire']) ?>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= url('inscriptions/documents/' . $inscription['id']) ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                <i class="fas fa-file-upload mr-2"></i>Gérer les documents
            </a>
            <a href="<?= url('inscriptions/liste') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center shadow-md">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
            <!-- Bouton Terminer supprimé car l'inscription est validée directement à l'étape 4 -->
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
            <p><?= $_SESSION['success'] ?></p>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r" role="alert">
            <p><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informations Élève -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-user-graduate text-blue-600 mr-2"></i>Élève
            </h2>
            <div class="space-y-4">
                <div class="flex items-start justify-between">
                    <span class="text-gray-600">Nom complet :</span>
                    <span class="font-medium text-gray-900 text-right"><?= e($inscription['eleve_nom']) ?> <?= e($inscription['eleve_prenom']) ?></span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-gray-600">Matricule :</span>
                    <span class="font-mono bg-gray-100 px-2 py-1 rounded text-gray-800"><?= e($inscription['eleve_matricule']) ?></span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-gray-600">Sexe :</span>
                    <span class="font-medium text-gray-900"><?= $inscription['eleve_sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-gray-600">Date de naissance :</span>
                    <span class="font-medium text-gray-900">
                        <?= date('d/m/Y', strtotime($inscription['eleve_date_naissance'])) ?>
                        <span class="text-sm text-gray-500">(<?= e($inscription['eleve_lieu_naissance'] ?? '') ?>)</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Informations Académiques -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-school text-indigo-600 mr-2"></i>Scolarité
            </h2>
            <div class="space-y-4">
                <div class="flex items-start justify-between">
                    <span class="text-gray-600">Classe :</span>
                    <span class="font-bold text-lg text-indigo-600"><?= e($inscription['classe_nom']) ?></span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-gray-600">Statut :</span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold 
                        <?= $inscription['statut'] === 'validee' ? 'bg-green-100 text-green-800' : 
                           ($inscription['statut'] === 'terminee' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') ?>">
                        <?= ucfirst($inscription['statut']) ?>
                    </span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-gray-600">Date d'inscription :</span>
                    <span class="font-medium text-gray-900"><?= date('d/m/Y H:i', strtotime($inscription['date_inscription'])) ?></span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-gray-600">Type :</span>
                    <?php if (isset($inscription['type_inscription']) && $inscription['type_inscription'] === 'reinscription'): ?>
                        <span class="font-medium text-blue-600">
                            <i class="fas fa-redo mr-1"></i>Réinscription
                        </span>
                    <?php else: ?>
                        <span class="font-medium text-green-600">
                            <i class="fas fa-user-plus mr-1"></i>Nouvelle inscription
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Finances et Paiements -->
    <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 border-b pb-4 gap-4">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>Paiements Inscription
            </h2>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm text-gray-600">Total payé</p>
                    <p class="text-xl font-bold text-green-600"><?= number_format($inscription['montant_paye'] ?? 0, 0, ',', ' ') ?> MGA</p>
                </div>
                <div class="h-10 w-px bg-gray-300 mx-2"></div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Reste à payer</p>
                    <p class="text-xl font-bold <?= ($inscription['reste_a_payer'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-400' ?>">
                        <?= number_format($inscription['reste_a_payer'] ?? 0, 0, ',', ' ') ?> MGA
                    </p>
                </div>
            </div>
        </div>

        <div class="mb-6 flex justify-end">
            <?php if (($inscription['reste_a_payer'] ?? 0) > 0 && $inscription['statut'] === 'validee'): ?>
                <a href="<?= url('inscriptions/ajouter-paiement/' . $inscription['id']) ?>" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition inline-flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i>Nouveau paiement
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($paiements)): ?>
            <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                <i class="fas fa-receipt text-4xl mb-3 text-gray-300"></i>
                <p>Aucun paiement enregistré pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($paiements as $paiement): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d/m/Y H:i', strtotime($paiement['date_paiement'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">
                                <?= e($paiement['numero_recu'] ?? $paiement['reference_externe'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= e($paiement['mode_paiement'] ?? $paiement['libelle'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                <?= number_format($paiement['montant'], 0, ',', ' ') ?> MGA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= e($paiement['commentaire'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= url('finance/recus?id=' . $paiement['id']) ?>" target="_blank" class="text-blue-600 hover:text-blue-900" title="Imprimer le reçu">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
