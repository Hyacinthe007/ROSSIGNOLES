<?php
$pageTitle = "Historique des Présences";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<style>
/* Styles cohérents avec presences/index.php et verification.php */
.page-header-clean {
    margin-bottom: 0;
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
    flex-wrap: wrap;
    gap: 1rem;
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
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
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
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s ease;
}

.stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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
}

.icon-blue { background: #e6f2ff; color: #4299e1; }
.icon-green { background: #d4f4dd; color: #48bb78; }
.icon-red { background: #ffe5e5; color: #f56565; }
.icon-purple { background: #f3e8ff; color: #9f7aea; }
.icon-orange { background: #fef3c7; color: #ed8936; }

/* Filtres */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.filters-section h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #1a202c;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filters-section h3 i {
    color: #4299e1;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-filter:hover {
    background: #3182ce;
    transform: translateY(-1px);
}

.btn-reset {
    background: #edf2f7;
    color: #4a5568;
    border: none;
    padding: 0.625rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-reset:hover {
    background: #e2e8f0;
    color: #2d3748;
    text-decoration: none;
}

/* Section principale */
.content-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
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

.section-subtitle {
    font-size: 0.875rem;
    color: #718096;
    margin-left: auto;
    font-weight: 400;
}

/* Tableau */
.table-container {
    overflow-x: auto;
}

.historique-table {
    width: 100%;
    border-collapse: collapse;
}

.historique-table thead {
    background: #f7fafc;
}

.historique-table th {
    padding: 0.875rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #090a0bff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}

.historique-table th.text-center {
    text-align: center;
}

.historique-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.2s ease;
}

.historique-table tbody tr:hover {
    background: #f7fafc;
}

.historique-table td {
    padding: 1rem;
    font-size: 0.9rem;
    color: #2d3748;
    white-space: nowrap;
}

.historique-table td.text-center {
    text-align: center;
}

.historique-table tfoot tr {
    background: #f7fafc;
    border-top: 2px solid #e2e8f0;
}

.historique-table tfoot td {
    padding: 1rem;
    font-weight: 600;
    font-size: 0.9rem;
    color: #1a202c;
}

/* Badges */
.badge-clean {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
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

.badge-jour {
    background: #edf2f7;
    color: #4a5568;
    font-size: 0.8rem;
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

.badge-neutral {
    background: #edf2f7;
    color: #718096;
}

/* Barre de progression */
.progress-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 120px;
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

/* Cartes d'analyse par classe */
.analyse-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
    margin-top: 1rem;
}

.analyse-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.2s ease;
}

.analyse-card:hover {
    border-color: #4299e1;
    box-shadow: 0 4px 12px rgba(66, 153, 225, 0.1);
    transform: translateY(-2px);
}

.analyse-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.analyse-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1a202c;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.analyse-card-taux {
    font-size: 1.25rem;
    font-weight: 700;
}

.analyse-progress-bar {
    width: 100%;
    height: 10px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 1rem;
}

.analyse-progress-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.6s ease;
}

.analyse-stats {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.analyse-stat {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.85rem;
}

.analyse-stat i {
    font-size: 0.75rem;
}

.stat-present { color: #48bb78; }
.stat-absent { color: #f56565; }

/* État vide */
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

/* Info bannière */
.info-banner {
    background: linear-gradient(135deg, #ebf8ff 0%, #f0fff4 100%);
    border: 1px solid #bee3f8;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.info-banner i {
    color: #4299e1;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.info-banner-text {
    font-size: 0.9rem;
    color: #2d3748;
}

.info-banner-text strong {
    color: #1a202c;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .analyse-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-row {
        grid-template-columns: 1fr 1fr;
    }
}

@media print {
    .filters-section,
    .header-actions .btn-retour,
    .sidebar,
    .no-print {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
}
</style>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="header-actions">
        <div class="page-header-clean">
            <h1>
                <i class="fas fa-history"></i>
                Historique des Présences
            </h1>
            <p>Statistiques de présence par cours sur une période donnée</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= url('presences') ?>" class="btn-retour">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
            <button onclick="window.print()" class="btn-retour" style="background: #805ad5;">
                <i class="fas fa-print"></i>
                Imprimer
            </button>
        </div>
    </div>

    <?php if (!empty($historique)):
        $totalCoursEffectues = array_sum(array_column($historique, 'nb_cours_effectues'));
        $totalPresentsGlobal = array_sum(array_column($historique, 'total_presents'));
        $totalAbsentsGlobal = array_sum(array_column($historique, 'total_absents'));
        $totalGlobal = $totalPresentsGlobal + $totalAbsentsGlobal;
        $tauxGlobal = $totalGlobal > 0 ? round(($totalPresentsGlobal / $totalGlobal) * 100, 1) : 0;
    ?>
    <!-- Statistiques globales -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-content">
                <h3>Cours effectués</h3>
                <p class="stat-number"><?= $totalCoursEffectues ?></p>
            </div>
            <div class="stat-icon icon-blue">
                <i class="fas fa-chalkboard"></i>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-content">
                <h3>Total présents</h3>
                <p class="stat-number"><?= $totalPresentsGlobal ?></p>
            </div>
            <div class="stat-icon icon-green">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-content">
                <h3>Total absents</h3>
                <p class="stat-number"><?= $totalAbsentsGlobal ?></p>
            </div>
            <div class="stat-icon icon-red">
                <i class="fas fa-user-times"></i>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-content">
                <h3>Taux moyen</h3>
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
        <h3><i class="fas fa-filter"></i> Filtrer l'historique</h3>
        <form method="GET" action="<?= url('presences/historique') ?>">
            <div class="filters-grid">
                <div class="filter-field">
                    <label for="date_debut"><i class="fas fa-calendar-alt" style="margin-right: 4px; color: #4299e1;"></i>Date début</label>
                    <input type="date" 
                           id="date_debut" 
                           name="date_debut" 
                           value="<?= htmlspecialchars($date_debut) ?>">
                </div>
                
                <div class="filter-field">
                    <label for="date_fin"><i class="fas fa-calendar-alt" style="margin-right: 4px; color: #4299e1;"></i>Date fin</label>
                    <input type="date" 
                           id="date_fin" 
                           name="date_fin" 
                           value="<?= htmlspecialchars($date_fin) ?>">
                </div>
                
                <div class="filter-field">
                    <label for="classe_id"><i class="fas fa-chalkboard-teacher" style="margin-right: 4px; color: #4299e1;"></i>Classe</label>
                    <select id="classe_id" name="classe_id">
                        <option value="">Toutes les classes</option>
                        <optgroup label="Secondaire">
                            <?php 
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
                    <label for="enseignant_id"><i class="fas fa-user-tie" style="margin-right: 4px; color: #4299e1;"></i>Enseignant</label>
                    <select id="enseignant_id" name="enseignant_id">
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
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="<?= url('presences/historique') ?>" class="btn-reset">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Info bannière -->
    <div class="info-banner">
        <i class="fas fa-info-circle"></i>
        <div class="info-banner-text">
            Statistiques de présence du 
            <strong><?= date('d/m/Y', strtotime($date_debut)) ?></strong> 
            au 
            <strong><?= date('d/m/Y', strtotime($date_fin)) ?></strong>
            <?php if (!empty($historique)): ?>
                — <strong><?= count($historique) ?></strong> cours analysés
            <?php endif; ?>
        </div>
    </div>

    <!-- Tableau des résultats -->
    <div class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <h2 class="section-title" style="margin-bottom: 0;">
                <i class="fas fa-table"></i>
                Détail par cours
            </h2>
        </div>

        <?php if (empty($historique)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>Aucun cours trouvé</h3>
                <p>Aucun cours trouvé pour cette période avec les filtres sélectionnés.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="historique-table">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-calendar-day mr-2"></i>Jour</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-clock mr-2"></i>Horaire</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-chalkboard-teacher mr-2"></i>Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-book mr-2"></i>Matière</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-user-tie mr-2"></i>Enseignant</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-check-double mr-2"></i>Cours</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-users mr-2"></i>Effectif</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-user-check mr-2"></i>Présents</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-user-times mr-2"></i>Absents</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider"><i class="fas fa-chart-bar mr-2"></i>Taux</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historique as $h): ?>
                            <?php $c = $h['cours']; ?>
                            <tr>
                                <td>
                                    <span class="badge-clean badge-jour">
                                        <?= ucfirst($c['jour_semaine']) ?>
                                    </span>
                                </td>
                                <td style="font-weight: 500; color: #4299e1;">
                                    <?= date('H:i', strtotime($c['heure_debut'])) ?>
                                    -
                                    <?= date('H:i', strtotime($c['heure_fin'])) ?>
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
                                <td>
                                    <span style="font-size: 0.875rem; color: #4a5568;">
                                        <?= htmlspecialchars($c['enseignant_nom'] ?? 'Non assigné') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-clean badge-info-light">
                                        <?= $h['nb_cours_effectues'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span style="font-weight: 500; color: #4a5568;">
                                        <?= $h['nb_eleves_total'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-clean badge-success-light">
                                        <i class="fas fa-check" style="font-size: 0.65rem;"></i>
                                        <?= $h['total_presents'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($h['total_absents'] > 0): ?>
                                        <span class="badge-clean badge-danger-light">
                                            <i class="fas fa-times" style="font-size: 0.65rem;"></i>
                                            <?= $h['total_absents'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-clean badge-neutral">0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $taux = $h['taux_presence_moyen'];
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
