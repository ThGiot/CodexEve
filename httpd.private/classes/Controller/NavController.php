<?php
namespace App\Controller;

use App\Core\AuthManager;
use App\Core\RoleManager;
use PDO;

class NavController
{
    private PDO $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    public function render(string $module): void
    {
        AuthManager::checkAuth();

        $roleManager = new RoleManager($this->dbh);
        $role = $roleManager->getUserRole(
            AuthManager::getUserId(),
            $_SESSION['module_actif'],
            $_SESSION['client_actif']
        );

        if (!$role) {
            http_response_code(403);
            exit(json_encode(['error' => 'Rôle non trouvé']));
        }

        // Charge la configuration du module
        $configFile = PRIVATE_PATH . "/module/{$module}/NavConfig.php";

        if (!file_exists($configFile)) {
            http_response_code(404);
            exit(json_encode(['error' => "Fichier NavConfig introuvable pour le module {$module}"]));
        }

        $navConfig = include $configFile;

        // Optionnel : filtrer les pages selon le rôle
        $navData = $this->filterByRole($navConfig, $role);

        header('Content-Type: application/json');
        echo json_encode($navData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function filterByRole(array $navConfig, int $role): array
    {
        return array_map(function ($section) use ($role) {
            $pages = array_filter($section['pages'], fn($p) => !isset($p['roles']) || in_array($role, $p['roles']));
            return ['label' => $section['label'], 'pages' => array_values($pages)];
        }, $navConfig);
    }
}
