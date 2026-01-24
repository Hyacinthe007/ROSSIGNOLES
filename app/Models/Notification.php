<?php
/**
 * Modèle Notification
 */

require_once __DIR__ . '/BaseModel.php';

class Notification extends BaseModel {
    protected $table = 'notifications';
    protected $fillable = [
        'user_id',
        'type',
        'titre',
        'message',
        'url_action',
        'icone',
        'lu',
        'date_lecture',
    ];
}

