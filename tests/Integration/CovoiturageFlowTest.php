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
        $_SERVER['REQUEST_URI']    = '/createCovoiturage';
        $_SERVER['HTTP_HOST']      = 'localhost';
        $_GET = []; $_POST = [];

        // Démarre la capture de la sortie
        ob_start();
        // Remplace error/exception handlers avec des no-ops
        set_error_handler(fn() => true);
        set_exception_handler(fn() => true);
    }

    protected function tearDown(): void
    {
        // Nettoie tous les buffers ouverts
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        restore_error_handler();
        restore_exception_handler();
    }

    public function testCreateCovoituragePageLoads(): void
    {
        require __DIR__ . '/../../public/index.php';

        $output = ob_get_clean(); // on récupère et ferme le buffer

        $this->assertStringContainsString('<form', $output);
        $this->assertStringContainsString('Création d\'un covoiturage', $output);
    }
}
