<?php
$pageTitle = "Détails du Cours - Présences";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="p-4 md:p-8">
    <!-- En-tête -->
    <div class="header-actions">
        <div class="page-header-clean">
            <h1>
                <i class="fas fa-users"></i>
                Liste de Présence
            </h1>
            <p>Détail des présences pour le cours du <?= date('d/m/Y', strtotime($date)) ?></p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= url('presences') ?>?date=<?= $date ?>" class="btn-retour">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
            <button onclick="window.print()" class="btn-retour" style="background: #805ad5;">
                <i class="fas fa-print"></i>
                Imprimer
            </button>
        </div>
    </div>

    <!-- Informations du cours -->
    <div class="cours-info-card">
        <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: #1a202c; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-chalkboard" style="color: #4299e1;"></i>
            Informations du Cours
        </h3>
        <div class="cours-info-grid">
            <div class="cours-info-item">
                <span class="info-label">Date</span>
                <span class="info-value">
                    <?= date('d/m/Y', strtotime($date)) ?>
                    (<?= ucfirst(strftime('%A', strtotime($date))) ?>)
                </span>
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

    <!-- Statistiques -->
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

    <!-- Liste des élèves -->
    <div class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <h2 class="section-title" style="margin-bottom: 0;">
                <i class="fas fa-list-check"></i>
                Liste des Élèves
            </h2>
            <div class="filter-tabs" id="statusFilter">
                <button class="filter-tab active" data-filter="all" onclick="filterByStatus('all', this)">
                    Tous (<?= count($eleves) ?>)
                </button>
                <button class="filter-tab" data-filter="present" onclick="filterByStatus('present', this)">
                    <i class="fas fa-check-circle" style="color: #48bb78;"></i> Présents (<?= $stats['presents'] ?>)
                </button>
                <button class="filter-tab" data-filter="absent" onclick="filterByStatus('absent', this)">
                    <i class="fas fa-times-circle" style="color: #f56565;"></i> Absents (<?= $stats['absents'] ?>)
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
                            <tr class="<?= $eleve['present'] ? 'row-present' : 'row-absent' ?>"
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
</div>

<script>
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
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
