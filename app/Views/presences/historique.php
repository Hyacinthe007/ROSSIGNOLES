<?php
$pageTitle = "Historique des Présences";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-history"></i> Historique des Présences par Cours</h1>
        <div class="actions">
            <a href="/presences" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/presences/historique" class="row g-3">
                <div class="col-md-3">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_debut" 
                           name="date_debut" 
                           value="<?= htmlspecialchars($date_debut) ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_fin" 
                           name="date_fin" 
                           value="<?= htmlspecialchars($date_fin) ?>">
                </div>
                
                <div class="col-md-2">
                    <label for="classe_id" class="form-label">Classe</label>
                    <select class="form-select" id="classe_id" name="classe_id">
                        <option value="">Toutes</option>
                        <?php foreach ($classes as $classe): ?>
                            <option value="<?= $classe['id'] ?>" 
                                    <?= $classe_id == $classe['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($classe['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="enseignant_id" class="form-label">Enseignant</label>
                    <select class="form-select" id="enseignant_id" name="enseignant_id">
                        <option value="">Tous</option>
                        <?php foreach ($enseignants as $enseignant): ?>
                            <option value="<?= $enseignant['id'] ?>" 
                                    <?= $enseignant_id == $enseignant['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($enseignant['nom_complet']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                Statistiques de présence du 
                <?= date('d/m/Y', strtotime($date_debut)) ?> 
                au 
                <?= date('d/m/Y', strtotime($date_fin)) ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($historique)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Aucun cours trouvé pour cette période avec les filtres sélectionnés.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Jour</th>
                                <th>Horaire</th>
                                <th>Classe</th>
                                <th>Matière</th>
                                <th>Enseignant</th>
                                <th class="text-center">Cours effectués</th>
                                <th class="text-center">Effectif</th>
                                <th class="text-center">Total présents</th>
                                <th class="text-center">Total absents</th>
                                <th class="text-center">Taux moyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historique as $h): ?>
                                <?php $c = $h['cours']; ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= ucfirst($c['jour_semaine']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= date('H:i', strtotime($c['heure_debut'])) ?>
                                        -
                                        <?= date('H:i', strtotime($c['heure_fin'])) ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($c['classe_code']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: <?= htmlspecialchars($c['couleur'] ?? '#3498db') ?>">
                                            <?= htmlspecialchars($c['matiere_nom']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($c['enseignant_nom'] ?? 'Non assigné') ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">
                                            <?= $h['nb_cours_effectues'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?= $h['nb_eleves_total'] ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            <?= $h['total_presents'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($h['total_absents'] > 0): ?>
                                            <span class="badge bg-danger">
                                                <?= $h['total_absents'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $taux = $h['taux_presence_moyen'];
                                        $badgeClass = $taux >= 90 ? 'bg-success' : ($taux >= 75 ? 'bg-warning' : 'bg-danger');
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= $taux ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <td colspan="5" class="text-end">TOTAUX :</td>
                                <td class="text-center">
                                    <?= array_sum(array_column($historique, 'nb_cours_effectues')) ?>
                                </td>
                                <td class="text-center">-</td>
                                <td class="text-center">
                                    <?= array_sum(array_column($historique, 'total_presents')) ?>
                                </td>
                                <td class="text-center">
                                    <?= array_sum(array_column($historique, 'total_absents')) ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $totalPresents = array_sum(array_column($historique, 'total_presents'));
                                    $totalAbsents = array_sum(array_column($historique, 'total_absents'));
                                    $total = $totalPresents + $totalAbsents;
                                    $tauxGlobal = $total > 0 ? round(($totalPresents / $total) * 100, 1) : 0;
                                    ?>
                                    <?= $tauxGlobal ?>%
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Graphique (optionnel) -->
                <div class="mt-4">
                    <h6>Analyse visuelle</h6>
                    <div class="row">
                        <?php
                        // Grouper par classe
                        $parClasse = [];
                        foreach ($historique as $h) {
                            $classeCode = $h['cours']['classe_code'];
                            if (!isset($parClasse[$classeCode])) {
                                $parClasse[$classeCode] = [
                                    'presents' => 0,
                                    'absents' => 0
                                ];
                            }
                            $parClasse[$classeCode]['presents'] += $h['total_presents'];
                            $parClasse[$classeCode]['absents'] += $h['total_absents'];
                        }
                        ?>
                        <?php foreach ($parClasse as $classe => $stats): ?>
                            <?php
                            $total = $stats['presents'] + $stats['absents'];
                            $taux = $total > 0 ? round(($stats['presents'] / $total) * 100, 1) : 0;
                            ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title"><?= htmlspecialchars($classe) ?></h6>
                                        <div class="progress" style="height: 30px;">
                                            <div class="progress-bar bg-success" 
                                                 role="progressbar" 
                                                 style="width: <?= $taux ?>%"
                                                 aria-valuenow="<?= $taux ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?= $taux ?>%
                                            </div>
                                        </div>
                                        <div class="mt-2 text-center">
                                            <small>
                                                <span class="text-success"><?= $stats['presents'] ?> présents</span>
                                                /
                                                <span class="text-danger"><?= $stats['absents'] ?> absents</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
