<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Config;

use PHPUnit\Framework\TestCase;
use PDO;

final class DatabaseTest extends TestCase
{
    public function testDatabaseFileReturnsPdoInstance(): void
    {
        // Charge config/database.php, qui renvoie l'instance PDO
        $pdo = require __DIR__ . '/../../config/database.php';

        // 1) C’est bien un PDO
        $this->assertInstanceOf(PDO::class, $pdo);

        // 2) On peut exécuter une simple requête "SELECT 1"
        $stmt = $pdo->query('SELECT 1');

        // Cast en int et assertion stricte
        $this->assertSame(1, (int) $stmt->fetchColumn());
    }
}
