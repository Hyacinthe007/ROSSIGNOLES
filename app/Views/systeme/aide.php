

<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Centre d'Aide & Documentation</h1>
            <p class="text-gray-600">Bienvenue dans l'espace d'assistance de ROSSIGNOLES. Trouvez ici les réponses à vos questions.</p>
        </div>

        <!-- Section de recherche rapide -->
        <div class="bg-blue-600 rounded-2xl p-8 text-white mb-10 shadow-lg">
            <h2 class="text-xl font-semibold mb-4 text-center">Comment pouvons-nous vous aider ?</h2>
            <div class="relative max-w-2xl mx-auto">
                <input type="text" id="helpSearchInput" placeholder="Rechercher un tutoriel ou un endpoint API..." class="w-full py-4 px-6 rounded-full text-gray-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all text-lg">
                <button class="absolute right-2 top-2 bottom-2 px-6 bg-blue-700 rounded-full hover:bg-blue-800 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12" id="helpCardsGrid">
            <!-- Élèves -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Gestion des Élèves</h3>
                <ul class="space-y-2 text-gray-600">
                    <li><a href="#" class="hover:text-indigo-600 transition-colors">Inscrire un nouvel élève</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition-colors">Gérer les documents d'inscription</a></li>
                    <li><a href="#" class="hover:text-indigo-600 transition-colors">Suivi des absences et retards</a></li>
                </ul>
            </div>

            <!-- Finance -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Facturation & Finance</h3>
                <ul class="space-y-2 text-gray-600">
                    <li><a href="#" class="hover:text-emerald-600 transition-colors">Générer les écheanciers d'écolage</a></li>
                    <li><a href="#" class="hover:text-emerald-600 transition-colors">Enregistrer un paiement et imprimer un reçu</a></li>
                    <li><a href="#" class="hover:text-emerald-600 transition-colors">Gérer les impayés et relances SMS</a></li>
                </ul>
            </div>

            <!-- Pédagogie -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-book-reader text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Pédagogie & Notes</h3>
                <ul class="space-y-2 text-gray-600">
                    <li><a href="#" class="hover:text-amber-600 transition-colors">Saisie des notes par classe</a></li>
                    <li><a href="#" class="hover:text-amber-600 transition-colors">Calcul des moyennes et bulletins</a></li>
                    <li><a href="#" class="hover:text-amber-600 transition-colors">Gestion des emplois du temps</a></li>
                </ul>
            </div>

            <!-- Configuration -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-cog text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Configuration Système</h3>
                <ul class="space-y-2 text-gray-600">
                    <li><a href="#" class="hover:text-purple-600 transition-colors">Paramétrer les rôles et permissions</a></li>
                    <li><a href="#" class="hover:text-purple-600 transition-colors">Ajouter des utilisateurs (Admin, Parents)</a></li>
                    <li><a href="#" class="hover:text-purple-600 transition-colors">Configuration des tranches IRSA</a></li>
                </ul>
            </div>

            <!-- API Documentation -->
            <a href="<?= url('systeme/api-docs') ?>" target="_blank" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow group">
                <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-slate-600 group-hover:text-white transition-all">
                    <i class="fas fa-code text-xl"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Documentation API</h3>
                <ul class="space-y-2 text-gray-600">
                    <li class="hover:text-slate-900 transition-colors font-medium flex items-center gap-2">
                        Consulter les endpoints <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                    </li>
                    <li class="text-xs text-gray-400 italic">Endpoints REST, JSON, Exports CSV</li>
                </ul>
            </a>
        </div>

        <!-- Section Support -->
        <div class="bg-gray-50 rounded-2xl p-8 border border-dashed border-gray-300 text-center">
            <h2 class="text-xl font-bold mb-2">Vous n'avez pas trouvé votre réponse ?</h2>
            <p class="text-gray-600 mb-6">Notre équipe technique est à votre disposition pour toute demande spécifique.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="mailto:j.hajahyacinthe@gmail.com" class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-200 hover:border-blue-400 transition-colors flex items-center">
                    <i class="fas fa-envelope text-blue-500 mr-2"></i> j.hajahyacinthe@gmail.com
                </a>
                <a href="tel:+261340000000" class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-200 hover:border-green-400 transition-colors flex items-center">
                    <i class="fas fa-phone text-green-500 mr-2"></i> +261 38 46 077 70 / +261 32 17 046 45
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Recherche dynamique
    document.getElementById('helpSearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        
        // Filtrer les cartes
        const cards = document.querySelectorAll('#helpCardsGrid > a, #helpCardsGrid > div');
        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(query) ? 'block' : 'none';
        });
    });
</script>


