<?php
// bootstrap.php

// 1) Autoload Composer
require __DIR__ . '/vendor/autoload.php';

// 2) Charger .env si présent
if (file_exists(__DIR__ . '/.env')) {
    \Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();
}

// IMPORTANT : on **n’inclut pas** public/index.php ici.
