<?php
declare(strict_types=1);

namespace App\Models;

class TypeDocument extends BaseModel {
    protected $table = 'types_documents';
    protected $fillable = ['libelle', 'code', 'description', 'actif'];
}
