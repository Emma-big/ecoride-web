<?php
// ./tests/bootstrap.php

// 1) Charge l’autoloader Composer
require __DIR__ . '/../vendor/autoload.php';

// 2) Définis la constante BASE_PATH si servi dans les tests
defined('BASE_PATH') || define('BASE_PATH', realpath(__DIR__ . '/../'));
