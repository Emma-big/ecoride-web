<?php
require_once __DIR__ . '/vendor/autoload.php';

use Adminlocal\EcoRide\Helpers\LoggerHelper;

// Simulation d'une erreur
LoggerHelper::error('Erreur de test capturée', ['code' => 500]);

// Simulation d'une information
LoggerHelper::info('Info de test capturée', ['action' => 'test_logger']);
