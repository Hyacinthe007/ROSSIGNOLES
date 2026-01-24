<?php
require_once __DIR__ . '/BaseModel.php';

class TypeDocument extends BaseModel {
    protected $table = 'types_documents';
    protected $fillable = ['libelle', 'code', 'description', 'actif'];
}
