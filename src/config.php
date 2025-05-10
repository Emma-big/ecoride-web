<?php
// src/config.php

// 1) Autoload + Dotenv (pour les DB_* en local)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
}

// 2) JAWSDB_URL (Heroku)
$jawsdbUrl = getenv('JAWSDB_URL') ?: null;

// DEBUG – vérifier que la variable est lue
error_log('DEBUG JAWSDB_URL='.getenv('JAWSDB_URL'));

// 3) Si JAWSDB_URL existe → on parse
if ($jawsdbUrl) {
    $parts  = parse_url($jawsdbUrl);
    $dbHost = $parts['host']   ?? '127.0.0.1';
    $dbPort = $parts['port']   ?? 3306;
    $dbName = ltrim($parts['path'] ?? '', '/');
    $dbUser = $parts['user']   ?? '';
    $dbPass = $parts['pass']   ?? '';
}
// 4) Sinon si on a DB_HOST dans l’environnement → fallback sur .env ou vars Heroku
elseif (getenv('DB_HOST')) {
    $dbHost = getenv('DB_HOST');
    $dbPort = getenv('DB_PORT') ?: 3306;
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');
}
// 5) Enfin, valeurs par défaut (localhost XAMPP)
else {
    $dbHost = '127.0.0.1';
    $dbPort = 3306;
    $dbName = 'ecoride';
    $dbUser = 'root';
    $dbPass = '';
}

$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $dbHost, $dbPort, $dbName
);

// === DÉBUG ===
error_log('DEBUG PDO DSN='.$dsn);
error_log('DEBUG PDO USER='.$dbUser);

try {
    return new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    throw $e;
}