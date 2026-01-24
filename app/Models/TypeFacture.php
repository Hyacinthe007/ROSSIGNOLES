<?php
/**
 * Modèle TypeFacture
 */

require_once __DIR__ . '/BaseModel.php';

class TypeFacture extends BaseModel {
    protected $table = 'types_facture';
    protected $fillable = [
        'code', 'libelle', 'description', 'prefixe_numero', 'actif'
    ];
}
