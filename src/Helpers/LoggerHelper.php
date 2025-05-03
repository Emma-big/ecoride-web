<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerHelper
{
    private static ?Logger $logger = null;

    public static function getLogger(): Logger
    {
        if (self::$logger === null) {
            self::$logger = new Logger('EcoRide');
            self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG));
        }

        return self::$logger;
    }

    public static function error(string $message, array $context = []): void
    {
        self::getLogger()->error($message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::getLogger()->info($message, $context);
    }
}
