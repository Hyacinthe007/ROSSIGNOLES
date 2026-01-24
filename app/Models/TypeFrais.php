<?php
/**
 * Modèle TypeFrais
 */

require_once __DIR__ . '/BaseModel.php';

class TypeFrais extends BaseModel {
    protected $table = 'types_frais';
    protected $fillable = ['libelle', 'categorie', 'actif'];
}
