<?php
// src/config.php

// 1) Récupération de l’URL JAWSDB depuis l’environnement Heroku
$jawsdbUrl = getenv('JAWSDB_URL') 
         ?: ($_SERVER['JAWSDB_URL'] ?? null) 
         ?: ($_ENV['JAWSDB_URL']   ?? null);

if (! $jawsdbUrl) {
    throw new \Exception("JAWSDB_URL introuvable – vérifie tes config vars sur Heroku.");
}

// 2) Parsing de l’URL
$parts  = parse_url($jawsdbUrl);
$dbHost = $parts['host']   ?? '127.0.0.1';
$dbPort = $parts['port']   ?? 3306;
$dbName = ltrim($parts['path'] ?? '', '/');
$dbUser = $parts['user']   ?? '';
$dbPass = $parts['pass']   ?? '';

// 3) Autoload Composer
require_once __DIR__ . '/../vendor/autoload.php';

// 4) Construction du DSN TCP
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $dbHost,
    $dbPort,
    $dbName
);

// 5) Connexion PDO
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    // On laisse remonter pour que le handler global affiche la 500
    throw $e;
}

// 6) Connexion MongoDB (inchangé)
$mongoUri    = getenv('MONGODB_URI')     ?: 'mongodb://localhost:27017';
$mongoDBName = getenv('MONGODB_DB_NAME') ?: 'avisDB';
try {
    $mongoClient = new MongoDB\Client($mongoUri);
    $mongoDB     = $mongoClient->selectDatabase($mongoDBName);
} catch (\Exception $e) {
    throw $e;
}

return $pdo;
