<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-bell text-blue-600 mr-2"></i>
            Créer une notification
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Envoyez une notification aux utilisateurs</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('notifications/add') ?>" class="space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Destinataire -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-gray-500"></i>Destinataire *
                    </label>
                    <select id="user_id" 
                            name="user_id" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionner un utilisateur</option>
                        <option value="all">Tous les utilisateurs</option>
                        <?php if (isset($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>">
                                    <?= e($user['username'] . ' (' . ($user['email'] ?? 'N/A') . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Sélectionnez un utilisateur spécifique ou "Tous les utilisateurs"</p>
                </div>

                <!-- Canal -->
                <div>
                    <label for="canal" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-broadcast-tower mr-2 text-gray-500"></i>Canal de communication *
                    </label>
                    <select id="canal" 
                            name="canal" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionner un canal</option>
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                        <option value="push">Notification push</option>
                        <option value="systeme">Notification système</option>
                    </select>
                </div>

                <!-- Titre -->
                <div class="md:col-span-2">
                    <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-heading mr-2 text-gray-500"></i>Titre *
                    </label>
                    <input type="text" 
                           id="titre" 
                           name="titre" 
                           required
                           maxlength="255"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Titre de la notification">
                </div>

                <!-- Contenu -->
                <div class="md:col-span-2">
                    <label for="contenu" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2 text-gray-500"></i>Contenu *
                    </label>
                    <textarea id="contenu" 
                              name="contenu"
                              rows="6"
                              required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Contenu de la notification..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Le contenu sera envoyé selon le canal sélectionné</p>
                </div>

                <!-- Date d'envoi programmé (optionnel) -->
                <div>
                    <label for="date_envoi" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Date d'envoi programmé
                    </label>
                    <input type="datetime-local" 
                           id="date_envoi" 
                           name="date_envoi"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour envoyer immédiatement</p>
                </div>

                <!-- Priorité -->
                <div>
                    <label for="priorite" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-exclamation-circle mr-2 text-gray-500"></i>Priorité
                    </label>
                    <select id="priorite" 
                            name="priorite"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="normale">Normale</option>
                        <option value="haute">Haute</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>
            </div>

            <!-- Informations supplémentaires (meta) -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Informations supplémentaires (optionnel)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Lien -->
                    <div>
                        <label for="meta_lien" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-link mr-2 text-gray-500"></i>Lien (URL)
                        </label>
                        <input type="url" 
                               id="meta_lien" 
                               name="meta_lien"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://...">
                    </div>

                    <!-- Type de notification -->
                    <div>
                        <label for="meta_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-2 text-gray-500"></i>Type de notification
                        </label>
                        <select id="meta_type" 
                                name="meta_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Aucun</option>
                            <option value="info">Information</option>
                            <option value="success">Succès</option>
                            <option value="warning">Avertissement</option>
                            <option value="error">Erreur</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Envoyer la notification</span>
                </button>
                <a href="<?= url('notifications/list') ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
            </div>
        </form>
    </div>
</div>

