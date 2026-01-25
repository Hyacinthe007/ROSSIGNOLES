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
                    <span class="font-bold text-lg text-indigo-600"><?= e($inscription['classe_code']) ?></span>
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

    <div class="mt-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users text-blue-600"></i>
                    Autres élèves de la classe (<?= count($elevesMemeClasse) ?>)
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold font-mono">
                            <th class="px-6 py-4">Matricule</th>
                            <th class="px-6 py-4">Nom complet</th>
                            <th class="px-6 py-4">Sexe</th>
                            <th class="px-6 py-4 text-center">Date inscription</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($elevesMemeClasse)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                    Cet élève est le seul inscrit dans cette classe pour le moment.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($elevesMemeClasse as $e): ?>
                                <tr class="hover:bg-blue-50/50 transition-colors group">
                                    <td class="px-6 py-4 font-mono text-sm text-gray-500"><?= e($e['matricule']) ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                                <?= strtoupper(substr($e['nom'], 0, 1) . substr($e['prenom'], 0, 1)) ?>
                                            </div>
                                            <span class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                                <?= e($e['nom']) ?> <?= e($e['prenom']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?= $e['sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                                        <?= date('d/m/Y', strtotime($e['date_inscription'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="<?= url('inscriptions/details/' . $e['inscription_id']) ?>" 
                                           class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg border border-blue-200 transition-all">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
