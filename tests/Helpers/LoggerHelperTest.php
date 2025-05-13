<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Helpers;

use Adminlocal\EcoRide\Helpers\LoggerHelper;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;

final class LoggerHelperTest extends TestCase
{
    public function testGetLoggerReturnsSingletonAndHasCorrectHandler(): void
    {
        $logger1 = LoggerHelper::getLogger();
        $logger2 = LoggerHelper::getLogger();

        // même instance
        $this->assertSame($logger1, $logger2);

        // nom du logger
        $this->assertInstanceOf(Logger::class, $logger1);
        $this->assertSame('EcoRide', $logger1->getName());

        // au moins un handler, et c'est bien un StreamHandler
        $handlers = $logger1->getHandlers();
        $this->assertNotEmpty($handlers);
        $this->assertInstanceOf(StreamHandler::class, $handlers[0]);
    }

    public function testErrorAndInfoMethodsDoNotThrow(): void
    {
        // On vérifie simplement que ces appels n'émettent pas d'exception
        LoggerHelper::info('Message info de test', ['foo' => 'bar']);
        LoggerHelper::error('Message erreur de test', ['baz' => 'qux']);

        $this->assertTrue(true);
    }
}
