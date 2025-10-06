<?php

require_once dirname(__DIR__, 3) . '/classes/ResponseHandler.php';
require_once dirname(__DIR__, 3) . '/classes/RequestHandler.php';
require_once dirname(__DIR__, 3) . '/classes/FileUploader.php';
require_once dirname(__DIR__, 3) . '/classes/DBHandler.php';

$responseHandler = new ResponseHandler();
$requestHandler = new RequestHandler();

// Le répertoire où les fichiers téléchargés seront enregistrés
$uploadDir = PUBLIC_PATH.'/assets/img/uploads/';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    try {
        $uploader = new FileUploader($_FILES['avatar'], $uploadDir);
        $newFileName = $uploader->upload();
    
    } catch (Exception $e) {
        exit($responseHandler->sendResponse(false, $e->getMessage()));
    }
} else {
    exit($responseHandler->sendResponse(false, "Aucun fichier n'a été uploadé."));
}
$dbHandler = new DBHandler($dbh);
$table = 'user';
$key = ['id' => $_SESSION['user']['id']];
$data = ['avatar' =>'/assets/img/uploads/'.$newFileName];
$dbHandler->update($table, $key, $data);
echo $responseHandler->sendResponse(true, 'Information enregistree');
$_SESSION['user']['avatar'] = '/assets/img/uploads/'.$newFileName;

?>
