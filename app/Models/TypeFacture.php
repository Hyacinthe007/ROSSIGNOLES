<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle TypeFacture
 */

class TypeFacture extends BaseModel {
    protected $table = 'types_facture';
    protected $fillable = [
        'code', 'libelle', 'description', 'prefixe_numero', 'actif'
    ];
}
