<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Loggable;

/**
 * Modèle NoteExamen
 * Gestion des notes d'examens finaux avec journalisation automatique
 */

class NoteExamen extends BaseModel {
    use Loggable;
    
    protected $table = 'notes_examens';
    protected $fillable = [
        'examen_id', 'eleve_id', 'note', 'absent', 
        'appreciation', 'saisi_par', 'date_saisie', 'modifie_par', 'date_modification'
    ];
    
    /**
     * Crée une note d'examen avec journalisation
     * @param array $data Données de la note
     * @return int|bool ID de la note créée ou false
     */
    public function create($data) {
        $noteId = parent::create($data);
        
        if ($noteId) {
            $this->logCreate(
                'notes',
                'note_examen',
                $noteId,
                [
                    'eleve_id' => $data['eleve_id'],
                    'examen_id' => $data['examen_id'],
                    'note' => $data['note'],
                    'absent' => $data['absent'] ?? 0
                ]
            );
        }
        
        return $noteId;
    }
    
    /**
     * Met à jour une note d'examen avec journalisation
     * @param int $id ID de la note
     * @param array $newData Nouvelles données
     * @return bool Succès de l'opération
     */
    public function update($id, $newData) {
        // Récupérer l'ancienne note
        $oldData = $this->find($id);
        
        if (!$oldData) {
            return false;
        }
        
        $success = parent::update($id, $newData);
        
        if ($success && isset($newData['note']) && $oldData['note'] != $newData['note']) {
            $this->logNoteChange(
                'examen',
                $id,
                $oldData['eleve_id'],
                $oldData['note'],
                $newData['note']
            );
        }
        
        return $success;
    }
    
    /**
     * Supprime une note d'examen avec journalisation
     * @param int $id ID de la note
     * @return bool Succès de l'opération
     */
    public function delete($id) {
        $note = $this->find($id);
        
        if (!$note) {
            return false;
        }
        
        $success = parent::delete($id);
        
        if ($success) {
            $this->logDelete(
                'notes',
                'note_examen',
                $id,
                [
                    'eleve_id' => $note['eleve_id'],
                    'examen_id' => $note['examen_id'],
                    'note' => $note['note']
                ]
            );
        }
        
        return $success;
    }
    
    /**
     * Récupère les notes d'un examen avec détails
     * @param int $examenId ID de l'examen
     * @return array Liste des notes
     */
    public function getByExamen($examenId) {
        return $this->query(
            "SELECT ne.*, 
                    e.matricule, e.nom, e.prenom,
                    u1.username as saisi_par_nom,
                    u2.username as modifie_par_nom
             FROM {$this->table} ne
             INNER JOIN eleves e ON ne.eleve_id = e.id
             LEFT JOIN users u1 ON ne.saisi_par = u1.id
             LEFT JOIN users u2 ON ne.modifie_par = u2.id
             WHERE ne.examen_id = ?
             ORDER BY e.nom ASC, e.prenom ASC",
            [$examenId]
        );
    }
}
