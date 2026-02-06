<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle PaieContrat
 * Gestion des contrats et salaires du personnel
 */
class PaieContrat extends BaseModel {
    protected $table = 'paie_contrats';
    protected $fillable = [
        'personnel_id', 'salaire_brut_base', 'soumis_cotisations', 'actif'
    ];
    
    /**
     * Récupère le contrat actif d'un personnel
     */
    public function getContratActif(int $personnelId) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} 
             WHERE personnel_id = ? AND actif = 1",
            [$personnelId]
        );
    }
    
    /**
     * Récupère tous les contrats actifs avec les infos du personnel
     */
    public function getAllContratsActifs() {
        return $this->query(
            "SELECT pc.*, 
                    p.matricule, p.nom, p.prenom, p.type_contrat, p.nb_enfants,
                    p.type_personnel
             FROM {$this->table} pc
             INNER JOIN personnels p ON pc.personnel_id = p.id
             WHERE pc.actif = 1 AND p.statut = 'actif' AND p.deleted_at IS NULL
             ORDER BY p.nom ASC, p.prenom ASC"
        );
    }
    
    /**
     * Crée ou met à jour le contrat d'un personnel
     */
    public function upsertContrat(int $personnelId, float $salaireBrut, bool $soumisCotisations = true) {
        // Désactiver l'ancien contrat s'il existe
        $this->query(
            "UPDATE {$this->table} SET actif = 0 WHERE personnel_id = ? AND actif = 1",
            [$personnelId]
        );
        
        // Créer le nouveau contrat
        return $this->create([
            'personnel_id' => $personnelId,
            'salaire_brut_base' => $salaireBrut,
            'soumis_cotisations' => $soumisCotisations ? 1 : 0,
            'actif' => 1
        ]);
    }
}
