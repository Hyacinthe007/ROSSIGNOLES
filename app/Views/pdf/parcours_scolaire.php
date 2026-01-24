<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .header { text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        h1 { color: #2c3e50; font-size: 22px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 10px; }
        .footer { margin-top: 30px; font-size: 12px; color: #7f8c8d; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Parcours Scolaire</h1>
        <p>École ROSSIGNOLES</p>
    </div>

    <div class="info">
        <p><strong>Élève :</strong> <?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?></p>
        <p><strong>Matricule :</strong> <?= e($eleve['matricule']) ?></p>
        <p><strong>Sexe :</strong> <?= e($eleve['sexe']) ?></p>
        <p><strong>Date de naissance :</strong> <?= formatDate($eleve['date_naissance']) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Année Scolaire</th>
                <th>Classe</th>
                <th>Date d'inscription</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($inscriptions as $ins): ?>
            <tr>
                <td><?= e($ins['annee_scolaire']) ?></td>
                <td><?= e($ins['classe_nom']) ?></td>
                <td><?= formatDate($ins['created_at']) ?></td>
                <td><?= e($ins['statut']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Document généré le <?= date('d/m/Y H:i') ?> - Système ERP ROSSIGNOLES
    </div>
</body>
</html>
