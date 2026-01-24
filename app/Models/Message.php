<?php
require_once __DIR__ . '/BaseModel.php';

class Message extends BaseModel {
    protected $table = 'messages';
    protected $fillable = ['expediteur_id', 'destinataire_id', 'sujet', 'contenu', 'lu', 'date_lecture', 'parent_message_id'];
    
    public function getBoiteReception($userId) {
        return $this->query(
            "SELECT m.*, u.username as expediteur_nom, u.email as expediteur_email
             FROM {$this->table} m
             JOIN users u ON m.expediteur_id = u.id
             WHERE m.destinataire_id = ?
             ORDER BY m.created_at DESC",
            [$userId]
        );
    }
    
    public function getMessagesEnvoyes($userId) {
        return $this->query(
            "SELECT m.*, u.username as destinataire_nom, u.email as destinataire_email
             FROM {$this->table} m
             JOIN users u ON m.destinataire_id = u.id
             WHERE m.expediteur_id = ?
             ORDER BY m.created_at DESC",
            [$userId]
        );
    }
    
    public function getConversation($messageId) {
        // Récupère le fil de discussion (message parent et réponses)
        return $this->query(
            "SELECT m.*, u1.username as exp_nom, u2.username as dest_nom
             FROM {$this->table} m
             JOIN users u1 ON m.expediteur_id = u1.id
             JOIN users u2 ON m.destinataire_id = u2.id
             WHERE m.id = ? OR m.parent_message_id = ?
             ORDER BY m.created_at ASC",
            [$messageId, $messageId]
        );
    }
    
    public function countNonLus($userId) {
        $res = $this->queryOne(
            "SELECT COUNT(*) as cnt FROM {$this->table} WHERE destinataire_id = ? AND lu = 0",
            [$userId]
        );
        return $res ? $res['cnt'] : 0;
    }
}
