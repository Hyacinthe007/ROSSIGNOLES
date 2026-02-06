<?php
$pageTitle = "Détails du Cours - Présences";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-users"></i> Liste de Présence</h1>
        <div class="actions">
            <a href="/presences?date=<?= $date ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>

    <!-- Informations du cours -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informations du Cours</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Date :</strong><br>
                    <?= date('d/m/Y', strtotime($date)) ?>
                    (<?= ucfirst(strftime('%A', strtotime($date))) ?>)
                </div>
                <div class="col-md-3">
                    <strong>Horaire :</strong><br>
                    <?= date('H:i', strtotime($cours['heure_debut'])) ?> 
                    - 
                    <?= date('H:i', strtotime($cours['heure_fin'])) ?>
                </div>
                <div class="col-md-3">
                    <strong>Classe :</strong><br>
                    <span class="badge bg-secondary fs-6">
                        <?= htmlspecialchars($cours['classe_code']) ?>
                    </span>
                </div>
                <div class="col-md-3">
                    <strong>Matière :</strong><br>
                    <span class="badge fs-6" style="background-color: <?= htmlspecialchars($cours['couleur'] ?? '#3498db') ?>">
                        <?= htmlspecialchars($cours['matiere_nom']) ?>
                    </span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <strong>Enseignant :</strong><br>
                    <?= htmlspecialchars($cours['enseignant_nom'] ?? 'Non assigné') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['total'] ?></div>
                    <div class="stat-label">Effectif total</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['presents'] ?></div>
                    <div class="stat-label">Présents</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['absents'] ?></div>
                    <div class="stat-label">Absents</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon <?= $stats['taux_presence'] >= 90 ? 'bg-success' : ($stats['taux_presence'] >= 75 ? 'bg-warning' : 'bg-danger') ?>">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= $stats['taux_presence'] ?>%</div>
                    <div class="stat-label">Taux de présence</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des élèves -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Liste des Élèves</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="80">Photo</th>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th width="120" class="text-center">Statut</th>
                            <th>Motif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 1; ?>
                        <?php foreach ($eleves as $eleve): ?>
                            <tr class="<?= $eleve['present'] ? 'table-success-light' : 'table-danger-light' ?>">
                                <td><?= $index++ ?></td>
                                <td>
                                    <?php if (!empty($eleve['photo'])): ?>
                                        <img src="/public/uploads/eleves/<?= htmlspecialchars($eleve['photo']) ?>" 
                                             alt="Photo" 
                                             class="rounded-circle" 
                                             width="40" 
                                             height="40"
                                             style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?= strtoupper(substr($eleve['nom'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                                <td><strong><?= htmlspecialchars($eleve['nom']) ?></strong></td>
                                <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                                <td class="text-center">
                                    <?php if ($eleve['present']): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Présent
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Absent
                                        </span>
                                        <?php if ($eleve['absence'] && $eleve['absence']['justifiee']): ?>
                                            <span class="badge bg-warning text-dark ms-1">
                                                <i class="fas fa-file-alt"></i> Justifié
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$eleve['present'] && $eleve['absence']): ?>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($eleve['absence']['motif'] ?? '-') ?>
                                        </small>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 1.5rem;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: #2c3e50;
}

.stat-label {
    color: #7f8c8d;
    font-size: 0.9rem;
}

.table-success-light {
    background-color: #d4edda !important;
}

.table-danger-light {
    background-color: #f8d7da !important;
}

.avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

@media print {
    .page-header .actions,
    .sidebar,
    .no-print {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
}
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
