<?php
$title = "Nouvelle Absence du Personnel";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Personnel', 'url' => '/personnel/list'],
    ['label' => 'Absences', 'url' => '/absences_personnel/list'],
    ['label' => 'Nouvelle']
];
?>

<div class="p-4 md:p-8">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-calendar-plus text-blue-600 mr-2"></i>
            Nouvelle Absence
        </h1>
        <p class="text-gray-600">Enregistrement d'une nouvelle demande d'absence ou de congé</p>
    </div>

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
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['matricule']) ?> - <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="type_absence" class="block text-sm font-medium text-gray-700 mb-1">Type d'absence <span class="text-red-500">*</span></label>
                        <select name="type_absence" id="type_absence" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" required>
                            <option value="conge_annuel">Congé annuel</option>
                            <option value="conge_maladie">Congé maladie</option>
                            <option value="conge_maternite">Congé maternité</option>
                            <option value="conge_paternite">Congé paternité</option>
                            <option value="conge_sans_solde">Congé sans solde</option>
                            <option value="absence_autorisee" selected>Absence autorisée</option>
                            <option value="absence_non_justifiee">Absence non justifiée</option>
                            <option value="formation">Formation</option>
                            <option value="mission">Mission</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>

                <!-- Dates -->
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date de début <span class="text-red-500">*</span></label>
                            <input type="date" name="date_debut" id="date_debut" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" required>
                        </div>
                        <div>
                            <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date de fin <span class="text-red-500">*</span></label>
                            <input type="date" name="date_fin" id="date_fin" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nb_jours" class="block text-sm font-medium text-gray-700 mb-1">Jours ouvrés <span class="text-red-500">*</span></label>
                            <input type="number" name="nb_jours" id="nb_jours" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" step="0.5" min="0" required>
                        </div>
                        <div>
                            <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="statut" id="statut" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5">
                                <option value="demande" selected>Demande</option>
                                <option value="validee">Validée</option>
                                <option value="refusee">Refusée</option>
                                <option value="annulee">Annulée</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motif et Justificatif -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                    <textarea name="motif" id="motif" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5"></textarea>
                </div>
                <div>
                    <label for="piece_justificative" class="block text-sm font-medium text-gray-700 mb-1">Pièce justificative</label>
                    <input type="text" name="piece_justificative" id="piece_justificative" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5" placeholder="Référence...">
                </div>
            </div>

            <!-- Remplacement -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Remplacement (Optionnel)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="remplace_par" class="block text-sm font-medium text-gray-700 mb-1">Personnel remplaçant</label>
                        <select name="remplace_par" id="remplace_par" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5">
                            <option value="">Aucun remplaçant</option>
                            <?php foreach ($personnels as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['matricule']) ?> - <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="commentaire_remplacement" class="block text-sm font-medium text-gray-700 mb-1">Commentaire sur le remplacement</label>
                        <textarea name="commentaire_remplacement" id="commentaire_remplacement" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2.5"></textarea>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="/absences_personnel/list" class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-medium transition shadow-sm">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium transition shadow-lg flex items-center">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Calcul automatique du nombre de jours
document.getElementById('date_debut').addEventListener('change', calculateDays);
document.getElementById('date_fin').addEventListener('change', calculateDays);

function calculateDays() {
    const startDate = document.getElementById('date_debut').value;
    const endDate = document.getElementById('date_fin').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (end >= start) {
            // Calcul simple (à améliorer pour compter uniquement les jours ouvrés si nécessaire)
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            document.getElementById('nb_jours').value = diffDays;
        }
    }
}
</script>