<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-cog text-blue-600 mr-2"></i>
                Configuration du Système
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Gérez les paramètres globaux et structurels de votre établissement</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('dashboard') ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-home mr-2 text-blue-500"></i> Retour
            </a>
        </div>
    </div>

    <!-- Onglets Navigation (Style Dashboard) -->
    <div class="flex border-b border-gray-200 mb-6 space-x-6 md:space-x-12 overflow-x-auto no-scrollbar scroll-smooth">
        <button onclick="switchTab('ecole')" id="tab-ecole" class="tab-top-btn py-4 px-1 border-b-2 font-medium text-sm transition-all border-blue-600 text-blue-600 whitespace-nowrap">
            <i class="fas fa-school mr-2"></i>École
        </button>
        <button onclick="switchTab('annees')" id="tab-annees" class="tab-top-btn py-4 px-1 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
            <i class="fas fa-calendar-alt mr-2"></i>Années scolaires
        </button>
        <button onclick="switchTab('finance')" id="tab-finance" class="tab-top-btn py-4 px-1 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
            <i class="fas fa-coins mr-2"></i>Finance
        </button>
        <button onclick="switchTab('enseignement')" id="tab-enseignement" class="tab-top-btn py-4 px-1 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
            <i class="fas fa-book mr-2"></i>Enseignement
        </button>
        <button onclick="switchTab('modeles')" id="tab-modeles" class="tab-top-btn py-4 px-1 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
            <i class="fas fa-copy mr-2"></i>Modèles notifications
        </button>
        <button onclick="switchTab('preferences')" id="tab-preferences" class="tab-top-btn py-4 px-1 border-b-2 font-medium text-sm transition-all border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
            <i class="fas fa-sliders-h mr-2"></i>Préférences
        </button>
    </div>

    <?php
    // Helper pour accéder facilement aux configurations
    $configMap = [];
    if (!empty($configs)) {
        foreach ($configs as $c) {
            $configMap[$c['cle']] = $c['valeur'];
        }
    }
    ?>

    <!-- Conteneurs des Onglets -->
    <div id="content-ecole" class="tab-content-panel transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <?php if (empty($configs)): ?>
                <div class="p-12 text-center text-gray-400">
                    <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                    <p class="text-lg">Aucune configuration disponible</p>
                </div>
            <?php else: ?>
                <div class="p-6 md:p-8">
                    <form method="POST" action="<?= url('systeme/config') ?>" class="space-y-8">
                        <?= csrf_field() ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <?php foreach ($configs as $config): ?>
                                <?php 
                                // Filtrer les configurations de préférence pour ne pas les afficher ici
                                if (in_array($config['cle'], ['theme_color', 'theme_font', 'theme_font_size', 'system_language'])) continue;
                                ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-gray-700"><?= e($config['cle']) ?></label>
                                    <input type="text" name="<?= e($config['cle']) ?>" value="<?= e($config['valeur'] ?? '') ?>"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50">
                                    <p class="text-[10px] text-gray-400 "><?= e($config['description']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex justify-end pt-6 border-t">
                            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-lg transition-all">
                                <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="content-annees" class="tab-content-panel hidden transition-all duration-300">
        <div class="space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button onclick="loadAnnees('list')" class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-600">Années Scolaires</span>
                </button>
                <button onclick="loadAnnees('periodes')" class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-teal-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-teal-600 group-hover:text-white transition-all">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-600">Périodes</span>
                </button>
                <button onclick="loadAnnees('calendrier')" class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-orange-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-orange-600 group-hover:text-white transition-all">
                        <i class="fas fa-umbrella-beach"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-600">Vacances & Fériés</span>
                </button>
            </div>
            <div id="anneesContent" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 min-h-[50vh]">
                <iframe src="<?= url('annees-scolaires/list') ?>?iframe=1" class="w-full min-h-[75vh]" loading="lazy"></iframe>
            </div>
        </div>
    </div>

    <div id="content-finance" class="tab-content-panel hidden transition-all duration-300">
        <div class="space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button onclick="loadFinance('tarifs')" class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-green-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-green-600 group-hover:text-white transition-all">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-600">Tarifs Inscription</span>
                </button>
                <button onclick="loadFinance('articles')" class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-600">Articles Scolaires</span>
                </button>
            </div>
            <div id="financeContent" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 min-h-[50vh]">
                <iframe src="<?= url('tarifs/liste') ?>?iframe=1" class="w-full min-h-[75vh]" loading="lazy"></iframe>
            </div>
        </div>
    </div>

    <div id="content-enseignement" class="tab-content-panel hidden transition-all duration-300">
        <div class="space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <button onclick="loadEnseignement('classes')" class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Classes</span>
                </button>
                <button onclick="loadEnseignement('niveaux')" class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-green-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-green-600 group-hover:text-white transition-all">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Niveaux</span>
                </button>
                <button onclick="loadEnseignement('cycles')" class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-orange-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-orange-600 group-hover:text-white transition-all">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Cycles</span>
                </button>
                <button onclick="loadEnseignement('series')" class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-purple-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-purple-600 group-hover:text-white transition-all">
                        <i class="fas fa-stream"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Séries</span>
                </button>
                <button onclick="loadEnseignement('matieres')" class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-pink-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-pink-600 group-hover:text-white transition-all">
                        <i class="fas fa-book"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Matières</span>
                </button>
                <button onclick="loadEnseignement('attributions')" class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all text-center group">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-2 font-bold group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Attributions</span>
                </button>
            </div>
            <div id="enseignementContent" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 min-h-[50vh]">
                <div class="flex flex-col items-center justify-center p-12 text-gray-300">
                    <i class="fas fa-mouse-pointer text-5xl mb-4"></i>
                    <p class="font-bold">Sélectionnez une catégorie ci-dessus</p>
                </div>
            </div>
        </div>
    </div>


    <div id="content-modeles" class="tab-content-panel hidden transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 min-h-[75vh]">
            <iframe src="<?= url('notifications/modeles') ?>?iframe=1" class="w-full min-h-[75vh]" loading="lazy"></iframe>
        </div>
    </div>

    <div id="content-preferences" class="tab-content-panel hidden transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-palette text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Personnalisation de l'interface</h2>
            </div>
            
            <form method="POST" action="<?= url('systeme/config') ?>" class="space-y-8">
                <?= csrf_field() ?>
                
                <!-- Theme Color -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-brush text-gray-400"></i> Couleur du thème
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Blue (Default) -->
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="theme_color" value="blue" class="peer hidden" <?= (!isset($configMap['theme_color']) || $configMap['theme_color'] == 'blue') ? 'checked' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all flex flex-col items-center hover:border-blue-300">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 mb-3 shadow-md transform group-hover:scale-110 transition-transform"></div>
                                <span class="text-sm font-bold text-gray-700">Bleu Océan</span>
                            </div>
                            <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-blue-600 transition-opacity">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </label>
                         <!-- Green -->
                         <label class="cursor-pointer group relative">
                            <input type="radio" name="theme_color" value="green" class="peer hidden" <?= (isset($configMap['theme_color']) && $configMap['theme_color'] == 'green') ? 'checked' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 transition-all flex flex-col items-center hover:border-green-300">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-green-700 mb-3 shadow-md transform group-hover:scale-110 transition-transform"></div>
                                <span class="text-sm font-bold text-gray-700">Vert Nature</span>
                            </div>
                            <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-green-600 transition-opacity">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </label>
                        <!-- Purple -->
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="theme_color" value="purple" class="peer hidden" <?= (isset($configMap['theme_color']) && $configMap['theme_color'] == 'purple') ? 'checked' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all flex flex-col items-center hover:border-purple-300">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 mb-3 shadow-md transform group-hover:scale-110 transition-transform"></div>
                                <span class="text-sm font-bold text-gray-700">Violet Royal</span>
                            </div>
                            <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-purple-600 transition-opacity">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </label>
                         <!-- Orange -->
                         <label class="cursor-pointer group relative">
                            <input type="radio" name="theme_color" value="orange" class="peer hidden" <?= (isset($configMap['theme_color']) && $configMap['theme_color'] == 'orange') ? 'checked' : '' ?>>
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all flex flex-col items-center hover:border-orange-300">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-500 to-orange-700 mb-3 shadow-md transform group-hover:scale-110 transition-transform"></div>
                                <span class="text-sm font-bold text-gray-700">Orange Sunset</span>
                            </div>
                            <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-orange-600 transition-opacity">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </label>
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Typography -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Font Family -->
                    <div class="space-y-3">
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                             <i class="fas fa-font text-gray-400 mr-1"></i> Police d'écriture
                        </label>
                        <div class="relative">
                            <select name="theme_font" class="w-full pl-4 pr-10 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 appearance-none">
                                <option value="Outfit" <?= (!isset($configMap['theme_font']) || $configMap['theme_font'] == 'Outfit') ? 'selected' : '' ?>>Outfit (Moderne, Par défaut)</option>
                                <option value="Inter" <?= (isset($configMap['theme_font']) && $configMap['theme_font'] == 'Inter') ? 'selected' : '' ?>>Inter (Clean)</option>
                                <option value="Roboto" <?= (isset($configMap['theme_font']) && $configMap['theme_font'] == 'Roboto') ? 'selected' : '' ?>>Roboto (Classique)</option>
                                <option value="Open Sans" <?= (isset($configMap['theme_font']) && $configMap['theme_font'] == 'Open Sans') ? 'selected' : '' ?>>Open Sans (Lisible)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 ">La police sélectionnée sera appliquée à l'ensemble de l'application.</p>
                    </div>
                    
                    <!-- Font Size -->
                     <div class="space-y-3">
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-text-height text-gray-400 mr-1"></i> Taille de police
                        </label>
                        <div class="relative">
                            <select name="theme_font_size" class="w-full pl-4 pr-10 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 appearance-none">
                                <option value="sm" <?= (isset($configMap['theme_font_size']) && $configMap['theme_font_size'] == 'sm') ? 'selected' : '' ?>>Compacte</option>
                                <option value="base" <?= (!isset($configMap['theme_font_size']) || $configMap['theme_font_size'] == 'base') ? 'selected' : '' ?>>Normale (Par défaut)</option>
                                <option value="lg" <?= (isset($configMap['theme_font_size']) && $configMap['theme_font_size'] == 'lg') ? 'selected' : '' ?>>Grande</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 ">Ajustez la taille du texte pour un meilleur confort de lecture.</p>
                    </div>
                </div>

                 <hr class="border-gray-100">

                <!-- Language -->
                <div class="space-y-4">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                        <i class="fas fa-globe text-gray-400 mr-1"></i> Langue du système
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="cursor-pointer group">
                             <input type="radio" name="system_language" value="fr" class="peer hidden" <?= (!isset($configMap['system_language']) || $configMap['system_language'] == 'fr') ? 'checked' : '' ?>>
                             <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all hover:bg-gray-50">
                                 <span class="text-3xl">🇫🇷</span>
                                 <div>
                                     <div class="font-bold text-gray-800">Français</div>
                                     <div class="text-xs text-gray-500">Langue par défaut</div>
                                 </div>
                             </div>
                        </label>
                        <label class="cursor-pointer group">
                             <input type="radio" name="system_language" value="en" class="peer hidden" <?= (isset($configMap['system_language']) && $configMap['system_language'] == 'en') ? 'checked' : '' ?>>
                             <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all hover:bg-gray-50">
                                 <span class="text-3xl">🇬🇧</span>
                                  <div>
                                     <div class="font-bold text-gray-800">English</div>
                                     <div class="text-xs text-gray-500">English Language</div>
                                 </div>
                             </div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> 
                        <span>Enregistrer les préférences</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(tabId) {
    const panels = document.querySelectorAll('.tab-content-panel');
    const buttons = document.querySelectorAll('.tab-top-btn');
    
    panels.forEach(p => p.classList.add('hidden'));
    buttons.forEach(b => {
        b.classList.remove('border-blue-600', 'text-blue-600');
        b.classList.add('border-transparent', 'text-gray-500');
    });

    document.getElementById('content-' + tabId).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + tabId);
    activeBtn.classList.add('border-blue-600', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    
    // Save state
    localStorage.setItem('active_config_tab', tabId);
}

document.addEventListener('DOMContentLoaded', function() {
    const savedTab = localStorage.getItem('active_config_tab') || 'ecole';
    switchTab(savedTab);
});

function loadAnnees(type) {
    const container = document.getElementById('anneesContent');
    const urls = {
        'list': "<?= url('annees-scolaires/list') ?>?iframe=1",
        'periodes': "<?= url('periodes/list') ?>?iframe=1",
        'calendrier': "<?= url('calendrier/list') ?>?iframe=1"
    };
    
    container.innerHTML = `<div class="p-20 text-center"><i class="fas fa-spinner fa-spin text-3xl text-blue-500 mb-4"></i><p class="text-sm font-bold text-gray-500">Chargement...</p></div>`;
    
    setTimeout(() => {
        container.innerHTML = `<iframe src="${urls[type]}" class="w-full min-h-[70vh]"></iframe>`;
    }, 100);
}


function loadEnseignement(type) {
    const container = document.getElementById('enseignementContent');
    const urls = {
        'classes': "<?= url('classes/list') ?>?iframe=1",
        'niveaux': "<?= url('pedagogie/niveaux') ?>?iframe=1",
        'cycles': "<?= url('pedagogie/cycles') ?>?iframe=1",
        'series': "<?= url('pedagogie/series') ?>?iframe=1",
        'matieres': "<?= url('matieres/list') ?>?iframe=1",
        'attributions': "<?= url('pedagogie/enseignements') ?>?iframe=1",
        'periodes': "<?= url('periodes/list') ?>?iframe=1"
    };
    
    container.innerHTML = `<div class="p-20 text-center"><i class="fas fa-spinner fa-spin text-3xl text-blue-500 mb-4"></i><p class="text-sm font-bold text-gray-500">Chargement...</p></div>`;
    
    setTimeout(() => {
        container.innerHTML = `<iframe src="${urls[type]}" class="w-full min-h-[70vh]"></iframe>`;
    }, 100);
}

function loadFinance(type) {
    const container = document.getElementById('financeContent');
    const urls = {
        'tarifs': "<?= url('tarifs/liste') ?>?iframe=1",
        'articles': "<?= url('articles/liste') ?>?iframe=1"
    };
    
    container.innerHTML = `<div class="p-20 text-center"><i class="fas fa-spinner fa-spin text-3xl text-green-500 mb-4"></i><p class="text-sm font-bold text-gray-500">Chargement...</p></div>`;
    
    setTimeout(() => {
        container.innerHTML = `<iframe src="${urls[type]}" class="w-full min-h-[70vh]"></iframe>`;
    }, 100);
}
</script>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

