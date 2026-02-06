<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                Assiduité
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gestion des absences et retards des élèves</p>
        </div>
        <?php
        // Déterminer l'onglet actif
        $type_actif = $_GET['type'] ?? 'absence';
        $texte_bouton = $type_actif === 'retard' ? 'Ajouter retard' : 'Ajouter absence';
        ?>
        <a href="<?= url('absences/add') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-lg">
            <i class="fas fa-plus"></i>
            <span><?= $texte_bouton ?></span>
        </a>
    </div>

    <!-- Onglets -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="<?= url('absences/list?type=absence') ?>" 
                   class="<?= $type_actif === 'absence' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-user-times mr-2"></i>
                    Absences
                    <?php if (isset($count_absences) && $count_absences > 0): ?>
                        <span class="ml-2 bg-red-100 text-red-600 py-1 px-2 rounded-full text-xs font-semibold"><?= $count_absences ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= url('absences/list?type=retard') ?>" 
                   class="<?= $type_actif === 'retard' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-clock mr-2"></i>
                    Retards
                    <?php if (isset($count_retards) && $count_retards > 0): ?>
                        <span class="ml-2 bg-orange-100 text-orange-600 py-1 px-2 rounded-full text-xs font-semibold"><?= $count_retards ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
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
                                    <div class="flex items-center gap-2">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   class="sr-only peer toggle-justifiee" 
                                                   data-absence-id="<?= $absence['id'] ?>"
                                                   <?= $absence['justifiee'] ? 'checked' : '' ?>>
                                            <div class="w-11 h-6 bg-red-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                                        </label>
                                        <span class="text-xs font-medium toggle-label-<?= $absence['id'] ?> <?= $absence['justifiee'] ? 'text-green-700' : 'text-red-700' ?>">
                                            <?= $absence['justifiee'] ? 'Justifiée' : 'Non justifiée' ?>
                                        </span>
                                    </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer les toggles de statut justifié/non justifié
    const toggles = document.querySelectorAll('.toggle-justifiee');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const absenceId = this.dataset.absenceId;
            const isJustifiee = this.checked;
            const label = document.querySelector(`.toggle-label-${absenceId}`);
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            
            // Désactiver le toggle pendant la requête
            this.disabled = true;
            
            // Envoyer la requête AJAX
            fetch('<?= url('absences/toggle-justifiee') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    absence_id: absenceId,
                    justifiee: isJustifiee ? 1 : 0
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mettre à jour le label
                    if (label) {
                        label.textContent = isJustifiee ? 'Justifiée' : 'Non justifiée';
                        label.className = `text-xs font-medium toggle-label-${absenceId} ${isJustifiee ? 'text-green-700' : 'text-red-700'}`;
                    }
                    
                    // Afficher un message de succès
                    showNotification('Statut mis à jour avec succès', 'success');
                } else {
                    // En cas d'erreur, revenir à l'état précédent
                    this.checked = !isJustifiee;
                    showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // En cas d'erreur, revenir à l'état précédent
                this.checked = !isJustifiee;
                showNotification('Erreur de connexion : ' + error.message, 'error');
            })
            .finally(() => {
                // Réactiver le toggle
                this.disabled = false;
            });
        });
    });
    
    // Fonction pour afficher les notifications
    function showNotification(message, type = 'success') {
        // Supprimer les notifications existantes pour éviter l'empilement
        const existing = document.querySelectorAll('.dynamic-notification');
        existing.forEach(el => el.remove());

        const notification = document.createElement('div');
        notification.className = `dynamic-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-[9999] transition-all transform duration-300 translate-y-0 opacity-100 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Retirer après 3 secondes
        setTimeout(() => {
            notification.classList.add('opacity-0', 'translate-y-[-20px]');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
});
</script>
