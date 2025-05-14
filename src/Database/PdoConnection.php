<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Database;

use PDO;
use Adminlocal\EcoRide\Database\DatabaseConnectionInterface;

class PdoConnection implements DatabaseConnectionInterface
{
    private PDO $pdo;

    public function __construct()
    {
        // Récupère la config (SQLite en CLI, MySQL sinon)
        $this->pdo = require __DIR__ . '/../config.php';
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
