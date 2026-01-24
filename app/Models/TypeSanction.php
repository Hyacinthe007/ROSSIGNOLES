<?php
declare(strict_types=1);

namespace App\Models;

class TypeSanction extends BaseModel {
    protected $table = 'types_sanctions';
    protected $fillable = ['libelle', 'gravite', 'description', 'actif'];
}
