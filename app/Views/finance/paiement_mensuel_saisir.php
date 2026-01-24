<?php
/**
 * Vue : Formulaire de saisie de paiement pour un élève
 */
require_once __DIR__ . '/../layout/header.php';

$totalSelectionne = 0;
?>

<div class="p-4 md:p-8 space-y-6 max-w-7xl mx-auto">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative animate-fade-in" role="alert">
            <span class="block sm:inline"><?= $_SESSION['success'] ?></span>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative animate-fade-in" role="alert">
            <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <!-- En-tête avec info élève -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg p-6 text-white text-center md:text-left mt-6">
        <div class="flex flex-col md:flex-row items-center md:items-start md:justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border-4 border-white/30 shadow-inner">
                    <span class="text-3xl font-bold text-white">
                        <?= strtoupper(substr($eleve['nom'], 0, 1) . substr($eleve['prenom'], 0, 1)) ?>
                    </span>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">
                        <?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?>
                    </h1>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-sm md:text-base opacity-90">
                        <span class="bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm shadow-sm flex items-center gap-2">
                            <i class="fas fa-id-card"></i> <?= e($eleve['matricule']) ?>
                        </span>
                        <span class="bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm shadow-sm flex items-center gap-2">
                            <i class="fas fa-school"></i> <?= e($eleve['classe_nom']) ?>
                        </span>
                        <span class="bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm shadow-sm flex items-center gap-2">
                            <i class="fas fa-calendar-alt"></i> <?= e($anneeScolaire['libelle']) ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <a href="<?= url('finance/paiement-mensuel') ?>" class="group bg-white/10 hover:bg-white/20 text-white px-5 py-2.5 rounded-lg border border-white/30 transition-all flex items-center gap-2 backdrop-blur-md">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <form method="POST" action="<?= url('finance/paiement-mensuel/enregistrer') ?>" id="paiementForm" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <?= csrf_field() ?>
        <input type="hidden" name="eleve_id" value="<?= $eleve['id'] ?>">

        <input type="hidden" name="annee_scolaire_id" value="<?= $anneeScolaire['id'] ?>">
        
        <!-- Colonne gauche : Échéances -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-calendar-check text-blue-500"></i>
                            Échéances à payer
                        </h2>
                        <p class="text-sm text-gray-500">Sélectionnez les mois que l'élève souhaite régler.</p>
                    </div>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if (empty($echeances)): ?>
                        <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Aucun échéancier trouvé</h3>
                            <p class="text-gray-500 max-w-sm mb-6">L'échéancier de cet élève n'a pas encore été généré pour cette année scolaire.</p>
                            <a href="<?= url('finance/paiement-mensuel/generer') ?>?eleve_id=<?= $eleve['id'] ?>&annee_scolaire_id=<?= $anneeScolaire['id'] ?>" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
                                <i class="fas fa-magic"></i>
                                Générer l'échéancier maintenant
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($echeances as $echeance): ?>
                            <?php 
                                $isPaid = $echeance['montant_restant'] <= 0;
                                $statusColor = $isPaid ? 'green' : ($echeance['statut'] == 'retard' || $echeance['statut'] == 'retard_grave' || $echeance['statut'] == 'exclusion' ? 'red' : 'blue');
                                $statusBg = $isPaid ? 'bg-green-50' : ($echeance['statut'] == 'retard' || $echeance['statut'] == 'retard_grave' || $echeance['statut'] == 'exclusion' ? 'bg-red-50' : 'bg-white');
                                $borderColor = $isPaid ? 'border-green-100' : ($echeance['statut'] == 'retard' || $echeance['statut'] == 'retard_grave' || $echeance['statut'] == 'exclusion' ? 'border-red-100' : 'border-gray-200');
                            ?>
                            <div class="echeance-card group relative p-4 rounded-xl border-2 transition-all duration-200 cursor-pointer hover:shadow-md <?= $statusBg ?> <?= $borderColor ?> <?= $isPaid ? 'opacity-60 cursor-not-allowed' : 'hover:-translate-y-1 hover:border-blue-300' ?>"
                                 data-echeance-id="<?= $echeance['id'] ?>"
                                 data-montant="<?= $echeance['montant_restant'] ?>"
                                 onclick="<?= $isPaid ? '' : 'toggleEcheance(this)' ?>">
                                
                                <!-- Checkbox visuelle -->
                                <?php if (!$isPaid): ?>
                                    <div class="absolute top-4 right-4 w-6 h-6 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center transition-colors group-hover:border-blue-400 checkbox-indicator">
                                        <i class="fas fa-check text-white text-xs transform scale-0 transition-transform"></i>
                                    </div>
                                    <input type="checkbox" name="echeances[]" value="<?= $echeance['id'] ?>" class="hidden echeance-checkbox">
                                <?php else: ?>
                                    <div class="absolute top-4 right-4 text-green-600">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="flex flex-col justify-between h-full">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="font-bold text-gray-800 text-lg"><?= e($echeance['mois_libelle']) ?></h3>
                                            <?php if ($echeance['jours_retard'] > 0 && !$isPaid): ?>
                                                <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-medium flex items-center gap-1">
                                                    <i class="fas fa-fire"></i> Retard
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="space-y-1 text-sm text-gray-600">
                                            <p class="flex items-center justify-between">
                                                <span><i class="fas fa-hourglass-end w-5 text-gray-400"></i> Date limite</span>
                                                <span class="font-medium"><?= date('d/m/Y', strtotime($echeance['date_limite'])) ?></span>
                                            </p>
                                            <p class="flex items-center justify-between font-mono">
                                                <span><i class="fas fa-coins w-5 text-gray-400"></i> Reste à payer</span>
                                                <span class="<?= $isPaid ? 'text-green-600' : 'text-blue-600' ?> font-bold">
                                                    <?= number_format($echeance['montant_restant'], 0, ',', ' ') ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <?php if ($isPaid): ?>
                                        <div class="mt-3 pt-3 border-t border-green-200 text-xs text-green-800 font-medium flex items-center justify-center bg-green-100 py-1 rounded">
                                            Payé le <?= date('d/m/Y', strtotime($echeance['date_paiement_complet'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Paiement -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Récapitulatif -->
            <div class="bg-white rounded-xl shadow-lg border border-blue-100 sticky top-6">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-blue-500"></i>
                        Récapitulatif
                    </h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Échéances sélectionnées</span>
                            <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded-md" id="nbEcheances">0</span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                            <span class="text-gray-600">Total à payer</span>
                            <span class="font-bold text-2xl text-blue-600" id="montantTotal">0</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant perçu <span class="text-red-500">*</span></label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm"></span>
                                </div>
                                <input type="text" 
                                       name="montant_paye" 
                                       id="montantPaye"
                                       class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-mono text-lg amount-format" 
                                       placeholder="0"
                                       required
                                       oninput="calculerRendu()">
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3 flex justify-between items-center border border-gray-200">
                            <span class="text-sm font-medium text-gray-600">Rendu monnaie</span>
                            <span class="font-bold text-lg text-gray-400 font-mono" id="renduMonnaie">0</span>
                        </div>
                    </div>

                    <div class="pt-2 space-y-3">
                        <button type="button" 
                                class="w-full text-left flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200 text-sm font-medium text-gray-700"
                                onclick="document.getElementById('detailsPaiement').classList.toggle('hidden');">
                            <span><i class="fas fa-sliders-h mr-2"></i> Options de paiement</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        
                        <div id="detailsPaiement" class="hidden space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200 animate-fade-in-down">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Mode de paiement</label>
                                <select name="mode_paiement_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <?php foreach ($modesPaiement as $mode): ?>
                                        <option value="<?= $mode['id'] ?>"><?= e($mode['libelle']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Date</label>
                                <input type="date" name="date_paiement" value="<?= date('Y-m-d') ?>" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Référence</label>
                                <input type="text" name="reference" placeholder="N° reçu, chèque..." class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Note interne</label>
                                <textarea name="remarque" rows="2" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex flex-col gap-3">
                        <button type="submit" id="btnEnregistrer" disabled 
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3.5 px-4 rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2 group">
                            <span>Enregistrer le paiement</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                        
                        <a href="<?= url('finance/paiement-mensuel') ?>" class="w-full text-center text-gray-500 hover:text-gray-700 font-medium py-2 rounded-lg transition-colors text-sm">
                            Annuler l'opération
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.echeance-card.selected {
    border-color: #3b82f6;
    background-color: #eff6ff;
}
.echeance-card.selected .checkbox-indicator {
    background-color: #3b82f6;
    border-color: #3b82f6;
}
.echeance-card.selected .checkbox-indicator i {
    transform: scale(1);
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down {
    animation: fadeInDown 0.3s ease-out;
}
</style>

<script>
let totalSelectionne = 0;
let nbEcheancesSelectionnees = 0;

function toggleEcheance(card) {
    if (card.classList.contains('cursor-not-allowed')) return;
    
    const checkbox = card.querySelector('.echeance-checkbox');
    const montant = parseFloat(card.dataset.montant);
    
    if (card.classList.contains('selected')) {
        card.classList.remove('selected');
        checkbox.checked = false;
        totalSelectionne -= montant;
        nbEcheancesSelectionnees--;
    } else {
        card.classList.add('selected');
        checkbox.checked = true;
        totalSelectionne += montant;
        nbEcheancesSelectionnees++;
    }
    
    updateRecapitulatif();
}

function updateRecapitulatif() {
    document.getElementById('nbEcheances').textContent = nbEcheancesSelectionnees;
    document.getElementById('montantTotal').innerHTML = formatMoney(totalSelectionne);
    document.getElementById('montantPaye').value = totalSelectionne > 0 ? totalSelectionne : '';
    
    const btnEnregistrer = document.getElementById('btnEnregistrer');
    if (nbEcheancesSelectionnees > 0) {
        btnEnregistrer.disabled = false;
    } else {
        btnEnregistrer.disabled = true;
    }
    
    calculerRendu();
}

function calculerRendu() {
    const montantPayeInput = document.getElementById('montantPaye');
    const montantPaye = parseFloat(unformatAmount(montantPayeInput.value)) || 0;
    const rendu = montantPaye - totalSelectionne;
    const renduEl = document.getElementById('renduMonnaie');
    
    renduEl.textContent = formatMoney(Math.max(0, rendu));
    
    if (rendu < 0 && nbEcheancesSelectionnees > 0) {
        renduEl.classList.remove('text-green-600', 'text-gray-400');
        renduEl.classList.add('text-red-500');
    } else if (rendu >= 0 && nbEcheancesSelectionnees > 0) {
        renduEl.classList.remove('text-red-500', 'text-gray-400');
        renduEl.classList.add('text-green-600');
    } else {
        renduEl.className = 'font-bold text-lg text-gray-400 font-mono';
    }
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-FR').format(amount);
}

// Validation avant soumission
document.getElementById('paiementForm').addEventListener('submit', function(e) {
    if (nbEcheancesSelectionnees === 0) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins une échéance à payer');
        return false;
    }
    
    const montantPayeInput = document.getElementById('montantPaye');
    const montantPaye = parseFloat(unformatAmount(montantPayeInput.value)) || 0;
    if (montantPaye < totalSelectionne) {
        e.preventDefault();
        alert('Le montant perçu est insuffisant pour couvrir les échéances sélectionnées');
        return false;
    }
    
    if(!confirm('Confirmer l\'enregistrement de ce paiement ?')) {
        e.preventDefault();
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
