<div class="p-4 md:p-8">
    <!-- En-tête de la page -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white mr-4 shadow-lg shadow-blue-200">
                    <i class="fas fa-wallet"></i>
                </div>
                Gestion de la Paie
            </h1>
            <p class="text-gray-600">Module de gestion des salaires, cotisations et bulletins de paie</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <form action="<?= url('paie/bulletins/generer') ?>" method="POST" class="inline">
                <?= csrf_field() ?>
                <input type="hidden" name="periode" value="<?= date('Y-m') ?>">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-lg shadow-blue-100 font-semibold">
                    <i class="fas fa-magic"></i>
                    <span>Générer le mois</span>
                </button>
            </form>
            <a href="<?= url('paie/contrats/form') ?>" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-sm font-semibold">
                <i class="fas fa-plus-circle text-blue-600"></i>
                <span>Nouveau contrat</span>
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Période en cours</p>
                    <p class="text-2xl font-bold text-gray-800 uppercase"><?= strftime('%B %Y') ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Dernière mise à jour</p>
                    <p class="text-2xl font-bold text-gray-800">Aujourd'hui</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                    <i class="fas fa-sync text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Conformité légale</p>
                    <p class="text-2xl font-bold text-gray-800">Loi 2026</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                    <i class="fas fa-gavel text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grille des modules -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Configuration -->
        <a href="<?= url('paie/configuration') ?>" class="group">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 h-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 transition-all duration-300 group-hover:scale-150 group-hover:bg-purple-100 opacity-50"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-purple-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-purple-100 group-hover:scale-110 transition-transform">
                        <i class="fas fa-cog text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Configuration</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        Paramétrage des taux CNAPS, OSTIE et FMFP. Gestion des tranches d'imposition IRSA.
                    </p>
                    <div class="flex items-center text-purple-600 font-semibold text-sm">
                        <span>Configurer les taux</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Contrats -->
        <a href="<?= url('paie/contrats') ?>" class="group">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 h-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-16 -mt-16 transition-all duration-300 group-hover:scale-150 group-hover:bg-emerald-100 opacity-50"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-emerald-100 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-signature text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Contrats</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        Gérez les salaires de base du personnel et leurs options de cotisations sociales.
                    </p>
                    <div class="flex items-center text-emerald-600 font-semibold text-sm">
                        <span>Gérer les salaires</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Bulletins -->
        <a href="<?= url('paie/bulletins') ?>" class="group">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 h-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 transition-all duration-300 group-hover:scale-150 group-hover:bg-blue-100 opacity-50"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-blue-100 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-invoice-dollar text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Bulletins</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        Générez et validez les fiches de paie mensuelles. Impression et export PDF.
                    </p>
                    <div class="flex items-center text-blue-600 font-semibold text-sm">
                        <span>Voir les bulletins</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Rapports -->
        <div class="group cursor-not-allowed">
            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-8 h-100 opacity-70 relative overflow-hidden">
                <div class="relative">
                    <div class="w-14 h-14 bg-gray-400 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-gray-100">
                        <i class="fas fa-chart-bar text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Rapports</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        Analyses et statistiques sur la masse salariale. (Prochainement disponible)
                    </p>
                    <div class="flex items-center text-gray-400 font-semibold text-sm">
                        <span>Bientôt disponible</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Information & Simulateur -->
    <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100">
            <div class="flex flex-wrap" id="tabs-paie">
                <button onclick="switchTab('tab-irsa')" id="btn-tab-irsa" class="tab-btn active px-8 py-5 text-sm font-bold border-b-2 transition-all border-blue-600 text-blue-600">
                    CALCUL IRSA 2026
                </button>
                <button onclick="switchTab('tab-cotis')" id="btn-tab-cotis" class="tab-btn px-8 py-5 text-sm font-bold border-b-2 transition-all border-transparent text-gray-500 hover:text-gray-700">
                    COTISATIONS SOCIALES
                </button>
                <button onclick="switchTab('tab-sim')" id="btn-tab-sim" class="tab-btn px-8 py-5 text-sm font-bold border-b-2 transition-all border-transparent text-gray-500 hover:text-gray-700">
                    SIMULATEUR DE SALAIRE
                </button>
            </div>
        </div>

        <div class="p-8">
            <!-- Tab IRSA -->
            <div id="tab-irsa" class="tab-content block animate-fade-in">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div>
                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            Barème Progressif IRSA
                        </h4>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            L'impôt est calculé sur le revenu brut après déduction des cotisations sociales (le cas échéant). Voici la grille officielle pour l'année 2026 :
                        </p>
                        <div class="overflow-hidden rounded-xl border border-gray-100">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-600 font-bold">
                                    <tr>
                                        <th class="px-6 py-4">Tranche (MGA)</th>
                                        <th class="px-6 py-4">Taux</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr><td class="px-6 py-4">0 à 350 000</td><td class="px-6 py-4 font-bold text-gray-400">0%</td></tr>
                                    <tr><td class="px-6 py-4 text-gray-700">350 001 à 400 000</td><td class="px-6 py-4 font-bold text-blue-500">5%</td></tr>
                                    <tr><td class="px-6 py-4 text-gray-700">400 001 à 500 000</td><td class="px-6 py-4 font-bold text-indigo-500">10%</td></tr>
                                    <tr><td class="px-6 py-4 text-gray-700">500 001 à 600 000</td><td class="px-6 py-4 font-bold text-orange-500">15%</td></tr>
                                    <tr><td class="px-6 py-4 text-gray-700">Plus de 600 000</td><td class="px-6 py-4 font-bold text-red-500">20%</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-2xl p-8">
                        <h4 class="text-lg font-bold text-blue-900 mb-4">Réduction pour charge de famille</h4>
                        <div class="space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-600 flex-shrink-0 shadow-sm border border-blue-100">
                                    <i class="fas fa-child"></i>
                                </div>
                                <p class="text-blue-800 text-sm leading-relaxed">
                                    Une réduction de <strong>2 000 Ar</strong> est appliquée sur l'impôt brut par enfant à charge.
                                </p>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-600 flex-shrink-0 shadow-sm border border-blue-100">
                                    <i class="fas fa-hand-holding-dollar"></i>
                                </div>
                                <p class="text-blue-800 text-sm leading-relaxed">
                                    Le montant minimum de l'IRSA à payer par employé est fixé à <strong>3 000 Ar</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Cotisations -->
            <div id="tab-cotis" class="tab-content hidden animate-fade-in text-center py-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <!-- CNAPS -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                        <h5 class="text-lg font-bold text-gray-800 mb-1">CNAPS</h5>
                        <p class="text-xs text-gray-400 mb-4">Retraite & Prévoyance</p>
                        <div class="space-y-2">
                            <div class="flex justify-between py-2 border-b border-gray-50">
                                <span class="text-gray-500 text-sm">Salarial</span>
                                <span class="font-bold text-blue-600 underline decoration-2 underline-offset-4">1%</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-500 text-sm">Patronal</span>
                                <span class="font-bold text-gray-800">13%</span>
                            </div>
                        </div>
                    </div>

                    <!-- OSTIE -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-heartbeat text-2xl"></i>
                        </div>
                        <h5 class="text-lg font-bold text-gray-800 mb-1">OSTIE / SMIE</h5>
                        <p class="text-xs text-gray-400 mb-4">Santé au travail</p>
                        <div class="space-y-2">
                            <div class="flex justify-between py-2 border-b border-gray-50">
                                <span class="text-gray-500 text-sm">Salarial</span>
                                <span class="font-bold text-emerald-600 underline decoration-2 underline-offset-4">1%</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-500 text-sm">Patronal</span>
                                <span class="font-bold text-gray-800">5%</span>
                            </div>
                        </div>
                    </div>

                    <!-- FMFP -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                        <h5 class="text-lg font-bold text-gray-800 mb-1">FMFP</h5>
                        <p class="text-xs text-gray-400 mb-4">Formation professionnelle</p>
                        <div class="space-y-2">
                            <div class="flex justify-between py-2 border-b border-gray-50">
                                <span class="text-gray-500 text-sm">Salarial</span>
                                <span class="font-bold text-gray-400">0%</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-500 text-sm">Patronal</span>
                                <span class="font-bold text-gray-800">1%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Simulateur -->
            <div id="tab-sim" class="tab-content hidden animate-fade-in">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100">
                        <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-vial text-blue-600 mr-3"></i>
                            Simulation de salaire Net
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Salaire Brut (Ar)</label>
                                <input type="number" id="sim-brut" oninput="runSim()" value="500000" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-blue-700">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Enfants à charge</label>
                                <input type="number" id="sim-enfants" oninput="runSim()" value="0" min="0" max="20" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Type de contrat</label>
                                <select id="sim-type" onchange="runSim()" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-500">
                                    <option value="cdi">CDI / CDD (Soumis)</option>
                                    <option value="non-soumis">Stagiaire / Autre (Non-soumis)</option>
                                </select>
                            </div>
                        </div>

                        <div id="sim-res" class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                            <!-- JS Injection -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>

<script>
function switchTab(tabId) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('block'));
    
    // Show active tab
    const activeTab = document.getElementById(tabId);
    activeTab.classList.remove('hidden');
    activeTab.classList.add('block');
    
    // Update buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeBtn = document.getElementById('btn-' + tabId);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-blue-600', 'text-blue-600');
}

function runSim() {
    const brut = parseFloat(document.getElementById('sim-brut').value) || 0;
    const enfants = parseInt(document.getElementById('sim-enfants').value) || 0;
    const type = document.getElementById('sim-type').value;
    
    let cnaps = 0;
    let ostie = 0;
    
    if (type === 'cdi') {
        cnaps = brut * 0.01;
        ostie = brut * 0.01;
    }
    
    const baseIRSA = brut - cnaps - ostie;
    let irsaBrut = 0;
    
    // Calcul progressif
    if (baseIRSA > 350000) {
        let taxable = baseIRSA;
        
        // 0-350k : 0%
        // 350k-400k : 5%
        if (taxable > 350000) {
            let part = Math.min(taxable - 350000, 50000);
            irsaBrut += part * 0.05;
        }
        // 400k-500k : 10%
        if (taxable > 400000) {
            let part = Math.min(taxable - 400000, 100000);
            irsaBrut += part * 0.10;
        }
        // 500k-600k : 15%
        if (taxable > 500000) {
            let part = Math.min(taxable - 500000, 100000);
            irsaBrut += part * 0.15;
        }
        // > 600k : 20%
        if (taxable > 600000) {
            let part = taxable - 600000;
            irsaBrut += part * 0.20;
        }
    }
    
    const reduction = enfants * 2000;
    const irsaNet = Math.max(3000, irsaBrut - reduction);
    const net = brut - cnaps - ostie - irsaNet;
    
    const res = document.getElementById('sim-res');
    res.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center md:text-left">
                <p class="text-xs text-gray-400 uppercase font-black tracking-widest mb-1">Cotisations</p>
                <p class="text-xl font-bold text-gray-800">-${Math.round(cnaps + ostie).toLocaleString()} <span class="text-sm font-normal text-gray-400">Ar</span></p>
            </div>
            <div class="text-center md:text-left">
                <p class="text-xs text-gray-400 uppercase font-black tracking-widest mb-1">IRSA (Net)</p>
                <p class="text-xl font-bold text-gray-800">-${Math.round(irsaNet).toLocaleString()} <span class="text-sm font-normal text-gray-400">Ar</span></p>
            </div>
            <div class="bg-blue-600 rounded-xl p-4 text-center md:text-right text-white shadow-lg shadow-blue-100">
                <p class="text-xs uppercase font-black tracking-widest mb-1 opacity-80">Salaire Net estimé</p>
                <p class="text-2xl font-black">${Math.round(net).toLocaleString()} Ar</p>
            </div>
        </div>
    `;
}

// Init sim
document.addEventListener('DOMContentLoaded', runSim);
</script>
