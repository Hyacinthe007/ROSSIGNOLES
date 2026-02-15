<?php
$pageTitle = "PrÃ©sences par Cours";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>



<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="header-actions">
        <div class="page-header-clean">
            <h1>
                <i class="fas fa-chalkboard-teacher"></i>
                Présences par Cours (<?= date('Y', strtotime($date)) ?> - <?= date('Y', strtotime($date)) + 1 ?>)
            </h1>
            <p>Consultez les présences pour chaque cours de la journée</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= url('presences/verification') ?>" class="btn-retour" style="background: #805ad5;">
                <i class="fas fa-search"></i>
                Vérifier une présence
            </a>
            <a href="<?= url('presences/historique') ?>" class="btn-retour">
                <i class="fas fa-history"></i>
                Historique
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <?php if (!empty($cours)): ?>
        <?php
        $totalCours = count($cours);
        $totalEleves = array_sum(array_column($cours, 'nb_eleves_total'));
        $totalPresents = array_sum(array_column($cours, 'nb_presents'));
        $totalAbsents = array_sum(array_column($cours, 'nb_absents'));
        $tauxGlobal = $totalEleves > 0 ? round(($totalPresents / $totalEleves) * 100, 1) : 0;
        ?>

        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-content">
                    <h3>Total cours</h3>
                    <p class="stat-number"><?= $totalCours ?></p>
                </div>
                <div class="stat-icon icon-blue">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>

            <div class="stat-box">
                <div class="stat-content">
                    <h3>Présents</h3>
                    <p class="stat-number"><?= $totalPresents ?></p>
                </div>
                <div class="stat-icon icon-green">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>

            <div class="stat-box">
                <div class="stat-content">
                    <h3>Absents</h3>
                    <p class="stat-number"><?= $totalAbsents ?></p>
                </div>
                <div class="stat-icon icon-red">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>

            <div class="stat-box">
                <div class="stat-content">
                    <h3>Taux de présence</h3>
                    <p class="stat-number"><?= $tauxGlobal ?>%</p>
                </div>
                <div class="stat-icon icon-purple">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="filters-section">
        <form method="GET" action="<?= url('presences') ?>">
            <div class="filters-grid">
                <div class="filter-field">
                    <label for="date">Date</label>
                    <input type="date"
                           id="date"
                           name="date"
                           value="<?= htmlspecialchars($date) ?>"
                           onchange="this.form.submit()">
                </div>

                <div class="filter-field">
                    <label for="classe_id">Classe</label>
                    <select id="classe_id" name="classe_id" onchange="this.form.submit()">
                        <option value="">Toutes les classes</option>
                        
                        <optgroup label="Secondaire">
                            <?php 
                            // Tri manuel pour le Secondaire : 6ème, 5ème, 4ème, 3ème
                            $ordreSecondaire = ['6', '5', '4', '3'];
                            foreach ($ordreSecondaire as $prefixe) {
                                foreach ($classes as $classe) {
                                    $code = htmlspecialchars($classe['code']);
                                    if (strpos($code, $prefixe) === 0) {
                                        $selected = ($classe_id == $classe['id']) ? 'selected' : '';
                                        echo "<option value=\"{$classe['id']}\" $selected>{$code}</option>";
                                    }
                                }
                            }
                            ?>
                        </optgroup>
                        
                        <optgroup label="Lycée">
                            <?php 
                            // Tri manuel pour le Lycée : 2nd, 1ère, Term
                            $ordreLycee = ['2nd', '1', 'T'];
                            foreach ($ordreLycee as $prefixe) {
                                foreach ($classes as $classe) {
                                    $code = htmlspecialchars($classe['code']);
                                    if (strpos($code, $prefixe) === 0) {
                                        $selected = ($classe_id == $classe['id']) ? 'selected' : '';
                                        echo "<option value=\"{$classe['id']}\" $selected>{$code}</option>";
                                    }
                                }
                            }
                            ?>
                        </optgroup>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="enseignant_id">Enseignant</label>
                    <select id="enseignant_id" name="enseignant_id" onchange="this.form.submit()">
                        <option value="">Tous les enseignants</option>
                        <?php foreach ($enseignants as $enseignant): ?>
                            <option value="<?= $enseignant['id'] ?>"
                                    <?= $enseignant_id == $enseignant['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($enseignant['nom_complet']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-field">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Liste des cours -->
    <div class="content-section">
        <h2 class="section-title">
            <i class="fas fa-list"></i>
            Cours du <?= date('d/m/Y', strtotime($date)) ?> (<?= ucfirst(strftime('%A', strtotime($date))) ?>)
        </h2>

        <?php if (empty($cours)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>Aucun cours programmé</h3>
                <p>Il n'y a pas de cours prévu pour cette date avec les filtres sélectionnés.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="cours-table">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-clock mr-2"></i>Horaire</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-chalkboard-teacher mr-2"></i>Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-book mr-2"></i>Matière</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-user-tie mr-2"></i>Enseignant</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-users mr-2"></i>Effectif</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-user-check mr-2"></i>Présents</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-user-times mr-2"></i>Absents</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-percentage mr-2"></i>Taux</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-tools mr-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cours as $c): ?>
                            <tr>
                                <td class="time-cell">
                                    <?= date('H:i', strtotime($c['heure_debut'])) ?> - <?= date('H:i', strtotime($c['heure_fin'])) ?>
                                </td>
                                <td>
                                    <span class="badge-clean badge-classe">
                                        <?= htmlspecialchars($c['classe_code']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-clean badge-matiere" style="background-color: <?= htmlspecialchars($c['couleur'] ?? '#4299e1') ?>">
                                        <?= htmlspecialchars($c['matiere_nom']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($c['enseignant_nom'] ?? 'Non assignÃ©') ?></td>
                                <td>
                                    <span class="badge-clean badge-info-light">
                                        <?= $c['nb_eleves_total'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-clean badge-success-light">
                                        <?= $c['nb_presents'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($c['nb_absents'] > 0): ?>
                                        <span class="badge-clean badge-danger-light">
                                            <?= $c['nb_absents'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-clean badge-info-light">0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $taux = $c['taux_presence'];
                                    $progressClass = $taux >= 90 ? 'progress-excellent' : ($taux >= 75 ? 'progress-good' : 'progress-poor');
                                    $textClass = $taux >= 90 ? 'text-excellent' : ($taux >= 75 ? 'text-good' : 'text-poor');
                                    ?>
                                    <div class="progress-wrapper">
                                        <div class="progress-bar-clean">
                                            <div class="progress-fill <?= $progressClass ?>" style="width: <?= $taux ?>%"></div>
                                        </div>
                                        <span class="progress-text <?= $textClass ?>">
                                            <?= $taux ?>%
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?= url('presences/details-cours') ?>?emploi_temps_id=<?= $c['id'] ?>&date=<?= $date ?>"
                                       class="btn-action"
                                       title="Voir les dÃ©tails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php require_once __DIR__ . '/../layout/footer.php'; ?>
