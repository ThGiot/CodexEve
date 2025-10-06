<?php

class Logger {
    private $dbh;

    public function __construct(PDO $dbh) {
        $this->dbh = $dbh;
    }

    /**
     * Insère un log dans la base de données.
     * 
     * @param string $logType Le type de log ('error', 'action', 'info').
     * @param string $action Description de l'action réalisée.
     * @param string $script Le nom du script où l'action a eu lieu.
     * @param string $ip L'adresse IP de l'utilisateur.
     * @param int|null $userId L'identifiant de l'utilisateur (optionnel).
     * @param int|null $clientId L'identifiant du client (optionnel).
     * @param string|null $additionalInfo Informations supplémentaires (optionnel).
     */
    public function log($logType, $action, $script, $ip = null, $userId = null, $clientId = null, $additionalInfo = null) {
        if ($ip === null) {
            $ip = self::getUserIP();
        }
        
        $query = "
            INSERT INTO logs (log_type, user_id, client_id, action, script, ip_address, additional_info)
            VALUES (:logType, :userId, :clientId, :action, :script, :ip, :additionalInfo)
        ";

        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':logType', $logType);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':clientId', $clientId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':script', $script);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':additionalInfo', $additionalInfo);

        $stmt->execute();
    }

    /**
     * Fonction utilitaire pour obtenir l'adresse IP de l'utilisateur.
     * 
     * @return string L'adresse IP de l'utilisateur.
     */
    public static function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}

?>
