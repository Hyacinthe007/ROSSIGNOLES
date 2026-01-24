<?php
/**
 * Contrôleur des notifications
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Notification.php';
require_once APP_PATH . '/Services/NotificationService.php';

class NotificationsController extends BaseController {
    private $notificationModel;
    private $notificationService;
    
    public function __construct() {
        $this->requireAuth();
        $this->notificationModel = new Notification();
        $this->notificationService = new NotificationService();
    }
    
    public function list() {
        // Récupérer toutes les notifications (ou filtrer par utilisateur si nécessaire)
        try {
            $notifications = $this->notificationModel->query(
                "SELECT n.*, u.username, u.email 
                 FROM notifications n
                 LEFT JOIN users u ON n.user_id = u.id
                 ORDER BY n.date_envoi DESC, n.created_at DESC
                 LIMIT 100"
            );
        } catch (PDOException $e) {
            // Si la table users n'existe pas ou erreur, récupérer sans JOIN
            $notifications = $this->notificationModel->all([], 'date_envoi DESC, created_at DESC');
        }
        
        $this->view('notifications/list', ['notifications' => $notifications]);
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $canal = $_POST['canal'] ?? 'systeme';
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $priorite = $_POST['priorite'] ?? 'normale';
            $dateEnvoi = $_POST['date_envoi'] ?? null;
            
            // Préparer les métadonnées
            $meta = [];
            if (!empty($_POST['meta_lien'])) {
                $meta['lien'] = $_POST['meta_lien'];
            }
            if (!empty($_POST['meta_type'])) {
                $meta['type'] = $_POST['meta_type'];
            }
            if ($priorite !== 'normale') {
                $meta['priorite'] = $priorite;
            }
            
            // Si "Tous les utilisateurs" est sélectionné
            if ($userId === 'all') {
                try {
                    $users = $this->notificationModel->query("SELECT id FROM users WHERE actif = 1");
                    foreach ($users as $user) {
                        $this->notificationService->send(
                            $user['id'],
                            $canal,
                            $titre,
                            $contenu,
                            $meta
                        );
                    }
                    $_SESSION['success_message'] = 'Notification envoyée à tous les utilisateurs !';
                } catch (PDOException $e) {
                    // Si erreur, créer une seule notification avec user_id null
                    $data = [
                        'user_id' => null,
                        'canal' => $canal,
                        'titre' => $titre,
                        'contenu' => $contenu,
                        'statut' => 'file_attente',
                        'meta' => json_encode($meta),
                    ];
                    $this->notificationModel->create($data);
                    $_SESSION['success_message'] = 'Notification créée avec succès !';
                }
            } else {
                // Notification pour un utilisateur spécifique
                $this->notificationService->send(
                    $userId,
                    $canal,
                    $titre,
                    $contenu,
                    $meta
                );
                $_SESSION['success_message'] = 'Notification envoyée avec succès !';
            }
            
            $this->redirect('notifications/list');
        } else {
            // Récupérer la liste des utilisateurs
            $users = [];
            try {
                $users = $this->notificationModel->query(
                    "SELECT id, username, email FROM users WHERE actif = 1 ORDER BY username ASC"
                );
            } catch (PDOException $e) {
                error_log("Table users n'existe pas ou erreur: " . $e->getMessage());
            }
            
            $this->view('notifications/add', ['users' => $users]);
        }
    }
    
    public function details($id) {
        try {
            $notification = $this->notificationModel->queryOne(
                "SELECT n.*, u.username, u.email 
                 FROM notifications n
                 LEFT JOIN users u ON n.user_id = u.id
                 WHERE n.id = ?",
                [$id]
            );
        } catch (PDOException $e) {
            $notification = $this->notificationModel->find($id);
        }
        
        if (!$notification) {
            http_response_code(404);
            die("Notification non trouvée");
        }
        
        // Décoder les métadonnées
        if (!empty($notification['meta'])) {
            $notification['meta_decoded'] = json_decode($notification['meta'], true);
        }
        
        $this->view('notifications/details', ['notification' => $notification]);
    }
    
    /**
     * Messagerie interne (Boîte de réception)
     */
    public function messagerie() {
        require_once APP_PATH . '/Models/Message.php';
        $messageModel = new Message();
        
        $userId = $_SESSION['user_id'];
        $messages = $messageModel->getBoiteReception($userId);
        
        require_once APP_PATH . '/Models/User.php';
        $userModel = new User();
        $users = $userModel->all(['actif' => 1], 'username ASC');
        
        $this->view('notifications/messagerie', [
            'messages' => $messages,
            'users' => $users,
            'vue' => 'reception'
        ]);
    }

    /**
     * Envoi d'un nouveau message interne
     */
    public function envoyerMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once APP_PATH . '/Models/Message.php';
            $messageModel = new Message();
            
            $data = [
                'expediteur_id' => $_SESSION['user_id'],
                'destinataire_id' => $_POST['destinataire_id'],
                'sujet' => $_POST['sujet'] ?? 'Sans objet',
                'contenu' => $_POST['contenu'],
                'parent_message_id' => $_POST['parent_message_id'] ?? null
            ];
            
            if ($messageModel->create($data)) {
                $_SESSION['success_message'] = "Message envoyé avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'envoi du message.";
            }
            $this->redirect('notifications/messagerie');
        }
    }
    
    /**
     * Liste des modèles de notifications
     */
    public function modeles() {
        require_once APP_PATH . '/Models/ModeleNotification.php';
        $model = new ModeleNotification();
        $modeles = $model->all([], 'nom ASC');
        $this->view('notifications/modeles', ['modeles' => $modeles]);
    }

    /**
     * Ajouter un modèle
     */
    public function addModele() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once APP_PATH . '/Models/ModeleNotification.php';
            $model = new ModeleNotification();
            
            $data = [
                'nom' => $_POST['nom'],
                'sujet' => $_POST['sujet'],
                'contenu' => $_POST['contenu'],
                'type' => $_POST['type'] ?? 'info'
            ];
            
            if ($model->create($data)) {
                $_SESSION['success_message'] = "Modèle créé avec succès.";
                $this->redirect('notifications/modeles');
            } else {
                $_SESSION['error_message'] = "Erreur lors de la création du modèle.";
            }
        }
        $this->view('notifications/modeles_add');
    }

    /**
     * Modifier un modèle
     */
    public function editModele($id) {
        require_once APP_PATH . '/Models/ModeleNotification.php';
        $model = new ModeleNotification();
        $modele = $model->find($id);
        
        if (!$modele) {
            $_SESSION['error_message'] = "Modèle non trouvé.";
            $this->redirect('notifications/modeles');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'],
                'sujet' => $_POST['sujet'],
                'contenu' => $_POST['contenu'],
                'type' => $_POST['type'] ?? 'info'
            ];
            
            if ($model->update($id, $data)) {
                $_SESSION['success_message'] = "Modèle mis à jour avec succès.";
                $this->redirect('notifications/modeles');
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour.";
            }
        }
        
        $this->view('notifications/modeles_edit', ['modele' => $modele]);
    }

    /**
     * Supprimer un modèle
     */
    public function deleteModele($id) {
        require_once APP_PATH . '/Models/ModeleNotification.php';
        $model = new ModeleNotification();
        if ($model->delete($id)) {
            $_SESSION['success_message'] = "Modèle supprimé.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression.";
        }
        $this->redirect('notifications/modeles');
    }
}

