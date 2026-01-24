<?php
require_once __DIR__ . '/BaseModel.php';

class TypeSanction extends BaseModel {
    protected $table = 'types_sanctions';
    protected $fillable = ['libelle', 'gravite', 'description', 'actif'];
}
