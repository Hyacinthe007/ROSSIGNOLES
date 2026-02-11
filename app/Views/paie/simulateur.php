<div class="p-4 md:p-8">
    <!-- En-tête de la page -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white mr-4 shadow-lg shadow-blue-200">
                    <i class="fas fa-vial"></i>
                </div>
                Simulateur de Salaire Net
            </h1>
            <p class="text-gray-600">Estimez le salaire net en fonction du brut, des cotisations et de l'IRSA</p>
        </div>
        <a href="<?= url('paie') ?>" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-sm font-semibold">
            <i class="fas fa-arrow-left text-blue-600"></i>
            <span>Retour</span>
        </a>
    </div>

    <!-- Simulateur principal -->
    <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden p-8">
            <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100">
                <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-vial text-blue-600 mr-3"></i>
                    Simulation de salaire Net
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Salaire Brut (Ar)</label>
                        <input type="number" id="sim-brut" oninput="runSim()" value="500000" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-blue-700">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Enfants à charge</label>
                        <input type="number" id="sim-enfants" oninput="runSim()" value="0" min="0" max="20" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Type de contrat</label>
                        <select id="sim-type" onchange="runSim()" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
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
