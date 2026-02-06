<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle PaieTrancheIrsa
 * Gestion des tranches progressives de l'IRSA
 */
class PaieTrancheIrsa extends BaseModel {
    protected $table = 'paie_tranches_irsa';
    protected $fillable = [
        'montant_min', 'montant_max', 'taux', 'annee_validite'
    ];
    
    /**
     * Récupère toutes les tranches pour une année donnée
     */
    public function getByAnnee(int $annee = 2026) {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE annee_validite = ? 
             ORDER BY montant_min ASC",
            [$annee]
        );
    }
    
    /**
     * Calcule l'IRSA brut pour un montant imposable donné
     */
    public function calculerIrsa(float $baseImposable, int $annee = 2026): float {
        $tranches = $this->getByAnnee($annee);
        
        if (empty($tranches)) {
            return 0.00;
        }
        
        $irsaTotal = 0.00;
        
        foreach ($tranches as $tranche) {
            $min = (float)$tranche['montant_min'];
            $max = $tranche['montant_max'] ? (float)$tranche['montant_max'] : PHP_FLOAT_MAX;
            $taux = (float)$tranche['taux'];
            
            // Si le salaire est dans cette tranche
            if ($baseImposable > $min) {
                // Calculer la portion imposable dans cette tranche
                $montantImposable = min($baseImposable, $max) - $min;
                $irsaTotal += $montantImposable * $taux;
            }
            
            // Si on a dépassé le salaire, on arrête
            if ($baseImposable <= $max) {
                break;
            }
        }
        
        return round($irsaTotal, 2);
    }
    
    /**
     * Initialise les tranches IRSA 2026 par défaut
     */
    public function initialiserDefauts2026() {
        $count = $this->queryOne(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE annee_validite = 2026"
        );
        
        if ($count['count'] == 0) {
            $tranches = [
                ['montant_min' => 0, 'montant_max' => 350000, 'taux' => 0.0000, 'annee_validite' => 2026],
                ['montant_min' => 350001, 'montant_max' => 400000, 'taux' => 0.0500, 'annee_validite' => 2026],
                ['montant_min' => 400001, 'montant_max' => 500000, 'taux' => 0.1000, 'annee_validite' => 2026],
                ['montant_min' => 500001, 'montant_max' => 600000, 'taux' => 0.1500, 'annee_validite' => 2026],
                ['montant_min' => 600001, 'montant_max' => 4000000, 'taux' => 0.2000, 'annee_validite' => 2026],
                ['montant_min' => 4000001, 'montant_max' => null, 'taux' => 0.2500, 'annee_validite' => 2026]
            ];
            
            foreach ($tranches as $tranche) {
                $this->create($tranche);
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Met à jour les tranches IRSA pour une année donnée en remplaçant les anciennes
     */
    public function updateTranches(int $annee, array $tranches) {
        // Supprimer les anciennes tranches pour cette année
        $this->execute("DELETE FROM {$this->table} WHERE annee_validite = ?", [$annee]);
        
        // Insérer les nouvelles tranches
        foreach ($tranches as $tranche) {
            $this->create([
                'montant_min' => (float)$tranche['min'],
                'montant_max' => (isset($tranche['max']) && $tranche['max'] !== '' && $tranche['max'] !== null) ? (float)$tranche['max'] : null,
                'taux' => (float)$tranche['taux'] / 100,
                'annee_validite' => $annee
            ]);
        }
        
        return true;
    }
}
