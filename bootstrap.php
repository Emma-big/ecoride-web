<?php
// bootstrap.php

// 1) Chargement de l’autoloader Composer
require __DIR__ . '/vendor/autoload.php';

// 2) Chargement du fichier .env si présent
if (file_exists(__DIR__ . '/.env')) {
    \Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();
}
