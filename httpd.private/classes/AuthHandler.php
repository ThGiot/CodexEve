<?php
class AuthHandler {
    private $responseHandler;
  
    public function __construct($responseHandler) {
        if ($responseHandler === null) {
            throw new Exception('responseHandler ne doit pas être null');
        }
        $this->responseHandler = $responseHandler;
        $this->checkSession();
    
    }

    public function checkSession() {
        if (!isset($_SESSION['user'])) {
            echo $this->responseHandler->sendResponse(false, 'aucune session user obtenue AUTH FAILED');
            exit();
        }
    }

    public function checkModulePermission($module_id,$dbh) {
        $query = "SELECT * FROM module_permission_role WHERE 
                                                module_id = :module_id AND
                                                user_id = :user_id AND
                                                client_id = :client_id";
        $stmt = $dbh->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':module_id', $module_id);
        $stmt->bindParam(':user_id', $_SESSION['user']['id']);
        $stmt->bindParam(':client_id', $_SESSION['client_actif']);
        
        $stmt->execute();

        // Check if any rows are returned, which would indicate permission
        if($stmt->rowCount() == 0) {
            echo $this->responseHandler->sendResponse(false, 'Permission denied for this module.');
            exit();
        }
    }

    public function superAdminAuth($dbh){
        
        $query = "  SELECT role_id FROM module_permission_role
        WHERE user_id = :user_id AND
        module_id = :module_id AND
        client_id = :client_id";
        $stmt = $dbh->prepare($query);
        // Exécuter la requête en liant les paramètres
        $stmt->bindParam(':user_id', $_SESSION['user']['id']);
        $stmt->bindParam(':module_id', $_SESSION['module_actif']);
        $stmt->bindParam(':client_id', $_SESSION['client_actif']);
        $stmt->execute();
        $role= $stmt->fetch(PDO::FETCH_ASSOC);


        if(empty($role) OR $role['role_id'] != 1){
            echo $responseHandler->sendResponse(false, 'Seul un gestionnaire Eve peux effectuer cette action');
            exit();
        }
    }
}

?>