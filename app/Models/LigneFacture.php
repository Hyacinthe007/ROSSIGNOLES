<?php
/**
 * Modèle LigneFacture
 */

require_once __DIR__ . '/BaseModel.php';

class LigneFacture extends BaseModel {
    protected $table = 'lignes_facture';
    protected $fillable = [
        // Doit correspondre exactement aux colonnes de la table lignes_facture
        // voir database/rossignoles_schema.sql
        'facture_id', 'type_frais_id', 'designation', 'quantite', 'prix_unitaire', 'montant'
    ];
}
