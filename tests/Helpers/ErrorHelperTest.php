<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use function Adminlocal\EcoRide\Helpers\renderError;

final class ErrorHelperTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRenderErrorOutputsCorrectStatusCodeAndMessage(): void
    {
        ob_start();
        renderError(418);
        $output = ob_get_clean();

        $this->assertSame(418, http_response_code());
        $this->assertStringContainsString('418', $output);
        $this->assertStringContainsString('Teapot', $output);
    }
}
