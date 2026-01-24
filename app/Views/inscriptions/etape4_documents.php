<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-file-upload text-blue-600 mr-2"></i>
            Documents d'Inscription
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Étape 4 sur 7 : Téléchargement des pièces justificatives</p>
    </div>

    <div class="mb-8">
        <?php 
        $stepNames = [1 => 'Type', 2 => 'Élève', 3 => 'Classe', 4 => 'Documents', 5 => 'Articles', 6 => 'Paiement', 7 => 'Confirmation'];
        ?>
        <div class="flex items-center justify-between mb-2">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?> text-center">
                    <span class="text-[10px] md:text-xs font-semibold <?= $i <= 4 ? 'text-blue-600' : 'text-gray-400' ?>">
                        Étape <?= $i ?>: <?= $stepNames[$i] ?>
                    </span>
                </div>
            <?php endfor; ?>
        </div>
        <div class="flex items-center justify-between">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?>">
                    <div class="h-2 <?= $i <= 4 ? 'bg-blue-600' : 'bg-gray-200' ?> rounded"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire d'upload -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 h-full flex flex-col">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-3">
                    <i class="fas fa-cloud-upload-alt text-blue-600 mr-2"></i>
                    Ajouter un document
                </h2>

                <form method="POST" enctype="multipart/form-data" action="<?= url('inscriptions/nouveau?etape=4') ?>" class="space-y-4">
                    <input type="hidden" name="action" value="upload">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de document *</label>
                        <select name="type_document" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Sélectionner --</option>
                            <option value="acte_naissance">Acte de naissance</option>
                            <option value="certificat_scolarite">Certificat de scolarité</option>
                            <option value="bulletin_notes">Bulletin de notes</option>
                            <option value="certificat_medical">Certificat médical</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fichier *</label>
                        <input type="file" name="fichier" required accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5 Mo)</p>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
                        <i class="fas fa-upload mr-2"></i>Télécharger
                    </button>
                </form>
            </div>
        </div>

        <!-- Liste des documents et exigences -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-3">Documents transmis</h2>
                
                <?php if (empty($documents)): ?>
                    <p class="text-gray-500  py-4 text-center">Aucun document pour le moment.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($documents as $doc): ?>
                            <div class="flex items-center justify-between p-3 border rounded-lg bg-gray-50">
                                <div class="flex items-center">
                                    <i class="fas <?= strpos($doc['type_mime'], 'pdf') !== false ? 'fa-file-pdf text-red-500' : 'fa-file-image text-blue-500' ?> text-xl mr-3"></i>
                                    <div>
                                        <p class="font-medium text-gray-800"><?= e($doc['nom_fichier']) ?></p>
                                        <p class="text-xs text-gray-500"><?= ucfirst(str_replace('_', ' ', $doc['type_document'])) ?></p>
                                    </div>
                                </div>
                                <form method="POST" action="<?= url('inscriptions/nouveau?etape=4') ?>" onsubmit="return confirm('Supprimer ce document ?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-2"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Exigences -->
            <?php if (!empty($exigences)): ?>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-3">Pièces obligatoires à fournir</h2>
                    <div class="grid grid-cols-1 gap-4">
                        <?php 
                        $allMandatoryUploaded = true;
                        foreach ($exigences as $ex): 
                            if (($ex['obligatoire'] ?? 0) == 0 && ($ex['obligatoire_pour_validation'] ?? 0) == 0) continue;
                            
                            $fourni = false;
                            foreach($documents as $d) {
                                if($d['type_document'] == $ex['type_document']) $fourni = true;
                                // Cas spécial : Certificat OU Bulletin
                                if($ex['type_document'] == 'certificat_scolarite' && $d['type_document'] == 'bulletin_notes') $fourni = true;
                            }
                            if (!$fourni) $allMandatoryUploaded = false;
                        ?>
                            <div class="flex items-center p-4 border rounded-xl <?= $fourni ? 'bg-green-50 border-green-200 text-green-800' : 'bg-amber-50 border-amber-200 text-amber-800' ?>">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4 <?= $fourni ? 'bg-green-100' : 'bg-amber-100' ?>">
                                    <i class="fas <?= $fourni ? 'fa-check text-green-600' : 'fa-exclamation text-amber-600' ?>"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-sm"><?= e($ex['libelle']) ?></p>
                                    <p class="text-xs opacity-75"><?= $fourni ? 'Document reçu' : 'Manquant (Obligatoire)' ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Boutons Navigation -->
    <div class="flex flex-col sm:flex-row gap-4 pt-8 mt-8 border-t">
        <a href="<?= url('inscriptions/nouveau?etape=3') ?>" class="px-8 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition text-center">
            <i class="fas fa-arrow-left mr-2"></i>Précédent
        </a>
        
        <?php if ($allMandatoryUploaded || ($inscription['type_inscription'] !== 'nouvelle')): ?>
            <a href="<?= url('inscriptions/nouveau?etape=5') ?>" class="flex-1 px-8 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-lg text-center flex items-center justify-center">
                <span>Continuer vers le paiement</span>
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        <?php else: ?>
            <button disabled class="flex-1 px-8 py-3 bg-gray-300 text-gray-500 font-bold rounded-lg cursor-not-allowed flex items-center justify-center" title="Veuillez télécharger les documents obligatoires">
                <span>Continuer vers le paiement (Documents requis)</span>
                <i class="fas fa-lock ml-2 text-xs"></i>
            </button>
        <?php endif; ?>
    </div>
</div>

