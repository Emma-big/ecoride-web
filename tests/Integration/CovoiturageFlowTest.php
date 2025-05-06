<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Integration;

use PHPUnit\Framework\TestCase;

final class CovoiturageFlowTest extends TestCase
{
    protected function setUp(): void
    {
        // 1) Simule un environnement HTTP
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/createCovoiturage';
        $_GET = []; $_POST = [];
        
        // 2) Démarre la capture de la sortie et mets un error handler neutre
        ob_start();
        set_error_handler(function() { /* rien */ });
        set_exception_handler(function() { /* rien */ });
    }

    protected function tearDown(): void
    {
        // 3) Restaure tout proprement
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        restore_error_handler();
        restore_exception_handler();
    }

    public function testCreateCovoituragePageLoads(): void
    {
        // 4) Inclut le front-controller
        require __DIR__ . '/../../public/index.php';

        // 5) Récupère le contenu généré
        $output = ob_get_contents();

        // 6) Assertion minimale : on a bien une balise <form> de création
        $this->assertStringContainsString('<form', $output);
        $this->assertStringContainsString('Création d\'un covoiturage', $output);
    }
}
