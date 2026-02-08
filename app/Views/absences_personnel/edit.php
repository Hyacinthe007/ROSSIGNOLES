<?php
$title = "Modifier une Absence du Personnel";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Personnel', 'url' => '/personnel/list'],
    ['label' => 'Absences', 'url' => '/absences_personnel/list'],
    ['label' => 'Modifier']
];
?>

<div class="p-4 md:p-8">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-edit text-blue-600 mr-2"></i>
            Modifier une Absence
        </h1>
        <p class="text-gray-600">Modification des détails de l'absence</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire principal -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <form method="POST" class="p-6 space-y-6">
                    <?= csrf_field() ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personnel et Type -->
                        <div class="space-y-6">
                            <div>
                                <label for="personnel_id" class="block text-sm font-medium text-gray-700 mb-1">Personnel <span class="text-red-500">*</span></label>
                                <select name="personnel_id" id="personnel_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" required>
                                    <option value="">Sélectionnez un personnel</option>
                                    <?php foreach ($personnels as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= $absence['personnel_id'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['matricule']) ?> - <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="type_absence" class="block text-sm font-medium text-gray-700 mb-1">Type d'absence <span class="text-red-500">*</span></label>
                                <select name="type_absence" id="type_absence" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" required>
                                    <option value="conge_annuel" <?= $absence['type_absence'] == 'conge_annuel' ? 'selected' : '' ?>>Congé annuel</option>
                                    <option value="conge_maladie" <?= $absence['type_absence'] == 'conge_maladie' ? 'selected' : '' ?>>Congé maladie</option>
                                    <option value="conge_maternite" <?= $absence['type_absence'] == 'conge_maternite' ? 'selected' : '' ?>>Congé maternité</option>
                                    <option value="conge_paternite" <?= $absence['type_absence'] == 'conge_paternite' ? 'selected' : '' ?>>Congé paternité</option>
                                    <option value="conge_sans_solde" <?= $absence['type_absence'] == 'conge_sans_solde' ? 'selected' : '' ?>>Congé sans solde</option>
                                    <option value="absence_autorisee" <?= $absence['type_absence'] == 'absence_autorisee' ? 'selected' : '' ?>>Absence autorisée</option>
                                    <option value="absence_non_justifiee" <?= $absence['type_absence'] == 'absence_non_justifiee' ? 'selected' : '' ?>>Absence non justifiée</option>
                                    <option value="formation" <?= $absence['type_absence'] == 'formation' ? 'selected' : '' ?>>Formation</option>
                                    <option value="mission" <?= $absence['type_absence'] == 'mission' ? 'selected' : '' ?>>Mission</option>
                                    <option value="autre" <?= $absence['type_absence'] == 'autre' ? 'selected' : '' ?>>Autre</option>
                                </select>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début <span class="text-red-500">*</span></label>
                                    <input type="date" name="date_debut" id="date_debut" value="<?= htmlspecialchars($absence['date_debut']) ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" required>
                                </div>
                                <div>
                                    <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin <span class="text-red-500">*</span></label>
                                    <input type="date" name="date_fin" id="date_fin" value="<?= htmlspecialchars($absence['date_fin']) ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="nb_jours" class="block text-sm font-medium text-gray-700 mb-1">Jours ouvrés <span class="text-red-500">*</span></label>
                                    <input type="number" name="nb_jours" id="nb_jours" value="<?= htmlspecialchars($absence['nb_jours']) ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" step="0.5" min="0" required>
                                </div>
                                <div>
                                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                    <select name="statut" id="statut" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5">
                                        <option value="demande" <?= $absence['statut'] == 'demande' ? 'selected' : '' ?>>Demande</option>
                                        <option value="validee" <?= $absence['statut'] == 'validee' ? 'selected' : '' ?>>Validée</option>
                                        <option value="refusee" <?= $absence['statut'] == 'refusee' ? 'selected' : '' ?>>Refusée</option>
                                        <option value="annulee" <?= $absence['statut'] == 'annulee' ? 'selected' : '' ?>>Annulée</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motif et Justificatif -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                            <textarea name="motif" id="motif" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5"><?= htmlspecialchars($absence['motif'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label for="piece_justificative" class="block text-sm font-medium text-gray-700 mb-1">Pièce justificative</label>
                            <input type="text" name="piece_justificative" id="piece_justificative" value="<?= htmlspecialchars($absence['piece_justificative'] ?? '') ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" placeholder="Référence...">
                        </div>
                    </div>

                    <!-- Remplacement -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Remplacement</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="remplace_par" class="block text-sm font-medium text-gray-700 mb-1">Personnel remplaçant</label>
                                <select name="remplace_par" id="remplace_par" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5">
                                    <option value="">Aucun remplaçant</option>
                                    <?php foreach ($personnels as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= $absence['remplace_par'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['matricule']) ?> - <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="commentaire_remplacement" class="block text-sm font-medium text-gray-700 mb-1">Commentaire remplacement</label>
                                <textarea name="commentaire_remplacement" id="commentaire_remplacement" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5"><?= htmlspecialchars($absence['commentaire_remplacement'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="/absences_personnel/details/<?= $absence['id'] ?>" class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-medium transition shadow-sm">
                            Annuler
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium transition shadow-lg flex items-center">
                            <i class="fas fa-save mr-2"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Actions de Validation (Sidebar) -->
        <div class="space-y-6">
            <?php if ($absence['statut'] == 'demande'): ?>
                <!-- Carte Validation -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-green-50 px-6 py-4 border-b border-green-100">
                        <h3 class="text-green-800 font-bold"><i class="fas fa-check-circle mr-2"></i>Validation</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">Valider cette demande d'absence.</p>
                        <form method="POST" action="/absences_personnel/valider/<?= $absence['id'] ?>">
                            <?= csrf_field() ?>
                            <div class="form-group mb-4">
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Remplaçant (Confirmation)</label>
                                <select name="remplace_par" class="w-full text-sm border-gray-300 rounded-lg p-2">
                                    <option value="">-- Idem formulaire --</option>
                                    <?php foreach ($personnels as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= $absence['remplace_par'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="w-full block text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition shadow-md" onclick="return confirm('Confirmer la validation ?')">
                                Valider la demande
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Carte Refus -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                        <h3 class="text-red-800 font-bold"><i class="fas fa-times-circle mr-2"></i>Refus</h3>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="/absences_personnel/refuser/<?= $absence['id'] ?>">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <label for="motif_refus" class="block text-sm font-medium text-gray-700 mb-1">Motif du refus <span class="text-red-500">*</span></label>
                                <textarea name="motif_refus" id="motif_refus" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-transparent p-2.5 text-sm" required></textarea>
                            </div>
                            <button type="submit" class="w-full block text-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition shadow-md" onclick="return confirm('Confirmer le refus ?')">
                                Refuser la demande
                            </button>
                        </form>
                    </div>
                </div>
            <?php elseif ($absence['statut'] == 'refusee'): ?>
                <div class="bg-red-50 rounded-xl p-6 border border-red-100">
                    <h3 class="text-red-800 font-bold mb-2"><i class="fas fa-info-circle mr-2"></i>Demande Refusée</h3>
                    <p class="text-sm text-red-600"><strong>Motif :</strong> <?= htmlspecialchars($absence['motif_refus'] ?? 'Non précisé') ?></p>
                </div>
            <?php elseif ($absence['statut'] == 'validee'): ?>
                <div class="bg-green-50 rounded-xl p-6 border border-green-100">
                    <h3 class="text-green-800 font-bold mb-2"><i class="fas fa-check-circle mr-2"></i>Demande Validée</h3>
                    <p class="text-sm text-green-600">Cette demande a été validée.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>