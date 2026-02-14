

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
                <input type="text" placeholder="Rechercher un tutoriel..." class="w-full py-4 px-6 rounded-full text-gray-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all text-lg">
                <button class="absolute right-2 top-2 bottom-2 px-6 bg-blue-700 rounded-full hover:bg-blue-800 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
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
        </div>

        <!-- ══════════════════════════════════════════════════════════════════ -->
        <!-- Section Documentation API                                         -->
        <!-- ══════════════════════════════════════════════════════════════════ -->
        <div class="mb-12">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <!-- En-tête de la section API -->
                <div class="bg-gradient-to-r from-slate-800 to-slate-700 p-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-plug text-xl text-cyan-300"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">Documentation API</h2>
                            <p class="text-slate-300 text-sm mt-1">Accédez aux données de ROSSIGNOLES via des endpoints REST (JSON)</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Note d'authentification -->
                    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                        <i class="fas fa-shield-alt text-amber-500 mt-0.5"></i>
                        <div>
                            <p class="font-semibold text-amber-800 text-sm">Authentification requise</p>
                            <p class="text-amber-700 text-sm mt-1">
                                Tous les endpoints nécessitent une session active. Connectez-vous d'abord via 
                                <code class="bg-amber-100 px-1.5 py-0.5 rounded text-xs font-mono">/auth/login</code>, 
                                puis accédez aux endpoints dans le même navigateur. 
                                Une requête non authentifiée recevra une réponse <code class="bg-amber-100 px-1.5 py-0.5 rounded text-xs font-mono">401</code> JSON.
                            </p>
                        </div>
                    </div>

                    <!-- Base URL -->
                    <div class="bg-slate-50 rounded-xl p-4 mb-6 flex items-center gap-3">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Base URL</span>
                        <code class="bg-white px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-mono text-slate-700 select-all">
                            http://localhost/ROSSIGNOLES/api/
                        </code>
                    </div>

                    <!-- Tableau des endpoints -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-slate-200">
                                    <th class="text-left py-3 px-4 font-semibold text-slate-600">Page correspondante</th>
                                    <th class="text-left py-3 px-4 font-semibold text-slate-600">Endpoint API</th>
                                    <th class="text-left py-3 px-4 font-semibold text-slate-600">Paramètres</th>
                                    <th class="text-left py-3 px-4 font-semibold text-slate-600">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <!-- Bulletins -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <a href="<?= url('bulletins/list') ?>" class="text-blue-600 hover:underline text-xs">/bulletins/list</a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/bulletins</code>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 text-xs">—</td>
                                    <td class="py-3 px-4 text-slate-600">Liste des bulletins avec élève, classe, période et année scolaire</td>
                                </tr>

                                <!-- Personnel -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <a href="<?= url('liste-personnel') ?>" class="text-blue-600 hover:underline text-xs">/liste-personnel</a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/personnel</code>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 text-xs">—</td>
                                    <td class="py-3 px-4 text-slate-600">Personnel actif (enseignants + administratifs) avec matricule, fonction et téléphone formaté</td>
                                </tr>

                                <!-- Élèves -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <a href="<?= url('eleves/list') ?>" class="text-blue-600 hover:underline text-xs">/eleves/list</a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/eleves</code>
                                    </td>
                                    <td class="py-3 px-4">
                                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-mono text-slate-600">?search=xxx</code>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">Liste des élèves avec dernière classe et statut financier</td>
                                </tr>

                                <!-- Reçus -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <a href="<?= url('finance/recus') ?>" class="text-blue-600 hover:underline text-xs">/finance/recus</a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/finance/recus</code>
                                    </td>
                                    <td class="py-3 px-4">
                                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-mono text-slate-600">?search=xxx</code>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">Reçus de paiement avec détails élève, facture, classe et mode de paiement</td>
                                </tr>

                                <!-- Échéanciers retard -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <a href="<?= url('finance/echeanciers') ?>" class="text-blue-600 hover:underline text-xs">/finance/echeanciers</a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/finance/echeanciers</code>
                                    </td>
                                    <td class="py-3 px-4">
                                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-mono text-slate-600">?statut=retard</code>
                                        <span class="text-slate-400 text-xs ml-1">(défaut)</span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">Recouvrement — élèves en retard de paiement</td>
                                </tr>

                                <!-- Échéanciers exclusion -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <a href="<?= url('finance/echeanciers') ?>?statut=exclusion" class="text-blue-600 hover:underline text-xs">/finance/echeanciers?statut=exclusion</a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/finance/echeanciers</code>
                                    </td>
                                    <td class="py-3 px-4">
                                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-xs font-mono">?statut=exclusion</code>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">Liste des élèves exclus pour défaut de paiement</td>
                                </tr>

                                <!-- Parents -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <a href="<?= url('parents/list') ?>" class="text-blue-600 hover:underline text-xs">/parents/list</a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/parents</code>
                                    </td>
                                    <td class="py-3 px-4">
                                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-mono text-slate-600">?search=xxx</code>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">Parents avec nombre d'enfants (recherche par nom parent ou enfant)</td>
                                </tr>

                                <!-- Notes -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <a href="<?= url('notes/list') ?>" class="text-blue-600 hover:underline text-xs">/notes/list</a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/notes</code>
                                    </td>
                                    <td class="py-3 px-4">
                                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-mono text-slate-600">?classe_id=X&periode_id=Y</code>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">Évaluations (examens + interrogations) avec classes et périodes disponibles</td>
                                </tr>

                                <!-- Export CSV -->
                                <tr class="hover:bg-slate-50 transition-colors bg-slate-50/50">
                                    <td class="py-3 px-4 text-slate-400 text-xs italic">Export CSV</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/finance/recus/export-excel</code>
                                    </td>
                                    <td class="py-3 px-4">
                                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-mono text-slate-600">?search=xxx</code>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">Téléchargement CSV des reçus (format Excel)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Exemple de réponse JSON -->
                    <div class="mt-8">
                        <h3 class="text-sm font-semibold text-slate-600 mb-3 flex items-center gap-2">
                            <i class="fas fa-code text-cyan-500"></i>
                            Exemple de réponse JSON
                        </h3>
                        <div class="bg-slate-900 rounded-xl p-5 overflow-x-auto">
                            <pre class="text-sm text-slate-200 font-mono leading-relaxed"><code>{
    <span class="text-emerald-400">"status"</span>: <span class="text-amber-300">"success"</span>,
    <span class="text-emerald-400">"count"</span>: <span class="text-cyan-300">42</span>,
    <span class="text-emerald-400">"data"</span>: [
        {
            <span class="text-emerald-400">"id"</span>: <span class="text-cyan-300">1</span>,
            <span class="text-emerald-400">"matricule"</span>: <span class="text-amber-300">"ELV-2025-001"</span>,
            <span class="text-emerald-400">"nom"</span>: <span class="text-amber-300">"RAKOTO"</span>,
            <span class="text-emerald-400">"prenom"</span>: <span class="text-amber-300">"Jean"</span>,
            <span class="text-emerald-400">"derniere_classe"</span>: <span class="text-amber-300">"6ème A"</span>,
            <span class="text-emerald-400">"statut_financier"</span>: <span class="text-cyan-300">null</span>
        },
        <span class="text-slate-500">// ...</span>
    ]
}</code></pre>
                        </div>
                    </div>

                    <!-- Réponse erreur 401 -->
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-slate-600 mb-3 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                            Réponse en cas d'erreur (non authentifié)
                        </h3>
                        <div class="bg-slate-900 rounded-xl p-5 overflow-x-auto">
                            <pre class="text-sm text-slate-200 font-mono leading-relaxed"><code><span class="text-red-400">// HTTP 401 Unauthorized</span>
{
    <span class="text-emerald-400">"status"</span>: <span class="text-red-400">"error"</span>,
    <span class="text-emerald-400">"message"</span>: <span class="text-amber-300">"Non autorisé. Veuillez vous connecter..."</span>
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Support -->
        <div class="bg-gray-50 rounded-2xl p-8 border border-dashed border-gray-300 text-center">
            <h2 class="text-xl font-bold mb-2">Vous n'avez pas trouvé votre réponse ?</h2>
            <p class="text-gray-600 mb-6">Notre équipe technique est à votre disposition pour toute demande spécifique.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="mailto:support@rossignole.mg" class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-200 hover:border-blue-400 transition-colors flex items-center">
                    <i class="fas fa-envelope text-blue-500 mr-2"></i> support@rossignole.mg
                </a>
                <a href="tel:+261340000000" class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-200 hover:border-green-400 transition-colors flex items-center">
                    <i class="fas fa-phone text-green-500 mr-2"></i> +261 34 00 000 00
                </a>
            </div>
        </div>
    </div>
</div>


