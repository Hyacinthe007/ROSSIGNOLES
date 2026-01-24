<?php
declare(strict_types=1);

namespace App\Models;

class ParametreEcole extends BaseModel {
    protected $table = 'parametres_ecole';
    protected $fillable = ['cle', 'valeur', 'type', 'description'];
    
    public function get($cle, $default = null) {
        $param = $this->queryOne("SELECT valeur FROM {$this->table} WHERE cle = ?", [$cle]);
        return $param ? $param['valeur'] : $default;
    }
    
    public function set($cle, $valeur) {
        $existing = $this->queryOne("SELECT id FROM {$this->table} WHERE cle = ?", [$cle]);
        if ($existing) {
            return $this->update($existing['id'], ['valeur' => $valeur]);
        }
        return $this->create(['cle' => $cle, 'valeur' => $valeur]);
    }
}
