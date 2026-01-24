<?php
$title = "Détails du Conseil de Classe";
$breadcrumbs = [
    ['label' => 'Tableau de bord', 'url' => '/dashboard'],
    ['label' => 'Évaluations', 'url' => '#'],
    ['label' => 'Conseils de classe', 'url' => '/conseils/list'],
    ['label' => 'Détails']
];
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Détails du Conseil de Classe</h3>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Classe :</th>
                                <td><?= htmlspecialchars($conseil['classe_nom']) ?></td>
                            </tr>
                            <tr>
                                <th>Période :</th>
                                <td><?= htmlspecialchars($conseil['periode_nom']) ?></td>
                            </tr>
                            <tr>
                                <th>Année scolaire :</th>
                                <td><?= htmlspecialchars($conseil['annee_libelle']) ?></td>
                            </tr>
                            <tr>
                                <th>Date du conseil :</th>
                                <td><?= date('d/m/Y', strtotime($conseil['date_conseil'])) ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Président :</th>
                                <td>
                                    <?php if (!empty($conseil['president_nom'])): ?>
                                        <?= htmlspecialchars($conseil['president_nom'] . ' ' . $conseil['president_prenom']) ?>
                                    <?php else: ?>
                                        <em>Non spécifié</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Secrétaire :</th>
                                <td>
                                    <?php if (!empty($conseil['secretaire_nom'])): ?>
                                        <?= htmlspecialchars($conseil['secretaire_nom'] . ' ' . $conseil['secretaire_prenom']) ?>
                                    <?php else: ?>
                                        <em>Non spécifié</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Statut :</th>
                                <td>
                                    <?php
                                    $statuts = [
                                        'prevu' => ['Prévu', 'warning'],
                                        'en_cours' => ['En cours', 'primary'],
                                        'cloture' => ['Clôturé', 'success'],
                                        'annule' => ['Annulé', 'danger']
                                    ];
                                    $statutInfo = $statuts[$conseil['statut']] ?? [$conseil['statut'], 'secondary'];
                                    ?>
                                    <span class="badge badge-<?= $statutInfo[1] ?>"><?= $statutInfo[0] ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if (!empty($conseil['ordre_du_jour'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Ordre du jour :</label>
                            <div class="well well-sm">
                                <?= nl2br(htmlspecialchars($conseil['ordre_du_jour'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($conseil['moyenne_classe'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Statistiques de la classe :</label>
                            <div class="well well-sm">
                                Moyenne de la classe : <?= htmlspecialchars($conseil['moyenne_classe']) ?><br>
                                Taux de réussite : <?= htmlspecialchars($conseil['taux_reussite'] ?? '') ?>%<br>
                                Félicitations : <?= htmlspecialchars($conseil['nb_felicitations'] ?? 0) ?><br>
                                Encouragements : <?= htmlspecialchars($conseil['nb_encouragements'] ?? 0) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($conseil['appreciation_generale'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Appréciation générale :</label>
                            <div class="well well-sm">
                                <?= nl2br(htmlspecialchars($conseil['appreciation_generale'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <h4>Décisions individuelles</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Élève</th>
                                        <th>Distinction</th>
                                        <th>Avertissement</th>
                                        <th>Décision de passage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($decisions)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune décision individuelle</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($decisions as $decision): ?>
                                            <tr>
                                                <td>
                                                    <?= htmlspecialchars($decision['eleve_nom'] . ' ' . $decision['eleve_prenom']) ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($decision['matricule']) ?></small>
                                                </td>
                                                <td>
                                                    <?php
                                                    $distinctions = [
                                                        'felicitations' => 'Félicitations',
                                                        'compliments' => 'Compliments',
                                                        'encouragements' => 'Encouragements',
                                                        'tableau_honneur' => 'Tableau d\'honneur',
                                                        'prix_excellence' => 'Prix d\'excellence',
                                                        'aucune' => 'Aucune'
                                                    ];
                                                    echo htmlspecialchars($distinctions[$decision['distinction']] ?? $decision['distinction']);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $avertissements = [
                                                        'travail' => 'Travail',
                                                        'conduite' => 'Conduite',
                                                        'assiduite' => 'Assiduité',
                                                        'travail_et_conduite' => 'Travail et conduite',
                                                        'aucun' => 'Aucun'
                                                    ];
                                                    echo htmlspecialchars($avertissements[$decision['avertissement']] ?? $decision['avertissement']);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $decisionsPassage = [
                                                        'passage_acquis' => 'Passage acquis',
                                                        'passage_probable' => 'Passage probable',
                                                        'passage_conditionnel' => 'Passage conditionnel',
                                                        'redoublement_envisage' => 'Redoublement envisagé',
                                                        'reorientation_suggeree' => 'Réorientation suggérée',
                                                        'non_decide' => 'Non décidé'
                                                    ];
                                                    echo htmlspecialchars($decisionsPassage[$decision['decision_passage']] ?? $decision['decision_passage']);
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <a href="/conseils/list" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>