<?php
//declare(strict_types=1);

use App\Controller\HandlerController;
use App\Core\AuthManager;
use App\Core\RoleManager;
use App\Core\PermissionManager;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../sql.php';

// --- Initialisation ---

$dbh = $dbh ?? null; // Ton objet PDO venant de sql.php
if (!$dbh) {
    exit('Erreur : connexion DB manquante.');
}

// --- Authentification ---
AuthManager::checkAuth();

// --- Détermination du module et de la page ---
$module = basename(__DIR__); // Ex: "Sms" ou "Clinical"
$page = $_GET['page'] ?? '1';

// --- Contrôleur principal ---
$controller = new HandlerController($dbh);
$controller->handle($module, $page);