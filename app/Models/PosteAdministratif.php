<?php
require_once __DIR__ . '/BaseModel.php';

class PosteAdministratif extends BaseModel {
    protected $table = 'postes_administratifs';
    protected $fillable = ['intitule', 'description', 'departement', 'hierarchie_id', 'actif'];
}
