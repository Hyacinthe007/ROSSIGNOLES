<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle ModePaiement
 */

class ModePaiement extends BaseModel {
    protected $table = 'modes_paiement';
    protected $fillable = [
        'code', 'libelle', 'description', 'necessite_reference', 'actif'
    ];
}
