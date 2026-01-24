<?php
/**
 * Modèle BulletinNote
 */

require_once __DIR__ . '/BaseModel.php';

class BulletinNote extends BaseModel {
    protected $table = 'bulletins_notes';
    protected $fillable = [
        'bulletin_id', 'matiere_id', 'moyenne_interrogations', 
        'note_examen', 'note_bulletin', 'coefficient', 'note_ponderee', 
        'rang_matiere', 'appreciation_matiere'
    ];
}
