<div class="min-h-screen bg-gray-50/50 pb-20">
    <!-- Top Navigation Bar -->
    <div class="bg-white border-b border-gray-200 sticky top-16 z-30 shadow-sm backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center h-auto md:h-16 py-3 md:py-0 gap-4">
                <div class="flex items-center gap-4">
                    <a href="<?= url('notes/list?classe_id=' . $evaluation['classe_id'] . '&periode_id=' . $evaluation['periode_id']) ?>" 
                       class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                             Saisie des notes
                             <span class="px-2 py-0.5 rounded text-xs font-normal <?= $type === 'examen' ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700' ?>">
                                <?= ucfirst($type) ?>
                            </span>
                        </h1>
                        <p class="text-sm text-gray-500">
                            <?= e($evaluation['matiere_nom']) ?> &bull; <?= e($evaluation['classe_nom']) ?>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <!-- Stats Rapides -->
                    <div class="hidden lg:flex items-center gap-6 text-sm">
                        <div class="text-center">
                            <span class="block text-gray-400 text-xs uppercase tracking-wider">Moyenne</span>
                            <span class="font-bold text-gray-900 text-lg" id="liveAverage">--</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-gray-400 text-xs uppercase tracking-wider">Remplissage</span>
                            <span class="font-bold text-blue-600 text-lg" id="fillRate">0%</span>
                        </div>
                        <div class="w-px h-8 bg-gray-200"></div>
                        <div class="text-center">
                            <span class="block text-gray-400 text-xs uppercase tracking-wider">Note Max</span>
                            <span class="font-bold text-gray-900 text-lg">/ <?= e($evaluation['note_sur']) ?></span>
                        </div>
                    </div>

                    <button type="button" onclick="document.getElementById('notesForm').submit()" 
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all font-medium">
                        <i class="fas fa-save"></i>
                        <span>Enregistrer</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Context Card for Mobile -->
        <div class="lg:hidden bg-white rounded-xl p-4 shadow-sm border border-gray-100 mb-6">
             <dl class="grid grid-cols-2 gap-x-4 gap-y-4">
                <div>
                    <dt class="text-xs text-gray-500 uppercase">Note sur</dt>
                    <dd class="text-lg font-bold text-gray-900"><?= e($evaluation['note_sur']) ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase">Moyenne</dt>
                    <dd class="text-lg font-bold text-gray-900" id="liveAverageMobile">--</dd>
                </div>
             </dl>
        </div>

        <form method="POST" action="<?= url('notes/saisie?type=' . urlencode($type) . '&id=' . $evaluation['id']) ?>" id="notesForm" class="relative">
            <?= csrf_field() ?>
            
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="gradeTable">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-16 text-center">#</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Matricule</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">Élève</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-40 text-center bg-blue-50/50 border-x border-gray-100">Note</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-24 text-center">Absent</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Appréciation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php foreach ($eleves as $index => $eleve): ?>
                                <tr class="group hover:bg-gray-50 transition-colors <?= isset($eleve['absent']) && $eleve['absent'] ? 'bg-gray-50/80 grayscale' : '' ?>" data-student-id="<?= $eleve['id'] ?>">
                                    <td class="px-6 py-4 text-center text-gray-400 text-xs">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                        <?= e($eleve['matricule']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold mr-3">
                                                <?= strtoupper(substr($eleve['nom'], 0, 1) . substr($eleve['prenom'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?= e($eleve['nom']) ?></div>
                                                <div class="text-xs text-gray-500"><?= e($eleve['prenom']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-0 py-0 p-0 border-x border-dashed border-gray-200 relative bg-blue-50/10">
                                        <div class="relative h-full w-full">
                                            <input type="number" 
                                                   name="notes[<?= $eleve['id'] ?>]" 
                                                   class="note-input block w-full h-[60px] text-center text-lg font-bold border-0 bg-transparent focus:ring-2 focus:ring-inset focus:ring-blue-500 focus:bg-white transition-all text-gray-900 placeholder-gray-200"
                                                   step="0.25" 
                                                   min="0" 
                                                   max="<?= e($evaluation['note_sur']) ?>"
                                                   data-max="<?= e($evaluation['note_sur']) ?>"
                                                   value="<?= isset($eleve['note']) ? e($eleve['note']) : '' ?>"
                                                   <?= isset($eleve['absent']) && $eleve['absent'] ? 'disabled' : '' ?>
                                                   placeholder="-"
                                                   onkeydown="handleKeyNavigation(event, this)"
                                                   oninput="validateAndCalculate(this)"
                                            >
                                            <!-- Visual feedback bar -->
                                            <div class="absolute bottom-0 left-0 h-1 bg-green-500 transition-all duration-300 opacity-0" style="width: 0%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" 
                                                       name="absences[<?= $eleve['id'] ?>]" 
                                                       class="sr-only peer" 
                                                       value="1" 
                                                       <?= isset($eleve['absent']) && $eleve['absent'] ? 'checked' : '' ?>
                                                       onchange="toggleNoteInput(this)"
                                                >
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" 
                                               name="appreciations[<?= $eleve['id'] ?>]" 
                                               class="appreciation-input block w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 text-sm text-gray-700 placeholder-gray-400 hover:border-gray-300 transition-colors"
                                               value="<?= isset($eleve['appreciation']) ? e($eleve['appreciation']) : '' ?>"
                                               placeholder="Ajouter une observation..."
                                               onkeydown="handleKeyNavigation(event, this)"
                                        >
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </main>
</div>

<!-- Floating Action Button for Mobile -->
<div class="lg:hidden fixed bottom-6 right-6 z-40">
    <button onclick="document.getElementById('notesForm').submit()" class="w-14 h-14 bg-blue-600 rounded-full text-white shadow-lg shadow-blue-600/40 flex items-center justify-center hover:bg-blue-700 transition-transform active:scale-95">
        <i class="fas fa-save text-xl"></i>
    </button>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
    
    // Auto-focus first empty input on load
    const inputs = document.querySelectorAll('.note-input:not([disabled])');
    for (let input of inputs) {
        if (!input.value) {
            input.focus();
            break;
        }
    }
});

function handleKeyNavigation(e, currentInput) {
    if (['ArrowUp', 'ArrowDown', 'Enter'].includes(e.key)) {
        e.preventDefault();
        
        const inputs = Array.from(document.querySelectorAll('.note-input, .appreciation-input'));
        const currentIndex = inputs.indexOf(currentInput);
        const isNote = currentInput.classList.contains('note-input');
        
        let nextIndex;
        
        // Navigation logique via grille
        // On suppose une structure régulière: Note -> Appréciation -> Note Suivante...
        // Cependant la liste `inputs` mélange tout. Mieux vaut sélectionner par colonne.
        
        const currentRow = currentInput.closest('tr');
        const allRows = Array.from(document.querySelectorAll('#gradeTable tbody tr'));
        const currentRowIndex = allRows.indexOf(currentRow);
        
        if (e.key === 'ArrowDown' || e.key === 'Enter') {
            if (currentRowIndex < allRows.length - 1) {
                const nextRow = allRows[currentRowIndex + 1];
                const targetSelector = isNote ? '.note-input' : '.appreciation-input';
                const nextInput = nextRow.querySelector(targetSelector);
                if (nextInput && !nextInput.disabled) nextInput.focus();
            }
        } else if (e.key === 'ArrowUp') {
            if (currentRowIndex > 0) {
                const prevRow = allRows[currentRowIndex - 1];
                const targetSelector = isNote ? '.note-input' : '.appreciation-input';
                const prevInput = prevRow.querySelector(targetSelector);
                if (prevInput && !prevInput.disabled) prevInput.focus();
            }
        }
    }
}

function validateAndCalculate(input) {
    const max = parseFloat(input.dataset.max);
    let val = parseFloat(input.value);
    
    const row = input.closest('tr');
    const bar = input.nextElementSibling; // The visual feedback bar
    
    // Validation
    if (val < 0) {
        input.value = 0;
        val = 0;
    } else if (val > max) {
        input.classList.add('text-red-600');
        input.parentElement.classList.add('bg-red-50');
        // Optional: shake effect
    } else {
        input.classList.remove('text-red-600');
        input.parentElement.classList.remove('bg-red-50');
    }
    
    // Visual feedback bar
    if (!isNaN(val) && val <= max) {
        const percentage = (val / max) * 100;
        bar.style.width = percentage + '%';
        bar.style.opacity = '1';
        
        // Color based on performance
        if (percentage < 30) bar.className = 'absolute bottom-0 left-0 h-1 bg-red-500 transition-all duration-300';
        else if (percentage < 50) bar.className = 'absolute bottom-0 left-0 h-1 bg-yellow-500 transition-all duration-300';
        else if (percentage >= 80) bar.className = 'absolute bottom-0 left-0 h-1 bg-green-500 transition-all duration-300';
        else bar.className = 'absolute bottom-0 left-0 h-1 bg-blue-500 transition-all duration-300';
    } else {
        bar.style.width = '0%';
        bar.style.opacity = '0';
    }
    
    updateStats();
}

function updateStats() {
    const inputs = document.querySelectorAll('.note-input:not(:disabled)');
    let total = 0;
    let count = 0;
    let filled = 0;
    let totalInputs = inputs.length;
    
    inputs.forEach(input => {
        if (input.value !== '') {
            const val = parseFloat(input.value);
            if (!isNaN(val)) {
                total += val;
                count++;
                filled++;
            }
        }
    });
    
    const avg = count > 0 ? (total / count).toFixed(2) : '--';
    const fillPercent = totalInputs > 0 ? Math.round((filled / totalInputs) * 100) : 0;
    
    document.getElementById('liveAverage').textContent = avg;
    document.getElementById('fillRate').textContent = fillPercent + '%';
    
    // Mobile stats
    const mobileStats = document.getElementById('liveAverageMobile');
    if (mobileStats) mobileStats.textContent = avg;
}

function toggleNoteInput(checkbox) {
    const row = checkbox.closest('tr');
    const noteInput = row.querySelector('.note-input');
    
    if (checkbox.checked) {
        noteInput.disabled = true;
        noteInput.oldValue = noteInput.value;
        noteInput.value = '';
        row.classList.add('bg-gray-50/80', 'grayscale');
        validateAndCalculate(noteInput); // Clear visual bar
    } else {
        noteInput.disabled = false;
        if (noteInput.oldValue) {
            noteInput.value = noteInput.oldValue;
            validateAndCalculate(noteInput); // Restore visual bar
        }
        row.classList.remove('bg-gray-50/80', 'grayscale');
        noteInput.focus();
    }
    updateStats();
}
</script>
