<div class="p-4 md:p-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-file-upload text-blue-600 mr-2"></i>
                Documents d'Inscription
            </h1>
            <p class="text-gray-600 text-sm md:text-base">
                Inscription #<?= e($inscription['id']) ?> - <?= e($inscription['eleve_nom']) ?> <?= e($inscription['eleve_prenom']) ?>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= url('inscriptions/details/' . $inscription['id']) ?>" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r animate-fade-in" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-xl"></i>
                <p><?= $_SESSION['success'] ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r animate-fade-in" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                <p><?= $_SESSION['error'] ?></p>
            </div>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total</p>
                    <p class="text-3xl font-bold"><?= $stats['total'] ?? 0 ?></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-file-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Validés</p>
                    <p class="text-3xl font-bold"><?= $stats['valides'] ?? 0 ?></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium mb-1">En attente</p>
                    <p class="text-3xl font-bold"><?= $stats['en_attente'] ?? 0 ?></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium mb-1">Refusés</p>
                    <p class="text-3xl font-bold"><?= $stats['refuses'] ?? 0 ?></p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-times-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire d'upload -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-3">
                    <i class="fas fa-cloud-upload-alt text-blue-600 mr-2"></i>
                    Ajouter un document
                </h2>

                <form method="POST" enctype="multipart/form-data" id="uploadForm" class="space-y-4">
                    <input type="hidden" name="action" value="upload">
                    <?php csrf_field(); ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type de document <span class="text-red-500">*</span>
                        </label>
                        <select name="type_document" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Sélectionner --</option>
                            <option value="acte_naissance">Acte de naissance</option>
                            <option value="certificat_scolarite">Certificat de scolarité</option>
                            <option value="bulletin_notes">Bulletin de notes</option>
                            <option value="photo_identite">Photo d'identité</option>
                            <option value="certificat_medical">Certificat médical</option>
                            <option value="fiche_renseignement">Fiche de renseignement</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fichier <span class="text-red-500">*</span>
                        </label>
                        <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors">
                            <input type="file" name="fichier" id="fichier" required accept=".pdf,.jpg,.jpeg,.png"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Cliquez ou glissez un fichier</p>
                                <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5 Mo)</p>
                            </div>
                        </div>
                        <div id="fileName" class="mt-2 text-sm text-gray-600 hidden"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                N° document
                            </label>
                            <input type="text" name="numero_document" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Ex: 123456">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Date émission
                            </label>
                            <input type="date" name="date_emission" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lieu d'émission
                        </label>
                        <input type="text" name="lieu_emission" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ex: Antananarivo">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Remarques
                        </label>
                        <textarea name="remarques" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Remarques optionnelles..."></textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="obligatoire_pour_validation" id="obligatoire" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="obligatoire" class="ml-2 text-sm text-gray-700">
                            Document obligatoire pour validation
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                        <i class="fas fa-upload mr-2"></i>Télécharger le document
                    </button>
                </form>
            </div>
        </div>

        <!-- Liste des documents -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-3">
                    <i class="fas fa-list text-indigo-600 mr-2"></i>
                    Documents fournis
                </h2>

                <?php if (empty($documents)): ?>
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Aucun document téléchargé</p>
                        <p class="text-gray-400 text-sm mt-2">Utilisez le formulaire ci-contre pour ajouter des documents</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($documents as $doc): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <!-- Icône du type de fichier -->
                                        <div class="flex-shrink-0">
                                            <?php if (strpos($doc['type_mime'], 'pdf') !== false): ?>
                                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file-pdf text-2xl text-red-600"></i>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file-image text-2xl text-blue-600"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Informations du document -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="font-semibold text-gray-800 truncate">
                                                    <?= e($doc['nom_fichier']) ?>
                                                </h3>
                                                <?php
                                                $statusColors = [
                                                    'valide' => 'bg-green-100 text-green-800',
                                                    'refuse' => 'bg-red-100 text-red-800',
                                                    'en_attente' => 'bg-yellow-100 text-yellow-800'
                                                ];
                                                $statusIcons = [
                                                    'valide' => 'fa-check-circle',
                                                    'refuse' => 'fa-times-circle',
                                                    'en_attente' => 'fa-clock'
                                                ];
                                                $statusClass = $statusColors[$doc['statut']] ?? 'bg-gray-100 text-gray-800';
                                                $statusIcon = $statusIcons[$doc['statut']] ?? 'fa-question-circle';
                                                ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                                    <i class="fas <?= $statusIcon ?> mr-1"></i>
                                                    <?= ucfirst($doc['statut']) ?>
                                                </span>
                                            </div>

                                            <div class="text-sm text-gray-600 space-y-1">
                                                <p>
                                                    <i class="fas fa-tag mr-1"></i>
                                                    <strong>Type:</strong> <?= ucfirst(str_replace('_', ' ', $doc['type_document'])) ?>
                                                </p>
                                                <?php if ($doc['numero_document']): ?>
                                                    <p>
                                                        <i class="fas fa-hashtag mr-1"></i>
                                                        <strong>N°:</strong> <?= e($doc['numero_document']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <?php if ($doc['date_emission']): ?>
                                                    <p>
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        <strong>Émis le:</strong> <?= date('d/m/Y', strtotime($doc['date_emission'])) ?>
                                                        <?php if ($doc['lieu_emission']): ?>
                                                            à <?= e($doc['lieu_emission']) ?>
                                                        <?php endif; ?>
                                                    </p>
                                                <?php endif; ?>
                                                <p>
                                                    <i class="fas fa-hdd mr-1"></i>
                                                    <strong>Taille:</strong> <?= number_format($doc['taille_fichier'] / 1024, 2) ?> Ko
                                                </p>
                                                <?php if ($doc['remarques']): ?>
                                                    <p class="text-gray-500 ">
                                                        <i class="fas fa-comment mr-1"></i>
                                                        <?= e($doc['remarques']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <?php if ($doc['statut'] === 'refuse' && $doc['motif_refus']): ?>
                                                    <p class="text-red-600 font-medium">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        Motif: <?= e($doc['motif_refus']) ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-col gap-2 ml-4">
                                        <!-- Prévisualiser -->
                                        <button onclick="previewDocument('<?= e($doc['id']) ?>', '<?= e($doc['type_mime']) ?>', '<?= e($doc['chemin_fichier']) ?>')"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm transition-colors"
                                                title="Prévisualiser">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <?php if ($doc['statut'] === 'en_attente'): ?>
                                            <!-- Valider -->
                                            <form method="POST" class="inline" onsubmit="return confirm('Valider ce document ?')">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="action" value="valider">
                                                <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                                <button type="submit" 
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg text-sm transition-colors w-full"
                                                        title="Valider">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <!-- Refuser -->
                                            <button onclick="showRefuseModal(<?= $doc['id'] ?>)"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition-colors"
                                                    title="Refuser">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>

                                        <!-- Supprimer -->
                                        <form method="POST" class="inline" onsubmit="return confirm('Supprimer ce document définitivement ?')">
                                            <?php csrf_field(); ?>
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                            <button type="submit" 
                                                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm transition-colors w-full"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Exigences de documents -->
            <?php if (!empty($exigences)): ?>
                <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-3">
                        <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                        Documents requis
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($exigences as $exigence): ?>
                            <?php
                            $isFourni = false;
                            foreach ($documents as $doc) {
                                if ($doc['type_document'] === $exigence['type_document']) {
                                    $isFourni = true;
                                    break;
                                }
                            }
                            ?>
                            <div class="border border-gray-200 rounded-lg p-4 <?= $isFourni ? 'bg-green-50 border-green-300' : 'bg-gray-50' ?>">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 mb-1">
                                            <?= e($exigence['libelle']) ?>
                                            <?php if ($exigence['obligatoire']): ?>
                                                <span class="text-red-500 text-sm">*</span>
                                            <?php endif; ?>
                                        </h4>
                                        <?php if ($exigence['description']): ?>
                                            <p class="text-sm text-gray-600"><?= e($exigence['description']) ?></p>
                                        <?php endif; ?>
                                        <?php if ($exigence['format_accepte']): ?>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-file mr-1"></i>
                                                Formats: <?= e($exigence['format_accepte']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <?php if ($isFourni): ?>
                                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                                        <?php else: ?>
                                            <i class="fas fa-exclamation-circle text-2xl text-gray-400"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de refus -->
<div id="refuseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 animate-scale-in">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-times-circle text-red-600 mr-2"></i>
            Refuser le document
        </h3>
        <form method="POST" id="refuseForm">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="refuser">
            <input type="hidden" name="document_id" id="refuseDocId">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Motif du refus <span class="text-red-500">*</span>
                </label>
                <textarea name="motif_refus" required rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                          placeholder="Expliquez pourquoi ce document est refusé..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="hideRefuseModal()" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition">
                    Annuler
                </button>
                <button type="submit" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Refuser
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de prévisualisation -->
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-eye text-blue-600 mr-2"></i>
                Prévisualisation
            </h3>
            <button onclick="hidePreviewModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="previewContent" class="p-4 overflow-auto max-h-[calc(90vh-80px)]">
            <!-- Le contenu sera inséré ici -->
        </div>
    </div>
</div>

<script>
// Afficher le nom du fichier sélectionné
document.getElementById('fichier').addEventListener('change', function(e) {
    const fileName = document.getElementById('fileName');
    if (this.files.length > 0) {
        fileName.textContent = '📄 ' + this.files[0].name;
        fileName.classList.remove('hidden');
    } else {
        fileName.classList.add('hidden');
    }
});

// Modal de refus
function showRefuseModal(docId) {
    document.getElementById('refuseDocId').value = docId;
    document.getElementById('refuseModal').classList.remove('hidden');
    document.getElementById('refuseModal').classList.add('flex');
}

function hideRefuseModal() {
    document.getElementById('refuseModal').classList.add('hidden');
    document.getElementById('refuseModal').classList.remove('flex');
}

// Modal de prévisualisation
function previewDocument(docId, mimeType, filepath) {
    const modal = document.getElementById('previewModal');
    const content = document.getElementById('previewContent');
    
    if (mimeType.includes('pdf')) {
        content.innerHTML = `<iframe src="${filepath}" class="w-full h-[600px] border-0"></iframe>`;
    } else if (mimeType.includes('image')) {
        content.innerHTML = `<img src="${filepath}" class="max-w-full h-auto mx-auto rounded-lg shadow-lg" alt="Document">`;
    } else {
        content.innerHTML = `<p class="text-center text-gray-500">Prévisualisation non disponible pour ce type de fichier.</p>`;
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function hidePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
    document.getElementById('previewModal').classList.remove('flex');
}

// Fermer les modals en cliquant en dehors
document.getElementById('refuseModal').addEventListener('click', function(e) {
    if (e.target === this) hideRefuseModal();
});

document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) hidePreviewModal();
});
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scale-in {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}
</style>

