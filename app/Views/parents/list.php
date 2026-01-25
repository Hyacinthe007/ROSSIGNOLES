<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    Liste des parents
                </h1>
                <p class="text-gray-600 text-sm md:text-base">Consultation et recherche des parents et tuteurs</p>
            </div>
        </div>
        
        <!-- Note informative -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded-r">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="text-blue-800 font-medium mb-1">Information importante</p>
                    <p class="text-blue-700 text-sm">
                        Les parents sont automatiquement créés lors de l'inscription des élèves. 
                        Cette page permet uniquement de <strong>consulter, rechercher et modifier</strong> les informations des parents existants.
                    </p>
                    <p class="text-blue-600 text-xs mt-2">
                        <i class="fas fa-lightbulb mr-1"></i>
                        <strong>Astuce :</strong> Vous pouvez rechercher un parent en tapant le nom de son enfant !
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Barre de recherche -->
        <div class="bg-white rounded-lg shadow-md p-4">
            <form method="GET" action="<?= url('parents/list') ?>" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="<?= e($_GET['search'] ?? '') ?>"
                               placeholder="Rechercher un parent ou un élève (nom, prénom, téléphone, email)..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition flex items-center justify-center gap-2 shadow">
                    <i class="fas fa-search"></i>
                    <span>Rechercher</span>
                </button>
                <?php if (!empty($_GET['search'])): ?>
                    <a href="<?= url('parents/list') ?>" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Réinitialiser</span>
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prénom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lien</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Enfants</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($parents)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                <?php if (!empty($search)): ?>
                                    <p class="font-medium">Aucun parent trouvé pour "<?= e($search) ?>"</p>
                                    <p class="text-sm mt-2">Essayez avec d'autres termes de recherche</p>
                                <?php else: ?>
                                    <p>Aucun parent enregistré</p>
                                    <p class="text-sm mt-2">Les parents seront créés automatiquement lors de l'inscription des élèves</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($parents as $parent): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= e($parent['nom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e($parent['prenom']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <i class="fas fa-phone mr-2"></i><?= e($parent['telephone'] ?: 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= e($parent['email'] ?: 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <?= e($parent['lien_parente'] ?? $parent['type_parent'] ?? 'Parent') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php 
                                    $nbEnfants = $parent['nb_enfants'] ?? 0;
                                    $badgeColor = $nbEnfants > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600';
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $badgeColor ?>">
                                        <i class="fas fa-child mr-1"></i>
                                        <?= $nbEnfants ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url('parents/details/' . $parent['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition"
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('parents/edit/' . $parent['id']) ?>" 
                                           class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded transition"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                         </a>
                                         <a href="<?= url('inscriptions/inscrire-enfant/' . $parent['id']) ?>" 
                                            class="text-purple-600 hover:text-purple-900 p-2 hover:bg-purple-50 rounded transition"
                                            title="Inscrire un autre enfant">
                                             <i class="fas fa-child"></i>
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
