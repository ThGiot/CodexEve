<?php

require_once 'ResponseHandler.php';
require_once 'AuthHandler.php';
require_once 'Validator.php';
require_once 'ClearPost.php';


class RequestHandler {
    private $authHandler;
    private $responseHandler;
    private $validator;
    private $clearPost;

    public function __construct() {
        $this->responseHandler = new ResponseHandler();
        $this->authHandler = new AuthHandler($this->responseHandler);
        $this->validator = new Validator();
        $this->clearPost = new ClearPost();
    }

    public function handleRequest($data, $rules) {
        // Étape 1: Vérifier la session
        $this->authHandler->checkSession();
    
        // Étape 2: Assainir les données
        $cleanData = $this->clearPost->clearPost($data);
    
        // Étape 3: Vérifier si `periodes` est JSON et le décoder si nécessaire
        if (isset($rules['periodes']) && $rules['periodes']['type'] === 'array' && is_string($cleanData['periodes'])) {
            $decoded = json_decode($cleanData['periodes'], true);
            if (!is_array($decoded)) {
                echo $this->responseHandler->sendResponse(false, "Erreur : periodes non valide (JSON invalide)");
                exit();
            }
            $cleanData['periodes'] = $decoded;
        }
    
        // Étape 4: Valider les données
        $validationResult = $this->validator->validate($cleanData, $rules);
        if (!$validationResult['success']) {
            echo $this->responseHandler->sendResponse(false, $validationResult['message']);
            exit();
        }
    
        return $cleanData;
    }
    

    public function verifyModulePermission($module_id,$dbh) {
        $this->authHandler->checkModulePermission($module_id,$dbh);
    }

    public function superAdminAuth($dbh){
        $this->authHandler->superAdminAuth($dbh);
    }
}

?>