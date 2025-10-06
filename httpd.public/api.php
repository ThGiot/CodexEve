<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/../httpd.private/config.php';
require_once PRIVATE_PATH . '/sql.php';
require_once PRIVATE_PATH . '/classes/SelectHandler.php';
require_once PRIVATE_PATH . '/classes/Logger.php';

try {
    // Connexion à la DB (via la classe Database ou autre handler déjà instancié)
   

    $logger = new Logger($dbh);

    //  Authentification par clé API
    $providedKey = $_GET['api_key'] ?? '';
    if ($providedKey !== API_KEY) {
        $logger->log('error', 'Clé API invalide', basename(__FILE__), Logger::getUserIP());
        http_response_code(401);
        throw new Exception("Unauthorized: Invalid API key.");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $logger->log('error', 'Méthode non autorisée', basename(__FILE__), Logger::getUserIP());
        throw new Exception("Only GET method is allowed.");
    }

    if (!isset($_GET['table'])) {
        $logger->log('error', 'Paramètre "table" manquant', basename(__FILE__), Logger::getUserIP());
        throw new Exception("Table parameter is required.");
    }

    $table = $_GET['table'];
    $filters = $_GET;
    unset($filters['table'], $filters['api_key'], $filters['limit'], $filters['offset'], $filters['order_by'], $filters['order']);

    $pagination = [
        'limit' => isset($_GET['limit']) ? (int) $_GET['limit'] : null,
        'offset' => isset($_GET['offset']) ? (int) $_GET['offset'] : null,
    ];

    $order = [
        'by' => isset($_GET['order_by']) ? $_GET['order_by'] : null,
        'direction' => isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC'
    ];

    $handler = new SelectHandler($dbh);
    $results = $handler->getAll($table, $filters, $pagination, $order);

    //  Log de l'action réussie
    $logger->log(
        'action',
        "Requête API SELECT sur table `$table`",
        basename(__FILE__),
        Logger::getUserIP(),
        null,
        null,
        json_encode([
            'filters' => $filters,
            'pagination' => $pagination,
            'order' => $order,
            'resultCount' => count($results)
        ])
    );

    echo json_encode(['success' => true, 'data' => $results]);
} catch (Exception $e) {
    //  Log de l'erreur
    if (isset($logger)) {
        $logger->log(
            'error',
            $e->getMessage(),
            basename(__FILE__),
            Logger::getUserIP(),
            null,
            null,
            json_encode($_GET)
        );
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
