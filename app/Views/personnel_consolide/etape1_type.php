<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-user-plus text-teal-600 mr-2"></i>
            Nouveau Personnel
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Étape 1 sur 2 : Choix du type de personnel</p>
    </div>

    <!-- Progression -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="h-2 bg-teal-600 rounded"></div>
            </div>
            <div class="flex-1 ml-2">
                <div class="h-2 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('personnel/nouveau?etape=1') ?>">
            <?= csrf_field() ?>
            
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Quel type de personnel souhaitez-vous ajouter ?</h2>
                
                <!-- Option Enseignant -->
                <label class="block cursor-pointer">
                    <div class="border-2 border-gray-300 rounded-lg p-6 hover:border-indigo-500 hover:bg-indigo-50 transition">
                        <div class="flex items-start">
                            <input type="radio" name="type_personnel" value="enseignant" required class="mt-1 mr-4 w-5 h-5 text-indigo-600">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-chalkboard-teacher text-indigo-600 text-2xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-800">Enseignant</h3>
                                </div>
                                <p class="text-gray-600 text-sm">
                                    Pour ajouter un enseignant. Vous devrez renseigner les informations pédagogiques 
                                    (spécialité, diplôme, lieu de naissance, etc.).
                                </p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Option Personnel Administratif -->
                <label class="block cursor-pointer">
                    <div class="border-2 border-gray-300 rounded-lg p-6 hover:border-teal-500 hover:bg-teal-50 transition">
                        <div class="flex items-start">
                            <input type="radio" name="type_personnel" value="administratif" required class="mt-1 mr-4 w-5 h-5 text-teal-600">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-users-cog text-teal-600 text-2xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-800">Personnel Administratif</h3>
                                </div>
                                <p class="text-gray-600 text-sm">
                                    Pour ajouter du personnel administratif ou de soutien 
                                    (secrétaire, comptable, agent d'entretien, etc.).
                                </p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t">
                <a href="<?= url('liste-personnel') ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <span>Continuer</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Ajouter un effet visuel lors de la sélection
document.querySelectorAll('input[name="type_personnel"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('label > div').forEach(div => {
            div.classList.remove('border-indigo-500', 'bg-indigo-50', 'border-teal-500', 'bg-teal-50');
            div.classList.add('border-gray-300');
        });
        
        const parent = this.closest('label').querySelector('div');
        if (this.value === 'enseignant') {
            parent.classList.remove('border-gray-300');
            parent.classList.add('border-indigo-500', 'bg-indigo-50');
        } else {
            parent.classList.remove('border-gray-300');
            parent.classList.add('border-teal-500', 'bg-teal-50');
        }
    });
});
</script>
