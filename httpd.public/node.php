<?php
session_start();

// Dependencies
require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';
require_once PRIVATE_PATH . '/classes/ClearPost.php';
require_once PRIVATE_PATH . '/classes/Logger.php';

// Normalise la charge utile
$_POST = ClearPost::clearPost($_POST);
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $rawInput = file_get_contents('php://input');
    $decoded = json_decode($rawInput, true);
    $data = is_array($decoded) ? ClearPost::clearPost($decoded) : [];
} else {
    $data = $_POST;
}

$nodeType = $data['node'] ?? null;
$publicNodes = ['clinical-procedure'];
$module_nom = null;
$module_id = null;

if (!isset($_SESSION['user']) && !in_array($nodeType, $publicNodes, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Session has expired']);
    exit();
}

// Get the module name
if (!in_array($nodeType, $publicNodes, true)) {
    if (!isset($data['module'])) {
        //  if(!isset($_SESSION['module_actif'])) exit('NO'.print_r($_SESSION));
        $query = "SELECT nom FROM module WHERE id = :id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $_SESSION['module_actif']);
        $stmt->execute();
        $module = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($module)) {
            $module_nom = $module['nom'];
            $module_id = $_SESSION['module_actif'];
        }
    } else {
        $module_nom = 'Personnal';
        $module_id = -1;
    }
    if (isset($data['module_personnal']) && $data['module_personnal'] == true) {
        $module_nom = 'Personnal';
    }
}

// Handling based on node type
switch ($nodeType) {
    case 'navbar':
        require PRIVATE_PATH . '/module/' . $module_nom . '/nav.php';
        break;

    case 'content':
        if (!isset($_SESSION['client_actif'])) {
            require PRIVATE_PATH . '/module/default/handler.php';
        } else {
            $logger = new Logger($dbh);
            $logger->log(
                logType: 'info',
                action: 'get_content request page:' . $data['page'],
                script: basename(__FILE__),
                ip: Logger::getUserIP(),
                userId: $_SESSION['user']['id'],
                clientId: $_SESSION['client_actif'],
                additionalInfo: ''
            );
            $page = $data['page'];
            require PRIVATE_PATH . '/module/' . $module_nom . '/handler.php';
        }
        break;

    case 'navitem':
        require PRIVATE_PATH . '/action/get_navitem.php';
        break;

    case 'action':
        $logger = new Logger($dbh);
        $logger->log(
            logType: 'info',
            action: 'action_node request :' . $data['action'],
            script: basename(__FILE__),
            ip: Logger::getUserIP(),
            userId: $_SESSION['user']['id'],
            clientId: $_SESSION['client_actif'],
            additionalInfo: ''
        );
        if (!isset($_SESSION['client_actif'])) {
            require PRIVATE_PATH . '/module/default/action/handler.php';
        } else {
            require PRIVATE_PATH . '/module/' . $module_nom . '/action/handler.php';
        }
        break;

    case 'clinical-procedure':
        require __DIR__ . '/../httpd.private/module/Clinical/handler.php';
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Type de nœud non reconnu']);
        break;
}

?>
