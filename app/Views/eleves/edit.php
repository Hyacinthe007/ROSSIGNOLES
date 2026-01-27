<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-edit text-blue-600 mr-2"></i>
            Modifier l'élève
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Modifiez les informations de l'élève</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        
        <?php if (isset($eleve['id'])): ?>
            <!-- Bouton Modifier Dossier Scolaire -->
            <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Dossier Scolaire & Frais</h3>
                        <p class="text-sm text-gray-600">Pour modifier la classe, recálculer les frais ou corriger une erreur d'inscription.</p>
                    </div>
                </div>
                
                <?php
                // Trouver l'inscription active pour cet élève
                $eleveModel = new \App\Models\Eleve(); 
                $inscriptionActive = $eleveModel->getInscriptionActive($eleve['id']);
                ?>
                
                <?php if ($inscriptionActive): ?>
                    <a href="<?= url('inscriptions/modifier/' . $inscriptionActive['id']) ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition flex items-center gap-2 shadow-sm whitespace-nowrap">
                        <i class="fas fa-edit"></i>
                        Modifier l'inscription
                    </a>
                <?php else: ?>
                    <a href="<?= url('inscriptions/nouveau?etape=1') ?>" 
                       class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition flex items-center gap-2 shadow-sm whitespace-nowrap">
                        <i class="fas fa-plus"></i>
                        Créer une inscription
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('eleves/edit/' . $eleve['id']) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <!-- Informations de l'élève -->
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Informations de l'élève</h2>
            
            <div class="space-y-6">
                <!-- Ligne 1 : Matricule -->
                <div>
                    <label for="matricule" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2 text-gray-500"></i>Matricule *
                    </label>
                    <div class="relative">
                        <input type="text" id="matricule" name="matricule" readonly
                               value="<?= e($eleve['matricule']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-800 font-mono text-lg cursor-not-allowed">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Le matricule ne peut pas être modifié
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
                               value="<?= e($eleve['nom']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Prénom -->
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-gray-500"></i>Prénom *
                        </label>
                        <input type="text" id="prenom" name="prenom" required
                               value="<?= e($eleve['prenom']) ?>"
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
                            <option value="M" <?= $eleve['sexe'] === 'M' ? 'selected' : '' ?>>Masculin</option>
                            <option value="F" <?= $eleve['sexe'] === 'F' ? 'selected' : '' ?>>Féminin</option>
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
                               value="<?= e($eleve['date_naissance']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Lieu de naissance -->
                    <div>
                        <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>Lieu de naissance *
                        </label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" required
                               value="<?= e($eleve['lieu_naissance']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Photo -->
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-camera mr-2 text-gray-500"></i>Photo de l'élève
                    </label>
                    <?php if (!empty($eleve['photo'])): ?>
                        <div class="mb-2 flex items-center gap-3">
                            <img src="<?= url($eleve['photo']) ?>" alt="Photo actuelle" class="w-16 h-16 rounded-full object-cover border border-gray-300">
                            <span class="text-sm text-green-600"><i class="fas fa-check-circle mr-1"></i>Photo actuelle</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/jpg,image/png"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Formats acceptés : JPG, JPEG, PNG (max 2 Mo). Laissez vide pour conserver la photo actuelle.
                    </p>
                </div>
            </div>

            <!-- Séparateur -->
            <div class="my-8 border-t border-gray-300"></div>

            <!-- Informations Parent/Tuteur -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-users text-blue-600 mr-2"></i>
                Informations Parent / Tuteur
            </h3>
            <p class="text-sm text-gray-600 mb-4">
                <i class="fas fa-info-circle mr-1"></i>
                Informations du parent ou tuteur légal de l'élève
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Ligne 1 : Nom, Prénom, Lien -->
                <!-- Nom du parent -->
                <div>
                    <label for="parent_nom" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-gray-500"></i>Nom du parent/tuteur *
                    </label>
                    <input type="text" id="parent_nom" name="parent_nom" required
                           value="<?= e($parent['nom'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Prénom du parent -->
                <div>
                    <label for="parent_prenom" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-gray-500"></i>Prénom du parent/tuteur *
                    </label>
                    <input type="text" id="parent_prenom" name="parent_prenom" required
                           value="<?= e($parent['prenom'] ?? '') ?>"
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
                        // Récupération de la valeur stockée (plusieurs clés possibles selon l'origine de la donnée)
                        $dbLien = $parent['lien_parente'] ?? $parent['type_parent'] ?? $parent['type_lien'] ?? '';
                        $currentLien = trim((string)$dbLien);
                        
                        foreach ($liens as $lien): 
                            // Normalisation : Accents -> Lettres de base, Minuscules, Pas d'espaces/tirets
                            $search  = ['À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ'];
                            $replace = ['A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y'];
                            
                            $normDB = strtolower(str_replace($search, $replace, $currentLien));
                            $normDB = str_replace(['-', ' ', '_'], '', $normDB);
                            
                            $normItem = strtolower(str_replace($search, $replace, $lien));
                            $normItem = str_replace(['-', ' ', '_'], '', $normItem);
                            
                            // Cas spécifiques techniques ou anglais
                            if ($normDB === 'father') $normDB = 'pere';
                            if ($normDB === 'mother') $normDB = 'mere';
                            if ($normDB === 'grandparent') $normDB = 'grandpere';
                            
                            $selected = ($currentLien === $lien) || (!empty($normDB) && $normDB === $normItem);
                        ?>
                            <option value="<?= $lien ?>" <?= $selected ? 'selected' : '' ?>><?= $lien ?></option>
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
                           value="<?= e($parent['telephone'] ?? '') ?>"
                           pattern="[0-9]{10}" 
                           maxlength="10"
                           placeholder="0123456789"
                           title="Le numéro de téléphone doit contenir exactement 10 chiffres"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>10 chiffres requis</p>
                </div>

                <!-- Email parent -->
                <div>
                    <label for="parent_email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-gray-500"></i>Email
                    </label>
                    <input type="email" id="parent_email" name="parent_email"
                           value="<?= e($parent['email'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Profession -->
                <div>
                    <label for="parent_profession" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-briefcase mr-2 text-gray-500"></i>Profession
                    </label>
                    <input type="text" id="parent_profession" name="parent_profession"
                           value="<?= e($parent['profession'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Ligne 3 : Adresse (Full width) -->
                <div class="md:col-span-3">
                    <label for="parent_adresse" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>Adresse *
                    </label>
                    <input type="text" id="parent_adresse" name="parent_adresse" required
                           value="<?= e($parent['adresse'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-8 border-t">
                <a href="<?= url('eleves/details/' . $eleve['id']) ?>" 
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2 shadow-md">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer les modifications</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
                    transformed = original.toLowerCase().replace(/(?:^|[\s-])\w/g, function(match) {
                        return match.toUpperCase();
                    });
                }

                if (original !== transformed) {
                    this.value = transformed;
                    this.setSelectionRange(cursorStart, cursorEnd);
                }
            });
        }
    });
    
    // Formater l'adresse en title case
    const adresseField = document.getElementById('parent_adresse');
    if (adresseField) {
        adresseField.addEventListener('blur', function() {
            this.value = this.value.toLowerCase().replace(/(?:^|[\s-])\w/g, function(match) {
                return match.toUpperCase();
            });
        });
    }
    
    // Validation téléphone : accepter uniquement les chiffres
    const telField = document.getElementById('parent_telephone');
    if (telField) {
        telField.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});
</script>
