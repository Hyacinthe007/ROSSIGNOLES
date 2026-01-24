<div class="min-h-screen bg-gray-50/50 pb-20">
    <!-- Top Navigation Bar -->
    <div class="bg-white border-b border-gray-200 sticky top-16 z-30 shadow-md backdrop-blur-xl bg-white/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row justify-between items-center h-auto lg:h-20 py-4 lg:py-0 gap-6">
                <!-- Title & Context -->
                <div class="flex items-center gap-5 w-full lg:w-auto">
                    <a href="<?= url('notes/list?classe_id=' . $evaluation['classe_id'] . '&periode_id=' . $evaluation['periode_id']) ?>" 
                       class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-50 text-gray-500 hover:bg-blue-50 hover:text-blue-600 border border-gray-200 hover:border-blue-100 transition-all duration-300 shadow-sm">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="flex-grow">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Saisie des notes</h1>
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider <?= $type === 'examen' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-teal-100 text-teal-700 border border-teal-200' ?>">
                                <?= ucfirst($type) ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-sm font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded"><?= e($evaluation['matiere_nom']) ?></span>
                            <span class="text-gray-300">&bull;</span>
                            <span class="text-sm font-medium text-gray-500"><?= e($evaluation['classe_nom']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Stats & Actions -->
                <div class="flex flex-wrap items-center justify-center lg:justify-end gap-6 w-full lg:w-auto">
                    <!-- Stats Group -->
                    <div class="flex items-center gap-8 bg-gray-50/50 px-6 py-2.5 rounded-2xl border border-gray-100">
                        <div class="text-center group">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-0.5 transition-colors group-hover:text-blue-500">Moyenne</span>
                            <span class="font-black text-gray-900 text-xl tracking-tight" id="liveAverage">--</span>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center group">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-0.5 transition-colors group-hover:text-blue-500">Remplissage</span>
                            <span class="font-black text-blue-600 text-xl tracking-tight" id="fillRate">0%</span>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center group">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-0.5 transition-colors group-hover:text-blue-500">Barème</span>
                            <span class="font-black text-gray-900 text-xl tracking-tight whitespace-nowrap">
                                <span class="text-gray-300 text-lg mr-1 font-light">/</span><?= number_format($evaluation['note_sur'], 2) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Buttons Group -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center p-1 bg-gray-100 rounded-xl">
                            <form id="excelImportForm" method="POST" action="<?= url('notes/saisie-masse/import') ?>" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="type" value="<?= e($type) ?>">
                                <input type="hidden" name="evaluation_id" value="<?= e($evaluation['id']) ?>">
                                <label class="inline-flex items-center gap-2 px-4 py-2 bg-white text-emerald-700 rounded-lg border border-transparent shadow-sm cursor-pointer hover:bg-emerald-50 hover:text-emerald-800 transition-all font-bold text-xs">
                                    <i class="fas fa-file-excel"></i>
                                    <span>Import</span>
                                    <input type="file" name="excel_file" id="excelFileInput" class="hidden" accept=".xlsx,.xls,.csv">
                                </label>
                            </form>
                            <a href="<?= url('notes/download-template?type=' . urlencode($type) . '&id=' . $evaluation['id']) ?>" 
                               class="hidden sm:inline-flex items-center justify-center p-2 text-gray-500 hover:text-blue-600 hover:bg-white rounded-lg transition-all"
                               title="Télécharger le modèle CSV">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                        </div>

                        <button type="button" id="saveAjaxButton"
                                class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 active:scale-95 transition-all font-bold text-sm tracking-wide">
                            <i class="fas fa-save"></i>
                            <span>Enregistrer</span>
                            <span id="saveStatus" class="ml-2 text-[10px] px-1.5 py-0.5 bg-blue-400/30 rounded hidden"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Aide rapide -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-keyboard text-blue-500"></i>
                    Raccourcis clavier
                </h2>
                <dl class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs text-gray-600">
                    <div>
                        <dt class="font-semibold text-gray-800">Entrée / Tab</dt>
                        <dd>Descendre à l'élève suivant</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-800">↑ / ↓</dt>
                        <dd>Remonter / descendre</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-800">Ctrl+S</dt>
                        <dd>Sauvegarde AJAX</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-800">Click colonne</dt>
                        <dd>Focus sur première case vide</dd>
                    </div>
                </dl>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-file-excel text-emerald-500"></i>
                    Import Excel
                </h2>
                <p class="text-xs text-gray-600 mb-2">
                    Utiliser un fichier avec les colonnes&nbsp;:
                    <span class="font-mono text-[11px] bg-gray-50 px-1 py-0.5 rounded border border-gray-100">Matricule</span>,
                    <span class="font-mono text-[11px] bg-gray-50 px-1 py-0.5 rounded border border-gray-100">Note</span>,
                    <span class="font-mono text-[11px] bg-gray-50 px-1 py-0.5 rounded border border-gray-100">Absent</span> (0/1),
                    <span class="font-mono text-[11px] bg-gray-50 px-1 py-0.5 rounded border border-gray-100">Appreciation</span>.
                </p>
                <?php if (!empty($import_summary)): ?>
                    <p class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-2 py-1">
                        <?= e($import_summary) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <form id="notesForm" class="relative">
            <input type="hidden" id="evaluationId" value="<?= e($evaluation['id']) ?>">
            <input type="hidden" id="evaluationType" value="<?= e($type) ?>">
            <?= csrf_field() ?>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse table-responsive-text" id="gradeTable">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-10 text-center">#</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Matricule</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">Élève</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32 text-center bg-blue-50/50 border-l border-gray-100 cursor-pointer" id="noteColumnHeader">
                                    Note
                                    <span class="block text-[10px] text-gray-400 font-normal">/ <?= number_format($evaluation['note_sur'], 2) ?></span>
                                </th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-20 text-center">Absent</th>
                                <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Appréciation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php foreach ($eleves as $index => $eleve): ?>
                                <tr class="group hover:bg-gray-50 transition-colors <?= isset($eleve['absent']) && $eleve['absent'] ? 'bg-gray-50/80 grayscale' : '' ?>" data-student-id="<?= $eleve['id'] ?>">
                                    <td class="px-4 py-3 text-center text-gray-400 text-xs">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 font-mono">
                                        <?= e($eleve['matricule']) ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
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
                                    <td class="px-0 py-0 border-l border-dashed border-gray-200 relative bg-blue-50/10">
                                        <div class="relative h-full w-full">
                                            <input type="number"
                                                   name="notes[<?= $eleve['id'] ?>]"
                                                   class="note-input block w-full h-[48px] text-center text-base md:text-lg font-bold border-0 bg-transparent focus:ring-2 focus:ring-inset focus:ring-blue-500 focus:bg-white transition-all text-gray-900 placeholder-gray-200"
                                                   step="0.25"
                                                   min="0"
                                                   max="<?= e($evaluation['note_sur']) ?>"
                                                   data-max="<?= e($evaluation['note_sur']) ?>"
                                                   value="<?= isset($eleve['note']) ? number_format($eleve['note'], 2, '.', '') : '' ?>"
                                                   <?= isset($eleve['absent']) && $eleve['absent'] ? 'disabled' : '' ?>
                                                   placeholder="-"
                                                   onkeydown="handleKeyNavigation(event, this)"
                                                   oninput="onNoteChanged(this)"
                                            >
                                            <div class="absolute bottom-0 left-0 h-1 bg-green-500 transition-all duration-300 opacity-0" style="width: 0%"></div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <div class="flex justify-center">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                       name="absences[<?= $eleve['id'] ?>]"
                                                       class="absence-input sr-only peer"
                                                       value="1"
                                                       <?= isset($eleve['absent']) && $eleve['absent'] ? 'checked' : '' ?>
                                                       onchange="onAbsenceChanged(this)"
                                                >
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input type="text"
                                               name="appreciations[<?= $eleve['id'] ?>]"
                                               class="appreciation-input block w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 text-sm text-gray-700 placeholder-gray-400 hover:border-gray-300 transition-colors"
                                               value="<?= isset($eleve['appreciation']) ? e($eleve['appreciation']) : '' ?>"
                                               placeholder="Ajouter une observation..."
                                               onkeydown="handleKeyNavigation(event, this)"
                                               oninput="onAppreciationChanged(this)"
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
    <button id="saveAjaxFab" class="w-14 h-14 bg-blue-600 rounded-full text-white shadow-lg shadow-blue-600/40 flex items-center justify-center hover:bg-blue-700 transition-transform active:scale-95">
        <i class="fas fa-save text-xl"></i>
    </button>
</div>

<script>
let pendingChanges = {};
let saveInProgress = false;
let saveTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    updateStats();

    // Auto-focus première case vide
    focusFirstEmptyNote();

    // Import Excel auto-submit
    const excelInput = document.getElementById('excelFileInput');
    if (excelInput) {
        excelInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                document.getElementById('excelImportForm').submit();
            }
        });
    }

    // Boutons sauvegarde AJAX
    const saveButton = document.getElementById('saveAjaxButton');
    const saveFab = document.getElementById('saveAjaxFab');
    if (saveButton) saveButton.addEventListener('click', saveChangesNow);
    if (saveFab) saveFab.addEventListener('click', function (e) {
        e.preventDefault();
        saveChangesNow();
    });

    // Ctrl+S => sauvegarde AJAX
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault();
            saveChangesNow();
        }
    });

    // Click sur l'en-tête de colonne "Note" => focus première case vide
    const noteHeader = document.getElementById('noteColumnHeader');
    if (noteHeader) {
        noteHeader.addEventListener('click', function () {
            focusFirstEmptyNote();
        });
    }
});

function focusFirstEmptyNote() {
    const inputs = document.querySelectorAll('.note-input:not([disabled])');
    for (let input of inputs) {
        if (!input.value) {
            input.focus();
            break;
        }
    }
}

function markChanged(studentId) {
    pendingChanges[studentId] = true;
    scheduleAutoSave();
}

function scheduleAutoSave() {
    if (saveTimeout) {
        clearTimeout(saveTimeout);
    }
    saveTimeout = setTimeout(saveChanges, 1500); // auto-save après 1,5s d'inactivité
}

function handleKeyNavigation(e, currentInput) {
    if (['ArrowUp', 'ArrowDown', 'Enter', 'Tab'].includes(e.key)) {
        if (e.key === 'Tab') {
            e.preventDefault();
        } else {
            e.preventDefault();
        }

        const isNote = currentInput.classList.contains('note-input');
        const isAppreciation = currentInput.classList.contains('appreciation-input');
        const currentRow = currentInput.closest('tr');
        const allRows = Array.from(document.querySelectorAll('#gradeTable tbody tr'));
        const currentRowIndex = allRows.indexOf(currentRow);

        if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === 'Tab') {
            if (currentRowIndex < allRows.length - 1) {
                const nextRow = allRows[currentRowIndex + 1];
                let targetSelector = '.note-input';
                if (isAppreciation) targetSelector = '.appreciation-input';
                const nextInput = nextRow.querySelector(targetSelector);
                if (nextInput && !nextInput.disabled) nextInput.focus();
            }
        } else if (e.key === 'ArrowUp') {
            if (currentRowIndex > 0) {
                const prevRow = allRows[currentRowIndex - 1];
                let targetSelector = '.note-input';
                if (isAppreciation) targetSelector = '.appreciation-input';
                const prevInput = prevRow.querySelector(targetSelector);
                if (prevInput && !prevInput.disabled) prevInput.focus();
            }
        }
    }
}

function onNoteChanged(input) {
    validateAndCalculate(input);
    const row = input.closest('tr');
    const studentId = row.dataset.studentId;
    markChanged(studentId);
}

function onAbsenceChanged(checkbox) {
    const row = checkbox.closest('tr');
    const noteInput = row.querySelector('.note-input');

    if (checkbox.checked) {
        noteInput.disabled = true;
        noteInput.oldValue = noteInput.value;
        noteInput.value = '';
        row.classList.add('bg-gray-50/80', 'grayscale');
        validateAndCalculate(noteInput);
    } else {
        noteInput.disabled = false;
        if (noteInput.oldValue) {
            noteInput.value = noteInput.oldValue;
            validateAndCalculate(noteInput);
        }
        row.classList.remove('bg-gray-50/80', 'grayscale');
        noteInput.focus();
    }

    const studentId = row.dataset.studentId;
    markChanged(studentId);
    updateStats();
}

function onAppreciationChanged(input) {
    const row = input.closest('tr');
    const studentId = row.dataset.studentId;
    markChanged(studentId);
}

function validateAndCalculate(input) {
    const max = parseFloat(input.dataset.max);
    let val = parseFloat(input.value);

    const bar = input.nextElementSibling;

    if (val < 0) {
        input.value = 0;
        val = 0;
    } else if (val > max) {
        input.classList.add('text-red-600');
        input.parentElement.classList.add('bg-red-50');
    } else {
        input.classList.remove('text-red-600');
        input.parentElement.classList.remove('bg-red-50');
    }

    if (!isNaN(val) && val <= max) {
        const percentage = (val / max) * 100;
        bar.style.width = percentage + '%';
        bar.style.opacity = '1';

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

    const avgEl = document.getElementById('liveAverage');
    const fillEl = document.getElementById('fillRate');
    if (avgEl) avgEl.textContent = avg;
    if (fillEl) fillEl.textContent = fillPercent + '%';
}

function collectChangedData() {
    const changed = {};
    const rows = document.querySelectorAll('#gradeTable tbody tr');

    rows.forEach(row => {
        const studentId = row.dataset.studentId;
        if (!pendingChanges[studentId]) return;

        const noteInput = row.querySelector('.note-input');
        const absenceInput = row.querySelector('.absence-input');
        const appreciationInput = row.querySelector('.appreciation-input');

        changed[studentId] = {
            note: noteInput && !noteInput.disabled ? noteInput.value : null,
            absent: absenceInput && absenceInput.checked ? 1 : 0,
            appreciation: appreciationInput ? appreciationInput.value : ''
        };
    });

    return changed;
}

function saveChanges() {
    if (saveInProgress) return;

    const changes = collectChangedData();
    if (Object.keys(changes).length === 0) return;

    performAjaxSave(changes);
}

function saveChangesNow() {
    if (saveTimeout) {
        clearTimeout(saveTimeout);
        saveTimeout = null;
    }
    saveChanges();
}

function performAjaxSave(changes) {
    const saveButton = document.getElementById('saveAjaxButton');
    const saveStatus = document.getElementById('saveStatus');
    saveInProgress = true;

    if (saveButton) {
        saveButton.disabled = true;
    }
    if (saveStatus) {
        saveStatus.classList.remove('hidden');
        saveStatus.textContent = 'Enregistrement...';
    }

    const evaluationId = document.getElementById('evaluationId').value;
    const evaluationType = document.getElementById('evaluationType').value;
    const csrfTokenInput = document.querySelector('input[name="_token"]');
    const csrfToken = csrfTokenInput ? csrfTokenInput.value : null;

    fetch("<?= url('notes/saisie-masse/save') ?>", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            <?php // CSRF pour compatibilité éventuelle ?>
        },
        body: JSON.stringify({
            _token: csrfToken,
            evaluation_id: evaluationId,
            type: evaluationType,
            changes: changes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data && data.success) {
            if (saveStatus) {
                saveStatus.textContent = 'Enregistré';
                setTimeout(() => {
                    saveStatus.classList.add('hidden');
                }, 2000);
            }
            // On vide la liste des pendingChanges pour les IDs traités
            Object.keys(changes).forEach(id => {
                delete pendingChanges[id];
            });
        } else {
            if (saveStatus) {
                saveStatus.textContent = data && data.message ? data.message : 'Erreur enregistrement';
                saveStatus.classList.remove('text-blue-100');
                saveStatus.classList.add('text-red-100');
            }
        }
    })
    .catch(() => {
        if (saveStatus) {
            saveStatus.textContent = 'Erreur réseau';
            saveStatus.classList.remove('text-blue-100');
            saveStatus.classList.add('text-red-100');
        }
    })
    .finally(() => {
        saveInProgress = false;
        if (saveButton) {
            saveButton.disabled = false;
        }
    });
}
</script>

