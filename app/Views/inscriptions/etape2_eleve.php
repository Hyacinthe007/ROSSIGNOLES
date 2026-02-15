<div class="p-0 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-<?= $type === 'nouvelle' ? 'user-plus' : 'redo' ?> text-<?= $type === 'nouvelle' ? 'blue' : 'green' ?>-600 mr-2"></i>
            <?= $type === 'nouvelle' ? 'Nouvelle Inscription' : 'Réinscription' ?>
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Étape 2 sur 7 : <?= $type === 'nouvelle' ? 'Créer le dossier élève' : 'Sélectionner l\'élève' ?></p>
    </div>

    <div class="mb-8">
        <?php 
        $stepNames = [1 => 'Type', 2 => 'Élève', 3 => 'Classe', 4 => 'Documents', 5 => 'Articles', 6 => 'Paiement', 7 => 'Confirmation'];
        ?>
        <div class="flex items-center justify-between mb-2">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?> text-center">
                    <span class="text-[10px] md:text-xs font-semibold <?= $i <= 2 ? 'text-blue-600' : 'text-gray-400' ?>">
                        Étape <?= $i ?>: <?= $stepNames[$i] ?>
                    </span>
                </div>
            <?php endfor; ?>
        </div>
        <div class="flex items-center justify-between">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?>">
                    <div class="h-2 <?= $i <= 2 ? 'bg-blue-600' : 'bg-gray-200' ?> rounded"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl p-6 md:p-8">
        <form method="POST" id="inscriptionForm" action="<?= url('inscriptions/nouveau?etape=2') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-bold text-red-800">Erreur</p>
                            <p class="text-red-700"><?= e($_SESSION['error']) ?></p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if ($type === 'nouvelle'): ?>
                <!-- Formulaire création élève -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user-graduate text-blue-600 mr-2"></i>
                        Informations de l'élève
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        Veuillez remplir les informations concernant l'élève
                    </p>
                
                <div class="space-y-6">
                    <!-- Ligne 1 : Matricule -->
                    <div>
                        <label for="matricule" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2 text-gray-500"></i>Matricule *
                        </label>
                        <div class="relative">
                            <input type="text" id="matricule" name="matricule" readonly
                                   value="<?= $nextMatricule ?? 'EL-' . date('y') . (date('y')+1) . '-XXXX' ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-800 font-mono text-lg cursor-not-allowed">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Ce numéro sera attribué automatiquement lors de l'enregistrement
                        </p>
                    </div>

                    <!-- Ligne 2 : Nom, Prénom, Sexe -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Nom -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-gray-500"></i>Nom *
                            </label>
                            <input type="text" id="nom" name="nom" required
                                   value="<?= e($savedData['nom'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Prénom -->
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-gray-500"></i>Prénom *
                            </label>
                            <input type="text" id="prenom" name="prenom" required
                                   value="<?= e($savedData['prenom'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Sexe -->
                        <div>
                            <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-venus-mars mr-2 text-gray-500"></i>Sexe *
                            </label>
                            <select id="sexe" name="sexe" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="M" <?= (isset($savedData['sexe']) && $savedData['sexe'] === 'M') ? 'selected' : '' ?>>Masculin</option>
                                <option value="F" <?= (isset($savedData['sexe']) && $savedData['sexe'] === 'F') ? 'selected' : '' ?>>Féminin</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ligne 3 : Date et Lieu de naissance -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date de naissance -->
                        <div>
                            <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-birthday-cake mr-2 text-gray-500"></i>Date de naissance *
                            </label>
                            <input type="date" id="date_naissance" name="date_naissance" required
                                   value="<?= e($savedData['date_naissance'] ?? '') ?>"
                                   max="<?= date('Y-m-d', strtotime('-3 years')) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   onchange="validateAge(this)">
                        </div>

                        <!-- Lieu de naissance -->
                        <div>
                            <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>Lieu de naissance *
                            </label>
                            <input type="text" id="lieu_naissance" name="lieu_naissance" required
                                   value="<?= e($savedData['lieu_naissance'] ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Photo -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-camera mr-2 text-gray-500"></i>Photo de l'élève
                        </label>
                        
                        <!-- Prévisualisation de la photo -->
                        <div id="photoPreview" class="mb-4 hidden">
                            <div class="relative inline-block">
                                <img id="previewImage" src="" alt="Photo" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300">
                                <button type="button" id="removePhoto" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                        
                        <?php if (isset($savedData['photo']) && $savedData['photo']): ?>
                            <div class="mb-2">
                                <span class="text-sm text-green-600"><i class="fas fa-check-circle mr-1"></i>Photo conservée</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex gap-2">
                            <!-- Bouton Webcam -->
                            <button type="button" id="openWebcam" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-camera"></i>
                                <span>Prendre une photo</span>
                            </button>
                            
                            <!-- Bouton Upload fichier -->
                            <label for="photo" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-upload"></i>
                                <span>Choisir un fichier</span>
                            </label>
                        </div>
                        
                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/jpg,image/png" class="hidden">
                        <input type="hidden" id="photoData" name="photo_data">
                        
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Formats acceptés : JPG, JPEG, PNG (max 2 Mo)
                        </p>
                    </div>
                    
                    <!-- Modal Webcam -->
                    <div id="webcamModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
                        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-800">
                                        <i class="fas fa-camera text-blue-600 mr-2"></i>
                                        Capture photo
                                    </h3>
                                    <button type="button" id="closeWebcam" class="text-gray-400 hover:text-gray-600 transition">
                                        <i class="fas fa-times text-2xl"></i>
                                    </button>
                                </div>
                                
                                <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                                    <video id="webcamVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                                    <canvas id="webcamCanvas" class="hidden"></canvas>
                                    
                                    <!-- Overlay de guidage -->
                                    <div class="absolute inset-0 pointer-events-none">
                                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 border-4 border-white border-dashed rounded-full opacity-30"></div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex gap-2">
                                    <button type="button" id="capturePhoto" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                                        <i class="fas fa-camera"></i>
                                        <span>Capturer</span>
                                    </button>
                                    <button type="button" id="cancelWebcam" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                                        <i class="fas fa-times"></i>
                                        <span>Annuler</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations Parent/Tuteur -->
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center mt-8">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    Informations Parent / Tuteur (Obligatoire)
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ces informations sont obligatoires pour finaliser l'inscription
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Ligne 1 : Nom, Prénom, Lien -->
                    <!-- Nom du parent -->
                    <div>
                        <label for="parent_nom" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-gray-500"></i>Nom du parent/tuteur *
                        </label>
                        <input type="text" id="parent_nom" name="parent_nom" required
                               value="<?= e($savedParentData['nom'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Prénom du parent -->
                    <div>
                        <label for="parent_prenom" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-gray-500"></i>Prénom du parent/tuteur *
                        </label>
                        <input type="text" id="parent_prenom" name="parent_prenom" required
                               value="<?= e($savedParentData['prenom'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Lien de parenté -->
                    <div>
                        <label for="parent_lien" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-link mr-2 text-gray-500"></i>Lien de parenté *
                        </label>
                        <select id="parent_lien" name="parent_lien" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sélectionner</option>
                            <?php 
                            $liens = ['Père', 'Mère', 'Tuteur', 'Tutrice', 'Grand-père', 'Grand-mère', 'Oncle', 'Tante', 'Autre'];
                            foreach ($liens as $lien): ?>
                                <option value="<?= $lien ?>" <?= (isset($savedParentData['lien_parente']) && $savedParentData['lien_parente'] === $lien) ? 'selected' : '' ?>><?= $lien ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Ligne 2 : Téléphone, Email, Profession -->
                    <!-- Téléphone parent -->
                    <div>
                        <label for="parent_telephone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-gray-500"></i>Téléphone *
                        </label>
                        <input type="tel" id="parent_telephone" name="parent_telephone" required
                               value="<?= e($savedParentData['telephone'] ?? '') ?>"
                               pattern="[0-9]{10}" maxlength="10" title="Le numéro doit contenir exactement 10 chiffres"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>

                    <!-- Email parent -->
                    <div>
                        <label for="parent_email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-gray-500"></i>Email
                        </label>
                        <input type="email" id="parent_email" name="parent_email"
                               value="<?= e($savedParentData['email'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Profession -->
                    <div>
                        <label for="parent_profession" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase mr-2 text-gray-500"></i>Profession
                        </label>
                        <input type="text" id="parent_profession" name="parent_profession"
                               value="<?= e($savedParentData['profession'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Ligne 3 : Adresse (Full width, Mandatory) -->
                    <div class="md:col-span-3">
                        <label for="parent_adresse" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>Adresse *
                        </label>
                        <input type="text" id="parent_adresse" name="parent_adresse" required
                               value="<?= e($savedParentData['adresse'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let formSubmitted = false;
                    const form = document.getElementById('inscriptionForm');
                    
                    // Mark form as submitted when submit button is clicked
                    form.addEventListener('submit', function() {
                        formSubmitted = true;
                    });
                    
                    // Warn user before leaving the page if form not submitted
                    window.addEventListener('beforeunload', function(e) {
                         if (!formSubmitted) {
                             const message = "Attention, vos données ne sont pas encore enregistrées. Si vous quittez cette page maintenant, les informations saisies seront perdues.";
                             e.returnValue = message; // Standard for most browsers
                             return message;
                         }
                    });

                    const inputs = [
                        { id: 'nom', mode: 'upper' },
                        { id: 'prenom', mode: 'capital' },
                        { id: 'parent_nom', mode: 'upper' },
                        { id: 'parent_prenom', mode: 'capital' }
                    ];

                    inputs.forEach(item => {
                        const el = document.getElementById(item.id);
                        if (el) {
                            el.addEventListener('input', function() {
                                const cursorStart = this.selectionStart;
                                const cursorEnd = this.selectionEnd;
                                let original = this.value;
                                let transformed = original;

                                if (item.mode === 'upper') {
                                    transformed = original.toUpperCase();
                                } else if (item.mode === 'capital') {
                                    // Capitalize first letter of each word (handles hyphens too if we use \b or regex)
                                    // Simple approach: lowercase everything then regex replace
                                    transformed = original.toLowerCase().replace(/(?:^|[\s-])\w/g, function(match) {
                                        return match.toUpperCase();
                                    });
                                }

                                if (original !== transformed) {
                                    this.value = transformed;
                                    // Restore cursor position
                                    this.setSelectionRange(cursorStart, cursorEnd);
                                }
                            });
                        }
                    });
                    
                    // === GESTION WEBCAM ===
                    const webcamModal = document.getElementById('webcamModal');
                    const webcamVideo = document.getElementById('webcamVideo');
                    const webcamCanvas = document.getElementById('webcamCanvas');
                    const openWebcamBtn = document.getElementById('openWebcam');
                    const closeWebcamBtn = document.getElementById('closeWebcam');
                    const cancelWebcamBtn = document.getElementById('cancelWebcam');
                    const capturePhotoBtn = document.getElementById('capturePhoto');
                    const photoInput = document.getElementById('photo');
                    const photoDataInput = document.getElementById('photoData');
                    const photoPreview = document.getElementById('photoPreview');
                    const previewImage = document.getElementById('previewImage');
                    const removePhotoBtn = document.getElementById('removePhoto');
                    
                    let stream = null;
                    
                    // Ouvrir la webcam
                    openWebcamBtn.addEventListener('click', async function() {
                        try {
                            stream = await navigator.mediaDevices.getUserMedia({ 
                                video: { 
                                    width: { ideal: 1280 },
                                    height: { ideal: 720 },
                                    facingMode: 'user'
                                } 
                            });
                            webcamVideo.srcObject = stream;
                            webcamModal.classList.remove('hidden');
                        } catch (error) {
                            alert('Impossible d\'accéder à la webcam. Veuillez vérifier les permissions.');
                            console.error('Erreur webcam:', error);
                        }
                    });
                    
                    // Fermer la webcam
                    function closeWebcam() {
                        if (stream) {
                            stream.getTracks().forEach(track => track.stop());
                            stream = null;
                        }
                        webcamModal.classList.add('hidden');
                    }
                    
                    closeWebcamBtn.addEventListener('click', closeWebcam);
                    cancelWebcamBtn.addEventListener('click', closeWebcam);
                    
                    // Capturer la photo
                    capturePhotoBtn.addEventListener('click', function() {
                        const context = webcamCanvas.getContext('2d');
                        webcamCanvas.width = webcamVideo.videoWidth;
                        webcamCanvas.height = webcamVideo.videoHeight;
                        context.drawImage(webcamVideo, 0, 0);
                        
                        // Convertir en blob puis en file
                        webcamCanvas.toBlob(function(blob) {
                            const file = new File([blob], 'photo_eleve.jpg', { type: 'image/jpeg' });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            photoInput.files = dataTransfer.files;
                            
                            // Afficher la prévisualisation
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImage.src = e.target.result;
                                photoPreview.classList.remove('hidden');
                                photoDataInput.value = e.target.result;
                            };
                            reader.readAsDataURL(blob);
                            
                            closeWebcam();
                        }, 'image/jpeg', 0.9);
                    });
                    
                    // Prévisualisation lors de l'upload de fichier
                    photoInput.addEventListener('change', function(e) {
                        if (e.target.files && e.target.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImage.src = e.target.result;
                                photoPreview.classList.remove('hidden');
                                photoDataInput.value = e.target.result;
                            };
                            reader.readAsDataURL(e.target.files[0]);
                        }
                    });
                    
                    // Supprimer la photo
                    removePhotoBtn.addEventListener('click', function() {
                        photoInput.value = '';
                        photoDataInput.value = '';
                        photoPreview.classList.add('hidden');
                        previewImage.src = '';
                    });
                });
                </script>

            <?php else: ?>
                <!-- Sélection élève existant avec recherche -->
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-check text-green-600 mr-2"></i>
                    Sélectionner l'élève à réinscrire
                </h3>
                
                <div>
                    <!-- Champ de recherche -->
                    <label for="search_eleve" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-2 text-gray-500"></i>Rechercher un élève
                    </label>
                    <div class="relative mb-2">
                        <input type="text" id="search_eleve" 
                               placeholder="Tapez le nom, prénom ou matricule..."
                               class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               autocomplete="off">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        La liste se filtre automatiquement au fur et à mesure que vous tapez
                    </p>

                    <!-- Liste des élèves (cachée par défaut) -->
                    <div id="eleves_list" class="hidden border border-gray-300 rounded-lg max-h-96 overflow-y-auto bg-white shadow-lg">
                        <div id="eleves_container">
                            <?php foreach ($eleves as $eleve): ?>
                                <div class="eleve-item p-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 transition"
                                     data-id="<?= $eleve['id'] ?>"
                                     data-matricule="<?= strtolower($eleve['matricule']) ?>"
                                     data-nom="<?= strtolower($eleve['nom']) ?>"
                                     data-prenom="<?= strtolower($eleve['prenom']) ?>"
                                     data-search="<?= strtolower($eleve['matricule'] . ' ' . $eleve['nom'] . ' ' . $eleve['prenom']) ?>">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                <?= e($eleve['nom']) ?> - <?= e($eleve['prenom']) ?>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <span class="inline-flex items-center">
                                                    <i class="fas fa-id-card mr-1 text-gray-400"></i>
                                                    <?= e($eleve['matricule']) ?>
                                                </span>
                                                <?php if (!empty($eleve['classe_actuelle'])): ?>
                                                    <span class="ml-3 inline-flex items-center">
                                                        <i class="fas fa-door-open mr-1 text-gray-400"></i>
                                                        <?= e($eleve['classe_actuelle']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div id="no_results" class="hidden p-6 text-center text-gray-500">
                            <i class="fas fa-search text-4xl mb-2"></i>
                            <p>Aucun élève trouvé</p>
                        </div>
                    </div>

                    <!-- Champ caché pour stocker l'ID sélectionné -->
                    <input type="hidden" id="eleve_id" name="eleve_id" required>
                    
                    <!-- Élève sélectionné -->
                    <div id="selected_eleve" class="hidden mt-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-800 font-semibold mb-1">Élève sélectionné :</p>
                                <p id="selected_eleve_info" class="text-gray-800 font-medium"></p>
                            </div>
                            <button type="button" id="clear_selection" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times-circle text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('search_eleve');
                    const elevesList = document.getElementById('eleves_list');
                    const elevesContainer = document.getElementById('eleves_container');
                    const noResults = document.getElementById('no_results');
                    const eleveIdInput = document.getElementById('eleve_id');
                    const selectedEleveDiv = document.getElementById('selected_eleve');
                    const selectedEleveInfo = document.getElementById('selected_eleve_info');
                    const clearButton = document.getElementById('clear_selection');
                    const eleveItems = document.querySelectorAll('.eleve-item');

                    // Afficher la liste quand on tape
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();
                        
                        if (searchTerm.length === 0) {
                            elevesList.classList.add('hidden');
                            return;
                        }

                        elevesList.classList.remove('hidden');
                        let visibleCount = 0;

                        eleveItems.forEach(item => {
                            const searchData = item.getAttribute('data-search');
                            
                            if (searchData.includes(searchTerm)) {
                                item.style.display = 'block';
                                visibleCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        // Afficher/masquer le message "Aucun résultat"
                        if (visibleCount === 0) {
                            elevesContainer.classList.add('hidden');
                            noResults.classList.remove('hidden');
                        } else {
                            elevesContainer.classList.remove('hidden');
                            noResults.classList.add('hidden');
                        }
                    });

                    // Sélectionner un élève
                    eleveItems.forEach(item => {
                        item.addEventListener('click', function() {
                            const id = this.getAttribute('data-id');
                            const nom = this.querySelector('p.font-semibold').textContent.trim();
                            const details = this.querySelector('p.text-sm').textContent.trim();
                            
                            // Remplir le champ caché
                            eleveIdInput.value = id;
                            
                            // Afficher l'élève sélectionné
                            selectedEleveInfo.innerHTML = `<strong>${nom}</strong><br><span class="text-sm text-gray-600">${details}</span>`;
                            selectedEleveDiv.classList.remove('hidden');
                            
                            // Masquer la liste et vider la recherche
                            elevesList.classList.add('hidden');
                            searchInput.value = '';
                            searchInput.disabled = true;
                        });
                    });

                    // Effacer la sélection
                    clearButton.addEventListener('click', function() {
                        eleveIdInput.value = '';
                        selectedEleveDiv.classList.add('hidden');
                        searchInput.disabled = false;
                        searchInput.focus();
                    });

                    // Cacher la liste si on clique en dehors
                    document.addEventListener('click', function(e) {
                        if (!searchInput.contains(e.target) && !elevesList.contains(e.target)) {
                            elevesList.classList.add('hidden');
                        }
                    });
                    // Validation de l'âge
                    window.validateAge = function(input) {
                        const date = new Date(input.value);
                        const today = new Date();
                        const minDate = new Date();
                        minDate.setFullYear(today.getFullYear() - 3); // Moins 3 ans
                        
                        if (date > minDate) {
                            alert("Attention : L'élève semble avoir moins de 3 ans (" + date.toLocaleDateString() + ").\nLes inscriptions sont réservées aux enfants d'au moins 3 ans.");
                            input.value = ''; // Reset
                        }
                    };

                });
                </script>
            <?php endif; ?>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t">
                <a href="<?= url('inscriptions/nouveau?etape=1') ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-<?= $type === 'nouvelle' ? 'blue' : 'green' ?>-600 hover:bg-<?= $type === 'nouvelle' ? 'blue' : 'green' ?>-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <span>Continuer</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>
