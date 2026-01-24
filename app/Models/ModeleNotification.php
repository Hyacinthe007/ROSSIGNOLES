<?php
declare(strict_types=1);

namespace App\Models;

class ModeleNotification extends BaseModel {
    protected $table = 'modeles_notifications';
    protected $fillable = ['nom', 'sujet', 'contenu', 'type'];
}
