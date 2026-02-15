<div class="p-0 md:p-8">
    <!-- En-tête -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-user-plus text-blue-600 mr-2"></i>
            Nouvelle Inscription / Réinscription
        </h1>
        <p class="text-gray-600 text-sm md:text-base">Étape 1 sur 7 : Choix du type d'inscription</p>
    </div>

    <div class="mb-8">
        <?php 
        $stepNames = [1 => 'Type', 2 => 'Élève', 3 => 'Classe', 4 => 'Documents', 5 => 'Articles', 6 => 'Paiement', 7 => 'Confirmation'];
        ?>
        <div class="flex items-center justify-between mb-2">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?> text-center">
                    <span class="text-[10px] md:text-xs font-semibold <?= $i == 1 ? 'text-blue-600' : 'text-gray-400' ?>">
                        Étape <?= $i ?>: <?= $stepNames[$i] ?>
                    </span>
                </div>
            <?php endfor; ?>
        </div>
        <div class="flex items-center justify-between">
            <?php for($i=1; $i<=7; $i++): ?>
                <div class="flex-1 <?= $i > 1 ? 'ml-2' : '' ?>">
                    <div class="h-2 <?= $i == 1 ? 'bg-blue-600' : 'bg-gray-200' ?> rounded"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <form method="POST" action="<?= url('inscriptions/nouveau?etape=1') ?>">
            <?= csrf_field() ?>
            
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Quel type d'inscription souhaitez-vous effectuer ?</h2>
                
                <!-- Option Nouvelle Inscription -->
                <label class="block cursor-pointer">
                    <div class="border-2 border-gray-300 rounded-lg p-6 hover:border-blue-500 hover:bg-blue-50 transition">
                        <div class="flex items-start">
                            <input type="radio" name="type_inscription" value="nouvelle" required class="mt-1 mr-4 w-5 h-5 text-blue-600">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-user-plus text-blue-600 text-2xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-800">Nouvelle Inscription</h3>
                                </div>
                                <p class="text-gray-600 text-sm">
                                    Pour un nouvel élève qui n'a jamais été inscrit dans l'établissement.
                                    Vous devrez créer le dossier de l'élève.
                                </p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Option Réinscription -->
                <label class="block cursor-pointer">
                    <div class="border-2 border-gray-300 rounded-lg p-6 hover:border-green-500 hover:bg-green-50 transition">
                        <div class="flex items-start">
                            <input type="radio" name="type_inscription" value="reinscription" required class="mt-1 mr-4 w-5 h-5 text-green-600">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-redo text-green-600 text-2xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-800">Réinscription</h3>
                                </div>
                                <p class="text-gray-600 text-sm">
                                    Pour un élève déjà inscrit précédemment dans l'établissement.
                                    Vous sélectionnerez l'élève parmi la liste existante.
                                </p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t">
                <a href="<?= url('inscriptions/liste') ?>" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                    <span>Continuer</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Success Modal -->
<?php if (isset($_SESSION['inscription_success_modal'])): ?>
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="successModal">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
      <div>
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
          <i class="fas fa-check text-green-600 text-xl"></i>
        </div>
        <div class="mt-3 text-center sm:mt-5">
          <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
            Inscription réussie !
          </h3>
          <div class="mt-2">
            <p class="text-sm text-gray-500">
              L'inscription a été enregistrée et validée avec succès. Que souhaitez-vous faire maintenant ?
            </p>
          </div>
        </div>
      </div>
      <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
        <a href="<?= url('inscriptions/recu/' . $_SESSION['inscription_success_modal']['id']) ?>" target="_blank" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
           <i class="fas fa-print mr-2 mt-1"></i> Imprimer le reçu
        </a>
        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:col-start-1 sm:text-sm">
           <i class="fas fa-plus mr-2 mt-1"></i> Nouvelle Inscription
        </button>
      </div>
    </div>
  </div>
</div>
<?php unset($_SESSION['inscription_success_modal']); ?>
<?php endif; ?>

<script>
function closeModal() {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.style.display = 'none';
        // Remove from DOM to clean up
        modal.remove();
    }
}

// Ajouter un effet visuel lors de la sélection
document.querySelectorAll('input[name="type_inscription"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('label > div').forEach(div => {
            div.classList.remove('border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
            div.classList.add('border-gray-300');
        });
        
        const parent = this.closest('label').querySelector('div');
        if (this.value === 'nouvelle') {
            parent.classList.remove('border-gray-300');
            parent.classList.add('border-blue-500', 'bg-blue-50');
        } else {
            parent.classList.remove('border-gray-300');
            parent.classList.add('border-green-500', 'bg-green-50');
        }
    });
});
</script>
