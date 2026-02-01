<div class="p-4 md:p-8">
    <!-- En-tête simplifié -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-file-invoice-dollar text-green-600 mr-2"></i>
                Paiement & Validation
            </h1>
            <p class="text-gray-600">Étape 6 sur 7 : Règlement des frais</p>
        </div>
    </div>

    <div class="mb-8">
        <?php 
        $stepNames = [1 => 'Type', 2 => 'Élève', 3 => 'Classe', 4 => 'Documents', 5 => 'Articles', 6 => 'Paiement', 7 => 'Confirmation'];
        ?>
        <div class="flex items-center justify-between mb-2">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?> text-center">
                    <span class="text-[10px] md:text-xs font-semibold <?= $i <= 6 ? 'text-blue-600' : 'text-gray-400' ?>">
                        Étape <?= $i ?>: <?= $stepNames[$i] ?>
                    </span>
                </div>
            <?php endfor; ?>
        </div>
        <div class="flex items-center justify-between">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?>">
                    <div class="h-2 <?= $i <= 6 ? 'bg-blue-600' : 'bg-gray-200' ?> rounded"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-4xl mx-auto">
        <form method="POST" id="inscriptionForm" action="<?= url('inscriptions/nouveau?etape=6') ?>">
            <?= csrf_field() ?>
            
            <!-- Informations clés résumées -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm border border-gray-100">
                <div>
                    <span class="text-gray-500 block">Classe demandée</span>
                    <span class="font-bold text-gray-800"><?= e($classe['nom']) ?></span>
                </div>
                <div>
                    <span class="text-gray-500 block">Année Scolaire</span>
                    <span class="font-bold text-gray-800"><?= e($tarifDroit['annee_scolaire']) ?></span>
                </div>
                <div>
                    <span class="text-gray-500 block">Date</span>
                    <span class="font-bold text-gray-800"><?= date('d/m/Y') ?></span>
                </div>
            </div>

            <!-- Option de paiement Écolage avec sélection personnalisée -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Option de paiement Écolage</h3>
                <div class="flex flex-wrap items-center gap-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <label class="text-gray-700 font-medium">Nombre de mois à payer :</label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="decrementMois()" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-xl transition flex items-center justify-center">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="nombre_mois" name="nombre_mois" min="1" max="9" value="<?= $savedFraisData['nombre_mois'] ?? 1 ?>" 
                               class="w-20 px-3 py-2 text-center border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-lg"
                               onchange="updatePaymentOption(this.value)" readonly>
                        <button type="button" onclick="incrementMois()" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-xl transition flex items-center justify-center">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <span class="text-gray-500 text-sm">mois (1 à 9)</span>
                    <span class="ml-auto text-blue-800 font-bold text-lg" id="montant_ecolage_preview"><?= number_format($tarifEcolage['montant'], 0, ',', ' ') ?> Ar</span>
                </div>
            </div>

            <!-- Section Règlement -->
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 mb-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-wallet text-gray-600 mr-2"></i>
                    Règlement
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Mode de paiement -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                            Mode de paiement *
                        </label>
                        <select name="mode_paiement" id="mode_paiement" required onchange="toggleReferenceRequired()" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                            <?php foreach ($modesPaiement as $mode): ?>
                                <?php 
                                    // Détection robuste du mode "Espèces" (avec ou sans 's', majuscules/minuscules)
                                    $isEspeces = (stripos($mode['libelle'], 'espèce') !== false || stripos($mode['libelle'], 'espece') !== false);
                                ?>
                                <option value="<?= $mode['id'] ?>" <?= $isEspeces ? 'selected' : '' ?>>
                                    <?= e($mode['libelle']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Référence -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1" id="reference_label">
                            Référence
                        </label>
                        <input type="text" name="reference_externe" id="reference_externe" placeholder="" 
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" disabled>
                    </div>
                </div>
            </div>

            <!-- Détails à payer -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails du paiement</h3>
                <table class="w-full">
                    <tbody class="text-sm" id="details_paiement_tbody">
                        <tr>
                            <td class="py-2 text-gray-600">Droit d'inscription</td>
                            <td class="py-2 text-right font-medium text-gray-800"><?= number_format($tarifDroit['montant'], 0, ',', ' ') ?> Ar</td>
                        </tr>
                        
                        <!-- Articles optionnels sélectionnés -->
                        <?php if (!empty($articlesChoisis)): ?>
                            <tr class="border-t border-gray-50">
                                <td colspan="2" class="py-2 text-[10px] font-bold text-blue-600 uppercase tracking-widest">Articles Optionnels</td>
                            </tr>
                            <?php foreach ($articlesChoisis as $article): ?>
                                <tr>
                                    <td class="py-2 pl-4 text-gray-600 ">
                                        <i class="fas fa-shopping-basket text-blue-400 mr-2 text-[10px]"></i>
                                        <?= e($article['libelle']) ?>
                                    </td>
                                    <td class="py-2 text-right font-medium text-blue-700"><?= number_format($article['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Les lignes d'écolage seront générées dynamiquement par JavaScript -->
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-dashed border-gray-300">
                            <td class="py-3 font-bold text-gray-800 text-lg">TOTAL À PAYER</td>
                            <td class="py-3 text-right font-bold text-green-700 text-xl" id="total_a_payer_display">
                                <?= number_format($tarifDroit['montant'] + $tarifEcolage['montant'] + ($montantTotalArticles ?? 0), 0, ',', ' ') ?> Ar
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Champs cachés pour les montants calculés -->
            <input type="hidden" name="frais_inscription_montant" value="<?= $tarifDroit['montant'] ?>">
            <input type="hidden" name="premier_mois_ecolage_montant" id="input_ecolage_montant" value="<?= $tarifEcolage['montant'] ?>">
            <input type="hidden" name="montant_articles_total" id="montant_articles_total" value="<?= $montantTotalArticles ?? 0 ?>">
            
            <!-- Champs cachés pour les paiements (auto-remplis) -->
            <input type="hidden" name="paiement_droit_inscription" id="paiement_droit_inscription" value="<?= $tarifDroit['montant'] ?>">
            <input type="hidden" name="paiement_premier_mois" id="paiement_premier_mois" value="<?= $tarifEcolage['montant'] ?>">
            <input type="hidden" name="montant" id="total_montant_paye" value="0">

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 mt-6 border-t">
                
                <a href="<?= url('inscriptions/nouveau?etape=5') ?>" 
                   class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition text-center">
                   <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
                
                <button type="submit" onclick="return confirm('Confirmez-vous le paiement et la validation de l\'inscription ?');"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center justify-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Valider l'inscription
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const tarifEcolageBase = <?= $tarifEcolage['montant'] ?>;
const tarifDroit = <?= $tarifDroit['montant'] ?>;
const montantArticles = <?= $montantTotalArticles ?? 0 ?>;
const moisDebut = <?= $tarifEcolage['mois_debut'] ?>; // Mois de début de l'année scolaire (1-12)

// Articles optionnels sélectionnés
const articlesData = <?= json_encode(array_map(function($article) {
    return [
        'id' => $article['id'],
        'libelle' => $article['libelle'],
        'prix' => $article['prix_unitaire']
    ];
}, $articlesChoisis)) ?>;


// Noms des mois
const nomsMois = [
    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];

function getNomMois(moisIndex) {
    // moisIndex est 1-12
    return nomsMois[moisIndex - 1] || 'Mois ' + moisIndex;
}

function updatePaymentOption(months) {
    months = parseInt(months) || 1;
    if (months < 1) months = 1;
    if (months > 9) months = 9;
    
    // Mettre à jour l'input si la valeur a été corrigée
    document.getElementById('nombre_mois').value = months;
    
    // Calculs
    const montantEcolage = tarifEcolageBase * months;
    const total = tarifDroit + montantEcolage + montantArticles;

    // Générer les lignes d'écolage
    const tbody = document.getElementById('details_paiement_tbody');
    
    // Garder seulement la ligne "Droit d'inscription" et "Articles"
    let baseHtml = `
        <tr>
            <td class="py-2 text-gray-600">Droit d'inscription</td>
            <td class="py-2 text-right font-medium text-gray-800">${new Intl.NumberFormat('fr-FR').format(tarifDroit)} Ar</td>
        </tr>
    `;

    // Ajouter les articles optionnels s'il y en a
    if (articlesData && articlesData.length > 0) {
        baseHtml += `
            <tr class="border-t border-gray-50">
                <td colspan="2" class="py-2 text-[10px] font-bold text-blue-600 uppercase tracking-widest">Articles Optionnels</td>
            </tr>
        `;
        
        articlesData.forEach(article => {
            baseHtml += `
                <tr>
                    <td class="py-2 pl-4 text-gray-600 ">
                        <i class="fas fa-shopping-basket text-blue-400 mr-2 text-[10px]"></i>
                        ${article.libelle}
                    </td>
                    <td class="py-2 text-right font-medium text-blue-700">${new Intl.NumberFormat('fr-FR').format(article.prix)} Ar</td>
                </tr>
            `;
        });
    }

    tbody.innerHTML = baseHtml;
    
    // Ajouter une ligne pour chaque mois d'écolage
    const currentYear = new Date().getFullYear();
    for (let i = 0; i < months; i++) {
        let moisActuel = moisDebut + i;
        let anneeAffichage = currentYear;
        
        // Gérer le passage à l'année suivante
        if (moisActuel > 12) {
            moisActuel = moisActuel - 12;
            anneeAffichage = currentYear + 1;
        }
        
        const nomMois = getNomMois(moisActuel);
        const tr = document.createElement('tr');
        tr.className = 'border-t border-gray-50';
        tr.innerHTML = `
            <td class="py-2 text-gray-600">
                Écolage ${nomMois} ${anneeAffichage}
            </td>
            <td class="py-2 text-right font-medium text-gray-800">${new Intl.NumberFormat('fr-FR').format(tarifEcolageBase)} Ar</td>
        `;
        tbody.appendChild(tr);
    }

    // Update DOM Display
    document.getElementById('montant_ecolage_preview').textContent = new Intl.NumberFormat('fr-FR').format(montantEcolage) + ' Ar';
    document.getElementById('total_a_payer_display').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' Ar';
    
    // Update Hidden Inputs (montants dus ET paiements auto-remplis)
    document.getElementById('input_ecolage_montant').value = montantEcolage;
    document.getElementById('paiement_droit_inscription').value = tarifDroit;
    document.getElementById('paiement_premier_mois').value = montantEcolage;
    document.getElementById('total_montant_paye').value = total;
}

// Fonctions pour les boutons +/-
function incrementMois() {
    const input = document.getElementById('nombre_mois');
    let value = parseInt(input.value) || 1;
    if (value < 9) {
        value++;
        input.value = value;
        updatePaymentOption(value);
    }
}

function decrementMois() {
    const input = document.getElementById('nombre_mois');
    let value = parseInt(input.value) || 1;
    if (value > 1) {
        value--;
        input.value = value;
        updatePaymentOption(value);
    }
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', function() {
    const savedNombreMois = <?= $savedFraisData['nombre_mois'] ?? 1 ?>;
    updatePaymentOption(savedNombreMois);
    toggleReferenceRequired(); // Initialiser l'état de la référence
});

// Activer/désactiver le champ référence selon le mode de paiement
function toggleReferenceRequired() {
    const modePaiement = document.getElementById('mode_paiement');
    const referenceInput = document.getElementById('reference_externe');
    const referenceLabel = document.getElementById('reference_label');
    
    // Vérifier si le mode sélectionné nécessite une référence
    const selectedOption = modePaiement.options[modePaiement.selectedIndex];
    const selectedText = selectedOption.text.toLowerCase();
    
    if (selectedText.includes('mobile') || selectedText.includes('money') || selectedText.includes('mvola') || selectedText.includes('orange') || selectedText.includes('airtel')) {
        // Activer le champ pour Mobile Money
        referenceInput.disabled = false;
        referenceInput.required = true;
        referenceInput.classList.remove('bg-gray-100');
        referenceInput.classList.add('bg-white');
        referenceLabel.innerHTML = 'Référence * <span class="text-red-500">(Obligatoire)</span>';
        referenceInput.placeholder = 'N° Transaction Mobile Money...';
    } else {
        // Désactiver et griser le champ pour les autres modes
        referenceInput.disabled = true;
        referenceInput.required = false;
        referenceInput.value = '';
        referenceInput.classList.remove('bg-white');
        referenceInput.classList.add('bg-gray-100');
        referenceLabel.textContent = 'Référence';
        referenceInput.placeholder = '';
    }
}
</script>

