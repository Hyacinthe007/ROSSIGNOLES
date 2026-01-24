<div class="p-4 md:p-8">
    <style>
        .final-receipt {
            background: white;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .final-receipt { box-shadow: none; border: 1px solid #ddd; width: 100%; max-width: none; margin: 0; padding: 20px; }
            @page { margin: 10mm; }
        }

        .header-receipt {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-receipt h1 {
            color: #059669;
            font-size: 24px;
            text-transform: uppercase;
            margin: 0;
        }

        .info-grid-receipt {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
            text-transform: uppercase;
            border-bottom: 1px solid #10b981;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .label {
            width: 140px;
            color: #6b7280;
            font-weight: 500;
        }

        .value {
            font-weight: 600;
        }

        .table-receipt {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table-receipt th {
            background: #059669;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }

        .table-receipt td {
            padding: 12px 10px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-amount {
            font-weight: bold;
            font-size: 24px;
            color: #059669;
        }

        .in-words {
            margin-top: 20px;
            padding: 15px;
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            font-style: ;
            font-size: 13px;
        }
    </style>




    <!-- Interface d'impression (Reçu Réel) -->
    <div class="final-receipt">
        <div class="header-receipt">
            <p class="text-xs text-gray-400 mb-2 uppercase tracking-widest no-print">Étape 7 sur 7 : Inscription Finalisée</p>
            <h1>Reçu de Paiement</h1>
            <div style="font-weight: bold; font-size: 18px; margin-top: 5px;">École ROSSIGNOLES</div>
            <div style="font-size: 12px; color: #666;">Antananarivo, Madagascar</div>
        </div>

        <div class="info-grid-receipt">
            <div>
                <div class="section-title">Information de l'élève</div>
                <div class="info-row"><span class="label">Année scolaire :</span> <span class="value"><?= e($paiement['annee_scolaire'] ?? 'N/A') ?></span></div>
                <div class="info-row"><span class="label">Matricule :</span> <span class="value"><?= e($inscription['eleve_matricule'] ?? '-') ?></span></div>
                <div class="info-row"><span class="label">Nom & Prénom :</span> <span class="value" style="text-transform: uppercase;"><?= e($inscription['eleve_nom'] . ' ' . $inscription['eleve_prenom']) ?></span></div>
                <div class="info-row"><span class="label">Classe :</span> <span class="value"><?= e($inscription['classe_nom'] ?? 'N/A') ?></span></div>
            </div>

            <div style="text-align: right; background: #f9fafb; padding: 15px; border-radius: 8px;">
                <div class="info-row" style="justify-content: flex-end;"><span style="font-weight: bold; color: #059669; margin-right: 10px;">N° Reçu :</span> <span class="value" style="font-family: 'Outfit', sans-serif;"><?= e($paiement['numero_paiement'] ?? 'PAY-'.$paiement['id']) ?></span></div>
                <div class="info-row" style="justify-content: flex-end; margin-top: 5px;"><span class="label" style="width: auto; margin-right: 10px;">Date :</span> <span class="value"><?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?></span></div>
                <div class="info-row" style="justify-content: flex-end;"><span class="label" style="width: auto; margin-right: 10px;">Mode :</span> <span class="value"><?= e($paiement['mode_paiement_libelle'] ?? 'Espèces') ?></span></div>
            </div>
        </div>

        <table class="table-receipt">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Montant (Ar)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalLignes = 0;
                foreach ($lignes as $ligne): 
                    $totalLignes += $ligne['montant'];
                ?>
                <tr>
                    <td><?= e($ligne['designation'] ?? $ligne['libelle']) ?></td>
                    <td style="text-align: right; font-weight: 600;"><?= number_format($ligne['montant'], 0, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <span style="font-weight: bold; font-size: 16px; margin-right: 20px;">MONTANT TOTAL :</span>
            <span class="total-amount"><?= number_format($totalLignes, 0, ',', ' ') ?> Ar</span>
        </div>

        <div class="in-words">
            Arrêté le présent reçu à la somme de : <strong><?= function_exists('numberToWords') ? numberToWords($totalLignes) : '---' ?> ariary</strong>.
        </div>

        <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
            <div style="text-align: center; width: 200px;">
                <div style="font-weight: bold; font-size: 12px; margin-bottom: 60px;">Le Responsable</div>
                <div style="border-top: 1px solid #333; padding-top: 5px; font-size: 10px; color: #666;">Signature & Cachet</div>
            </div>
        </div>
    </div>

    <!-- Actions Finale -->
    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center no-print">
        <button onclick="window.print()" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2">
            <i class="fas fa-print"></i>
            Imprimer le Reçu
        </button>
        <a href="<?= url('inscriptions/liste') ?>" class="px-8 py-4 bg-gray-800 hover:bg-black text-white font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2">
            <i class="fas fa-list"></i>
            Aller à la liste des inscriptions
        </a>
        <a href="<?= url('inscriptions/nouveau') ?>" class="px-8 py-4 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-700 font-bold rounded-xl transition flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i>
            Nouvelle Inscription
        </a>
    </div>
</div>
