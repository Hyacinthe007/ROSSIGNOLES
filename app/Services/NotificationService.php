<?php
/**
 * Service de notifications
 */

class NotificationService {
    
    /**
     * Envoie une notification
     */
    public function send($userId, $canal, $titre, $contenu, $meta = []) {
        require_once APP_PATH . '/Models/Notification.php';
        $notificationModel = new Notification();
        
        // Mapper le canal vers un type de notification générique
        $type = $meta['type'] ?? match ($canal) {
            'sms'   => 'info',
            'email' => 'info',
            'push'  => 'info',
            default => 'info',
        };

        $data = [
            'user_id' => $userId,
            'titre' => $titre,
            'canal' => $canal,
            'type'  => $type,
            'message' => $contenu,
            'url_action' => $meta['url_action'] ?? null,
            'icone' => $meta['icone'] ?? null,
            'lu' => 0,
            'statut' => 'file_attente',
            'date_envoi' => date('Y-m-d H:i:s'),
        ];
        
        $id = $notificationModel->create($data);
        
        // Envoi réel selon le canal
        $this->processNotification($id, $canal, $titre, $contenu, $userId);

        return $id;
    }
    
    /**
     * Traite l'envoi de la notification
     */
    private function processNotification($id, $canal, $titre, $contenu, $userId = null) {
        require_once APP_PATH . '/Models/Notification.php';
        $notificationModel = new Notification();
        
        try {
            switch ($canal) {
                case 'email':
                    // TODO: implémenter l'envoi email
                    $notificationModel->update($id, ['statut' => 'envoye']);
                    break;
                case 'sms':
                    if ($userId) {
                        // Récupérer le numéro de téléphone si on a un userId
                        require_once APP_PATH . '/Models/BaseModel.php';
                        $db = BaseModel::getDBConnection();
                        // Chercher dans parents liés si l'utilisateur est un parent ou lié à un élève
                        $sql = "SELECT p.telephone_principal 
                                FROM parents p
                                JOIN users u ON u.email = p.email OR u.username = p.telephone_principal
                                WHERE u.id = ? LIMIT 1";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([$userId]);
                        $parent = $stmt->fetch();
                        
                        if ($parent && !empty($parent['telephone_principal'])) {
                            require_once APP_PATH . '/Services/SmsService.php';
                            $sms = new SmsService();
                            $res = $sms->send($parent['telephone_principal'], $contenu);
                            
                            if ($res['success']) {
                                $notificationModel->update($id, ['statut' => 'envoye']);
                            } else {
                                $notificationModel->update($id, ['statut' => 'echec', 'erreur' => $res['message']]);
                            }
                        } else {
                            $notificationModel->update($id, ['statut' => 'echec', 'erreur' => 'Numéro de téléphone non trouvé pour cet utilisateur']);
                        }
                    }
                    break;
                default:
                    $notificationModel->update($id, ['statut' => 'envoye']);
            }
        } catch (Exception $e) {
            $notificationModel->update($id, ['statut' => 'echec', 'erreur' => $e->getMessage()]);
        }
    }
}

