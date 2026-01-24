<?php
require_once __DIR__ . '/BaseModel.php';

class ModeleNotification extends BaseModel {
    protected $table = 'modeles_notifications';
    protected $fillable = ['nom', 'sujet', 'contenu', 'type'];
}
