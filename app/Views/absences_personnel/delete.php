<?php
$title = "Supprimer une Absence du Personnel";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Personnel', 'url' => '/personnel/list'],
    ['label' => 'Absences', 'url' => '/absences_personnel/list'],
    ['label' => 'Supprimer']
];
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Supprimer une Absence du Personnel</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Confirmation requise</h5>
                    Êtes-vous sûr de vouloir supprimer définitivement cette absence ?
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Personnel :</th>
                                <td>
                                    <?= htmlspecialchars($absence['personnel_nom'] . ' ' . $absence['personnel_prenom']) ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($absence['matricule']) ?></small>
                                </td>
                            </tr>
                            <tr>
                                <th>Type d'absence :</th>
                                <td>
                                    <?php
                                    $types = [
                                        'conge_annuel' => 'Congé annuel',
                                        'conge_maladie' => 'Congé maladie',
                                        'conge_maternite' => 'Congé maternité',
                                        'conge_paternite' => 'Congé paternité',
                                        'conge_sans_solde' => 'Congé sans solde',
                                        'absence_autorisee' => 'Absence autorisée',
                                        'absence_non_justifiee' => 'Absence non justifiée',
                                        'formation' => 'Formation',
                                        'mission' => 'Mission',
                                        'autre' => 'Autre'
                                    ];
                                    echo htmlspecialchars($types[$absence['type_absence']] ?? $absence['type_absence']);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Dates :</th>
                                <td>
                                    Du <?= date('d/m/Y', strtotime($absence['date_debut'])) ?>
                                    au <?= date('d/m/Y', strtotime($absence['date_fin'])) ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Statut :</th>
                                <td>
                                    <?php
                                    $statuts = [
                                        'demande' => ['Demande', 'warning'],
                                        'validee' => ['Validée', 'success'],
                                        'refusee' => ['Refusée', 'danger'],
                                        'annulee' => ['Annulée', 'secondary']
                                    ];
                                    $statutInfo = $statuts[$absence['statut']] ?? [$absence['statut'], 'secondary'];
                                    ?>
                                    <span class="badge badge-<?= $statutInfo[1] ?>"><?= $statutInfo[0] ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <form method="POST" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Oui, supprimer
                    </button>
                </form>
                <a href="/absences_personnel/details/<?= $absence['id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </div>
    </div>
</div>