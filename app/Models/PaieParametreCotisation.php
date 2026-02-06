<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle PaieParametreCotisation
 * Gestion des taux de cotisations sociales (CNAPS, OSTIE, FMFP)
 */
class PaieParametreCotisation extends BaseModel {
    protected $table = 'paie_parametres_cotisations';
    protected $fillable = [
        'nom', 'taux_salarial', 'taux_patronal', 'description', 'actif'
    ];
    
    /**
     * Récupère tous les paramètres actifs
     */
    public function getActifs() {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE actif = 1 ORDER BY nom ASC"
        );
    }
    
    /**
     * Récupère un paramètre par son nom
     */
    public function getByNom(string $nom) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE nom = ? AND actif = 1",
            [$nom]
        );
    }
    
    /**
     * Met à jour les taux d'une cotisation
     */
    public function updateTaux(string $nom, float $tauxSalarial, float $tauxPatronal) {
        $sql = "UPDATE {$this->table} 
                SET taux_salarial = ?, taux_patronal = ?, updated_at = NOW() 
                WHERE nom = ?";
        return $this->query($sql, [$tauxSalarial, $tauxPatronal, $nom]);
    }
    
    /**
     * Initialise les paramètres par défaut si la table est vide
     */
    public function initialiserDefauts() {
        $count = $this->queryOne("SELECT COUNT(*) as count FROM {$this->table}");
        
        if ($count['count'] == 0) {
            $defauts = [
                ['nom' => 'CNAPS', 'taux_salarial' => 0.0100, 'taux_patronal' => 0.1300, 'description' => 'Caisse Nationale de Prévoyance Sociale'],
                ['nom' => 'OSTIE', 'taux_salarial' => 0.0100, 'taux_patronal' => 0.0500, 'description' => 'Organisation Sanitaire Tananarivienne Inter-Entreprises'],
                ['nom' => 'FMFP', 'taux_salarial' => 0.0000, 'taux_patronal' => 0.0100, 'description' => 'Fonds Malgache de Formation Professionnelle']
            ];
            
            foreach ($defauts as $defaut) {
                $this->create($defaut);
            }
            
            return true;
        }
        
        return false;
    }
}
