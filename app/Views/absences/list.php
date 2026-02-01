<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <?php
            // Déterminer le titre et l'icône selon le type filtré
            $titre = 'Liste des absences et retards';
            $icone = 'fa-user-times';
            $couleur = 'red';
            $description = 'Gestion des absences et retards des élèves';
            
            if (isset($type_filtre) && $type_filtre === 'retard') {
                $titre = 'Liste des retards';
                $icone = 'fa-clock';
                $couleur = 'orange';
                $description = 'Gestion des retards des élèves';
            } elseif (isset($type_filtre) && $type_filtre === 'absence') {
                $titre = 'Liste des absences';
                $icone = 'fa-user-times';
                $couleur = 'red';
                $description = 'Gestion des absences des élèves';
            }
            ?>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas <?= $icone ?> text-<?= $couleur ?>-600 mr-2"></i>
                <?= $titre ?>
            </h1>
            <p class="text-gray-600 text-sm md:text-base"><?= $description ?></p>
        </div>
        <a href="<?= url('absences/add') ?>" class="bg-<?= $couleur ?>-600 hover:bg-<?= $couleur ?>-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span>Ajouter <?= isset($type_filtre) && $type_filtre === 'retard' ? 'un retard' : 'une absence' ?></span>
        </a>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professeur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($absences)): ?>
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                Aucune absence trouvée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($absences as $absence): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= e($absence['nom'] . ' ' . $absence['prenom']) ?>
                                    <div class="text-xs text-gray-500"><?= e($absence['matricule']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="font-semibold text-purple-700">
                                        <?= e($absence['classe_code'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= formatDate($absence['date_absence']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($absence['type'] === 'retard'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            <i class="fas fa-clock mr-1"></i>Retard
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-user-times mr-1"></i>Absence
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php
                                    // Afficher l'intervalle de temps si disponible
                                    if (!empty($absence['heure_debut']) && !empty($absence['heure_fin'])) {
                                        echo '<span class="font-mono text-blue-700">';
                                        echo e(substr($absence['heure_debut'], 0, 5)) . ' - ' . e(substr($absence['heure_fin'], 0, 5));
                                        echo '</span>';
                                    } else {
                                        // Sinon afficher la période classique
                                        $periodes = [
                                            'matin' => 'Matin',
                                            'apres_midi' => 'Après-midi',
                                            'journee' => 'Journée'
                                        ];
                                        echo '<span class="text-gray-500">' . e($periodes[$absence['periode']] ?? $absence['periode']) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php if (!empty($absence['matiere_nom'])): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium">
                                            <i class="fas fa-book mr-1"></i>
                                            <?= e($absence['matiere_nom']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php if (!empty($absence['professeur_nom'])): ?>
                                        <span class="inline-flex items-center text-gray-700">
                                            <i class="fas fa-user-tie mr-1 text-indigo-500"></i>
                                            <?= e($absence['professeur_nom']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= e($absence['motif'] ?: 'Non spécifié') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($absence['justifiee']): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Justifiée
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>Non justifiée
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('absences/details/' . $absence['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('absences/edit/' . $absence['id']) ?>" 
                                           class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
