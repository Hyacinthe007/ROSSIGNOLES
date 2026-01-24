<?php
require_once __DIR__ . '/BaseModel.php';

class DecisionConseil extends BaseModel {
    protected $table = 'decisions_conseil';
    protected $fillable = [
        'conseil_classe_id',
        'eleve_id',
        'bulletin_id',
        'distinction',
        'avertissement',
        'appreciation_conseil',
        'points_positifs',
        'points_vigilance',
        'conseils_eleve',
        'decision_passage',
        'necessite_suivi',
        'type_suivi'
    ];
}
