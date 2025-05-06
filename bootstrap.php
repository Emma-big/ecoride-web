<?php
// bootstrap.php

// 1) Autoload Composer
require __DIR__ . '/vendor/autoload.php';

// 2) Charger .env si présent (pour les variables d’environnement)
if (file_exists(__DIR__ . '/.env')) {
    \Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();
}
