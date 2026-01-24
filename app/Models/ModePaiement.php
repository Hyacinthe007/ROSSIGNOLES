<?php
/**
 * Modèle ModePaiement
 */

require_once __DIR__ . '/BaseModel.php';

class ModePaiement extends BaseModel {
    protected $table = 'modes_paiement';
    protected $fillable = [
        'code', 'libelle', 'description', 'necessite_reference', 'actif'
    ];
}
