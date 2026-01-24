<?php
$isEnseignant = ($personnel['type_personnel'] === 'enseignant');
$colorClass = $isEnseignant ? 'indigo' : 'teal';
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-edit text-<?= $colorClass ?>-600 mr-2"></i>
                    Modifier <?= $isEnseignant ? 'l\'Enseignant' : 'le Personnel Administratif' ?>
                </h1>
                <p class="text-gray-600 text-sm md:text-base">Mettre à jour les informations de <strong><?= e($personnel['nom'] . ' ' . $personnel['prenom']) ?></strong></p>
            </div>
            <a href="<?= url('personnel/details/' . $personnel['id']) ?>" class="inline-flex items-center gap-2 text-gray-600 hover:text-<?= $colorClass ?>-600 transition font-medium">
                <i class="fas fa-arrow-left"></i>
                Retour à la fiche
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-10 border border-gray-100">
        <form method="POST" action="<?= url('personnel/edit/' . $personnel['id']) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="type_personnel" value="<?= e($personnel['type_personnel']) ?>">
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 shadow-md rounded-r-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                        <div>
                            <p class="font-bold">Erreur</p>
                            <p><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 shadow-md rounded-r-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <div>
                            <p class="font-bold">Succès</p>
                            <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="space-y-10">
                <!-- SECTION 1: INFORMATION PERSONNEL -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle mr-3 text-<?= $colorClass ?>-600"></i>
                            Informations Personnelles
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                        <!-- Matricule (Gauche) -->
                        <div class="md:col-span-1">
                            <label for="matricule" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-id-card mr-2 text-gray-400"></i>Matricule (fixe)
                            </label>
                            <input type="text" id="matricule" name="matricule" value="<?= e($personnel['matricule']) ?>" readonly
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 cursor-not-allowed font-mono font-bold text-<?= $colorClass ?>-700 text-lg shadow-sm">
                        </div>

                        <!-- Photo (Droite) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-camera mr-2 text-gray-400"></i>Photo de profil
                            </label>
                            
                            <div class="flex flex-col md:flex-row items-center gap-6 bg-gray-50 p-5 rounded-2xl border border-dashed border-gray-300 transition-colors hover:border-<?= $colorClass ?>-400">
                                <!-- Preview Circle -->
                                <div class="relative group">
                                    <div class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-200 flex items-center justify-center">
                                        <?php 
                                            $photoUrl = !empty($personnel['photo']) ? public_url($personnel['photo']) : null;
                                        ?>
                                        <img id="photoPreview" src="<?= $photoUrl ?? '' ?>" alt="Aperçu" class="w-full h-full object-cover <?= $photoUrl ? '' : 'hidden' ?>">
                                        <i id="photoPlaceholder" class="fas fa-user text-5xl text-gray-400 <?= $photoUrl ? 'hidden' : '' ?>"></i>
                                    </div>
                                    <button type="button" id="removePhoto" class="absolute -top-1 -right-1 bg-red-500 text-white w-8 h-8 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 transition-all opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Controls -->
                                <div class="flex-grow flex flex-col gap-3 w-full md:w-auto text-center md:text-left">
                                    <p class="text-xs text-gray-500 mb-1">Mettez à jour la photo en téléchargeant un fichier ou en utilisant votre caméra.</p>
                                    <div class="flex flex-wrap gap-3">
                                        <input type="file" id="photo" name="photo" accept="image/*" class="hidden">
                                        <button type="button" onclick="document.getElementById('photo').click()" 
                                                class="flex-1 md:flex-none px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-50 hover:border-<?= $colorClass ?>-400 transition flex items-center justify-center gap-2 shadow-sm">
                                            <i class="fas fa-upload text-<?= $colorClass ?>-600"></i> Sélectionner
                                        </button>
                                        <button type="button" id="startCamera" 
                                                class="flex-1 md:flex-none px-5 py-2.5 bg-<?= $colorClass ?>-600 text-white rounded-xl text-sm font-bold hover:bg-<?= $colorClass ?>-700 transition flex items-center justify-center gap-2 shadow-lg shadow-<?= $colorClass ?>-100">
                                            <i class="fas fa-video"></i> Caméra
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <div>
                            <label for="nom" class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
                            <input type="text" id="nom" name="nom" required value="<?= e($personnel['nom']) ?>"
                                   oninput="this.value = this.value.toUpperCase()"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <div>
                            <label for="prenom" class="block text-sm font-semibold text-gray-700 mb-2">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" required value="<?= e($personnel['prenom']) ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <div>
                            <label for="sexe" class="block text-sm font-semibold text-gray-700 mb-2">Sexe *</label>
                            <select id="sexe" name="sexe" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                                <option value="M" <?= ($personnel['sexe'] === 'M') ? 'selected' : '' ?>>Masculin</option>
                                <option value="F" <?= ($personnel['sexe'] === 'F') ? 'selected' : '' ?>>Féminin</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_naissance" class="block text-sm font-semibold text-gray-700 mb-2">Date de naissance *</label>
                            <input type="date" id="date_naissance" name="date_naissance" required value="<?= e($personnel['date_naissance']) ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <div>
                            <label for="lieu_naissance" class="block text-sm font-semibold text-gray-700 mb-2">Lieu de naissance *</label>
                            <input type="text" id="lieu_naissance" name="lieu_naissance" required value="<?= e($personnel['lieu_naissance'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <div>
                            <label for="situation_matrimoniale" class="block text-sm font-semibold text-gray-700 mb-2">Situation matrimoniale</label>
                            <select id="situation_matrimoniale" name="situation_matrimoniale"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                                <option value="celibataire" <?= (($personnel['situation_matrimoniale'] ?? '') === 'celibataire') ? 'selected' : '' ?>>Célibataire</option>
                                <option value="marie" <?= (($personnel['situation_matrimoniale'] ?? '') === 'marie') ? 'selected' : '' ?>>Marié(e)</option>
                                <option value="divorce" <?= (($personnel['situation_matrimoniale'] ?? '') === 'divorce') ? 'selected' : '' ?>>Divorcé(e)</option>
                                <option value="veuf" <?= (($personnel['situation_matrimoniale'] ?? '') === 'veuf') ? 'selected' : '' ?>>Veuf/Veuve</option>
                            </select>
                        </div>

                        <div>
                            <label for="cin" class="block text-sm font-semibold text-gray-700 mb-2">Numéro CIN *</label>
                            <input type="text" id="cin" name="cin" required maxlength="12" value="<?= e($personnel['cin'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition" placeholder="12 Chiffres">
                        </div>

                        <div>
                            <label for="numero_cnaps" class="block text-sm font-semibold text-gray-700 mb-2">N° CNAPS</label>
                            <input type="text" id="numero_cnaps" name="numero_cnaps" value="<?= e($personnel['numero_cnaps'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <div>
                            <label for="iban" class="block text-sm font-semibold text-gray-700 mb-2">RIB / Compte Bancaire</label>
                            <input type="text" id="iban" name="iban" value="<?= e($personnel['iban'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <div>
                            <label for="diplome" class="block text-sm font-semibold text-gray-700 mb-2">Diplôme <?= $isEnseignant ? '*' : '' ?></label>
                            <?php if ($isEnseignant): ?>
                            <select id="diplome" name="diplome" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm transition">
                                <option value="">Sélectionner...</option>
                                <?php 
                                    $diplomes = ['BAC+2', 'Licence', 'Maitrise', 'CAPEN', 'Doctorat'];
                                    foreach($diplomes as $d):
                                ?>
                                    <option value="<?= $d ?>" <?= (($personnel['diplome'] ?? '') === $d) ? 'selected' : '' ?>><?= $d ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php else: ?>
                            <input type="text" id="diplome" name="diplome" value="<?= e($personnel['diplome'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 shadow-sm transition">
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="annee_obtention_diplome" class="block text-sm font-semibold text-gray-700 mb-2">Année d'obtention</label>
                            <input type="number" id="annee_obtention_diplome" name="annee_obtention_diplome" min="1950" max="<?= date('Y') ?>" 
                                   value="<?= e($personnel['annee_obtention_diplome'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <div>
                            <label for="nb_enfants" class="block text-sm font-semibold text-gray-700 mb-2">Nombre d'enfants</label>
                            <input type="number" id="nb_enfants" name="nb_enfants" min="0" value="<?= e($personnel['nb_enfants'] ?? 0) ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: CONTRAT -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-contract mr-3 text-<?= $colorClass ?>-600"></i>
                            Contrat et Affectation
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="date_embauche" class="block text-sm font-semibold text-gray-700 mb-2">Date d'embauche *</label>
                            <input type="date" id="date_embauche" name="date_embauche" value="<?= e($personnel['date_embauche'] ?? '') ?>" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <div>
                            <label for="type_contrat" class="block text-sm font-semibold text-gray-700 mb-2">Type de contrat</label>
                            <select id="type_contrat" name="type_contrat"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                                <option value="cdi" <?= (($personnel['type_contrat'] ?? '') === 'cdi') ? 'selected' : '' ?>>CDI</option>
                                <option value="cdd" <?= (($personnel['type_contrat'] ?? '') === 'cdd') ? 'selected' : '' ?>>CDD</option>
                                <option value="stage" <?= (($personnel['type_contrat'] ?? '') === 'stage') ? 'selected' : '' ?>>Stage</option>
                                <option value="prestataire" <?= (($personnel['type_contrat'] ?? '') === 'prestataire') ? 'selected' : '' ?>>Prestataire</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_fin_contrat" class="block text-sm font-semibold text-gray-700 mb-2">Fin de contrat (si CDD)</label>
                            <input type="date" id="date_fin_contrat" name="date_fin_contrat" value="<?= e($personnel['date_fin_contrat'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <?php if ($isEnseignant): ?>
                        <div class="md:col-span-2">
                            <label for="specialite" class="block text-sm font-semibold text-gray-700 mb-2">Spécialité(s) *</label>
                            <div class="relative">
                                <input type="text" id="specialite_input" autocomplete="off" placeholder="Ajouter une spécialité..."
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm transition">
                                <input type="hidden" id="specialite" name="specialite" value="<?= e($personnel['specialite'] ?? '') ?>">
                                <div id="specialite_suggestions" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-48 overflow-y-auto"></div>
                                <div id="specialite_tags" class="mt-3 flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                        <div>
                            <label for="grade" class="block text-sm font-semibold text-gray-700 mb-2">Grade</label>
                            <input type="text" id="grade" name="grade" value="<?= e($personnel['grade'] ?? '') ?>" placeholder="Ex: Principal, Senior..."
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm transition">
                        </div>
                        <?php else: ?>
                        <div class="md:col-span-3">
                            <label for="poste" class="block text-sm font-semibold text-gray-700 mb-2">Poste Administratif *</label>
                            <select id="poste" name="poste_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 shadow-sm transition">
                                <option value="">Sélectionner un poste...</option>
                                <?php if (!empty($postes)): ?>
                                    <?php foreach ($postes as $poste): ?>
                                        <option value="<?= $poste['id'] ?>" <?= (isset($personnel['poste_id']) && $personnel['poste_id'] == $poste['id']) ? 'selected' : '' ?>>
                                            <?= e($poste['libelle']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SECTION 3: CONTACT -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-address-book mr-3 text-<?= $colorClass ?>-600"></i>
                            Informations de Contact
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="telephone" class="block text-sm font-semibold text-gray-700 mb-2">Téléphone *</label>
                            <input type="tel" id="telephone" name="telephone" required pattern="[0-9]{10}" maxlength="10" 
                                   value="<?= e($personnel['telephone'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                            <p class="text-xs text-gray-500 mt-1">10 chiffres requis</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" value="<?= e($personnel['email'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>

                        <div>
                            <label for="adresse" class="block text-sm font-semibold text-gray-700 mb-2">Adresse complète *</label>
                            <input type="text" id="adresse" name="adresse" required value="<?= e($personnel['adresse'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 shadow-sm transition">
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: CONTACT D'URGENCE -->
                <div class="bg-red-50 p-6 rounded-2xl border border-red-100">
                    <div class="border-b border-red-200 pb-3 mb-6">
                        <h3 class="text-xl font-bold text-red-800 flex items-center">
                            <i class="fas fa-ambulance mr-3"></i>
                            Contact d'urgence (Obligatoire)
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="urgence_nom" class="block text-sm font-semibold text-red-700 mb-2">Nom complet *</label>
                            <input type="text" id="urgence_nom" name="urgence_nom" required value="<?= e($personnel['urgence_nom'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500 shadow-sm transition">
                        </div>
                        <div>
                            <label for="urgence_telephone" class="block text-sm font-semibold text-red-700 mb-2">Téléphone *</label>
                            <input type="tel" id="urgence_telephone" name="urgence_telephone" required 
                                   pattern="[0-9]{10}" maxlength="10"
                                   value="<?= e($personnel['urgence_telephone'] ?? '') ?>"
                                   class="w-full px-4 py-2.5 border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500 shadow-sm transition">
                            <p class="text-xs text-red-600 mt-1">10 chiffres requis</p>
                        </div>
                        <div>
                            <label for="urgence_lien" class="block text-sm font-semibold text-red-700 mb-2">Lien de parenté *</label>
                            <input type="text" id="urgence_lien" name="urgence_lien" required value="<?= e($personnel['urgence_lien'] ?? '') ?>"
                                   placeholder="Ex: Frère, Épouse"
                                   class="w-full px-4 py-2.5 border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500 shadow-sm transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-10 mt-10 border-t border-gray-100">
                <a href="<?= url('personnel/details/' . $personnel['id']) ?>" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-6 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-<?= $colorClass ?>-600 hover:bg-<?= $colorClass ?>-700 text-white font-bold py-4 px-6 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-<?= $colorClass ?>-100">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer les modifications</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Camera Modal (Inchangé mais utile) -->
<div id="cameraModal" class="fixed inset-0 z-[100] hidden bg-black bg-opacity-75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-camera text-<?= $colorClass ?>-600"></i>
                Prendre une photo
            </h3>
            <button type="button" id="closeCamera" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 text-gray-500 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 bg-gray-900 relative min-h-[300px] flex items-center justify-center">
            <video id="video" class="w-full h-auto rounded-xl shadow-lg" autoplay playsinline></video>
            <canvas id="canvas" class="hidden"></canvas>
            <div id="cameraLoading" class="absolute inset-0 flex flex-col items-center justify-center text-white bg-gray-900 bg-opacity-50">
                <div class="w-12 h-12 border-4 border-<?= $colorClass ?>-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <p class="font-medium">Initialisation...</p>
            </div>
        </div>
        <div class="p-6 bg-gray-50 flex flex-col items-center gap-4">
            <button type="button" id="captureBtn" class="group relative bg-<?= $colorClass ?>-600 text-white p-6 rounded-full hover:bg-<?= $colorClass ?>-700 transition-all transform hover:scale-110 shadow-xl">
                <i class="fas fa-circle text-3xl"></i>
            </button>
            <p class="text-xs text-gray-500">Cliquez pour capturer</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- GESTION DE LA PHOTO ET CAMÉRA ---
    const startCameraBtn = document.getElementById('startCamera');
    const cameraModal = document.getElementById('cameraModal');
    const closeCameraBtn = document.getElementById('closeCamera');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const cameraLoading = document.getElementById('cameraLoading');
    const photoInput = document.getElementById('photo');
    const previewImg = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');
    const removePhotoBtn = document.getElementById('removePhoto');
    
    let stream = null;

    function updatePreview(src) {
        previewImg.src = src;
        previewImg.classList.remove('hidden');
        photoPlaceholder.classList.add('hidden');
    }

    startCameraBtn.addEventListener('click', async () => {
        cameraModal.classList.remove('hidden');
        cameraLoading.classList.remove('hidden');
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }, 
                audio: false 
            });
            video.srcObject = stream;
            video.onloadedmetadata = () => { cameraLoading.classList.add('hidden'); video.play(); };
        } catch (err) {
            alert("Accès caméra refusé.");
            cameraModal.classList.add('hidden');
        }
    });

    function stopCamera() {
        if (stream) { stream.getTracks().forEach(track => track.stop()); video.srcObject = null; }
        cameraModal.classList.add('hidden');
    }

    closeCameraBtn.addEventListener('click', stopCamera);
    
    captureBtn.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        context.translate(canvas.width, 0); context.scale(-1, 1);
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob((blob) => {
            const file = new File([blob], "photo_camera.jpg", { type: "image/jpeg" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            photoInput.files = dataTransfer.files;
            const reader = new FileReader();
            reader.onload = (e) => updatePreview(e.target.result);
            reader.readAsDataURL(file);
            stopCamera();
        }, 'image/jpeg', 0.9);
    });

    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => updatePreview(e.target.result);
            reader.readAsDataURL(this.files[0]);
        }
    });

    removePhotoBtn.addEventListener('click', () => {
        photoInput.value = '';
        previewImg.src = '';
        previewImg.classList.add('hidden');
        photoPlaceholder.classList.remove('hidden');
    });

    // --- AUTRES SCRIPTS ---
    const nomField = document.getElementById('nom');
    if (nomField) nomField.addEventListener('input', function() { this.value = this.value.toUpperCase(); });
    
    const prenomField = document.getElementById('prenom');
    if (prenomField) prenomField.addEventListener('input', function() {
        this.value = this.value.toLowerCase().replace(/(?:^|[\s-])\w/g, match => match.toUpperCase());
    });
    
    // Téléphone : uniquement chiffres
    ['telephone', 'urgence_telephone'].forEach(id => {
        const field = document.getElementById(id);
        if (field) {
            field.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });

    // === SYSTÈME DE TAGS POUR SPÉCIALITÉS ===
    const specialiteInput = document.getElementById('specialite_input');
    const specialiteHidden = document.getElementById('specialite');
    const specialiteSuggestions = document.getElementById('specialite_suggestions');
    const specialiteTags = document.getElementById('specialite_tags');
    
    if (specialiteInput && specialiteHidden && specialiteTags) {
        const currentSpecialitesString = specialiteHidden.value;
        let selectedSpecialites = currentSpecialitesString ? currentSpecialitesString.split(',').map(s => s.trim()).filter(s => s !== "") : [];
        
        const possibleSpecialites = ['Mathématiques', 'Physique', 'Chimie', 'Biologie', 'SVT', 'Français', 'Anglais', 'Espagnol', 'Allemand', 'Histoire', 'Géographie', 'Philosophie', 'Économie', 'Informatique', 'EPS', 'Arts Plastiques', 'Musique', 'Technologie'];
        
        function updateHiddenField() { specialiteHidden.value = selectedSpecialites.join(', '); }
        function addTag(specialite) {
            if (!selectedSpecialites.includes(specialite)) { selectedSpecialites.push(specialite); renderTags(); updateHiddenField(); }
            specialiteInput.value = ''; specialiteSuggestions.classList.add('hidden');
        }
        function removeTag(specialite) { selectedSpecialites = selectedSpecialites.filter(s => s !== specialite); renderTags(); updateHiddenField(); }
        function renderTags() {
            specialiteTags.innerHTML = '';
            selectedSpecialites.forEach(specialite => {
                const tag = document.createElement('div');
                tag.className = 'inline-flex items-center gap-1 bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-medium';
                tag.innerHTML = `<span>${specialite}</span><button type="button" class="hover:bg-indigo-200 rounded-full p-1"><i class="fas fa-times text-xs"></i></button>`;
                tag.querySelector('button').addEventListener('click', () => removeTag(specialite));
                specialiteTags.appendChild(tag);
            });
        }
        
        renderTags(); // Initial render

        specialiteInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            if (query.length > 0) {
                const filtered = possibleSpecialites.filter(s => s.toLowerCase().includes(query) && !selectedSpecialites.includes(s));
                if (filtered.length > 0) {
                    specialiteSuggestions.innerHTML = '';
                    filtered.forEach(s => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-2 hover:bg-indigo-50 cursor-pointer text-sm';
                        div.textContent = s;
                        div.onclick = () => addTag(s);
                        specialiteSuggestions.appendChild(div);
                    });
                    specialiteSuggestions.classList.remove('hidden');
                } else { specialiteSuggestions.classList.add('hidden'); }
            } else { specialiteSuggestions.classList.add('hidden'); }
        });
        
        document.addEventListener('click', (e) => {
            if (!specialiteInput.contains(e.target) && !specialiteSuggestions.contains(e.target)) specialiteSuggestions.classList.add('hidden');
        });
    }
});
</script>
