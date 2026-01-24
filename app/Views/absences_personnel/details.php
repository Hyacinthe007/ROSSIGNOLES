<?php
$title = "Détails de l'Absence du Personnel";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Personnel', 'url' => '/personnel/list'],
    ['label' => 'Absences', 'url' => '/absences_personnel/list'],
    ['label' => 'Détails']
];
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Détails de l'Absence</h3>
                <div class="card-tools">
                    <a href="/absences_personnel/edit/<?= $absence['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="/absences_personnel/delete/<?= $absence['id'] ?>" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> Supprimer
                    </a>
                </div>
            </div>
            <div class="card-body">
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
                            <tr>
                                <th>Dates :</th>
                                <td>
                                    Du <?= date('d/m/Y', strtotime($absence['date_debut'])) ?>
                                    au <?= date('d/m/Y', strtotime($absence['date_fin'])) ?>
                                    <br><small class="text-muted">(<?= htmlspecialchars($absence['nb_jours']) ?> jours ouvrés)</small>
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
                    
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Demandé par :</th>
                                <td>
                                    <?php if (!empty($absence['demande_par_nom'])): ?>
                                        <?= htmlspecialchars($absence['demande_par_nom']) ?>
                                        <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($absence['date_demande'])) ?></small>
                                    <?php else: ?>
                                        <em>Non spécifié</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Validé par :</th>
                                <td>
                                    <?php if (!empty($absence['valide_par_nom'])): ?>
                                        <?= htmlspecialchars($absence['valide_par_nom']) ?>
                                        <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($absence['date_validation'])) ?></small>
                                    <?php else: ?>
                                        <em>Non validé</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if (!empty($absence['remplace_nom'])): ?>
                            <tr>
                                <th>Remplaçant :</th>
                                <td>
                                    <?= htmlspecialchars($absence['remplace_nom'] . ' ' . $absence['remplace_prenom']) ?>
                                    <?php if (!empty($absence['commentaire_remplacement'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($absence['commentaire_remplacement']) ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                
                <?php if (!empty($absence['motif'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Motif :</label>
                            <div class="well well-sm">
                                <?= nl2br(htmlspecialchars($absence['motif'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($absence['piece_justificative'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Pièce justificative :</label>
                            <div>
                                <?= htmlspecialchars($absence['piece_justificative']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($absence['motif_refus'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Motif du refus :</label>
                            <div class="alert alert-danger">
                                <?= nl2br(htmlspecialchars($absence['motif_refus'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="/absences_personnel/list" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
                
                <?php if ($absence['statut'] == 'demande'): ?>
                <div class="float-right">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#validerModal">
                        <i class="fas fa-check"></i> Valider
                    </button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#refuserModal">
                        <i class="fas fa-times"></i> Refuser
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de validation -->
<div class="modal fade" id="validerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Valider l'absence</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir valider cette absence ?</p>
                <div class="form-group">
                    <label for="remplace_par_modal">Personnel remplaçant (optionnel)</label>
                    <select name="remplace_par_modal" id="remplace_par_modal" class="form-control select2">
                        <option value="">Aucun remplaçant</option>
                        <?php 
                        // On réutilise la requête pour récupérer les personnels
                        $personnelModel = new Personnel();
                        $personnels = $personnelModel->query(
                            "SELECT id, matricule, nom, prenom FROM personnels WHERE statut = 'actif' ORDER BY nom ASC"
                        );
                        foreach ($personnels as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['matricule']) ?> - <?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="commentaire_remplacement_modal">Commentaire sur le remplacement</label>
                    <textarea name="commentaire_remplacement_modal" id="commentaire_remplacement_modal" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form method="POST" action="/absences_personnel/valider/<?= $absence['id'] ?>" style="display:inline;">
                    <input type="hidden" name="remplace_par" id="remplace_par_hidden">
                    <input type="hidden" name="commentaire_remplacement" id="commentaire_remplacement_hidden">
                    <button type="submit" class="btn btn-success">Valider</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de refus -->
<div class="modal fade" id="refuserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refuser l'absence</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir refuser cette absence ?</p>
                <div class="form-group">
                    <label for="motif_refus_modal">Motif du refus <span class="text-danger">*</span></label>
                    <textarea name="motif_refus_modal" id="motif_refus_modal" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form method="POST" action="/absences_personnel/refuser/<?= $absence['id'] ?>" style="display:inline;">
                    <input type="hidden" name="motif_refus" id="motif_refus_hidden">
                    <button type="submit" class="btn btn-danger">Refuser</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Transfert des valeurs des modals vers les champs cachés
document.getElementById('validerModal').addEventListener('shown.bs.modal', function () {
    document.getElementById('remplace_par_hidden').value = document.getElementById('remplace_par_modal').value;
    document.getElementById('commentaire_remplacement_hidden').value = document.getElementById('commentaire_remplacement_modal').value;
});

document.getElementById('refuserModal').addEventListener('shown.bs.modal', function () {
    document.getElementById('motif_refus_hidden').value = document.getElementById('motif_refus_modal').value;
});

// Mise à jour des champs cachés lorsque les valeurs changent
document.getElementById('remplace_par_modal').addEventListener('change', function () {
    document.getElementById('remplace_par_hidden').value = this.value;
});

document.getElementById('commentaire_remplacement_modal').addEventListener('input', function () {
    document.getElementById('commentaire_remplacement_hidden').value = this.value;
});

document.getElementById('motif_refus_modal').addEventListener('input', function () {
    document.getElementById('motif_refus_hidden').value = this.value;
});
</script>