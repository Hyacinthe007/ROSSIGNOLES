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
        <div class="school-name">ERP École ROSSIGNOLES</div>
        <div class="cert-title">Certificat de Scolarité</div>
        <p>Année Scolaire : <strong><?= e($annee_scolaire) ?></strong></p>
    </div>
    
    <div class="content">
        <p>Je soussigné, le Directeur de l'établissement ROSSIGNOLES, certifie par la présente que :</p>
        
        <div class="highlight-box">
            <div class="eleve-info">
                <?= e($eleve['nom'] . ' ' . $eleve['prenom']) ?><br>
                Né(e) le <?= formatDate($eleve['date_naissance']) ?><br>
                Matricule : <?= e($eleve['matricule']) ?>
            </div>
        </div>
        
        <p>est régulièrement inscrit(e) dans notre établissement en classe de <strong><?= e($inscription['nom']) ?></strong> 
        pour le compte de l'année scolaire <strong><?= e($annee_scolaire) ?></strong>.</p>
        
        <p>Le présent certificat est délivré à l'intéressé(e) pour servir et valoir ce que de droit.</p>
    </div>
    
    <div class="footer">
        <div class="date-place">Fait à ......................., le <?= $date_actuelle ?></div>
        <strong>Le Directeur</strong>
        <div class="signature-box"></div>
        <p>(Signature et Cachet)</p>
    </div>
</body>
</html>
