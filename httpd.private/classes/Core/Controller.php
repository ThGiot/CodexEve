<?php
namespace App\Core;

use PDO;

abstract class Controller
{
    protected PDO $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    protected function render(string $viewPath, array $data = []): void
    {
        extract($data);
        require $viewPath;
    }
}
