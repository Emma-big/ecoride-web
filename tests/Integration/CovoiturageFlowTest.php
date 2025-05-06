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
        $_SERVER['REQUEST_URI']    = '/covoiturage/create';  // <-- ajuste selon ta route réelle
        $_SERVER['HTTP_HOST']      = 'localhost';
        $_GET = [];
        $_POST = [];

        // Intercepter les erreurs/exceptions pour ne pas interrompre le test
        set_error_handler(fn() => true);
        set_exception_handler(fn() => true);
    }

    protected function tearDown(): void
    {
        // Restaurer l’environnement
        restore_error_handler();
        restore_exception_handler();
    }

    public function testCreateCovoituragePageLoads(): void
    {
        // Démarrer la capture de la sortie
        ob_start();

        // Charger le front-controller
        require __DIR__ . '/../../public/index.php';

        // Récupérer le HTML rendu
        $output = ob_get_clean();

        // Vérifications basiques
        $this->assertStringContainsString('<form', $output);
        $this->assertStringContainsString('Création d\'un covoiturage', $output);
    }
}
