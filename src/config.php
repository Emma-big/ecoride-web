<?php
// src/config.php

// 1) Récupération de l’URL JAWSDB
$jawsdbUrl = getenv('JAWSDB_URL') ?: ($_ENV['JAWSDB_URL'] ?? null);

// **DEBUG TEMPORAIRE**
file_put_contents('php://stderr', "[DEBUG] JAWSDB_URL={$jawsdbUrl}\n");

// 2) Parsing
if ($jawsdbUrl) {
    $parts  = parse_url($jawsdbUrl);
    $dbHost = $parts['host']   ?? 'localhost';
    $dbPort = $parts['port']   ?? 3306;
    $dbName = ltrim($parts['path'] ?? '', '/');
    $dbUser = $parts['user']   ?? '';
    $dbPass = $parts['pass']   ?? '';
} else {
    $dbHost = 'localhost';
    $dbPort = 3306;
    $dbName = 'ecoride';
    $dbUser = 'root';
    $dbPass = '';
}

// **DEBUG TEMPORAIRE**
file_put_contents('php://stderr', "[DEBUG] DB_HOST={$dbHost}; DB_PORT={$dbPort}; DB_NAME={$dbName}; DB_USER={$dbUser}\n");

// 3) Autoload
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// 4) Connexion PDO
$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    throw $e;
}

return $pdo;
