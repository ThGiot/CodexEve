<?php
session_start();

// Dependencies
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH.'/sql.php';
require_once PRIVATE_PATH.'/classes/ClearPost.php';
require_once PRIVATE_PATH.'/classes/Logger.php';

// Check user session

if(!isset($_SESSION['user'])) {   
    
    http_response_code(400);
    echo json_encode(['error' => 'Session has expired']);
    exit();
}

// Clean the POST data
$_POST = ClearPost::clearPost($_POST);

// Get the module name
// Détecter le type de contenu de la requête
if (strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
    // Si le contenu est du JSON, décodez-le
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    // Sinon, utilisez simplement $_POST
    $data = $_POST;
}
if(!isset($data['module'])){
  //  if(!isset($_SESSION['module_actif'])) exit('NO'.print_r($_SESSION));
    $query = "SELECT nom FROM module WHERE id = :id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':id', $_SESSION['module_actif']);
    $stmt->execute();
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($module)){

        }else{
        $module_nom = $module['nom'];
        $module_id = $_SESSION['module_actif'];
        }
}else{
    $module_nom = 'Personnal';
    $module_id = -1;
}
if(isset($data['module_personnal']) AND $data['module_personnal'] == true){
    $module_nom = 'Personnal';
}


//print_r($_SESSION);
// Récupérer le type de nœud, peu importe la source des données
$nodeType = $data['node'] ?? null;

// Handling based on node type
switch($nodeType) {
    case 'navbar':
        require PRIVATE_PATH.'/module/'.$module_nom.'/nav.php';
        break;
       
    case 'content':
        if(!isset($_SESSION['client_actif'])){
          
            require PRIVATE_PATH.'/module/default/handler.php';
        }else{
            $logger = new Logger($dbh);
            $logger->log(
            logType: 'info',
            action: 'get_content request page:'.$data['page'],
            script: basename(__FILE__),
            ip: Logger::getUserIP(),
                userId: $_SESSION['user']['id'],
            clientId: $_SESSION['client_actif'],
            additionalInfo: ''
        );
            $page = $data['page'];
            require PRIVATE_PATH.'/module/'.$module_nom.'/handler.php';
        }
        break;
    case 'navitem':
        
        require PRIVATE_PATH.'/action/get_navitem.php';
        break;
    case 'action':
        $logger = new Logger($dbh);
        $logger->log(
            logType: 'info',
            action: 'action_node request :'.$data['action'],
            script: basename(__FILE__),
            ip: Logger::getUserIP(),
                userId: $_SESSION['user']['id'],
            clientId: $_SESSION['client_actif'],
            additionalInfo: ''
        );
        if(!isset($_SESSION['client_actif'])){
            require PRIVATE_PATH.'/module/default/action/handler.php';
          
        }else{
            
            require PRIVATE_PATH.'/module/'.$module_nom.'/action/handler.php';
        }
        break;
    default:
    http_response_code(400);
    echo json_encode(['error' => 'Type de nœud non reconnu']);
    break;
        break;
}

?>
