<?php
// tests/Controllers/RegisterPostTest.php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Controllers;

use PHPUnit\Framework\TestCase;

final class RegisterPostTest extends TestCase
{
    protected function setUp(): void
    {
        // Simulation minimale
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'pseudo'   => 'foo',
            'password' => 'bar',
            // ...
        ];
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Détourne la sortie
        ob_start();
    }

    protected function tearDown(): void
    {
        ob_end_clean();
        session_unset();
        session_destroy();
        $_POST = [];
    }

    public function testRegisterPostCreatesUserAndRedirects(): void
    {
        // Configure un PDO sqlite in-memory et injecte-le
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE utilisateurs (utilisateur_id INTEGER PRIMARY KEY, pseudo TEXT, password TEXT)');
        // On fait en sorte que notre controller utilise ce $pdo
        // Par exemple en mockant src/config.php pour retourner $pdo…
        // (ou extraire la création de PDO dans une fabrique injectée)

        require __DIR__ . '/../../src/controllers/post/registerPost.php';

        // Vérifier qu'une ligne a été insérée
        $count = $pdo->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
        $this->assertSame('1', $count);

        // Vérifier la redirection
        $headers = headers_list();
        $this->assertStringContainsString('Location: /confirmation', implode("\n", $headers));
    }
}
