<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement - <?= e($inscription['eleve_nom'] . ' ' . $inscription['eleve_prenom']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        
        @media print {
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
                padding: 0;
                margin: 0;
            }
            .no-print { display: none !important; }
            .receipt-container {
                border: none;
                box-shadow: none;
                padding: 15px;
                max-width: 100%;
            }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            background: #f3f4f6;
            padding: 20px;
        }
        
        .receipt-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border: 2px solid #1f2937;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 2px double #1f2937;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .school-name {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .school-subtitle {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }
        
        .receipt-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            padding: 6px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
        }
        
        .receipt-number {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 4px 15px;
            font-size: 11px;
        }
        
        .info-label {
            font-weight: bold;
            color: #374151;
        }
        
        .info-value {
            color: #1f2937;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .payment-table th,
        .payment-table td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
        }
        
        .payment-table th {
            background: #f3f4f6;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        
        .payment-table .amount {
            text-align: right;
            font-family: 'Outfit', sans-serif;
        }
        
        .total-row {
            background: #1f2937 !important;
            color: white !important;
            font-weight: bold;
            font-size: 12px;
        }
        
        .total-row td {
            border-color: #1f2937 !important;
        }
        
        .amount-words {
            margin: 12px 0;
            padding: 10px;
            background: #fef3c7;
            border: 1px solid #f59e0b;
            font-style: ;
            font-size: 11px;
        }
        
        .amount-words-label {
            font-weight: bold;
            color: #92400e;
        }
        
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #1f2937;
            margin-top: 40px;
            padding-top: 5px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .print-btn:hover {
            background: #059669;
        }
        
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #6b7280;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }
        
        .back-btn:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <a href="<?= url('inscriptions/nouveau?etape=1') ?>" class="back-btn no-print">
        ← Retour
    </a>
    <button onclick="window.print()" class="print-btn no-print">
        🖨️ Imprimer
    </button>

    <div class="receipt-container">
        <!-- En-tête École -->
        <div class="header">
            <div class="school-name">LES ROSSIGNOLES</div>
            <div class="school-subtitle">Établissement Scolaire Privé</div>
            <div class="school-subtitle"><?= date('d/m/Y à H:i') ?></div>
        </div>
        
        <div class="receipt-title">REÇU DE PAIEMENT</div>
        
        <div class="receipt-number">
            <strong>N° Reçu:</strong> <?= str_pad($inscription['id'], 5, '0', STR_PAD_LEFT) ?>
        </div>
        
        <!-- Informations Élève -->
        <div class="info-section">
            <div class="info-grid">
                <span class="info-label">N° Matricule :</span>
                <span class="info-value"><?= e($inscription['eleve_matricule']) ?></span>
                
                <span class="info-label">Nom Complet :</span>
                <span class="info-value"><strong><?= e($inscription['eleve_nom'] . ' ' . $inscription['eleve_prenom']) ?></strong></span>
                
                <span class="info-label">Classe :</span>
                <span class="info-value"><?= e($inscription['classe_nom']) ?></span>
                
                <span class="info-label">Année Scolaire :</span>
                <span class="info-value"><?= e($inscription['annee_scolaire']) ?></span>
                
                <span class="info-label">Date d'inscription :</span>
                <span class="info-value"><?= date('d/m/Y à H:i', strtotime($inscription['date_inscription'] ?? 'now')) ?></span>
                
                <?php if (!empty($inscription['mode_paiement'])): ?>
                <span class="info-label">Mode :</span>
                <span class="info-value"><?= e($inscription['mode_paiement']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Détails Paiement -->
        <table class="payment-table">
            <thead>
                <tr>
                    <th style="width: 70%">Désignation</th>
                    <th style="width: 30%">Montant (Ar)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalPaye = 0;
                foreach ($paiements as $paiement): 
                    $totalPaye += $paiement['montant'];
                ?>
                <tr>
                    <td><?= e($paiement['type_frais'] ?? 'Paiement Inscription') ?></td>
                    <td class="amount"><?= number_format($paiement['montant'], 0, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td>TOTAL PAYÉ</td>
                    <td class="amount"><?= number_format($totalPaye, 0, ',', ' ') ?></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Montant en Lettres -->
        <div class="amount-words">
            <span class="amount-words-label">Arrêté le présent reçu à la somme de :</span><br>
            <strong><?= convertirEnLettres($totalPaye) ?> Ariary</strong>
        </div>
        
        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">L'intéressé(e)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">La Direction</div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Document généré automatiquement le <?= date('d/m/Y à H:i:s') ?> - LES ROSSIGNOLES
        </div>
    </div>
</body>
</html>
<?php
/**
 * Convertit un nombre en lettres (français)
 */
function convertirEnLettres($nombre) {
    $nombre = intval($nombre);
    
    if ($nombre == 0) return 'Zéro';
    
    $unite = ['', 'Un', 'Deux', 'Trois', 'Quatre', 'Cinq', 'Six', 'Sept', 'Huit', 'Neuf'];
    $dixanie = ['', 'Dix', 'Vingt', 'Trente', 'Quarante', 'Cinquante', 'Soixante', 'Soixante', 'Quatre-Vingt', 'Quatre-Vingt'];
    $special = ['Dix', 'Onze', 'Douze', 'Treize', 'Quatorze', 'Quinze', 'Seize', 'Dix-Sept', 'Dix-Huit', 'Dix-Neuf'];
    
    $result = '';
    
    // Millions
    if ($nombre >= 1000000) {
        $millions = floor($nombre / 1000000);
        if ($millions == 1) {
            $result .= 'Un Million ';
        } else {
            $result .= convertirEnLettres($millions) . ' Millions ';
        }
        $nombre = $nombre % 1000000;
    }
    
    // Milliers
    if ($nombre >= 1000) {
        $milliers = floor($nombre / 1000);
        if ($milliers == 1) {
            $result .= 'Mille ';
        } else {
            $result .= convertirEnLettres($milliers) . ' Mille ';
        }
        $nombre = $nombre % 1000;
    }
    
    // Centaines
    if ($nombre >= 100) {
        $centaines = floor($nombre / 100);
        if ($centaines == 1) {
            $result .= 'Cent ';
        } else {
            $result .= $unite[$centaines] . ' Cents ';
        }
        $nombre = $nombre % 100;
    }
    
    // Dizaines et unités
    if ($nombre >= 10) {
        $dizaine = floor($nombre / 10);
        $u = $nombre % 10;
        
        if ($dizaine == 1) {
            $result .= $special[$u] . ' ';
        } elseif ($dizaine == 7 || $dizaine == 9) {
            if ($u < 10) {
                $result .= $dixanie[$dizaine] . '-' . $special[$u] . ' ';
            }
        } else {
            $result .= $dixanie[$dizaine];
            if ($u == 1 && $dizaine != 8) {
                $result .= ' et Un ';
            } elseif ($u > 0) {
                $result .= '-' . $unite[$u] . ' ';
            } else {
                $result .= ' ';
            }
        }
    } elseif ($nombre > 0) {
        $result .= $unite[$nombre] . ' ';
    }
    
    return trim($result);
}
?>

