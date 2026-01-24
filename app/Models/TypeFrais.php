<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle TypeFrais
 */

class TypeFrais extends BaseModel {
    protected $table = 'types_frais';
    protected $fillable = ['libelle', 'categorie', 'actif'];
}
