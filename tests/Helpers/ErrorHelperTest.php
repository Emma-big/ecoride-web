<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Helpers;

use PHPUnit\Framework\TestCase;

use function Helpers\renderError;

final class ErrorHelperTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRenderErrorOutputsCorrectStatusCodeAndMessage(): void
    {
        // On intercepte les headers et la sortie
        ob_start();
        renderError(418); // “I’m a teapot”
        $output = ob_get_clean();

        // Vérifie que le header HTTP a été envoyé
        $this->assertSame(418, http_response_code());

        // Doit contenir une balise <h1> avec le code
        $this->assertStringContainsString('418', $output);
        $this->assertStringContainsString('Teapot', $output);
    }
}
