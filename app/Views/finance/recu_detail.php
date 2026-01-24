<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement - <?= e($paiement['numero_paiement'] ?? 'N/A') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; background: white; }
            .receipt { box-shadow: none !important; border: 1px solid #ddd; padding: 20px !important; margin: 0 !important; width: 100% !important; max-width: none !important; }
            @page { margin: 10mm; }
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f3f4f6;
            color: #1f2937;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .receipt {
            background: white;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 8px;
        }

        /* En-tête */
        .header {
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #059669;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 5px 0;
        }
        .school-name {
            font-weight: bold;
            font-size: 18px;
        }

        /* Grille d'info */
        .info-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }

        .student-info h3 {
            font-size: 15px;
            border-bottom: 1px solid #10b981;
            padding-bottom: 5px;
            margin-bottom: 15px;
            color: #059669;
            text-transform: uppercase;
        }

        .receipt-meta {
            text-align: right;
            background: #f9fafb;
            padding: 15px;
            border-radius: 6px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            width: 140px;
            font-weight: 600;
            color: #4b5563;
        }
        .info-value {
            flex: 1;
            font-weight: 500;
        }

        /* Tableau détails */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table th {
            background: #059669;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }
        .details-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .details-table tr:last-child td {
            border-bottom: 2px solid #059669;
        }
        .amount-col {
            text-align: right;
            width: 150px;
        }

        /* Totaux et Lettres */
        .totals-section {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .total-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            width: 100%;
            margin-bottom: 5px;
        }
        .total-label {
            font-weight: bold;
            margin-right: 20px;
            font-size: 16px;
        }
        .total-amount {
            font-weight: bold;
            font-size: 20px;
            color: #059669;
        }

        .in-words {
            width: 100%;
            margin-top: 15px;
            padding: 10px;
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            font-style: ;
            font-size: 13px;
        }

        /* Signature */
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 50px;
            margin-bottom: 5px;
        }
        .signature-label {
            font-size: 12px;
            color: #666;
        }

        /* Boutons */
        .actions {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
            text-decoration: none;
        }
        .btn:hover { background: #059669; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <div class="actions no-print">
        <button onclick="window.print()" class="btn">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Fermer
        </button>
    </div>

    <div class="receipt">
        <!-- En-tête -->
        <div class="header">
            <h1>Reçu de Paiement</h1>
            <div class="school-name">École ROSSIGNOLES</div>
            <div style="font-size: 12px; color: #666;">Enseignement Général - Maternelle, Primaire, Secondaire</div>
            <div style="font-size: 12px; color: #666;">Antananarivo, Madagascar</div>
        </div>

        <div class="info-grid">
            <!-- Information de l'élève -->
            <div class="student-info">
                <h3>Information de l'élève</h3>
                
                <div class="info-row">
                    <span class="info-label">Année scolaire :</span>
                    <span class="info-value"><?= e($paiement['annee_scolaire'] ?? 'N/A') ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Matricule :</span>
                    <span class="info-value"><?= e($paiement['matricule'] ?? '-') ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Nom - Prénom :</span>
                    <span class="info-value" style="text-transform: uppercase;">
                        <?= e(($paiement['eleve_nom'] ?? '') . ' ' . ($paiement['eleve_prenom'] ?? '')) ?>
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Date d'inscription :</span>
                    <span class="info-value">
                        <?= !empty($paiement['date_inscription']) ? date('d/m/Y', strtotime($paiement['date_inscription'])) : '-' ?>
                    </span>
                </div>
            </div>

            <!-- Info Reçu -->
            <div class="receipt-meta">
                <div class="info-row">
                    <span style="font-weight: bold; color: #059669;">N° Reçu :</span>
                    <span class="info-value" style="text-align: right; font-family: 'Outfit', sans-serif; font-weight: bold;">
                        <?= e($paiement['numero_paiement'] ?? 'PAY-' . $paiement['id']) ?>
                    </span>
                </div>
                <div class="info-row" style="margin-top: 10px;">
                    <span class="info-label">Date :</span>
                    <span class="info-value" style="text-align: right;">
                        <?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mode :</span>
                    <span class="info-value" style="text-align: right;">
                        <?= e($paiement['mode_paiement_libelle'] ?? 'Espèces') ?>
                    </span>
                </div>
                <?php if (!empty($paiement['reference_paiement'])): ?>
                <div class="info-row">
                    <span class="info-label">Réf :</span>
                    <span class="info-value" style="text-align: right;">
                        <?= e($paiement['reference_paiement']) ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tableau Détails -->
        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount-col">Montant (Ar)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lignes)): ?>
                    <?php foreach ($lignes as $ligne): ?>
                        <tr>
                            <td>
                                <?php 
                                    $libelle = e($ligne['designation'] ?? $ligne['libelle']);
                                    // Amélioration de l'affichage pour les écolages
                                    if (stripos($libelle, 'ecolage') !== false || stripos($libelle, 'écolage') !== false) {
                                        // Si on a des infos de mois dans la ligne (à prévoir lors de la création facture)
                                        // Sinon on essaie de deviner ou on laisse tel quel
                                        echo $libelle;
                                    } else {
                                        echo $libelle;
                                    }
                                ?>
                            </td>
                            <td class="amount-col"><?= number_format($ligne['montant'], 0, ',', ' ') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback si pas de lignes (ex: paiement direct sans facture détaillée) -->
                    <tr>
                        <td>
                            <?php 
                                $desc = $paiement['facture_description'] ?? 'Paiement scolarité';
                                echo e($desc);
                            ?>
                        </td>
                        <td class="amount-col"><?= number_format($paiement['montant'], 0, ',', ' ') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>


        <!-- Totaux -->
        <?php 
        // Calculer le total à partir des lignes de facture
        $montantTotal = 0;
        if (!empty($lignes)) {
            foreach ($lignes as $ligne) {
                $montantTotal += $ligne['montant'];
            }
        } else {
            // Si pas de lignes, utiliser le montant du paiement
            $montantTotal = $paiement['montant'];
        }
        ?>
        <div class="totals-section">
            <div class="total-row">
                <span class="total-label">Montant total :</span>
                <span class="total-amount"><?= number_format($montantTotal, 0, ',', ' ') ?> Ar</span>
            </div>
        </div>

        <!-- En lettres -->
        <div class="in-words">
            Arrêté le présent reçu à la somme de : <strong><?= numberToWords($montantTotal) ?> ariary</strong>.
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Le Responsable</div>
                <div class="signature-line" style="border:none; height: 60px;"></div>
                <div style="font-size: 10px; color: #999; border-top: 1px dotted #ccc; padding-top: 5px;">Signature et Cachet</div>
            </div>
        </div>

        <!-- Pied de page -->
        <div style="margin-top: 40px; text-align: center; font-size: 11px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 10px;">
            <p>École ROSSIGNOLES - Document généré électroniquement le <?= date('d/m/Y à H:i') ?></p>
        </div>
    </div>

</body>
</html>

