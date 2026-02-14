<?php
$pageTitle = "Vérification des Présences";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<style>
/* Styles cohérents avec presences/index.php */
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
    color: white;
    text-decoration: none;
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
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-filter:hover {
    background: #3182ce;
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

/* Résultat de vérification */
.verification-result {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    border-left: 5px solid;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.verification-result.present {
    border-left-color: #48bb78;
    background: linear-gradient(135deg, #f0fff4 0%, #fff 50%);
}

.verification-result.absent {
    border-left-color: #f56565;
    background: linear-gradient(135deg, #fff5f5 0%, #fff 50%);
}

.result-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.result-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
    flex-shrink: 0;
}

.result-icon.icon-present {
    background: linear-gradient(135deg, #48bb78, #38a169);
}

.result-icon.icon-absent {
    background: linear-gradient(135deg, #f56565, #e53e3e);
}

.result-info h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
    color: #1a202c;
}

.result-info p {
    margin: 0;
    color: #718096;
    font-size: 0.95rem;
}

.result-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.result-detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.result-detail-item .label {
    font-size: 0.8rem;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.result-detail-item .value {
    font-size: 0.95rem;
    color: #2d3748;
    font-weight: 500;
}

/* Info cours */
.cours-info-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e2e8f0;
}

.cours-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.5rem;
}

.cours-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.cours-info-item .info-label {
    font-size: 0.8rem;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.cours-info-item .info-value {
    font-size: 1rem;
    color: #2d3748;
    font-weight: 600;
}

/* Section principale */
.content-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
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

/* Tableau des élèves */
.table-container {
    overflow-x: auto;
}

.eleves-table {
    width: 100%;
    border-collapse: collapse;
}

.eleves-table thead {
    background: #f7fafc;
}

.eleves-table th {
    padding: 0.875rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}

.eleves-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.2s ease;
}

.eleves-table tbody tr:hover {
    background: #f7fafc;
}

.eleves-table tbody tr.row-present {
    background: #f0fff4;
}

.eleves-table tbody tr.row-present:hover {
    background: #e6ffec;
}

.eleves-table tbody tr.row-absent {
    background: #fff5f5;
}

.eleves-table tbody tr.row-absent:hover {
    background: #ffe5e5;
}

.eleves-table tbody tr.row-highlighted {
    background: #fffbeb;
    border: 2px solid #f6e05e;
    animation: highlight-pulse 2s ease-in-out;
}

@keyframes highlight-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(246, 224, 94, 0.4); }
    50% { box-shadow: 0 0 0 8px rgba(246, 224, 94, 0); }
}

.eleves-table td {
    padding: 1rem;
    font-size: 0.9rem;
    color: #2d3748;
}

/* Avatar */
.avatar-small {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-placeholder-sm {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
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

.badge-present {
    background: #d4f4dd;
    color: #22543d;
}

.badge-absent {
    background: #ffe5e5;
    color: #742a2a;
}

.badge-justified {
    background: #fefce8;
    color: #854d0e;
}

.badge-matiere {
    color: white;
    font-weight: 500;
}

.badge-classe {
    background: #e6f2ff;
    color: #2c5282;
}

/* Cours disponibles cards */
.cours-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.cours-card {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.cours-card:hover {
    border-color: #4299e1;
    transform: translateY(-2px);
    text-decoration: none;
    color: inherit;
}

.cours-card.active {
    border-color: #4299e1;
    background: #ebf8ff;
}

.cours-card-time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #4299e1;
    font-size: 1rem;
}

.cours-card-details {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

/* Recherche élève */
.search-eleve-container {
    position: relative;
    max-width: 400px;
}

.search-eleve-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: white;
}

.search-eleve-input:focus {
    outline: none;
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 0.95rem;
}

.search-results-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    max-height: 250px;
    overflow-y: auto;
    z-index: 100;
    display: none;
    margin-top: 4px;
}

.search-result-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-bottom: 1px solid #f7fafc;
    text-decoration: none;
    color: inherit;
}

.search-result-item:hover {
    background: #ebf8ff;
    text-decoration: none;
    color: inherit;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-name {
    font-weight: 500;
    color: #2d3748;
}

.search-result-matricule {
    font-size: 0.8rem;
    color: #a0aec0;
}

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

/* Steps */
.steps-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.step-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.step-item.step-done {
    background: #d4f4dd;
    color: #22543d;
}

.step-item.step-active {
    background: #e6f2ff;
    color: #2c5282;
    box-shadow: 0 0 0 2px #4299e1;
}

.step-item.step-pending {
    background: #edf2f7;
    color: #a0aec0;
}

.step-arrow {
    color: #cbd5e0;
    font-size: 0.75rem;
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

/* Onglets filtre */
.filter-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.filter-tab {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
}

.filter-tab.active {
    background: #4299e1;
    color: white;
}

.filter-tab:not(.active) {
    background: #edf2f7;
    color: #4a5568;
}

.filter-tab:not(.active):hover {
    background: #e2e8f0;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .cours-cards {
        grid-template-columns: 1fr;
    }
    
    .result-details {
        grid-template-columns: 1fr 1fr;
    }
    
    .steps-indicator {
        flex-direction: column;
        align-items: stretch;
    }
    
    .step-arrow {
        display: none;
    }
}

@media print {
    .filters-section,
    .header-actions .btn-retour,
    .search-eleve-container,
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
                <i class="fas fa-search"></i>
                Vérification des Présences
            </h1>
            <p>Vérifiez la présence d'un élève pour un cours passé avec un enseignant donné</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= url('presences') ?>" class="btn-retour">
                <i class="fas fa-clipboard-check"></i>
                Présences du jour
            </a>
            <a href="<?= url('presences/historique') ?>" class="btn-retour" style="background: #805ad5;">
                <i class="fas fa-history"></i>
                Historique
            </a>
        </div>
    </div>

    <!-- Indicateur d'étapes -->
    <div class="steps-indicator">
        <span class="step-item <?= $enseignant_id ? 'step-done' : 'step-active' ?>">
            <i class="fas <?= $enseignant_id ? 'fa-check-circle' : 'fa-circle' ?>"></i>
            1. Enseignant
        </span>
        <i class="fas fa-chevron-right step-arrow"></i>
        <span class="step-item <?= ($enseignant_id && $date) ? 'step-done' : ($enseignant_id ? 'step-active' : 'step-pending') ?>">
            <i class="fas <?= ($enseignant_id && $date) ? 'fa-check-circle' : 'fa-circle' ?>"></i>
            2. Date
        </span>
        <i class="fas fa-chevron-right step-arrow"></i>
        <span class="step-item <?= $emploi_temps_id ? 'step-done' : (($enseignant_id && $date) ? 'step-active' : 'step-pending') ?>">
            <i class="fas <?= $emploi_temps_id ? 'fa-check-circle' : 'fa-circle' ?>"></i>
            3. Cours
        </span>
        <i class="fas fa-chevron-right step-arrow"></i>
        <span class="step-item <?= $eleve_id ? 'step-done' : ($emploi_temps_id ? 'step-active' : 'step-pending') ?>">
            <i class="fas <?= $eleve_id ? 'fa-check-circle' : 'fa-circle' ?>"></i>
            4. Vérifier
        </span>
    </div>
    
    <!-- Statistiques -->
    <?php if ($stats): ?>
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-content">
                        <h3>Effectif total</h3>
                        <p class="stat-number"><?= $stats['total'] ?></p>
                    </div>
                    <div class="stat-icon icon-blue">
                        <i class="fas fa-users"></i>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-content">
                        <h3>Présents</h3>
                        <p class="stat-number"><?= $stats['presents'] ?></p>
                    </div>
                    <div class="stat-icon icon-green">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-content">
                        <h3>Absents</h3>
                        <p class="stat-number"><?= $stats['absents'] ?></p>
                    </div>
                    <div class="stat-icon icon-red">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-content">
                        <h3>Taux de présence</h3>
                        <p class="stat-number"><?= $stats['taux_presence'] ?>%</p>
                    </div>
                    <div class="stat-icon icon-purple">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    
    <!-- Filtres -->
    <div class="filters-section">
        <h3><i class="fas fa-filter"></i> Critères de recherche</h3>
        <form method="GET" action="<?= url('presences/verification') ?>" id="verificationForm">
            <div class="filters-grid">
                <div class="filter-field">
                    <label for="enseignant_id"><i class="fas fa-user-tie"></i> Enseignant</label>
                    <select id="enseignant_id" name="enseignant_id" onchange="onEnseignantChange()">
                        <option value="">-- Sélectionner un enseignant --</option>
                        <?php foreach ($enseignants as $enseignant): ?>
                            <option value="<?= $enseignant['id'] ?>"
                                    <?= $enseignant_id == $enseignant['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($enseignant['nom_complet']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="date"><i class="fas fa-calendar-alt"></i> Date du cours</label>
                    <input type="date"
                           id="date"
                           name="date"
                           value="<?= htmlspecialchars($date ?? '') ?>"
                           max="<?= date('Y-m-d') ?>"
                           onchange="onDateChange()">
                </div>

                <?php if (!empty($cours_disponibles)): ?>
                <div class="filter-field">
                    <label for="emploi_temps_id"><i class="fas fa-clock"></i> Créneau horaire</label>
                    <select id="emploi_temps_id" name="emploi_temps_id" onchange="this.form.submit()">
                        <option value="">-- Sélectionner un cours --</option>
                        <?php foreach ($cours_disponibles as $cd): ?>
                            <option value="<?= $cd['id'] ?>"
                                    <?= $emploi_temps_id == $cd['id'] ? 'selected' : '' ?>>
                                <?= date('H:i', strtotime($cd['heure_debut'])) ?>-<?= date('H:i', strtotime($cd['heure_fin'])) ?> | <?= htmlspecialchars($cd['matiere_nom']) ?> (<?= htmlspecialchars($cd['classe_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="filter-field" style="flex-direction: row; gap: 0.5rem; align-items: flex-end;">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="<?= url('presences/verification') ?>" class="btn-reset">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </a>
                </div>
            </div>
            
            <!-- Champ caché pour eleve_id -->
            <input type="hidden" name="eleve_id" id="hidden_eleve_id" value="<?= htmlspecialchars($eleve_id ?? '') ?>">
        </form>
    </div>

    <?php if ($enseignant_id && $date && empty($cours_disponibles) && !$emploi_temps_id): ?>
        <!-- Aucun cours trouvé -->
        <div class="content-section">
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>Aucun cours trouvé</h3>
                <p>L'enseignant sélectionné n'a pas de cours programmé à cette date.<br>Vérifiez la date ou changez d'enseignant.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($cours): ?>
        <!-- Informations du cours sélectionné -->
        <div class="cours-info-card">
            <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: #1a202c; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chalkboard" style="color: #4299e1;"></i> 
                Cours sélectionné
            </h3>
            <div class="cours-info-grid">
                <div class="cours-info-item">
                    <span class="info-label">Date</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($date)) ?></span>
                </div>
                <div class="cours-info-item">
                    <span class="info-label">Horaire</span>
                    <span class="info-value" style="color: #4299e1;">
                        <?= date('H:i', strtotime($cours['heure_debut'])) ?> - <?= date('H:i', strtotime($cours['heure_fin'])) ?>
                    </span>
                </div>
                <div class="cours-info-item">
                    <span class="info-label">Classe</span>
                    <span class="info-value">
                        <span class="badge-clean badge-classe"><?= htmlspecialchars($cours['classe_code']) ?></span>
                    </span>
                </div>
                <div class="cours-info-item">
                    <span class="info-label">Matière</span>
                    <span class="info-value">
                        <span class="badge-clean badge-matiere" style="background-color: <?= htmlspecialchars($cours['couleur'] ?? '#4299e1') ?>">
                            <?= htmlspecialchars($cours['matiere_nom']) ?>
                        </span>
                    </span>
                </div>
                <div class="cours-info-item">
                    <span class="info-label">Enseignant</span>
                    <span class="info-value"><?= htmlspecialchars($cours['enseignant_nom'] ?? 'Non assigné') ?></span>
                </div>
            </div>
        </div>

        <!-- Résultat de vérification -->
        <?php if ($resultat_verification): ?>
            <div class="verification-result <?= $resultat_verification['present'] ? 'present' : 'absent' ?>">
                <div class="result-header">
                    <div class="result-icon <?= $resultat_verification['present'] ? 'icon-present' : 'icon-absent' ?>">
                        <i class="fas <?= $resultat_verification['present'] ? 'fa-check' : 'fa-times' ?>"></i>
                    </div>
                    <div class="result-info">
                        <h3>
                            <?= htmlspecialchars($resultat_verification['eleve']['nom'] . ' ' . $resultat_verification['eleve']['prenom']) ?>
                            <?php if ($resultat_verification['present']): ?>
                                — <span style="color: #48bb78;">Présent(e)</span>
                            <?php else: ?>
                                — <span style="color: #f56565;">Absent(e)</span>
                                <?php if ($resultat_verification['absence'] && $resultat_verification['absence']['justifiee']): ?>
                                    <span class="badge-clean badge-justified" style="margin-left: 0.5rem;">
                                        <i class="fas fa-file-alt"></i> Justifié
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </h3>
                        <p>Matricule : <?= htmlspecialchars($resultat_verification['eleve']['matricule']) ?></p>
                    </div>
                </div>
                <div class="result-details">
                    <div class="result-detail-item">
                        <span class="label">Date</span>
                        <span class="value"><?= date('d/m/Y', strtotime($resultat_verification['date'])) ?></span>
                    </div>
                    <div class="result-detail-item">
                        <span class="label">Horaire</span>
                        <span class="value">
                            <?= date('H:i', strtotime($resultat_verification['cours']['heure_debut'])) ?> - 
                            <?= date('H:i', strtotime($resultat_verification['cours']['heure_fin'])) ?>
                        </span>
                    </div>
                    <div class="result-detail-item">
                        <span class="label">Matière</span>
                        <span class="value"><?= htmlspecialchars($resultat_verification['cours']['matiere_nom']) ?></span>
                    </div>
                    <div class="result-detail-item">
                        <span class="label">Classe</span>
                        <span class="value"><?= htmlspecialchars($resultat_verification['cours']['classe_code']) ?></span>
                    </div>
                    <?php if (!$resultat_verification['present'] && $resultat_verification['absence']): ?>
                    <div class="result-detail-item">
                        <span class="label">Motif d'absence</span>
                        <span class="value"><?= htmlspecialchars($resultat_verification['absence']['motif'] ?? 'Non renseigné') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Liste complète des élèves -->
        <div class="content-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="section-title" style="margin-bottom: 0;">
                    <i class="fas fa-list-check"></i>
                    Liste de présence
                </h2>
                <div class="filter-tabs" id="statusFilter">
                    <button class="filter-tab active" data-filter="all" onclick="filterByStatus('all', this)">
                        Tous (<?= count($eleves) ?>)
                    </button>
                    <button class="filter-tab" data-filter="present" onclick="filterByStatus('present', this)">
                        <i class="fas fa-check-circle" style="color: #48bb78;"></i> Présents (<?= $stats ? $stats['presents'] : 0 ?>)
                    </button>
                    <button class="filter-tab" data-filter="absent" onclick="filterByStatus('absent', this)">
                        <i class="fas fa-times-circle" style="color: #f56565;"></i> Absents (<?= $stats ? $stats['absents'] : 0 ?>)
                    </button>
                </div>
            </div>

            <?php if (empty($eleves)): ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <h3>Aucun élève trouvé</h3>
                    <p>Il n'y a pas d'élève inscrit dans cette classe.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="eleves-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 50px;">Photo</th>
                                <th><i class="fas fa-id-card" style="margin-right: 4px;"></i>Matricule</th>
                                <th><i class="fas fa-user" style="margin-right: 4px;"></i>Nom</th>
                                <th><i class="fas fa-user" style="margin-right: 4px;"></i>Prénom</th>
                                <th style="width: 130px; text-align: center;"><i class="fas fa-clipboard-check" style="margin-right: 4px;"></i>Statut</th>
                                <th><i class="fas fa-comment" style="margin-right: 4px;"></i>Motif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $index = 1; ?>
                            <?php foreach ($eleves as $eleve): ?>
                                <tr class="<?= $eleve['present'] ? 'row-present' : 'row-absent' ?> <?= ($eleve_id && $eleve['id'] == $eleve_id) ? 'row-highlighted' : '' ?>"
                                    data-status="<?= $eleve['present'] ? 'present' : 'absent' ?>">
                                    <td style="font-weight: 500; color: #a0aec0;"><?= $index++ ?></td>
                                    <td>
                                        <?php if (!empty($eleve['photo'])): ?>
                                            <img src="<?= url('public/uploads/eleves/' . htmlspecialchars($eleve['photo'])) ?>" 
                                                 alt="Photo" 
                                                 class="avatar-small">
                                        <?php else: ?>
                                            <div class="avatar-placeholder-sm">
                                                <?= strtoupper(substr($eleve['nom'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-family: monospace; font-size: 0.85rem; color: #718096;">
                                        <?= htmlspecialchars($eleve['matricule']) ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($eleve['nom']) ?></strong></td>
                                    <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                                    <td style="text-align: center;">
                                        <?php if ($eleve['present']): ?>
                                            <span class="badge-clean badge-present">
                                                <i class="fas fa-check-circle"></i> Présent
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-clean badge-absent">
                                                <i class="fas fa-times-circle"></i> Absent
                                            </span>
                                            <?php if ($eleve['absence'] && $eleve['absence']['justifiee']): ?>
                                                <span class="badge-clean badge-justified" style="margin-left: 4px;">
                                                    <i class="fas fa-file-alt"></i>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$eleve['present'] && $eleve['absence']): ?>
                                            <small style="color: #718096;">
                                                <?= htmlspecialchars($eleve['absence']['motif'] ?? '-') ?>
                                            </small>
                                        <?php else: ?>
                                            <span style="color: #cbd5e0;">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Bouton imprimer -->
                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button onclick="window.print()" class="btn-filter" style="background: #805ad5;">
                        <i class="fas fa-print"></i> Imprimer la liste
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Soumission automatique quand l'enseignant ou la date change
function onEnseignantChange() {
    // Réinitialiser les champs dépendants
    const dateField = document.getElementById('date');
    const emploiTempsField = document.getElementById('emploi_temps_id');
    const hiddenEleve = document.getElementById('hidden_eleve_id');
    
    if (emploiTempsField) {
        emploiTempsField.value = '';
    }
    if (hiddenEleve) {
        hiddenEleve.value = '';
    }
    
    // Soumettre si date est remplie
    if (dateField && dateField.value) {
        document.getElementById('verificationForm').submit();
    }
}

function onDateChange() {
    const enseignantField = document.getElementById('enseignant_id');
    const emploiTempsField = document.getElementById('emploi_temps_id');
    const hiddenEleve = document.getElementById('hidden_eleve_id');
    
    if (emploiTempsField) {
        emploiTempsField.value = '';
    }
    if (hiddenEleve) {
        hiddenEleve.value = '';
    }
    
    // Soumettre si enseignant est sélectionné
    if (enseignantField && enseignantField.value) {
        document.getElementById('verificationForm').submit();
    }
}

// Recherche d'élève avec autocomplétion
const searchInput = document.getElementById('searchEleveInput');
const searchDropdown = document.getElementById('searchResultsDropdown');
let searchTimeout;

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchDropdown.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            const classeId = <?= json_encode($cours ? $cours['classe_id'] : null) ?>;
            fetch(`<?= url('presences/api/search-eleve') ?>?q=${encodeURIComponent(query)}&classe_id=${classeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        searchDropdown.innerHTML = '<div style="padding: 1rem; text-align: center; color: #a0aec0;"><i class="fas fa-search"></i> Aucun élève trouvé</div>';
                    } else {
                        searchDropdown.innerHTML = data.map(eleve => {
                            const initial = eleve.nom ? eleve.nom.charAt(0).toUpperCase() : '?';
                            const photo = eleve.photo 
                                ? `<img src="<?= url('public/uploads/eleves') ?>/${eleve.photo}" class="avatar-small" alt="">`
                                : `<div class="avatar-placeholder-sm">${initial}</div>`;
                            
                            return `<a href="#" class="search-result-item" onclick="selectEleve(${eleve.id}); return false;">
                                ${photo}
                                <div>
                                    <div class="search-result-name">${eleve.nom} ${eleve.prenom}</div>
                                    <div class="search-result-matricule">${eleve.matricule}</div>
                                </div>
                            </a>`;
                        }).join('');
                    }
                    searchDropdown.style.display = 'block';
                })
                .catch(() => {
                    searchDropdown.style.display = 'none';
                });
        }, 300);
    });

    // Fermer le dropdown quand on clique en dehors
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.style.display = 'none';
        }
    });
}

function selectEleve(eleveId) {
    document.getElementById('hidden_eleve_id').value = eleveId;
    document.getElementById('verificationForm').submit();
}

// Filtrage par statut
function filterByStatus(status, button) {
    // Mettre à jour les onglets
    document.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
    button.classList.add('active');
    
    // Filtrer les lignes
    document.querySelectorAll('.eleves-table tbody tr').forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else {
            row.style.display = row.dataset.status === status ? '' : 'none';
        }
    });
}

// Si un élève est surligné, faire défiler jusqu'à lui
document.addEventListener('DOMContentLoaded', function() {
    const highlighted = document.querySelector('.row-highlighted');
    if (highlighted) {
        setTimeout(() => {
            highlighted.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 500);
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
