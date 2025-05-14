<?php
declare(strict_types=1);

// ** Forcer le mode TEST pour tous les scripts lancés par PHPUnit **
putenv('APP_ENV=testing');
$_ENV['APP_ENV'] = 'testing';

if (! defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

// Charger ErrorHelper pour que renderError() existe
require_once BASE_PATH . '/src/Helpers/ErrorHelper.php';

require BASE_PATH . '/vendor/autoload.php';
if (file_exists(BASE_PATH . '/.env')) {
    \Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();
}
