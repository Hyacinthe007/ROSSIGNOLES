<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">
                <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                Emplois du temps
            </h1>
            <p class="text-gray-500 text-xs md:text-sm">Gestion des horaires de cours</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.print()" class="no-print bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-sm border border-gray-200 font-medium">
                <i class="fas fa-print"></i>
                <span>Imprimer</span>
            </button>
            <?php if (hasPermission('calendrier.add')): ?>
            <a href="<?= url('pedagogie/emplois-temps/add') ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus-circle"></i>
                <span>Programmer un cours</span>
            </a>
            <?php endif; ?>
            <?php if (hasPermission('pedagogie.enseignements')): ?>
            <a href="<?= url('pedagogie/enseignements') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Enseignements</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtre par classe, enseignant et période -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-100">
        <form id="filterForm" method="GET" action="<?= url('pedagogie/emplois-temps') ?>" class="flex flex-col md:flex-row items-end gap-4">
            <?php if (isset($_GET['iframe'])): ?>
                <input type="hidden" name="iframe" value="1">
            <?php endif; ?>
            
            <div class="w-full md:w-48">
                <label for="semaine" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest text-[10px]">
                    <i class="fas fa-calendar-week mr-1 text-green-500"></i>Semaine
                </label>
                <input type="week" id="semaine" name="semaine" value="<?= $selectedWeek ?>" 
                       onchange="this.form.submit()"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 transition-all bg-gray-50 outline-none text-sm">
            </div>

            <div class="flex-1 w-full md:w-auto">
                <label for="classe_id" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest text-[10px]">
                    <i class="fas fa-door-open mr-1 text-purple-500"></i>Par Classe
                </label>
                <select id="classe_id" 
                        name="classe_id"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 transition-all bg-gray-50 outline-none text-sm">
                    <option value="">Toutes les classes</option>
                    <?php if (isset($classes)): 
                        $currentCycle = '';
                        foreach ($classes as $classe): 
                            if ($currentCycle !== $classe['cycle_nom']): 
                                if ($currentCycle !== '') echo '</optgroup>';
                                $currentCycle = $classe['cycle_nom'];
                                echo '<optgroup label="' . e($currentCycle) . '">';
                            endif;
                    ?>
                            <option value="<?= $classe['id'] ?>" <?= (isset($_GET['classe_id']) && $_GET['classe_id'] == $classe['id']) ? 'selected' : '' ?>>
                                <?= e($classe['libelle']) ?>
                            </option>
                        <?php endforeach; 
                        if ($currentCycle !== '') echo '</optgroup>';
                    endif; ?>
                </select>
            </div>

            <div class="flex-1 w-full md:w-auto">
                <label for="personnel_id" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest text-[10px]">
                    <i class="fas fa-chalkboard-teacher mr-1 text-blue-500"></i>Par Enseignant
                </label>
                <select id="personnel_id" 
                        name="personnel_id"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all bg-gray-50 outline-none text-sm">
                    <option value="">Tous les enseignants</option>
                    <?php if (isset($enseignants)): ?>
                        <?php foreach ($enseignants as $enseignant): ?>
                            <option value="<?= $enseignant['id'] ?>" <?= (isset($_GET['personnel_id']) && $_GET['personnel_id'] == $enseignant['id']) ? 'selected' : '' ?>>
                                <?= e($enseignant['nom']) ?> <?= e($enseignant['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="w-full md:w-auto bg-gray-50 px-4 py-1.5 rounded-lg border border-gray-100 h-[38px] flex items-center">
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" name="periode" value="tous" onchange="this.form.submit()" <?= (!isset($_GET['periode']) || $_GET['periode'] == 'tous') ? 'checked' : '' ?> class="w-3 h-3 text-purple-600 focus:ring-purple-500">
                        <span class="text-xs font-medium text-gray-600 group-hover:text-purple-600 transition">Tous</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" name="periode" value="matin" onchange="this.form.submit()" <?= (isset($_GET['periode']) && $_GET['periode'] == 'matin') ? 'checked' : '' ?> class="w-3 h-3 text-purple-600 focus:ring-purple-500">
                        <span class="text-xs font-medium text-gray-600 group-hover:text-purple-600 transition">Matin</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" name="periode" value="midi" onchange="this.form.submit()" <?= (isset($_GET['periode']) && $_GET['periode'] == 'midi') ? 'checked' : '' ?> class="w-3 h-3 text-purple-600 focus:ring-purple-500">
                        <span class="text-xs font-medium text-gray-600 group-hover:text-purple-600 transition">Après</span>
                    </label>
                </div>
            </div>
        </form>
    </div>

    <!-- Affichage de la semaine -->
    <div class="mb-4 flex items-center justify-between px-2">
        <h2 class="text-sm font-bold text-gray-600 flex items-center gap-2">
            <i class="fas fa-calendar-alt text-blue-500"></i>
            <?= $weekLabel ?>
        </h2>
        <div class="flex gap-2">
            <a href="<?= url('pedagogie/emplois-temps') ?>?semaine=<?= date('Y-\WW', strtotime('-1 week', strtotime($selectedWeek))) ?>&<?= http_build_query(array_diff_key($_GET, ['semaine' => ''])) ?>" 
               class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-500 transition no-print" title="Semaine précédente">
                <i class="fas fa-chevron-left"></i>
            </a>
            <a href="<?= url('pedagogie/emplois-temps') ?>?semaine=<?= date('Y-\WW', strtotime('+1 week', strtotime($selectedWeek))) ?>&<?= http_build_query(array_diff_key($_GET, ['semaine' => ''])) ?>" 
               class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-500 transition no-print" title="Semaine suivante">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- Tableau des emplois du temps (Style Grille) -->
    <?php if (!empty($emploisTemps)): ?>
        <?php
        $selectedPeriode = $_GET['periode'] ?? 'tous';
        $joursSemaine = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
        
        // 1. Extraire tous les créneaux horaires uniques
        $timeSlots = [];
        foreach ($emploisTemps as $et) {
            $heureDebut = date('H:i', strtotime($et['heure_debut']));
            $heureFin = date('H:i', strtotime($et['heure_fin']));
            
            if ($selectedPeriode === 'matin' && $heureDebut >= '13:00') continue;
            if ($selectedPeriode === 'midi' && $heureDebut < '13:00') continue;
            
            $slot = $heureDebut . ' - ' . $heureFin;
            if (!in_array($slot, $timeSlots)) {
                $timeSlots[] = $slot;
            }
        }
        
        usort($timeSlots, function($a, $b) {
            return strcmp(substr($a, 0, 5), substr($b, 0, 5));
        });

        $grid = [];
        foreach ($timeSlots as $slot) {
            $grid[$slot] = array_fill_keys($joursSemaine, []);
        }

        foreach ($emploisTemps as $et) {
            $heureDebut = date('H:i', strtotime($et['heure_debut']));
            $heureFin = date('H:i', strtotime($et['heure_fin']));
            $slot = $heureDebut . ' - ' . $heureFin;
            $jour = strtolower($et['jour_semaine']);
            
            if (isset($grid[$slot][$jour])) {
                $grid[$slot][$jour][] = $et;
            }
        }
        ?>

        <style>
            .tt-container {
                background: white;
                border-radius: 8px;
                overflow: hidden;
                border: 1px solid #E5E7EB;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .tt-grid {
                display: grid;
                grid-template-columns: 120px repeat(5, 1fr);
            }
            .tt-cell {
                padding: 12px 10px;
                min-height: 70px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: flex-start;
                text-align: left;
                position: relative;
                border-right: 1px solid #F3F4F6;
                border-bottom: 1px solid #F3F4F6;
                background: white;
            }
            .tt-cell:hover {
                background-color: #F9FAFB;
            }
            .tt-actions {
                position: absolute;
                top: 2px;
                right: 2px;
                opacity: 0;
                transition: opacity 0.2s;
            }
            .tt-cell:hover .tt-actions {
                opacity: 1;
            }
            .tt-add-btn {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: all 0.2s;
                background: rgba(255, 255, 255, 0.8);
                z-index: 10;
            }
            .tt-cell:hover .tt-add-btn {
                opacity: 1;
            }
            .tt-add-btn a {
                width: 32px;
                height: 32px;
                background: #9333EA;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 6px -1px rgba(147, 51, 234, 0.3);
                transform: scale(0.8);
                transition: transform 0.2s;
            }
            .tt-add-btn a:hover {
                transform: scale(1.1);
                background: #7E22CE;
            }
            .tt-header-cell {
                height: 45px;
                background: #2563EB;
                color: white;
                font-weight: 700;
                text-transform: uppercase;
                font-size: 0.7rem;
                letter-spacing: 1px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-right: 1px solid rgba(255, 255, 255, 0.1);
            }
            .tt-time-col {
                background: #F9FAFB;
                color: #4B5563;
                font-weight: 700;
                font-size: 0.7rem;
                border-right: 1px solid #E5E7EB;
            }
            .course-info {
                display: flex;
                flex-direction: column;
                gap: 2px;
            }
            .course-code {
                font-family: 'Outfit', sans-serif;
                font-weight: 700;
                font-size: 0.8rem;
                color: #111827;
                line-height: 1.2;
            }
            .vacation-orange {
                color: #FF6B00;
            }
            .tt-grid > div:last-child {
                border-right: none;
            }
            @media (max-width: 768px) {
                .tt-grid {
                    grid-template-columns: 80px repeat(5, 1fr);
                }
                .course-code { font-size: 0.65rem; }
            }
        </style>

        <div class="tt-container">
            <div class="tt-grid">
                <div class="tt-header-cell">
                    <i class="far fa-clock text-lg opacity-40"></i>
                </div>
                <?php foreach ($joursSemaine as $jour): ?>
                    <div class="tt-header-cell flex flex-col items-center leading-tight">
                        <span class="text-[10px]"><?= strtoupper($jour) ?></span>
                        <span class="text-[9px] opacity-70 font-normal"><?= isset($weekDates[$jour]) ? date('d/m', strtotime($weekDates[$jour])) : '' ?></span>
                        <?php if (isset($vacances[$jour])): ?>
                            <div class="w-1.5 h-1.5 bg-orange-400 rounded-full mt-1 animate-pulse"></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($timeSlots as $slot): ?>
                    <div class="tt-cell tt-time-col">
                        <?= $slot ?>
                    </div>
                    <?php foreach ($joursSemaine as $jour): ?>
                        <div class="tt-cell <?= isset($vacances[$jour]) ? 'bg-orange-50/30' : '' ?>">
                            <?php if (isset($vacances[$jour])): ?>
                                <div class="flex flex-col items-center justify-center h-full w-full pointer-events-none opacity-60">
                                    <i class="fas fa-umbrella-beach vacation-orange mb-1 text-xs"></i>
                                    <span class="text-[9px] font-bold vacation-orange uppercase tracking-tighter"><?= e($vacances[$jour]['libelle']) ?></span>
                                </div>
                            <?php else: ?>
                                <?php if (!empty($grid[$slot][$jour])): ?>
                                    <?php foreach ($grid[$slot][$jour] as $et): ?>
                                        <div class="course-info">
                                            <span class="course-code">
                                                <?= e($et['enseignant_nom']) ?> - <?= e($et['classe_code'] ?? '') ?> - <?= e($et['matiere_code'] ?? '') ?>
                                            </span>
                                        </div>
                                        <?php if (hasPermission('calendrier.edit')): ?>
                                        <div class="tt-actions no-print">
                                            <a href="<?= url('pedagogie/emplois-temps/edit/' . $et['id']) ?><?= isset($_GET['iframe']) ? '&iframe=1' : '' ?>" 
                                            class="text-gray-300 hover:text-blue-600 transition p-1" title="Modifier">
                                                <i class="fas fa-edit text-[11px]"></i>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <!-- Bouton Plus au survol -->
                                <?php if (hasPermission('calendrier.add')): ?>
                                <div class="tt-add-btn no-print">
                                    <a href="<?= url('pedagogie/emplois-temps/add') ?>?jour=<?= $jour ?>&h_debut=<?= substr($slot, 0, 5) ?>&h_fin=<?= substr($slot, -5) ?><?= isset($_GET['classe_id']) ? '&classe_id=' . $_GET['classe_id'] : '' ?><?= isset($_GET['iframe']) ? '&iframe=1' : '' ?>" title="Programmer un cours à ce créneau">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>


    <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <div class="text-gray-300 mb-4">
                <i class="fas fa-calendar-times text-6xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-700 mb-2">Aucun emploi du temps</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                <?php if (isset($_GET['classe_id'])): ?>
                    Il n'y a pas encore de cours programmés pour cette classe ou la période sélectionnée.
                    <div class="mt-4">
                        <a href="<?= url('pedagogie/emplois-temps/add') ?>" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                            <i class="fas fa-plus"></i> Programmer le premier cours
                        </a>
                    </div>
                <?php else: ?>
                    Veuillez sélectionner une classe dans le filtre ci-dessus pour consulter son planning hebdomadaire.
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<style media="print">
    .no-print { display: none !important; }
    body { background: white !important; padding: 0 !important; }
    .tt-container { box-shadow: none !important; border: none !important; }
    .tt-main { padding: 0 !important; }
    aside, nav, .mb-6, .mb-8, .bg-white.rounded-lg.shadow-md.p-6 { display: none !important; }
</style>



