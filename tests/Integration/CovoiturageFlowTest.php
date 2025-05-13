<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
final class CovoiturageFlowTest extends TestCase
{
    protected function setUp(): void
    {
        // Simuler les variables d'environnement que index.php attend
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/covoiturageForm';  // ← ici
        $_SERVER['HTTP_HOST']      = 'localhost';
        $_GET = [];
        $_POST = [];

        // Intercepter les erreurs/exceptions pour ne pas interrompre le test
        set_error_handler(fn() => true);
        set_exception_handler(fn() => true);
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        restore_exception_handler();
    }

    public function testCreateCovoituragePageLoads(): void
    {
        ob_start();
        require __DIR__ . '/../../public/index.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('<form', $output);
        $this->assertStringContainsString('Création d\'un covoiturage', $output);
    }
}
