<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle DocumentsInscription
 */

class DocumentsInscription extends BaseModel {
    protected $table = 'documents_inscription';
    protected $fillable = [
        'inscription_id', 'eleve_id', 'type_document', 'ordre_affichage',
        'nom_fichier', 'chemin_fichier', 'taille_fichier', 'type_mime',
        'statut', 'obligatoire_pour_validation', 'valide_par', 'date_validation',
        'motif_refus', 'numero_document', 'date_emission', 'date_expiration',
        'lieu_emission', 'remarques', 'telecharge_par'
    ];
    
    /**
     * Récupère les documents d'une inscription
     */
    public function getByInscription($inscriptionId) {
        return $this->query(
            "SELECT di.*, 
                    u1.username as telecharge_par_username,
                    u2.username as valide_par_username
             FROM {$this->table} di
             LEFT JOIN users u1 ON di.telecharge_par = u1.id
             LEFT JOIN users u2 ON di.valide_par = u2.id
             WHERE di.inscription_id = ?
             ORDER BY di.ordre_affichage ASC, di.created_at ASC",
            [$inscriptionId]
        );
    }
    
    /**
     * Récupère les documents d'un élève (même sans inscription)
     */
    public function getByEleve($eleveId) {
        return $this->query(
            "SELECT di.*, 
                    u1.username as telecharge_par_username,
                    u2.username as valide_par_username
             FROM {$this->table} di
             LEFT JOIN users u1 ON di.telecharge_par = u1.id
             LEFT JOIN users u2 ON di.valide_par = u2.id
             WHERE di.eleve_id = ?
             ORDER BY di.ordre_affichage ASC, di.created_at DESC",
            [$eleveId]
        );
    }
    
    /**
     * Récupère les exigences de documents pour une année scolaire et type d'inscription
     */
    public function getExigences($anneeScolaireId, $typeInscription) {
        return $this->query(
            "SELECT * FROM exigences_documents_inscription
             WHERE annee_scolaire_id = ? 
             AND type_inscription = ?
             AND actif = 1
             ORDER BY ordre ASC",
            [$anneeScolaireId, $typeInscription]
        );
    }
    
    /**
     * Vérifie si tous les documents obligatoires sont validés
     */
    public function checkDocumentsComplets($inscriptionId) {
        $result = $this->queryOne(
            "SELECT 
                (SELECT COUNT(*) FROM exigences_documents_inscription ed 
                 INNER JOIN inscriptions i ON ed.annee_scolaire_id = i.annee_scolaire_id 
                    AND ed.type_inscription = i.type_inscription
                 WHERE i.id = ? AND ed.obligatoire = 1 AND ed.actif = 1) as nb_requis,
                (SELECT COUNT(*) FROM {$this->table} di 
                 WHERE di.inscription_id = ? AND di.statut = 'valide' 
                    AND di.obligatoire_pour_validation = 1) as nb_valides",
            [$inscriptionId, $inscriptionId]
        );
        
        return $result && $result['nb_requis'] == $result['nb_valides'];
    }
    
    /**
     * Valide un document
     */
    public function valider($id, $userId) {
        return $this->update($id, [
            'statut' => 'valide',
            'valide_par' => $userId,
            'date_validation' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Refuse un document
     */
    public function refuser($id, $motif, $userId) {
        return $this->update($id, [
            'statut' => 'refuse',
            'motif_refus' => $motif,
            'valide_par' => $userId,
            'date_validation' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Supprime un document et son fichier
     */
    public function deleteDocument($id) {
        $doc = $this->findById($id);
        if ($doc && file_exists($doc['chemin_fichier'])) {
            unlink($doc['chemin_fichier']);
        }
        return $this->delete($id);
    }
    
    /**
     * Obtient les statistiques des documents pour une inscription
     */
    public function getStats($inscriptionId) {
        return $this->queryOne(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN statut = 'valide' THEN 1 ELSE 0 END) as valides,
                SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as refuses,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN obligatoire_pour_validation = 1 THEN 1 ELSE 0 END) as obligatoires
             FROM {$this->table}
             WHERE inscription_id = ?",
            [$inscriptionId]
        );
    }
}
