<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-envelope text-orange-600 mr-2"></i>
                Messagerie interne
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gérez vos échanges avec l'équipe et les parents</p>
        </div>
        <div>
            <button onclick="document.getElementById('modalMessage').classList.remove('hidden')" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 shadow-lg shadow-orange-200">
                <i class="fas fa-paper-plane"></i>
                <span>Nouveau message</span>
            </button>
        </div>
    </div>

    <!-- Modal Nouveau Message -->
    <div id="modalMessage" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
            <div class="p-6 border-b flex items-center justify-between bg-gray-50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-pen-nib text-orange-600"></i>
                    Composer un message
                </h3>
                <button onclick="document.getElementById('modalMessage').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="<?= url('notifications/messagerie/envoyer') ?>" method="POST" class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Destinataire</label>
                    <select name="destinataire_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        <option value="">Sélectionner un utilisateur...</option>
                        <?php foreach ($users as $u): ?>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <option value="<?= $u['id'] ?>"><?= e($u['username']) ?> (<?= e($u['role'] ?? 'Utilisateur') ?>)</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sujet</label>
                    <input type="text" name="sujet" required placeholder="Objet de votre message" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Message</label>
                    <textarea name="contenu" required rows="5" placeholder="Votre message..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"></textarea>
                </div>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalMessage').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-gray-600 font-medium hover:bg-gray-100 transition">
                        Annuler
                    </button>
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-orange-100 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <?php if (empty($messages)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                <p>Aucun message trouvé. La table <code class="bg-gray-100 px-2 py-1 rounded">messages</code> est vide ou n'existe pas.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expéditeur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinataire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sujet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($messages as $msg): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= e($msg['expediteur_nom'] ?? 'Utilisateur #' . ($msg['expediteur_id'] ?? 'N/A')) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= e($msg['destinataire_nom'] ?? 'Utilisateur #' . ($msg['destinataire_id'] ?? 'N/A')) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?= e($msg['sujet'] ?? 'Sans objet') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= e(formatDate($msg['date_envoi'] ?? $msg['created_at'] ?? '', 'd/m/Y H:i')) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= ($msg['lu'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= ($msg['lu'] ?? 0) ? 'Lu' : 'Non lu' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="#" class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded transition">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

