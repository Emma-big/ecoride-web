<?php
// src/config.php

// 1) Récupérer l’URL JAWSDB (Heroku) via getenv()
$jawsdbUrl = getenv('JAWSDB_URL');

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

// 2) Autoloader Composer
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// 3) Connexion PDO (forcée en TCP grâce au port)
$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $dbHost, $dbPort, $dbName
);
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    // on laisse remonter pour le handler global
    throw $e;
}

// 4) Connexion MongoDB (inchangée)
$mongoUri    = getenv('MONGODB_URI')     ?: 'mongodb://localhost:27017';
$mongoDBName = getenv('MONGODB_DB_NAME') ?: 'avisDB';
try {
    $mongoClient = new MongoDB\Client($mongoUri);
    $mongoDB     = $mongoClient->selectDatabase($mongoDBName);
} catch (\Exception $e) {
    throw $e;
}

// 5) Retourner le PDO
return $pdo;
