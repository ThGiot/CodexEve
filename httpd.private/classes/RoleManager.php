<?php
class RoleManager {
    private $dbh;

    public function __construct(PDO $dbh) {
        $this->dbh = $dbh;
    }

    public function getUserRole($userId, $moduleId, $clientId) {
        $query = "SELECT role_id FROM module_permission_role
                  WHERE user_id = :user_id AND module_id = :module_id AND client_id = :client_id";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':module_id' => $moduleId,
            ':client_id' => $clientId
        ]);
        return $stmt->fetchColumn() ?: null;
    }
}
