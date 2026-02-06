<?php
$isEnseignant = ($type === 'enseignant');
$colorClass = $isEnseignant ? 'indigo' : 'teal';
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-<?= $isEnseignant ? 'chalkboard-teacher' : 'users-cog' ?> text-<?= $colorClass ?>-600 mr-2"></i>
            Nouveau <?= $isEnseignant ? 'Enseignant' : 'Personnel Administratif' ?>
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Étape 2 sur 2 : Informations du personnel</p>
    </div>

    <!-- Progression -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="h-2 bg-<?= $colorClass ?>-600 rounded"></div>
            </div>
            <div class="flex-1 ml-2">
                <div class="h-2 bg-<?= $colorClass ?>-600 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('personnel/nouveau?etape=2') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-md" role="alert">
                    <p class="font-bold">Erreur</p>
                    <p><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-md" role="alert">
                    <p class="font-bold">Succès</p>
                    <p><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
                </div>
            <?php endif; ?>
            
            
            <div class="space-y-8">
                <!-- SECTION 1: INFORMATION PERSONNEL -->
                <div class="border-b border-gray-200 pb-2 mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-<?= $colorClass ?>-600"></i>
                        Information personnel
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <!-- Matricule (Gauche) -->
                    <div class="md:col-span-1">
                        <label for="matricule" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2 text-gray-500"></i>Matricule *
                        </label>
                        <input type="text" id="matricule" name="matricule" value="<?= $matricule ?>" readonly
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 cursor-not-allowed font-mono font-bold text-<?= $colorClass ?>-700 text-lg shadow-sm">
                    </div>

                    <!-- Photo (Droite - Prend 2 colonnes pour être plus large ou juste mieux aligné) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-camera mr-2 text-gray-500"></i>Photo du personnel
                        </label>
                        
                        <div class="flex flex-col md:flex-row items-center gap-6 bg-gray-50 p-4 rounded-2xl border border-dashed border-gray-300">
                            <!-- Preview Circle -->
                            <div class="relative group">
                                <div class="w-24 h-24 md:w-28 md:h-28 rounded-full overflow-hidden border-4 border-white shadow-md bg-gray-200 flex items-center justify-center">
                                    <img id="photoPreview" src="<?= url('public/img/default-avatar.png') ?>" alt="Aperçu" class="w-full h-full object-cover hidden">
                                    <i id="photoPlaceholder" class="fas fa-user text-4xl text-gray-400"></i>
                                </div>
                                <button type="button" id="removePhoto" class="absolute -top-1 -right-1 bg-red-500 text-white w-7 h-7 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 transition-all opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>

                            <!-- Controls -->
                            <div class="flex-grow flex flex-col gap-3 w-full md:w-auto">
                                <p class="text-xs text-gray-500 mb-1 hidden md:block">Téléchargez une photo ou utilisez votre caméra pour une capture instantanée.</p>
                                <div class="flex flex-wrap gap-2">
                                    <input type="file" id="photo" name="photo" accept="image/*" class="hidden">
                                    <button type="button" onclick="document.getElementById('photo').click()" 
                                            class="flex-1 md:flex-none px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 hover:border-<?= $colorClass ?>-400 transition flex items-center justify-center gap-2 shadow-sm">
                                        <i class="fas fa-upload text-<?= $colorClass ?>-600"></i> Sélectionner
                                    </button>
                                    <button type="button" id="startCamera" 
                                            class="flex-1 md:flex-none px-4 py-2.5 bg-<?= $colorClass ?>-600 text-white rounded-xl text-sm font-medium hover:bg-<?= $colorClass ?>-700 transition flex items-center justify-center gap-2 shadow-lg shadow-<?= $colorClass ?>-200">
                                        <i class="fas fa-video"></i> Caméra
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <!-- Nom, Prénom, Sexe -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-gray-500"></i>Nom *
                        </label>
                        <input type="text" id="nom" name="nom" required
                               oninput="this.value = this.value.toUpperCase()"
                               style="text-transform: uppercase"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-<?= $colorClass ?>-500 focus:border-transparent shadow-sm">
                    </div>

                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom *
                        </label>
                        <input type="text" id="prenom" name="prenom" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>

                    <div>
                        <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-venus-mars mr-2 text-gray-500"></i>Sexe *
                        </label>
                        <select id="sexe" name="sexe" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                            <option value="">Sélectionner</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>

                    <!-- Date naissance, Lieu naissance, Matrimoniale -->
                    <div>
                        <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-birthday-cake mr-2 text-gray-500"></i>Date de naissance *
                        </label>
                        <input type="date" id="date_naissance" name="date_naissance" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>

                    <div>
                        <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>Lieu de naissance *
                        </label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>

                    <div>
                        <label for="situation_matrimoniale" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-heart mr-2 text-gray-500"></i>Situation matrimoniale *
                        </label>
                        <select id="situation_matrimoniale" name="situation_matrimoniale" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                            <option value="">Sélectionner</option>
                            <option value="celibataire">Célibataire</option>
                            <option value="marie">Marié(e)</option>
                            <option value="divorce">Divorcé(e)</option>
                            <option value="veuf">Veuf/Veuve</option>
                        </select>
                    </div>

                    <!-- CIN, CNAPS, RIB -->
                    <div>
                        <label for="cin" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card-alt mr-2 text-gray-500"></i>Numéro CIN *
                        </label>
                        <input type="text" id="cin" name="cin" required maxlength="12"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500"
                               placeholder="12 Chiffres">
                    </div>

                    <div>
                        <label for="numero_cnaps" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-shield-alt mr-2 text-gray-500"></i>N° CNAPS
                        </label>
                        <input type="text" id="numero_cnaps" name="numero_cnaps"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>

                    <div>
                        <label for="iban" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-university mr-2 text-gray-500"></i>RIB / Compte Bancaire
                        </label>
                        <input type="text" id="iban" name="iban"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>

                    <!-- Diplome, Annee obtention, Specialité (if teacher) -->
                    <div>
                        <label for="diplome" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-certificate mr-2 text-gray-500"></i>Diplôme <?= $isEnseignant ? '*' : '' ?>
                        </label>
                        <?php if ($isEnseignant): ?>
                        <select id="diplome" name="diplome" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">Sélectionner...</option>
                            <option value="BAC+2">BAC+2</option>
                            <option value="Licence">Licence</option>
                            <option value="Maitrise">Maitrise</option>
                            <option value="CAPEN">CAPEN</option>
                            <option value="Doctorat">Doctorat</option>
                        </select>
                        <?php else: ?>
                        <input type="text" id="diplome" name="diplome"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="annee_obtention_diplome" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-graduation-cap mr-2 text-gray-500"></i>Année d'obtention *
                        </label>
                        <input type="number" id="annee_obtention_diplome" name="annee_obtention_diplome" min="1950" max="<?= date('Y') ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>

                    <div>
                        <label for="nb_enfants" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-child mr-2 text-gray-500"></i>Nombre d'enfants *
                        </label>
                        <input type="number" id="nb_enfants" name="nb_enfants" min="0" value="0" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>
                </div>

                <!-- SECTION 2: CONTRAT -->
                <div class="border-b border-gray-200 pb-2 mb-4 mt-8">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-file-contract mr-2 text-<?= $colorClass ?>-600"></i>
                        Contrat et Affectation
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="date_embauche" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-check mr-2 text-gray-500"></i>Date d'embauche *
                        </label>
                        <input type="date" id="date_embauche" name="date_embauche" value="<?= date('Y-m-d') ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>

                    <div>
                        <label for="type_contrat" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-handshake mr-2 text-gray-500"></i>Type de contrat *
                        </label>
                        <select id="type_contrat" name="type_contrat" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                            <option value="">Sélectionner...</option>
                            <option value="cdi">CDI</option>
                            <option value="cdd">CDD</option>
                            <option value="stage">Stage</option>
                            <option value="prestataire">Prestataire</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_fin_contrat" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-times mr-2 text-gray-500"></i>Fin de contrat (si CDD)
                        </label>
                        <input type="date" id="date_fin_contrat" name="date_fin_contrat"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>

                    <?php if ($isEnseignant): ?>
                    <!-- Spécialités & Grade -->
                    <div class="md:col-span-2">
                        <label for="specialite" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-book mr-2 text-gray-500"></i>Spécialité(s) *
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="specialite_input" 
                                   autocomplete="off"
                                   placeholder="Tapez pour rechercher..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <input type="hidden" id="specialite" name="specialite">
                            <div id="specialite_suggestions" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto"></div>
                            <div id="specialite_tags" class="mt-2 flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    <div>
                        <label for="grade" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-layer-group mr-2 text-gray-500"></i>Grade *
                        </label>
                        <input type="text" id="grade" name="grade" placeholder="Ex: Principal, Senior..." required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <?php else: ?>
                    <div class="md:col-span-3">
                        <label for="poste" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase mr-2 text-gray-500"></i>Poste Administratif *
                        </label>
                        <select id="poste" name="poste_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                            <option value="">Sélectionner un poste...</option>
                            <option value="Comptable">Comptable</option>
                            <option value="Ressource Humaine">Ressource Humaine</option>
                            <option value="Directeur">Directeur</option>
                            <option value="Gardien">Gardien</option>
                            <option value="Secrétaire">Secrétaire</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- SECTION 3: CONTACT -->
                <div class="border-b border-gray-200 pb-2 mb-4 mt-8">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-address-book mr-2 text-<?= $colorClass ?>-600"></i>
                        Contact
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-gray-500"></i>Téléphone *
                        </label>
                        <input type="tel" id="telephone" name="telephone" required
                               pattern="[0-9]{10}" maxlength="10" placeholder="0340156789"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                        <p class="text-xs text-gray-500 mt-1">10 chiffres requis</p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-gray-500"></i>Email
                        </label>
                        <input type="email" id="email" name="email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>

                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-home mr-2 text-gray-500"></i>Adresse complète *
                        </label>
                        <input type="text" id="adresse" name="adresse" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $colorClass ?>-500">
                    </div>
                </div>

                <!-- SECTION 4: CONTACT D'URGENCE -->
                <div class="border-b border-gray-200 pb-2 mb-4 mt-8">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-ambulance mr-2 text-red-600"></i>
                        Contact d'urgence (Obligatoire)
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="urgence_nom" class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
                        <input type="text" id="urgence_nom" name="urgence_nom" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="urgence_telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                        <input type="tel" id="urgence_telephone" name="urgence_telephone" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="urgence_lien" class="block text-sm font-medium text-gray-700 mb-2">Lien de parenté *</label>
                        <input type="text" id="urgence_lien" name="urgence_lien" required
                               placeholder="Ex: Frère, Épouse"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
            </div>
            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t">
                <a href="<?= url('personnel/nouveau?etape=1') ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-<?= $colorClass ?>-600 hover:bg-<?= $colorClass ?>-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Camera Modal -->
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
                <p class="font-medium">Initialisation de la caméra...</p>
            </div>
        </div>
        <div class="p-6 bg-gray-50 flex flex-col items-center gap-4">
            <button type="button" id="captureBtn" class="group relative bg-<?= $colorClass ?>-600 text-white p-5 rounded-full hover:bg-<?= $colorClass ?>-700 transition-all transform hover:scale-110 shadow-xl">
                <i class="fas fa-circle text-3xl"></i>
                <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-<?= $colorClass ?>-600 font-bold text-sm opacity-0 group-hover:opacity-100 transition-opacity">CAPTURER</span>
            </button>
            <p class="text-xs text-gray-500  mt-6">Positionnez le visage au centre du cadre</p>
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
    const previewContainer = document.getElementById('photoPreviewContainer');
    const previewImg = document.getElementById('photoPreview');
    const removePhotoBtn = document.getElementById('removePhoto');
    
    let stream = null;

    const photoPlaceholder = document.getElementById('photoPlaceholder');

    // Mettre à jour l'affichage de l'aperçu
    function updatePreview(src) {
        previewImg.src = src;
        previewImg.classList.remove('hidden');
        photoPlaceholder.classList.add('hidden');
    }

    // Ouvrir la caméra
    startCameraBtn.addEventListener('click', async () => {
        cameraModal.classList.remove('hidden');
        cameraLoading.classList.remove('hidden');
        
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }, 
                audio: false 
            });
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                cameraLoading.classList.add('hidden');
                video.play();
            };
        } catch (err) {
            console.error("Erreur caméra:", err);
            alert("Impossible d'accéder à la caméra. Vérifiez les permissions.");
            cameraModal.classList.add('hidden');
        }
    });

    // Fermer la caméra
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
        }
        cameraModal.classList.add('hidden');
    }

    closeCameraBtn.addEventListener('click', stopCamera);
    
    // Capturer la photo
    captureBtn.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Miroir horizontal si c'est la caméra frontale
        context.translate(canvas.width, 0);
        context.scale(-1, 1);
        
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

    // Gestion de l'input file classique
    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => updatePreview(e.target.result);
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Supprimer la photo
    removePhotoBtn.addEventListener('click', () => {
        photoInput.value = '';
        previewImg.src = '';
        previewImg.classList.add('hidden');
        photoPlaceholder.classList.remove('hidden');
    });

    // --- AUTRES SCRIPTS EXISTANTS ---
    // Nom en majuscules
    const nomField = document.getElementById('nom');
    if (nomField) {
        nomField.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Prénom en title case
    const prenomField = document.getElementById('prenom');
    if (prenomField) {
        prenomField.addEventListener('input', function() {
            this.value = this.value.toLowerCase().replace(/(?:^|[\s-])\w/g, match => match.toUpperCase());
        });
    }
    
    // Adresse en title case
    const adresseField = document.getElementById('adresse');
    if (adresseField) {
        adresseField.addEventListener('blur', function() {
            this.value = this.value.toLowerCase().replace(/(?:^|[\s-])\w/g, match => match.toUpperCase());
        });
    }
    
    // Téléphone : uniquement chiffres
    const telephoneField = document.getElementById('telephone');
    if (telephoneField) {
        telephoneField.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
    
    // === SYSTÈME DE TAGS POUR SPÉCIALITÉS ===
    const specialiteInput = document.getElementById('specialite_input');
    const specialiteHidden = document.getElementById('specialite');
    const specialiteSuggestions = document.getElementById('specialite_suggestions');
    const specialiteTags = document.getElementById('specialite_tags');
    
    if (specialiteInput && specialiteHidden && specialiteSuggestions && specialiteTags) {
        const specialites = ['Mathématiques', 'Physique', 'Chimie', 'Biologie', 'SVT', 'Français', 'Anglais', 'Espagnol', 'Allemand', 'Histoire', 'Géographie', 'Philosophie', 'Économie', 'Informatique', 'EPS', 'Arts Plastiques', 'Musique', 'Technologie'];
        let selectedSpecialites = [];
        
        function updateHiddenField() { specialiteHidden.value = selectedSpecialites.join(', '); }
        function addTag(specialite) {
            if (!selectedSpecialites.includes(specialite)) {
                selectedSpecialites.push(specialite);
                renderTags();
                updateHiddenField();
            }
            specialiteInput.value = '';
            specialiteSuggestions.classList.add('hidden');
        }
        function removeTag(specialite) {
            selectedSpecialites = selectedSpecialites.filter(s => s !== specialite);
            renderTags();
            updateHiddenField();
        }
        function renderTags() {
            specialiteTags.innerHTML = '';
            selectedSpecialites.forEach(specialite => {
                const tag = document.createElement('div');
                tag.className = 'inline-flex items-center gap-1 bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm';
                tag.innerHTML = `<span>${specialite}</span><button type="button" class="hover:bg-indigo-200 rounded-full p-1"><i class="fas fa-times text-xs"></i></button>`;
                tag.querySelector('button').addEventListener('click', () => removeTag(specialite));
                specialiteTags.appendChild(tag);
            });
        }
        function showSuggestions(query) {
            const filtered = specialites.filter(s => s.toLowerCase().includes(query.toLowerCase()) && !selectedSpecialites.includes(s));
            if (filtered.length === 0) { specialiteSuggestions.classList.add('hidden'); return; }
            specialiteSuggestions.innerHTML = '';
            filtered.forEach(specialite => {
                const item = document.createElement('div');
                item.className = 'px-4 py-2 hover:bg-indigo-50 cursor-pointer';
                item.textContent = specialite;
                item.addEventListener('click', () => addTag(specialite));
                specialiteSuggestions.appendChild(item);
            });
            specialiteSuggestions.classList.remove('hidden');
        }
        
        specialiteInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length > 0) showSuggestions(query);
            else specialiteSuggestions.classList.add('hidden');
        });
        
        document.addEventListener('click', function(e) {
            if (!specialiteInput.contains(e.target) && !specialiteSuggestions.contains(e.target)) {
                specialiteSuggestions.classList.add('hidden');
            }
        });
    }
});
</script>

