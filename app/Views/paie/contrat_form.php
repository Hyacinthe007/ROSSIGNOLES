<div class="p-4 md:p-8">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center text-white mr-4 shadow-lg shadow-emerald-100">
                    <i class="fas <?= isset($contrat) ? 'fa-edit' : 'fa-plus-circle' ?>"></i>
                </div>
                <?= isset($contrat) ? 'Modifier le Contrat' : 'Nouveau Contrat de Paie' ?>
            </h1>
            <p class="text-gray-600">Définition des conditions salariales et paramètres sociaux</p>
        </div>
        <a href="<?= url('paie/contrats') ?>" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-5 py-3 rounded-xl transition flex items-center gap-2 shadow-sm font-semibold">
            <i class="fas fa-arrow-left text-emerald-600"></i>
            <span>Retour aux contrats</span>
        </a>
    </div>

    <form action="<?= url('paie/contrats/save') ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <?= csrf_field() ?>
        <?php if (isset($contrat['id'])): ?>
            <input type="hidden" name="id" value="<?= $contrat['id'] ?>">
        <?php endif; ?>

        <div class="lg:col-span-2 space-y-6">
            <!-- Section Personnel -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h5 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-user-tie text-emerald-600"></i>
                        Sélection du Personnel
                    </h5>
                </div>
                <div class="p-8">
                    <?php if (isset($contrat)): ?>
                        <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 flex items-center gap-5">
                            <div class="w-16 h-16 bg-emerald-600 rounded-full flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-emerald-100 border-4 border-white">
                                <?= strtoupper(substr($contrat['nom'], 0, 1)) ?>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-800 tracking-tight"><?= htmlspecialchars($contrat['nom'] . ' ' . $contrat['prenom']) ?></h4>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="px-2 py-0.5 bg-white text-emerald-700 text-[10px] font-black uppercase rounded border border-emerald-200"><?= htmlspecialchars($contrat['matricule'] ?? 'N/A') ?></span>
                                    <span class="px-2 py-0.5 bg-white text-gray-500 text-[10px] font-black uppercase rounded border border-gray-200"><?= ucfirst($contrat['type_personnel'] ?? 'Personnel') ?></span>
                                </div>
                            </div>
                            <input type="hidden" name="personnel_id" value="<?= $contrat['personnel_id'] ?>">
                        </div>
                    <?php else: ?>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Employé / Membre du Personnel</label>
                            <select name="personnel_id" class="w-full px-5 py-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold text-gray-800 appearance-none bg-no-repeat bg-[right_1.5rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%22%3E%3C/path%3E%3C/svg%3E')" required>
                                <option value="">Choisir un personnel...</option>
                                <?php foreach ($personnels_sans_contrat as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= (isset($_GET['personnel_id']) && $_GET['personnel_id'] == $p['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?> (<?= ucfirst($p['type_personnel']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="mt-3 text-[10px] text-gray-400 font-bold uppercase tracking-tighter italic">Seuls les membres sans contrat actif sont listés.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section Paramètres -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h5 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-money-check-alt text-emerald-600"></i>
                        Conditions Salariales
                    </h5>
                </div>
                <div class="p-8 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Salaire Brut de Base</label>
                            <div class="relative">
                                <input type="number" name="salaire_brut_base" class="w-full pl-6 pr-16 py-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-black text-2xl text-emerald-700" 
                                       value="<?= $contrat['salaire_brut_base'] ?? '' ?>" step="1000" min="0" required placeholder="0">
                                <span class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">MGA</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Type de Contrat</label>
                            <select name="type_contrat" class="w-full px-5 py-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold text-gray-800 appearance-none bg-no-repeat bg-[right_1.5rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%22%3E%3C/path%3E%3C/svg%3E')" required>
                                <option value="cdi" <?= (isset($contrat['type_contrat']) && $contrat['type_contrat'] == 'cdi') ? 'selected' : '' ?>>CDI (Indeterminé)</option>
                                <option value="cdd" <?= (isset($contrat['type_contrat']) && $contrat['type_contrat'] == 'cdd') ? 'selected' : '' ?>>CDD (Determiné)</option>
                                <option value="stagiaire" <?= (isset($contrat['type_contrat']) && $contrat['type_contrat'] == 'stagiaire') ? 'selected' : '' ?>>Stagiaire</option>
                                <option value="interimaire" <?= (isset($contrat['type_contrat']) && $contrat['type_contrat'] == 'interimaire') ? 'selected' : '' ?>>Intérimaire</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Enfants à charge</label>
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 text-xl border border-gray-100">
                                    <i class="fas fa-child"></i>
                                </div>
                                <input type="number" name="nb_enfants" class="w-full px-5 py-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold text-gray-800" 
                                       value="<?= $contrat['nb_enfants'] ?? 0 ?>" min="0" max="25">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Date d'effet</label>
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 text-xl border border-gray-100">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <input type="date" name="date_debut" class="w-full px-5 py-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold text-gray-800" 
                                       value="<?= $contrat['date_debut'] ?? date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Toggle Cotisations -->
                    <div class="p-6 bg-emerald-50 rounded-3xl border border-emerald-100 relative overflow-hidden group">
                        <div class="absolute right-0 bottom-0 text-emerald-100 -mr-8 -mb-8 scale-150 transform group-hover:scale-[1.7] transition-transform">
                            <i class="fas fa-shield-alt text-8xl"></i>
                        </div>
                        <div class="relative flex items-start gap-4">
                            <label class="relative inline-flex items-center cursor-pointer mt-1">
                                <input type="checkbox" name="soumis_cotisations" id="soumis_cotisations" value="1" 
                                       <?= (!isset($contrat) || (isset($contrat['soumis_cotisations']) && $contrat['soumis_cotisations'])) ? 'checked' : '' ?>
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                            <div>
                                <h6 class="font-black text-emerald-900 uppercase text-xs tracking-widest mb-1">Soumis aux cotisations sociales</h6>
                                <p class="text-xs text-emerald-700 opacity-80 leading-relaxed font-bold">
                                    Active le prélèvement automatique CNAPS (1%) et OSTIE (1%) sur le bulletin.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-5 rounded-2xl font-black text-lg transition-all shadow-xl shadow-emerald-100 flex items-center justify-center gap-3 active:scale-95">
                            <i class="fas fa-save shadow-inner"></i>
                            <span><?= isset($contrat) ? 'Enregistrer les modifications' : 'Créer le contrat de paie' ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Analyse -->
        <div class="space-y-6">
            <div class="bg-indigo-600 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl shadow-indigo-100">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <h5 class="text-lg font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-calculator"></i>
                    Aperçu Direct
                </h5>
                <div id="simulation-box" class="space-y-5">
                    <p class="text-indigo-200 text-sm font-bold italic">Saisissez un montant pour simuler le net...</p>
                </div>
            </div>

            <div class="bg-amber-50 rounded-3xl p-8 border border-amber-100">
                <h6 class="text-amber-900 font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    Rappel Législatif
                </h6>
                <div class="space-y-3 text-xs font-bold text-amber-800 tracking-tight">
                    <div class="flex justify-between items-center border-b border-amber-200/50 pb-2">
                        <span class="opacity-60">CNAPS Salarial</span>
                        <span class="bg-white px-2 py-0.5 rounded text-amber-700">1%</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-amber-200/50 pb-2">
                        <span class="opacity-60">OSTIE Salarial</span>
                        <span class="bg-white px-2 py-0.5 rounded text-amber-700">1%</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-amber-200/50 pb-2">
                        <span class="opacity-60">Réduction / Enfant</span>
                        <span class="bg-white px-2 py-0.5 rounded text-amber-700">2 000 Ar</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="opacity-60">Minimum IRSA</span>
                        <span class="bg-white px-2 py-0.5 rounded text-amber-700">3 000 Ar</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const brutInput = document.querySelector('input[name="salaire_brut_base"]');
    const enfantsInput = document.querySelector('input[name="nb_enfants"]');
    const soumisCheck = document.getElementById('soumis_cotisations');
    const simBox = document.getElementById('simulation-box');

    function simulate() {
        const brut = parseFloat(brutInput.value) || 0;
        const enfants = parseInt(enfantsInput.value) || 0;
        const soumis = soumisCheck.checked;

        if (brut <= 0) {
            simBox.innerHTML = '<p class="text-indigo-200 text-sm font-bold italic">Saisissez un montant pour simuler le net...</p>';
            return;
        }

        let cnaps = soumis ? brut * 0.01 : 0;
        let ostie = soumis ? brut * 0.01 : 0;
        let baseIRSA = brut - cnaps - ostie;

        // Tranches IRSA simplifiées (Barème 2026)
        let irsa = 0;
        if (baseIRSA > 350000) {
            let taxable = baseIRSA;
            if (taxable > 350000) irsa += Math.min(part = taxable - 350000, 50000) * 0.05;
            if (taxable > 400000) irsa += Math.min(part = taxable - 400000, 100000) * 0.10;
            if (taxable > 500000) irsa += Math.min(part = taxable - 500000, 100000) * 0.15;
            if (taxable > 600000) irsa += (taxable - 600000) * 0.20;
        }
        
        irsa = Math.max(3000, irsa - (enfants * 2000));
        let net = brut - cnaps - ostie - irsa;

        simBox.innerHTML = `
            <div class="space-y-4">
                <div class="flex justify-between text-xs font-black uppercase tracking-widest text-indigo-200">
                    <span>Détail estimé :</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="opacity-70 italic">Retenues Sociales</span>
                        <span class="font-black">-${Math.round(cnaps + ostie).toLocaleString()} Ar</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="opacity-70 italic">Impôt IRSA Net</span>
                        <span class="font-black">-${Math.round(irsa).toLocaleString()} Ar</span>
                    </div>
                </div>
                <div class="pt-4 border-t border-indigo-400/30">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300 mb-1">Net à percevoir</p>
                    <p class="text-4xl font-black text-emerald-300 uppercase leading-none">${Math.round(net).toLocaleString()} Ar</p>
                </div>
            </div>
        `;
    }

    brutInput.addEventListener('input', simulate);
    enfantsInput.addEventListener('input', simulate);
    soumisCheck.addEventListener('change', simulate);
    
    // Initial sync
    simulate();
});
</script>
