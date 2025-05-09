<?php
// src/config.php

// 1) Récupération de l’URL JAWSDB
$jawsdbUrl = getenv('JAWSDB_URL') ?: ($_ENV['JAWSDB_URL'] ?? null);

// 2) Parsing
if ($jawsdbUrl) {
    $parts  = parse_url($jawsdbUrl);
    $dbHost = $parts['host']   ?? 'localhost';
    $dbPort = $parts['port']   ?? 3306;
    $dbName = ltrim($parts['path'] ?? '', '/');
    $dbUser = $parts['user']   ?? '';
    $dbPass = $parts['pass']   ?? '';
} else {
    // fallback local
    $dbHost = 'localhost';
    $dbPort = 3306;
    $dbName = 'ecoride';
    $dbUser = 'root';
    $dbPass = '';
}

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
