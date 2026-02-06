<?php
$pageTitle = "PrÃ©sences par Cours";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<style>
/* Style inspirÃ© de la page classes/eleves */
.main-content {
    padding: 2rem;
    background: #f8f9fa;
}

.page-header-clean {
    margin-bottom: 2rem;
}

.page-header-clean h1 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1a202c;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-header-clean h1 i {
    color: #4299e1;
}

.page-header-clean p {
    color: #718096;
    margin: 0;
    font-size: 0.95rem;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.btn-retour {
    background: #4299e1;
    color: white;
    border: none;
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.btn-retour:hover {
    background: #3182ce;
}

/* Cartes de statistiques */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-box {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s ease;
}

.stat-box:hover {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stat-content h3 {
    font-size: 0.875rem;
    color: #718096;
    margin: 0 0 0.5rem 0;
    font-weight: 500;
}

.stat-content .stat-number {
    font-size: 2rem;
    font-weight: 600;
    color: #1a202c;
    margin: 0;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.icon-blue { background: #e6f2ff; color: #4299e1; }
.icon-green { background: #d4f4dd; color: #48bb78; }
.icon-red { background: #ffe5e5; color: #f56565; }
.icon-purple { background: #f3e8ff; color: #9f7aea; }

/* Filtres */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-field {
    display: flex;
    flex-direction: column;
}

.filter-field label {
    font-size: 0.875rem;
    color: #4a5568;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.filter-field input,
.filter-field select {
    padding: 0.625rem 0.875rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    background: white;
}

.filter-field input:focus,
.filter-field select:focus {
    outline: none;
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.btn-filter {
    background: #4299e1;
    color: white;
    border: none;
    padding: 0.625rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.btn-filter:hover {
    background: #3182ce;
}

/* Section principale */
.content-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1a202c;
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    color: #4299e1;
}

/* Tableau des cours */
.table-container {
    overflow-x: auto;
}

.cours-table {
    width: 100%;
    border-collapse: collapse;
}

.cours-table thead {
    background: #f7fafc;
}

.cours-table th {
    padding: 0.875rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}

.cours-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.2s ease;
}

.cours-table tbody tr:hover {
    background: #f7fafc;
}

.cours-table td {
    padding: 1rem;
    font-size: 0.9rem;
    color: #2d3748;
}

.time-cell {
    font-weight: 600;
    color: #4299e1;
}

.badge-clean {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

.badge-classe {
    background: #e6f2ff;
    color: #2c5282;
}

.badge-matiere {
    color: white;
    font-weight: 500;
}

.badge-nouvelle {
    background: #e6f2ff;
    color: #2c5282;
}

.badge-success-light {
    background: #d4f4dd;
    color: #22543d;
}

.badge-danger-light {
    background: #ffe5e5;
    color: #742a2a;
}

.badge-info-light {
    background: #e6f2ff;
    color: #2c5282;
}

/* Barre de progression */
.progress-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.progress-bar-clean {
    flex: 1;
    height: 6px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

.progress-excellent { background: #48bb78; }
.progress-good { background: #ecc94b; }
.progress-poor { background: #f56565; }

.progress-text {
    font-size: 0.875rem;
    font-weight: 600;
    min-width: 45px;
    text-align: right;
}

.text-excellent { color: #48bb78; }
.text-good { color: #ecc94b; }
.text-poor { color: #f56565; }

/* Bouton d'action */
.btn-action {
    background: transparent;
    color: #4299e1;
    border: none;
    padding: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 1.125rem;
    border-radius: 6px;
}

.btn-action:hover {
    background: #e6f2ff;
    color: #2c5282;
}

.btn-details-link {
    background: #4299e1;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.btn-details-link:hover {
    background: #3182ce;
}

/* Ã‰tat vide */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state i {
    font-size: 3rem;
    color: #cbd5e0;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.125rem;
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #718096;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .cours-table {
        font-size: 0.85rem;
    }

    .cours-table th,
    .cours-table td {
        padding: 0.75rem 0.5rem;
    }
}
</style>

<div class="p-4 md:p-8">
    <!-- En-tÃªte -->
    <div class="header-actions">
        <div class="page-header-clean">
            <h1>
                <i class="fas fa-chalkboard-teacher"></i>
                PrÃ©sences par Cours (<?= date('Y', strtotime($date)) ?> - <?= date('Y', strtotime($date)) + 1 ?>)
            </h1>
            <p>Consultez les prÃ©sences pour chaque cours de la journÃ©e</p>
        </div>
        <a href="/presences/historique" class="btn-retour">
            <i class="fas fa-history"></i>
            Historique
        </a>
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
                    <h3>PrÃ©sents</h3>
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
                    <h3>Taux de prÃ©sence</h3>
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
        <form method="GET" action="/presences">
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
                    <label for="classe_id">Filtrer par Classe</label>
                    <select id="classe_id" name="classe_id" onchange="this.form.submit()">
                        <option value="">Toutes les classes</option>
                        <?php foreach ($classes as $classe): ?>
                            <option value="<?= $classe['id'] ?>"
                                    <?= $classe_id == $classe['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($classe['code']) ?> - <?= htmlspecialchars($classe['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="enseignant_id">Filtrer par Enseignant</label>
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
                <h3>Aucun cours programmÃ©</h3>
                <p>Il n'y a pas de cours prÃ©vu pour cette date avec les filtres sÃ©lectionnÃ©s.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="cours-table">
                    <thead>
                        <tr>
                            <th>Horaire</th>
                            <th>Classe</th>
                            <th>MatiÃ¨re</th>
                            <th>Enseignant</th>
                            <th>Effectif</th>
                            <th>PrÃ©sents</th>
                            <th>Absents</th>
                            <th>Taux</th>
                            <th>Actions</th>
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
                                    <a href="/presences/details-cours?emploi_temps_id=<?= $c['id'] ?>&date=<?= $date ?>"
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
