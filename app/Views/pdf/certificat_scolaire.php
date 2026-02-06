<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            padding: 40px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 50px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .cert-title {
            text-transform: uppercase;
            font-size: 28px;
            margin: 20px 0;
            color: #111;
        }
        .content {
            margin: 40px 0;
            font-size: 16px;
        }
        .highlight-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .eleve-info {
            font-weight: bold;
            font-size: 1.2em;
        }
        .footer {
            margin-top: 80px;
            text-align: right;
        }
        .signature-box {
            height: 100px;
        }
        .date-place {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">ERP Ã‰cole ROSSIGNOLES</div>
        <div class="cert-title">Certificat de ScolaritÃ©</div>
        <p>AnnÃ©e Scolaire : <strong><?= e($annee_scolaire) ?></strong></p>
    </div>

    <div class="content">
        <p>Je soussignÃ©, le Directeur de l'Ã©tablissement ROSSIGNOLES, certifie par la prÃ©sente que :</p>

        <div class="highlight-box">
            <div class="eleve-info">
                <?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?><br>
                NÃ©(e) le <?= formatDate($eleve['date_naissance']) ?><br>
                Matricule : <?= e($eleve['matricule']) ?>
            </div>
        </div>

        <p>est rÃ©guliÃ¨rement inscrit(e) dans notre Ã©tablissement en classe de <strong><?= e($inscription['nom']) ?></strong>
        pour le compte de l'annÃ©e scolaire <strong><?= e($annee_scolaire) ?></strong>.</p>

        <p>Le prÃ©sent certificat est dÃ©livrÃ© Ã  l'intÃ©ressÃ©(e) pour servir et valoir ce que de droit.</p>
    </div>

    <div class="footer">
        <div class="date-place">Fait Ã  ......................., le <?= $date_actuelle ?></div>
        <strong>Le Directeur</strong>
        <div class="signature-box"></div>
        <p>(Signature et Cachet)</p>
    </div>
</body>
</html>
