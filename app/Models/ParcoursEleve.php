<?php
declare(strict_types=1);

namespace App\Models;

class ParcoursEleve extends BaseModel {
    protected $table = 'parcours_eleves';
    protected $fillable = [
        'eleve_id',
        'annee_scolaire_id',
        'classe_id',
        'inscription_id',
        'resultat',
        'mention',
        'moyenne_annuelle',
        'rang_classe',
        'classe_suivante_id',
        'date_debut',
        'date_fin',
        'saisi_par',
    ];
    
    public function getHistorique($eleveId) {
        return $this->query(
            "SELECT pe.*, c.nom as classe_nom, a.libelle as annee_libelle
             FROM {$this->table} pe
             JOIN classes c ON pe.classe_id = c.id
             JOIN annees_scolaires a ON pe.annee_scolaire_id = a.id
             WHERE pe.eleve_id = ?
             ORDER BY a.date_debut DESC",
            [$eleveId]
        );
    }
}
