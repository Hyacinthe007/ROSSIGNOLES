<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Messages
 * Correspond à la table 'messages'
 */

class Messages extends BaseModel {
    protected $table = 'messages';
    protected $fillable = [
        'expediteur_id', 'destinataire_id', 'sujet', 'contenu',
        'lu', 'date_lecture', 'parent_message_id'
    ];
    
    /**
     * Récupère les messages reçus par un utilisateur
     */
    public function getRecus($userId, $nonLusOnly = false) {
        $where = "m.destinataire_id = ?";
        $params = [$userId];
        
        if ($nonLusOnly) {
            $where .= " AND m.lu = 0";
        }
        
        return $this->query(
            "SELECT m.*, 
                    u_exp.username as expediteur_username,
                    u_exp.avatar as expediteur_avatar
             FROM {$this->table} m
             INNER JOIN users u_exp ON m.expediteur_id = u_exp.id
             WHERE {$where}
             ORDER BY m.created_at DESC",
            $params
        );
    }
    
    /**
     * Récupère les messages envoyés par un utilisateur
     */
    public function getEnvoyes($userId) {
        return $this->query(
            "SELECT m.*, 
                    u_dest.username as destinataire_username,
                    u_dest.avatar as destinataire_avatar
             FROM {$this->table} m
             INNER JOIN users u_dest ON m.destinataire_id = u_dest.id
             WHERE m.expediteur_id = ?
             ORDER BY m.created_at DESC",
            [$userId]
        );
    }
    
    /**
     * Récupère une conversation entre deux utilisateurs
     */
    public function getConversation($userId1, $userId2) {
        return $this->query(
            "SELECT m.*, 
                    u_exp.username as expediteur_username,
                    u_dest.username as destinataire_username
             FROM {$this->table} m
             INNER JOIN users u_exp ON m.expediteur_id = u_exp.id
             INNER JOIN users u_dest ON m.destinataire_id = u_dest.id
             WHERE (m.expediteur_id = ? AND m.destinataire_id = ?)
                OR (m.expediteur_id = ? AND m.destinataire_id = ?)
             ORDER BY m.created_at ASC",
            [$userId1, $userId2, $userId2, $userId1]
        );
    }
    
    /**
     * Marque un message comme lu
     */
    public function marquerLu($id) {
        return $this->update($id, [
            'lu' => 1,
            'date_lecture' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Compte les messages non lus d'un utilisateur
     */
    public function countNonLus($userId) {
        $result = $this->queryOne(
            "SELECT COUNT(*) as total 
             FROM {$this->table} 
             WHERE destinataire_id = ? AND lu = 0",
            [$userId]
        );
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Récupère les réponses à un message
     */
    public function getReponses($messageId) {
        return $this->query(
            "SELECT m.*, 
                    u_exp.username as expediteur_username
             FROM {$this->table} m
             INNER JOIN users u_exp ON m.expediteur_id = u_exp.id
             WHERE m.parent_message_id = ?
             ORDER BY m.created_at ASC",
            [$messageId]
        );
    }
}
