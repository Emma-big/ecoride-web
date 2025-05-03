<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Adminlocal\EcoRide\Helpers\LoggerHelper;

// Gestion automatique des erreurs fatales
set_exception_handler(function (Throwable $e) {
    LoggerHelper::error('Exception non capturée', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);

    // Tu peux aussi afficher un message simple si besoin
    echo "Une erreur est survenue. Consultez les logs.";
});
