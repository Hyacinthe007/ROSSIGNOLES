<?php
declare(strict_types=1);

namespace App\Models;

class PosteAdministratif extends BaseModel {
    protected $table = 'postes_administratifs';
    protected $fillable = ['intitule', 'description', 'departement', 'hierarchie_id', 'actif'];
}
