<?php
// src/config.php

// 1) Récupération de l’URL JAWSDB depuis l’environnement (Heroku ou .env)
$jawsdbUrl = getenv('JAWSDB_URL')
         ?: ($_SERVER['JAWSDB_URL'] ?? null)
         ?: ($_ENV['JAWSDB_URL']   ?? null);

// 2) Parsing de l’URL ou fallback sur DB_* ou fallback dur-codé
if ($jawsdbUrl) {
    $parts  = parse_url($jawsdbUrl);
    $dbHost = $parts['host']   ?? '127.0.0.1';
    $dbPort = $parts['port']   ?? 3306;
    $dbName = ltrim($parts['path'] ?? '', '/');
    $dbUser = $parts['user']   ?? '';
    $dbPass = $parts['pass']   ?? '';
}
elseif (getenv('DB_HOST')) {
    // Si tu as défini DB_HOST, DB_NAME, etc. dans ton .env
    $dbHost = getenv('DB_HOST');
    $dbPort = getenv('DB_PORT') ?: 3306;
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');
}
else {
    // Fallback local  
    $dbHost = '127.0.0.1';
    $dbPort = 3306;
    $dbName = 'ecoride';
    $dbUser = 'root';
    $dbPass = '';
}

// 3) Autoload Composer + Dotenv
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    // Charge ton .env en local si présent
    if (class_exists(\Dotenv\Dotenv::class)) {
        \Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
    }
}

// 4) Connexion PDO (forcée en TCP)
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $dbHost,
    $dbPort,
    $dbName
);

try {
    $pdo = new PDO(
        $dsn,
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (\PDOException $e) {
    // En cas d’erreur, on la remonte
    throw $e;
}

// 5) Connexion MongoDB (inchangé)
$mongoUri    = getenv('MONGODB_URI')     ?: 'mongodb://localhost:27017';
$mongoDBName = getenv('MONGODB_DB_NAME') ?: 'avisDB';
try {
    $mongoClient = new MongoDB\Client($mongoUri);
    $mongoDB     = $mongoClient->selectDatabase($mongoDBName);
} catch (\Exception $e) {
    throw $e;
}

return $pdo;
