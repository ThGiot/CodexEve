<?php
namespace App\Controller;

use App\Core\Controller;
use App\Core\AuthManager;
use App\Core\RoleManager;
use Module\Clinical\Policy\ModulePolicy; // adapte selon le module

class HandlerController extends Controller
{
    public function handle(string $module, string $page): void
    {
        AuthManager::checkAuth();

        $roleManager = new RoleManager($this->dbh);
        $role = $roleManager->getUserRole(
            AuthManager::getUserId(),
            $_SESSION['module_actif'],
            $_SESSION['client_actif']
        );

        if (!ModulePolicy::canAccess((int)$role, $page)) {
            http_response_code(403);
            echo "Accès refusé.";
            return;
        }

        // 🔍 Trouve le nom de la vue correspondant à la page
        $viewFile = $this->resolveViewName($page, $module);

        // 🧩 Nouveau chemin vers /View/
        $viewPath = PRIVATE_PATH . "/module/{$module}/View/{$viewFile}";
        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo "Vue introuvable : {$viewFile} IN {$module}";
            return;
        }

        $this->render($viewPath);
    }

    private function resolveViewName(string $page, string $module): string
    {
        $configPath = PRIVATE_PATH . "/module/{$module}/NavConfig.php";

        if (!file_exists($configPath)) {
            throw new \RuntimeException("NavConfig.php introuvable pour le module {$module}");
        }

        $config = include $configPath;

        foreach ($config as $section) {
            foreach ($section['pages'] as $p) {
                if ((string)$p['link'] === $page && isset($p['view'])) {
                    return $p['view'];
                }
            }
        }

        return 'default.php';
    }
}
