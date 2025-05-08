<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
final class SearchFlowTest extends TestCase
{
    protected function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/covoiturage';   // ta route de recherche
        $_SERVER['HTTP_HOST']      = 'localhost';
        $_GET = []; $_POST = [];

        ob_start();
        set_error_handler(fn() => true);
        set_exception_handler(fn() => true);
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        restore_error_handler();
        restore_exception_handler();
    }

    public function testSearchPageLoads(): void
    {
        require __DIR__ . '/../../public/index.php';
        $html = ob_get_clean();

        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('Rechercher un covoiturage', $html);
    }
}
