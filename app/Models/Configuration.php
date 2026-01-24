<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Configuration
 * Gère les paramètres de configuration système
 */

class Configuration extends BaseModel {
    protected $table = 'parametres_ecole';
    protected $fillable = ['cle', 'valeur', 'type', 'description'];
    
    /**
     * Récupère une valeur de configuration par sa clé
     * 
     * @param string $cle La clé de configuration
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed La valeur de la configuration
     */
    public function get($cle, $default = null) {
        $param = $this->queryOne("SELECT valeur FROM {$this->table} WHERE cle = ?", [$cle]);
        return $param ? $param['valeur'] : $default;
    }
    
    /**
     * Définit ou met à jour une valeur de configuration
     * 
     * @param string $cle La clé de configuration
     * @param mixed $valeur La valeur à enregistrer
     * @return bool|int ID de l'enregistrement créé ou true si mis à jour
     */
    public function set($cle, $valeur) {
        $existing = $this->queryOne("SELECT id FROM {$this->table} WHERE cle = ?", [$cle]);
        if ($existing) {
            return $this->update($existing['id'], ['valeur' => $valeur]);
        }
        return $this->create(['cle' => $cle, 'valeur' => $valeur]);
    }
    
    /**
     * Récupère toutes les configurations
     * 
     * @param array $conditions Conditions de filtrage
     * @param string|null $orderBy Ordre de tri
     * @param int|null $limit Limite de résultats
     * @return array Liste des configurations
     */
    public function getAll($conditions = [], $orderBy = null, $limit = null) {
        return $this->all($conditions, $orderBy, $limit);
    }
}
