<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle ParametresEcole
 * Correspond à la table 'parametres_ecole'
 */

class ParametresEcole extends BaseModel {
    protected $table = 'parametres_ecole';
    protected $fillable = [
        'cle', 'valeur', 'type', 'groupe', 'description'
    ];
    
    /**
     * Récupère la valeur d'un paramètre par sa clé
     */
    public function get($cle, $default = null) {
        $param = $this->queryOne(
            "SELECT * FROM {$this->table} WHERE cle = ?",
            [$cle]
        );
        
        if (!$param) {
            return $default;
        }
        
        // Convertir selon le type
        return $this->convertValue($param['valeur'], $param['type']);
    }
    
    /**
     * Définit la valeur d'un paramètre
     */
    public function set($cle, $valeur, $type = 'string', $groupe = null, $description = null) {
        // Vérifier si le paramètre existe
        $existing = $this->queryOne(
            "SELECT id FROM {$this->table} WHERE cle = ?",
            [$cle]
        );
        
        // Convertir la valeur en string pour stockage
        $valeurStr = $this->valueToString($valeur, $type);
        
        if ($existing) {
            // Mise à jour
            return $this->query(
                "UPDATE {$this->table} SET valeur = ?, type = ? WHERE cle = ?",
                [$valeurStr, $type, $cle]
            );
        } else {
            // Insertion
            return $this->query(
                "INSERT INTO {$this->table} (cle, valeur, type, groupe, description) 
                 VALUES (?, ?, ?, ?, ?)",
                [$cle, $valeurStr, $type, $groupe, $description]
            );
        }
    }
    
    /**
     * Récupère tous les paramètres d'un groupe
     */
    public function getByGroupe($groupe) {
        $params = $this->query(
            "SELECT * FROM {$this->table} WHERE groupe = ? ORDER BY cle",
            [$groupe]
        );
        
        $result = [];
        foreach ($params as $param) {
            $result[$param['cle']] = $this->convertValue($param['valeur'], $param['type']);
        }
        
        return $result;
    }
    
    /**
     * Récupère tous les paramètres groupés
     */
    public function getAllGrouped() {
        $params = $this->query(
            "SELECT * FROM {$this->table} ORDER BY groupe, cle"
        );
        
        $result = [];
        foreach ($params as $param) {
            $groupe = $param['groupe'] ?? 'general';
            if (!isset($result[$groupe])) {
                $result[$groupe] = [];
            }
            $result[$groupe][$param['cle']] = [
                'valeur' => $this->convertValue($param['valeur'], $param['type']),
                'type' => $param['type'],
                'description' => $param['description']
            ];
        }
        
        return $result;
    }
    
    /**
     * Convertit une valeur selon son type
     */
    private function convertValue($valeur, $type) {
        switch ($type) {
            case 'integer':
                return (int) $valeur;
            case 'boolean':
                return filter_var($valeur, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($valeur, true);
            case 'string':
            default:
                return $valeur;
        }
    }
    
    /**
     * Convertit une valeur en string pour stockage
     */
    private function valueToString($valeur, $type) {
        switch ($type) {
            case 'json':
                return json_encode($valeur);
            case 'boolean':
                return $valeur ? '1' : '0';
            default:
                return (string) $valeur;
        }
    }
}
