<?php
// src/config.php

// 1) Démarrage de la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Récupération des infos de connexion à MySQL (JAWSDB ou local)
$jawsdbUrl = getenv('JAWSDB_URL');
if ($jawsdbUrl) {
    $dbparts = parse_url($jawsdbUrl);
    $dbHost = $dbparts['host']   ?? 'localhost';
    $dbName = ltrim($dbparts['path'] ?? '', '/') ?: 'ecoride';
    $dbUser = $dbparts['user']   ?? 'root';
    $dbPass = $dbparts['pass']   ?? '';
    $dbPort = $dbparts['port']   ?? 3306;
} else {
    $dbHost = 'localhost';
    $dbName = 'ecoride';
    $dbUser = 'root';
    $dbPass = '';
    $dbPort = 3306;
}

// 3) Charger l’autoloader Composer (PDO, MongoDB…)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// 4) Connexion MySQL via PDO
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $dbHost,
    $dbPort,
    $dbName
);

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    // Laisse tomber vers le handler global → 500
    throw $e;
}

// 5) Connexion MongoDB (idem, via MONGODB_URI ou local)
$mongoUri    = getenv('MONGODB_URI')     ?: 'mongodb://localhost:27017';
$mongoDBName = getenv('MONGODB_DB_NAME') ?: 'avisDB';

try {
    $mongoClient = new MongoDB\Client($mongoUri);
    $mongoDB     = $mongoClient->selectDatabase($mongoDBName);
} catch (\Exception $e) {
    throw $e;
}

// 6) On renvoie l’objet PDO pour l’usage dans l’app
return $pdo;
