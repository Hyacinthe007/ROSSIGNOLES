<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Notification
 */

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

