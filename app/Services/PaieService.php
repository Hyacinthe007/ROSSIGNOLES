<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\PaieParametreCotisation;
use App\Models\PaieTrancheIrsa;
use App\Models\PaieContrat;
use App\Models\PaieBulletin;
use App\Models\PaieRetenueDiverse;
use App\Models\Personnel;

/**
 * Service de calcul de paie
 * Gère tous les calculs liés aux bulletins de paie selon la législation malgache 2026
 */
class PaieService {
    
    private $parametreCotisationModel;
    private $trancheIrsaModel;
    private $contratModel;
    private $bulletinModel;
    private $personnelModel;
    
    // Constantes
    const IRSA_MINIMUM = 3000.00;
    const REDUCTION_PAR_ENFANT = 2000.00;
    
    public function __construct() {
        $this->parametreCotisationModel = new PaieParametreCotisation();
        $this->trancheIrsaModel = new PaieTrancheIrsa();
        $this->contratModel = new PaieContrat();
        $this->bulletinModel = new PaieBulletin();
        $this->personnelModel = new Personnel();
    }
    
    /**
     * Calcule le bulletin de paie complet pour un personnel
     */
    public function calculerBulletin(int $personnelId, string $periode): array {
        // 1. Récupérer les données du personnel
        $personnel = $this->personnelModel->find($personnelId);
        if (!$personnel) {
            throw new \Exception("Personnel introuvable");
        }
        
        // 2. Récupérer le contrat actif
        $contrat = $this->contratModel->getContratActif($personnelId);
        if (!$contrat) {
            throw new \Exception("Aucun contrat actif trouvé pour ce personnel");
        }
        
        $salaireBrut = (float)$contrat['salaire_brut_base'];
        $soumisCotisations = (bool)$contrat['soumis_cotisations'];
        $typeContrat = $personnel['type_contrat'];
        $nbEnfants = (int)$personnel['nb_enfants'];
        
        // 3. Récupérer les paramètres de cotisations
        $cnaps = $this->parametreCotisationModel->getByNom('CNAPS');
        $ostie = $this->parametreCotisationModel->getByNom('OSTIE');
        $fmfp = $this->parametreCotisationModel->getByNom('FMFP');
        
        // 4. Calculer les cotisations salariales
        $montantCnapsSal = 0.00;
        $montantOstieSal = 0.00;
        
        // Règle : CDI ou CDD avec option cochée
        if (($typeContrat === 'cdi' || ($typeContrat === 'cdd' && $soumisCotisations))) {
            $montantCnapsSal = $salaireBrut * (float)$cnaps['taux_salarial'];
            $montantOstieSal = $salaireBrut * (float)$ostie['taux_salarial'];
        }
        
        // 5. Calculer la base imposable IRSA
        $baseImposableIrsa = $salaireBrut - $montantCnapsSal - $montantOstieSal;
        
        // 6. Calculer l'IRSA brut
        $irsaBrut = $this->trancheIrsaModel->calculerIrsa($baseImposableIrsa);
        
        // 7. Appliquer la réduction pour charges de famille
        $reductionChargesFamille = $nbEnfants * self::REDUCTION_PAR_ENFANT;
        
        // 8. Calculer l'IRSA net (minimum 3000 Ar)
        $irsaNet = max(self::IRSA_MINIMUM, $irsaBrut - $reductionChargesFamille);
        
        // 9. Récupérer les retenues diverses
        $totalRetenuesDiverses = 0.00; // TODO: Implémenter la récupération des retenues
        
        // 10. Calculer le salaire net
        $salaireNet = $salaireBrut - $montantCnapsSal - $montantOstieSal - $irsaNet - $totalRetenuesDiverses;
        
        // 11. Calculer les charges patronales
        $montantCnapsPat = $salaireBrut * (float)$cnaps['taux_patronal'];
        $montantOstiePat = $salaireBrut * (float)$ostie['taux_patronal'];
        $montantFmfpPat = $salaireBrut * (float)$fmfp['taux_patronal'];
        
        // 12. Calculer le coût total employeur
        $coutTotalEmployeur = $salaireBrut + $montantCnapsPat + $montantOstiePat + $montantFmfpPat;
        
        // 13. Retourner le résultat
        return [
            'personnel_id' => $personnelId,
            'periode' => $periode,
            'date_edition' => date('Y-m-d'),
            
            // Revenus
            'salaire_brut' => round($salaireBrut, 2),
            
            // Cotisations salariales
            'montant_cnaps_sal' => round($montantCnapsSal, 2),
            'montant_ostie_sal' => round($montantOstieSal, 2),
            
            // IRSA
            'base_imposable_irsa' => round($baseImposableIrsa, 2),
            'irsa_brut' => round($irsaBrut, 2),
            'reduction_charges_famille' => round($reductionChargesFamille, 2),
            'irsa_net' => round($irsaNet, 2),
            
            // Autres retenues
            'total_retenues_diverses' => round($totalRetenuesDiverses, 2),
            
            // Net
            'salaire_net' => round($salaireNet, 2),
            
            // Charges patronales
            'montant_cnaps_pat' => round($montantCnapsPat, 2),
            'montant_ostie_pat' => round($montantOstiePat, 2),
            'montant_fmfp_pat' => round($montantFmfpPat, 2),
            'cout_total_employeur' => round($coutTotalEmployeur, 2),
            
            // Statut
            'statut' => 'brouillon'
        ];
    }
    
    /**
     * Génère les bulletins pour tous les personnels actifs
     */
    public function genererBulletinsMasse(string $periode): array {
        $contrats = $this->contratModel->getAllContratsActifs();
        $resultats = [
            'succes' => 0,
            'erreurs' => 0,
            'details' => []
        ];
        
        foreach ($contrats as $contrat) {
            try {
                // Vérifier si le bulletin existe déjà
                $existant = $this->bulletinModel->getByPersonnelPeriode(
                    (int)$contrat['personnel_id'],
                    $periode
                );
                
                if ($existant) {
                    $resultats['details'][] = [
                        'personnel' => $contrat['nom'] . ' ' . $contrat['prenom'],
                        'statut' => 'existe_deja'
                    ];
                    continue;
                }
                
                // Calculer et créer le bulletin
                $bulletin = $this->calculerBulletin((int)$contrat['personnel_id'], $periode);
                $this->bulletinModel->create($bulletin);
                
                $resultats['succes']++;
                $resultats['details'][] = [
                    'personnel' => $contrat['nom'] . ' ' . $contrat['prenom'],
                    'statut' => 'cree'
                ];
                
            } catch (\Exception $e) {
                $resultats['erreurs']++;
                $resultats['details'][] = [
                    'personnel' => $contrat['nom'] . ' ' . $contrat['prenom'],
                    'statut' => 'erreur',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $resultats;
    }
}
