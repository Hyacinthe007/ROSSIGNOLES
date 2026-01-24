<?php
require_once __DIR__ . '/BaseModel.php';

class Note extends BaseModel {
    protected $table = null; // Modèle d'agrégation virtuelle
    
    /**
     * Récupère toutes les notes (examens et interrogations) pour un élève et une période
     */
    public function getByElevePeriode($eleveId, $periodeId) {
        $sql = "
            SELECT 
                'examen' as type_evaluation,
                ef.id as evaluation_id,
                ef.nom as evaluation_nom,
                ef.date_examen as date_evaluation,
                ef.note_sur,
                ne.note,
                ne.absent,
                ne.appreciation,
                m.id as matiere_id,
                m.nom as matiere_nom,
                m.code as matiere_code,
                COALESCE(mc.coefficient, ms.coefficient, mn.coefficient, 1.00) as coefficient,
                ef.personnel_id,
                p.nom as prof_nom, p.prenom as prof_prenom
            FROM notes_examens ne
            JOIN examens_finaux ef ON ne.examen_id = ef.id
            JOIN matieres m ON ef.matiere_id = m.id
            JOIN classes c ON ef.classe_id = c.id
            LEFT JOIN matieres_classes mc ON (mc.matiere_id = ef.matiere_id AND mc.classe_id = c.id AND mc.annee_scolaire_id = c.annee_scolaire_id)
            LEFT JOIN matieres_series ms ON (ms.matiere_id = ef.matiere_id AND ms.serie_id = c.serie_id AND ms.actif = 1)
            LEFT JOIN matieres_niveaux mn ON (mn.matiere_id = ef.matiere_id AND mn.niveau_id = c.niveau_id AND mn.actif = 1)
            LEFT JOIN personnels p ON ef.personnel_id = p.id
            WHERE ne.eleve_id = ? AND ef.periode_id = ?
            
            UNION ALL
            
            SELECT 
                'interrogation' as type_evaluation,
                i.id as evaluation_id,
                i.nom as evaluation_nom,
                i.date_interrogation as date_evaluation,
                i.note_sur,
                ni.note,
                ni.absent,
                ni.appreciation,
                m.id as matiere_id,
                m.nom as matiere_nom,
                m.code as matiere_code,
                COALESCE(mc.coefficient, ms.coefficient, mn.coefficient, 1.00) as coefficient,
                i.personnel_id,
                p.nom as prof_nom, p.prenom as prof_prenom
            FROM notes_interrogations ni
            JOIN interrogations i ON ni.interrogation_id = i.id
            JOIN matieres m ON i.matiere_id = m.id
            JOIN classes c ON i.classe_id = c.id
            LEFT JOIN matieres_classes mc ON (mc.matiere_id = i.matiere_id AND mc.classe_id = c.id AND mc.annee_scolaire_id = c.annee_scolaire_id)
            LEFT JOIN matieres_series ms ON (ms.matiere_id = i.matiere_id AND ms.serie_id = c.serie_id AND ms.actif = 1)
            LEFT JOIN matieres_niveaux mn ON (mn.matiere_id = i.matiere_id AND mn.niveau_id = c.niveau_id AND mn.actif = 1)
            LEFT JOIN personnels p ON i.personnel_id = p.id
            WHERE ni.eleve_id = ? AND i.periode_id = ?
            
            ORDER BY matiere_nom ASC, date_evaluation ASC
        ";
        
        return $this->query($sql, [$eleveId, $periodeId, $eleveId, $periodeId]);
    }
}
