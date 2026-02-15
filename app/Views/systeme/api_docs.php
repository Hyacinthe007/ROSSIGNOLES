
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Documentation API</h1>
                <p class="text-gray-600">Interface de programmation pour l'accès aux données de ROSSIGNOLES.</p>
            </div>
            <a href="<?= url('systeme/aide') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors text-sm font-medium">
                <i class="fas fa-arrow-left"></i> Retour à l'aide
            </a>
        </div>

        <div class="mb-12">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <!-- En-tête de la section API -->
                <div class="bg-gradient-to-r from-slate-800 to-slate-700 p-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-plug text-xl text-cyan-300"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">Endpoints REST (JSON)</h2>
                            <p class="text-slate-300 text-sm mt-1">Accédez aux données de manière programmatique</p>
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
                    <div class="bg-slate-50 rounded-xl p-4 mb-6 flex items-center justify-between gap-3 group">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Base URL</span>
                            <code id="apiUrlCode" class="bg-white px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-mono text-slate-700 select-all">
                                http://localhost/ROSSIGNOLES/api/
                            </code>
                        </div>
                        <button onclick="copyApiUrl()" class="text-slate-400 hover:text-blue-600 transition-colors p-2" title="Copier l'URL">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>

                    <!-- Recherche Endpoints -->
                    <div class="relative mb-6">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="apiSearchInput" placeholder="Filtrer un endpoint (ex: finance, eleves...)" 
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                    </div>

                    <!-- Tableau des endpoints -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="apiEndpointsTable">
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

                                <!-- Élèves par classe -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <a href="<?= url('classes/eleves') ?>" class="text-blue-600 hover:underline text-xs">/classes/eleves</a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded mr-1">GET</span>
                                        <code class="text-xs font-mono text-slate-700">/api/classes/eleves</code>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 text-xs">—</td>
                                    <td class="py-3 px-4 text-slate-600">Liste exhaustive des élèves groupés par classe avec effectifs</td>
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
        <div class="bg-slate-50 rounded-2xl p-8 border border-dashed border-slate-300 text-center">
            <h2 class="text-xl font-bold mb-2">Besoin d'aide pour l'intégration ?</h2>
            <p class="text-gray-600 mb-6">Notre équipe technique peut vous accompagner pour des besoins spécifiques.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="mailto:dev@rossignole.mg" class="bg-white px-6 py-3 rounded-xl shadow-sm border border-slate-200 hover:border-slate-400 transition-colors flex items-center">
                    <i class="fas fa-terminal text-slate-500 mr-2"></i> dev@rossignole.mg
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Recherche dynamique pour les endpoints
    document.getElementById('apiSearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#apiEndpointsTable tbody tr');
        
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? 'table-row' : 'none';
        });
    });

    // Fonction de copie de l'URL
    function copyApiUrl() {
        const url = document.getElementById('apiUrlCode').innerText.trim();
        navigator.clipboard.writeText(url).then(() => {
            const btn = event.currentTarget;
            const icon = btn.querySelector('i');
            icon.className = 'fas fa-check text-green-500';
            setTimeout(() => {
                icon.className = 'fas fa-copy';
            }, 2000);
        });
    }
</script>
