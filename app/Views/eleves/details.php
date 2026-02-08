<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                Détails de l'élève
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Informations complètes de l'élève</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('eleves/list') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- 1. Informations Générales (Toujours visibles) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Informations Élève -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b pb-2">
                <i class="fas fa-user-graduate text-blue-600"></i>
                Informations de l'élève
            </h3>
            
            <div class="flex items-start gap-6">
                <!-- Photo -->
                <div class="flex-shrink-0">
                    <?php if (!empty($eleve['photo'])): ?>
                        <img src="/ROSSIGNOLES/public/<?= e($eleve['photo']) ?>" alt="Photo de <?= e($eleve['prenom']) ?>" 
                             class="w-24 h-24 rounded-full object-cover shadow border-2 border-blue-100"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold" style="display:none;">
                            <?= strtoupper(substr($eleve['prenom'], 0, 1) . substr($eleve['nom'], 0, 1)) ?>
                        </div>
                    <?php else: ?>
                        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold">
                            <?= strtoupper(substr($eleve['prenom'], 0, 1) . substr($eleve['nom'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?></h2>
                    <div class="grid grid-cols-1 gap-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Matricule :</span><span class="font-medium"><?= e($eleve['matricule']) ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Né(e) le :</span><span class="font-medium"><?= formatDate($eleve['date_naissance']) ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Sexe :</span><span class="font-medium"><?= $eleve['sexe'] == 'M' ? 'Masculin' : 'Féminin' ?></span></div>
                        <?php if ($classe): ?>
                        <div class="flex justify-between"><span class="text-gray-500">Classe :</span><span class="font-bold text-blue-600"><?= e($classe['classe_code']) ?></span></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations Parent Principal -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b pb-2">
                <i class="fas fa-users text-purple-600"></i>
                Informations Parent / Tuteur
            </h3>

            <?php if (!empty($parents)): ?>
                <?php $parent = $parents[0]; ?>
                <h2 class="text-xl font-bold text-gray-900 mb-3"><?= e($parent['nom'] . ' ' . $parent['prenom']) ?></h2>
                <div class="grid grid-cols-1 gap-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Lien :</span><span class="font-medium"><?= ucfirst($parent['lien_parente'] ?? $parent['type_lien'] ?? 'Parent') ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Téléphone :</span>
                        <span class="font-bold text-gray-900">
                            <?php 
                                if (!empty($parent['telephone'])) {
                                $tel = preg_replace('/[^0-9]/', '', $parent['telephone']);
                                if (strlen($tel) === 10) {
                                    echo e(substr($tel, 0, 3) . ' ' . substr($tel, 3, 2) . ' ' . substr($tel, 5, 3) . ' ' . substr($tel, 8, 2));
                                    } else {
                                            echo e($parent['telephone']);
                                            }
                                        } else {
                                         echo 'Non renseigné';
                                        }
                            ?>
                        </span>
                    </div>
                    <div class="flex justify-between"><span class="text-gray-500">Adresse :</span><span class="text-gray-700"><?= e($parent['adresse'] ?? 'Non renseignée') ?></span></div>
                    <?php if (!empty($parent['profession'])): ?>
                    <div class="flex justify-between"><span class="text-gray-500">Profession :</span><span><?= e($parent['profession']) ?></span></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-gray-400 italic">Aucun parent renseigné</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. Navigation par Onglets -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="detailsTabs" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg transition-colors duration-200 border-blue-600 text-blue-600" 
                            id="finance-tab" data-target="finance-content" type="button" role="tab">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Ecolage & Échéancier
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg transition-colors duration-200 text-gray-500" 
                            id="history-tab" data-target="history-content" type="button" role="tab">
                        <i class="fas fa-history mr-2"></i>Historique de paiement
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- 3. Contenu des Onglets -->
    <div id="tabContent">
        <!-- Onglet 1 : Ecolage & Échéancier -->
        <div id="finance-content" class="tab-pane active" role="tabpanel">
            <?php if ($inscription && $situationFinanciere): ?>
                <!-- Échéancier -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mois / Période</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Montant dû</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Payé</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Reste</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($echeancier as $ligne): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800"><?= e($ligne['nom_mois']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600"><?= number_format($ligne['montant_du'], 0, ',', ' ') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-green-600"><?= number_format($ligne['montant_paye'], 0, ',', ' ') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold <?= $ligne['reste_a_payer'] > 0 ? 'text-red-500' : 'text-gray-400' ?>">
                                    <?= number_format($ligne['reste_a_payer'], 0, ',', ' ') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php
                                    $s = $ligne['statut'];
                                    $st = [
                                        'paye' => ['Payé', 'bg-green-100 text-green-700'],
                                        'partiel' => ['Partiel', 'bg-yellow-100 text-yellow-700'],
                                        'exonere' => ['Exonéré', 'bg-blue-100 text-blue-700'],
                                        'impaye' => ['Impayé', 'bg-red-100 text-red-700']
                                    ];
                                    $label = $st[$s] ?? ['Impayé', 'bg-red-100 text-red-700'];
                                    ?>
                                    <span class="px-2.5 py-1 <?= $label[1] ?> rounded-full text-[10px] font-bold uppercase tracking-wider">
                                        <?= $label[0] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <?php if ($ligne['statut'] != 'paye' && $ligne['statut'] != 'exonere'): ?>
                                    <a href="<?= url('finance/paiement-mensuel/saisir?eleve_id=' . $eleve['id'] . '&annee_scolaire_id=' . ($anneeActive['id'] ?? '')) ?>" 
                                       class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                                        <i class="fas fa-credit-card"></i> Regler
                                    </a>
                                    <?php else: ?>
                                        <span class="text-gray-300 text-xs italic">Aucune action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
                    <i class="fas fa-file-invoice-dollar text-5xl mb-4 block"></i>
                    Aucun échéancier n'est rattaché à cet élève pour l'année en cours.
                </div>
            <?php endif; ?>
        </div>

        <!-- Onglet 2 : Historique de paiement -->
        <div id="history-content" class="tab-pane hidden" role="tabpanel">

                <?php if (empty($paiements)): ?>
                    <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-lg">
                        <i class="fas fa-receipt text-5xl mb-4 text-gray-300"></i>
                        <p class="text-lg">Aucun paiement enregistré.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Désignations</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mode</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Réf. Reçu</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Montant</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php 
                                // --- LOGIQUE D'ECLATEMENT DES PAIEMENTS (Split par ligne de facture) ---
                                $finalRows = []; 
                                $lignesDisponibles = $lignesFacture ?? []; 
                                $paiementsChrono = $paiements ?? []; 
                                usort($paiementsChrono, function($a, $b) { return strtotime($a['date_paiement']) - strtotime($b['date_paiement']); });

                                foreach ($paiementsChrono as $p) {
                                    $factureId = $p['facture_id'] ?? null;
                                    $remarque = $p['remarque'] ?: ($p['motif'] ?? '');
                                    $montantPaiement = (float)$p['montant'];

                                    // Stratégie de matching smart par facture
                                    $tempItems = []; $montantCumule = 0; $keysToConsume = [];
                                    
                                    foreach ($lignesDisponibles as $k => $ligne) {
                                        // On ne match que les lignes de la MÊME facture
                                        if ($factureId && $ligne['facture_id'] != $factureId) continue;

                                        $montantLigne = (float)$ligne['montant'];
                                        
                                        // Si la ligne peut "rentrer" dans le montant du paiement
                                        if (($montantCumule + $montantLigne) <= ($montantPaiement + 50)) { 
                                            $montantCumule += $montantLigne;
                                            $tempItems[] = ['designation' => $ligne['designation'], 'montant' => $montantLigne];
                                            $keysToConsume[] = $k;
                                        }
                                        
                                        if (abs($montantCumule - $montantPaiement) < 50) break;
                                    }
                                    
                                    if (!empty($tempItems)) {
                                        foreach ($keysToConsume as $k) unset($lignesDisponibles[$k]);
                                        foreach ($tempItems as $item) {
                                            $finalRows[] = array_merge($p, ['motif_affiche' => $item['designation'], 'montant_affiche' => $item['montant']]);
                                        }
                                        
                                        // S'il reste un reliquat (paiement partiel d'une ligne ou trop perçu)
                                        $reste = $montantPaiement - $montantCumule;
                                        if ($reste > 50) {
                                            $finalRows[] = array_merge($p, ['motif_affiche' => $remarque . " (Reliquat)", 'montant_affiche' => $reste]);
                                        }
                                    } else {
                                        $motif = $remarque ?: 'Paiement';
                                        if (stripos($motif, 'droit') !== false && strlen($motif) < 30) $motif = "Droit d'inscription";
                                        $finalRows[] = array_merge($p, ['motif_affiche' => $motif, 'montant_affiche' => $montantPaiement]);
                                    }
                                }

                                // Fonction de tri personnalisée pour organiser les paiements
                                usort($finalRows, function($a, $b) {
                                    $motifA = strtolower($a['motif_affiche']);
                                    $motifB = strtolower($b['motif_affiche']);
                                    
                                    // Fonction pour déterminer la priorité de tri
                                    $getPriority = function($motif) {
                                        // 1. Droit d'inscription en premier
                                        if (stripos($motif, 'droit') !== false || 
                                            (stripos($motif, 'inscription') !== false && stripos($motif, 'écolage') === false && stripos($motif, 'ecolage') === false)) {
                                            return 1.0;
                                        } 
                                        // 2. Articles dans un ordre spécifique
                                        elseif (stripos($motif, 'tee') !== false || stripos($motif, 't-shirt') !== false || stripos($motif, 'tshirt') !== false) {
                                            return 2.1; // Tee-shirt
                                        }
                                        elseif (stripos($motif, 'logo') !== false) {
                                            return 2.2; // Logo
                                        }
                                        elseif (stripos($motif, 'carnet') !== false) {
                                            return 2.3; // Carnet de correspondance
                                        }
                                        // 3. Écolages par mois
                                        elseif (stripos($motif, 'écolage') !== false || stripos($motif, 'ecolage') !== false) {
                                            // Extraire le mois pour les écolages
                                            $mois = ['janvier' => 1, 'février' => 2, 'fevrier' => 2, 'mars' => 3, 'avril' => 4, 
                                                     'mai' => 5, 'juin' => 6, 'juillet' => 7, 'août' => 8, 'aout' => 8,
                                                     'septembre' => 9, 'octobre' => 10, 'novembre' => 11, 'décembre' => 12, 'decembre' => 12];
                                            
                                            foreach ($mois as $nomMois => $numMois) {
                                                if (stripos($motif, $nomMois) !== false) {
                                                    // Septembre (9) commence l'année scolaire
                                                    // Septembre = 3.09, Octobre = 3.10, ..., Décembre = 3.12, Janvier = 3.01, ..., Juin = 3.06
                                                    return 3 + ($numMois / 100);
                                                }
                                            }
                                            return 3.0; // Écolage sans mois identifié
                                        } 
                                        // 4. Autres articles
                                        else {
                                            return 2.9; // Autres articles après Carnet
                                        }
                                    };
                                    
                                    $priorityA = $getPriority($motifA);
                                    $priorityB = $getPriority($motifB);
                                    
                                    if ($priorityA != $priorityB) {
                                        return $priorityA <=> $priorityB;
                                    }
                                    
                                    // Si même priorité, trier par date de paiement (plus ancien en premier)
                                    return strtotime($a['date_paiement']) <=> strtotime($b['date_paiement']);
                                });
                                
                                foreach ($finalRows as $row): // Ordre logique : Inscription > Articles > Écolages chronologiques
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                        <?= date('d/m/Y H:i', strtotime($row['date_paiement'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        <?= e($row['motif_affiche']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                        <?= e($row['mode_paiement'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                        <?= e($row['numero_recu'] ?? $row['reference_externe'] ?? '-') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-black text-gray-900">
                                        <?= number_format($row['montant_affiche'], 0, ',', ' ') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="<?= url('finance/recus?id=' . $row['id']) ?>" target="_blank" 
                                           class="text-blue-500 hover:text-blue-700 p-2 hover:bg-blue-50 rounded-lg transition-all" title="Imprimer le reçu">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#detailsTabs button');
    const panes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.getAttribute('data-target');

            // Visuel Tabs
            tabs.forEach(t => {
                t.classList.remove('border-blue-600', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.add('border-blue-600', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');

            // Visuel Contenu
            panes.forEach(p => {
                if (p.id === target) p.classList.remove('hidden');
                else p.classList.add('hidden');
            });
            
            localStorage.setItem('activeEleveTab_v2', target);
        });
    });

    const savedTab = localStorage.getItem('activeEleveTab_v2');
    if (savedTab) {
        const tabToClick = document.querySelector(`button[data-target="${savedTab}"]`);
        if (tabToClick) tabToClick.click();
    }
});
</script>
