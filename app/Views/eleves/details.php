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
            <a href="<?= url('eleves/edit/' . $eleve['id']) ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>Modifier</span>
            </a>
            <a href="<?= url('eleves/list') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Grille côte à côte -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Côté gauche : Informations de l'élève -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-user-graduate text-blue-600"></i>
                Informations de l'élève
            </h3>
            
            <!-- Photo et Informations -->
            <div class="flex items-start gap-6">
                <!-- Photo à gauche -->
                <div class="flex-shrink-0">
                    <?php if (!empty($eleve['photo'])): ?>
                        <img src="/ROSSIGNOLES/public/<?= e($eleve['photo']) ?>" alt="Photo de <?= e($eleve['prenom']) ?>" 
                             class="w-32 h-32 rounded-full object-cover shadow-lg border-4 border-blue-100"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-4xl font-bold shadow-lg" style="display:none;">
                            <?= strtoupper(substr($eleve['prenom'], 0, 1) . substr($eleve['nom'], 0, 1)) ?>
                        </div>
                    <?php else: ?>
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                            <?= strtoupper(substr($eleve['prenom'], 0, 1) . substr($eleve['nom'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Informations à droite -->
                <div class="flex-1 space-y-3">
                    <!-- Nom et prénom -->
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                            <?= e($eleve['prenom'] . ' ' . $eleve['nom']) ?>
                        </h2>
                    </div>

                    <!-- Matricule -->
                    <div>
                        <p class="text-sm text-gray-500">Matricule</p>
                        <p class="font-semibold text-gray-800 font-mono"><?= e($eleve['matricule']) ?></p>
                    </div>

                    <!-- Date de naissance -->
                    <div>
                        <p class="text-sm text-gray-500">Date de naissance</p>
                        <p class="text-gray-800"><?= formatDate($eleve['date_naissance']) ?></p>
                    </div>

                    <!-- Sexe -->
                    <div>
                        <p class="text-sm text-gray-500">Sexe</p>
                        <p class="text-gray-800"><?= $eleve['sexe'] == 'M' ? 'Masculin' : 'Féminin' ?></p>
                    </div>

                    <!-- Lieu de naissance -->
                    <div>
                        <p class="text-sm text-gray-500">Lieu de naissance</p>
                        <p class="text-gray-800"><?= e($eleve['lieu_naissance'] ?: 'Non renseigné') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Côté droit : Informations Parent -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-users text-purple-600"></i>
                Informations Parent / Tuteur
            </h3>

            <?php if (!empty($parents)): ?>
                <?php $parent = $parents[0]; ?>
                <div class="space-y-4">
                    <!-- Nom Prénom Parent -->
                    <div>
                        <p class="text-sm text-gray-500">Nom et Prénom</p>
                        <p class="text-lg font-bold text-gray-800"><?= e($parent['nom'] . ' ' . $parent['prenom']) ?></p>
                        <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm mt-2">
                            <?= e($parent['type_lien'] ?? 'Parent') ?>
                        </span>
                    </div>

                    <!-- Contact -->
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Contact</p>
                        <div class="space-y-2">
                            <?php if (!empty($parent['telephone'])): ?>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone text-purple-600 w-5"></i>
                                <span class="text-gray-800"><?= e($parent['telephone']) ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($parent['email'])): ?>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-purple-600 w-5"></i>
                                <span class="text-gray-800"><?= e($parent['email']) ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($parent['profession'])): ?>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-briefcase text-purple-600 w-5"></i>
                                <span class="text-gray-800"><?= e($parent['profession']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div>
                        <p class="text-sm text-gray-500">Adresse</p>
                        <div class="flex items-start gap-2 mt-2">
                            <i class="fas fa-home text-purple-600 w-5 mt-1"></i>
                            <span class="text-gray-800"><?= e($parent['adresse'] ?: 'Non renseignée') ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-300 text-5xl mb-3"></i>
                    <p class="text-gray-500 ">Aucun parent/tuteur enregistré</p>
                    <p class="text-sm text-gray-400 mt-1">Veuillez créer le lien dans la base de données</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Classe actuelle (pleine largeur) -->
    <?php if ($classe): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-door-open text-green-600"></i>
            Classe actuelle
        </h3>
        <div class="grid grid-cols-1 md:grid-cols- gap-4">
            <div>
                <p class="text-sm text-gray-500">Classe</p>
                <p class="text-lg font-semibold text-gray-800"><?= e($classe['classe_nom']) ?></p>
                <p class="text-sm text-gray-600"><?= e($classe['classe_code']) ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Date d'inscription</p>
                <p class="text-gray-700"><?= formatDate($classe['date_inscription']) ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Situation Financière & Échéancier (pleine largeur) -->
    <?php if ($inscription && $situationFinanciere): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-money-bill-wave text-green-600"></i>
                Situation Financière & Échéancier
            </h3>
            
            <!-- Statut global -->
            <?php
            $tauxPaiement = $situationFinanciere['taux_paiement'] ?? 0;
            $nbImpayes = $situationFinanciere['nb_echeances_impayees'] ?? 0;
            
            if ($tauxPaiement >= 100):
                $statutClass = 'bg-green-100 text-green-800';
                $statutIcon = 'fa-check-circle';
                $statutTexte = 'À jour';
            elseif ($nbImpayes > 0):
                $statutClass = 'bg-red-100 text-red-800';
                $statutIcon = 'fa-exclamation-circle';
                $statutTexte = 'En retard';
            else:
                $statutClass = 'bg-yellow-100 text-yellow-800';
                $statutIcon = 'fa-clock';
                $statutTexte = 'Paiement partiel';
            endif;
            ?>
            <span class="px-4 py-2 <?= $statutClass ?> rounded-full text-sm font-semibold flex items-center gap-2">
                <i class="fas <?= $statutIcon ?>"></i>
                <?= $statutTexte ?>
            </span>
        </div>

        <!-- Résumé financier -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-blue-600 mb-1">Total à payer</p>
                <p class="text-2xl font-bold text-blue-800"><?= number_format($situationFinanciere['total_a_payer'] ?? 0, 0, ',', ' ') ?> MGA</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-green-600 mb-1">Total payé</p>
                <p class="text-2xl font-bold text-green-800"><?= number_format($situationFinanciere['total_paye'] ?? 0, 0, ',', ' ') ?> MGA</p>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg">
                <p class="text-sm text-orange-600 mb-1">Reste à payer</p>
                <p class="text-2xl font-bold text-orange-800"><?= number_format($situationFinanciere['total_reste'] ?? 0, 0, ',', ' ') ?> MGA</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm text-purple-600 mb-1">Taux de paiement</p>
                <p class="text-2xl font-bold text-purple-800"><?= number_format($tauxPaiement, 1) ?>%</p>
            </div>
        </div>

        <!-- Tableau des échéances -->
        <?php if (!empty($echeancier)): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Période</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Montant dû</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Montant payé</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Reste</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Statut</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($echeancier as $ligne): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">
                            <?= e($ligne['nom_mois']) ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-800">
                            <?= number_format($ligne['montant_du'], 0, ',', ' ') ?> MGA
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-700">
                            <?= number_format($ligne['montant_paye'], 0, ',', ' ') ?> MGA
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-orange-700">
                            <?= number_format($ligne['reste_a_payer'], 0, ',', ' ') ?> MGA
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php
                            switch ($ligne['statut']):
                                case 'paye':
                                    echo '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">✓ Payé</span>';
                                    break;
                                case 'partiel':
                                    echo '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">⚠ Partiel</span>';
                                    break;
                                case 'exonere':
                                    echo '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Exonéré</span>';
                                    break;
                                default:
                                    echo '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">✗ Impayé</span>';
                            endswitch;
                            ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($ligne['statut'] != 'paye' && $ligne['statut'] != 'exonere'): ?>
                            <a href="<?= url('finance/ecolage/payer/' . $eleve['id']) ?>" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center gap-1">
                                <i class="fas fa-credit-card"></i>Payer
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center text-gray-500 py-8">Aucun échéancier disponible</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Autres parents (si plusieurs) -->
    <?php if (!empty($parents) && count($parents) > 1): ?>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-users text-purple-600"></i>
            Autres Parents / Tuteurs
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach (array_slice($parents, 1) as $parent): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="font-semibold text-gray-800 text-lg"><?= e($parent['nom'] . ' ' . $parent['prenom']) ?></p>
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs mt-1">
                                <?= e($parent['type_lien'] ?? 'Parent') ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-2 mt-3">
                        <?php if (!empty($parent['telephone'])): ?>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-phone text-blue-600 w-4"></i>
                                <span class="text-gray-700"><?= e($parent['telephone']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($parent['email'])): ?>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-envelope text-blue-600 w-4"></i>
                                <span class="text-gray-700"><?= e($parent['email']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($parent['profession'])): ?>
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-briefcase text-blue-600 w-4"></i>
                                <span class="text-gray-700"><?= e($parent['profession']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($parent['adresse'])): ?>
                            <div class="flex items-start gap-2 text-sm">
                                <i class="fas fa-home text-blue-600 w-4 mt-1"></i>
                                <span class="text-gray-700"><?= e($parent['adresse']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

