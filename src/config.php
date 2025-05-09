<?php
// src/config.php

// 1) Autoload + Dotenv (pour DB_* en local)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
}

// 2) Récupération JAWSDB_URL (Heroku)
$jawsdbUrl = getenv('JAWSDB_URL') ?: null;

// 3) Si JAWSDB_URL présente → on parse
if ($jawsdbUrl) {
    $parts  = parse_url($jawsdbUrl);
    $dbHost = $parts['host']   ?? '127.0.0.1';
    $dbPort = $parts['port']   ?? 3306;
    $dbName = ltrim($parts['path'] ?? '', '/');
    $dbUser = $parts['user']   ?? '';
    $dbPass = $parts['pass']   ?? '';
}
// 4) Sinon si on a DB_HOST dans l’environnement → on l’utilise
elseif (getenv('DB_HOST')) {
    $dbHost = getenv('DB_HOST');
    $dbPort = getenv('DB_PORT') ?: 3306;
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');
}
// 5) Sinon valeurs par défaut (localhost)
else {
    $dbHost = '127.0.0.1';
    $dbPort = 3306;
    $dbName = 'ecoride';
    $dbUser = 'root';
    $dbPass = '';
}

// 6) Création du DSN et connexion PDO
$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

try {
    return new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    // On renvoie une exception pour être affichée par le layout
    throw $e;
}
