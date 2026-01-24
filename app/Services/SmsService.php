<?php
/**
 * Service d'envoi de SMS
 */

class SmsService {
    private $config;
    private $logFile;

    public function __construct() {
        $this->config = require CONFIG_PATH . '/sms.php';
        $this->logFile = APP_PATH . '/../storage/logs/sms.log';
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    /**
     * Envoie un SMS
     * 
     * @param string $telephone Numéro de téléphone au format national ou international
     * @param string $message Message à envoyer
     * @return bool Succès de l'envoi
     */
    public function send($telephone, $message) {
        // Préparation du numéro (nettoyage des espaces, points, etc.)
        $telephone = preg_replace('/[^0-9+]/', '', $telephone);

        if (!$this->config['enabled']) {
            $this->logSms($telephone, $message, "SIMULATION (Désactivé dans config)");
            return true;
        }

        switch ($this->config['provider']) {
            case 'simulation':
                return $this->logSms($telephone, $message, "LOG ONLY");
            
            case 'twilio':
                return $this->sendViaTwilio($telephone, $message);
            
            case 'generic_http':
                return $this->sendViaGenericHttp($telephone, $message);
                
            default:
                $this->logSms($telephone, $message, "ERROR: Provider inconnu");
                return false;
        }
    }

    /**
     * Envoi via une API HTTP Générique (GET ou POST)
     * Très courant pour les passerelles locales
     */
    private function sendViaGenericHttp($telephone, $message) {
        $conf = $this->config['generic_http'];
        if (empty($conf['url'])) {
            $this->logSms($telephone, $message, "ERROR: URL HTTP non configurée");
            return false;
        }

        $params = $conf['params']['extra_params'] ?? [];
        $params[$conf['params']['to_key']] = $telephone;
        $params[$conf['params']['msg_key']] = $message;
        
        // Ajout des clés API si renseignées
        if (!empty($conf['api_key'])) $params['api_key'] = $conf['api_key'];
        if (!empty($conf['api_secret'])) $params['api_secret'] = $conf['api_secret'];

        try {
            $queryString = http_build_query($params);
            $fullUrl = $conf['url'];

            if (strtoupper($conf['method']) === 'GET') {
                $fullUrl .= (strpos($fullUrl, '?') === false ? '?' : '&') . $queryString;
                $response = file_get_contents($fullUrl);
            } else {
                // POST Request
                $opts = [
                    'http' => [
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $queryString
                    ]
                ];
                $context  = stream_context_create($opts);
                $response = file_get_contents($fullUrl, false, $context);
            }

            $this->logSms($telephone, $message, "GENERIC_HTTP Response: " . substr($response, 0, 100));
            return true;
        } catch (Exception $e) {
            $this->logSms($telephone, $message, "GENERIC_HTTP ERROR: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Simulation / Implémentation Twilio
     */
    private function sendViaTwilio($telephone, $message) {
        // Pour Twilio, on utilise généralement leur SDK ou une requête POST vers leur API REST
        $conf = $this->config['twilio'];
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$conf['sid']}/Messages.json";
        
        $data = [
            'From' => $conf['from'],
            'To'   => $telephone,
            'Body' => $message,
        ];

        // Exemple de requête cURL ou stream_context pour Twilio
        // Note: Nécessite l'authentification Basic (SID:TOKEN)
        $this->logSms($telephone, $message, "TWILIO (Prêt pour config)");
        
        // Code d'envoi réel à décommenter ou ajuster selon besoins lors de la réception des clés
        return true;
    }

    /**
     * Log le SMS dans un fichier
     */
    private function logSms($telephone, $message, $providerInfo) {
        $logEntry = "[" . date('Y-m-d H:i:s') . "] [$providerInfo] To: $telephone | Msg: $message" . PHP_EOL;
        return file_put_contents($this->logFile, $logEntry, FILE_APPEND) !== false;
    }
}
