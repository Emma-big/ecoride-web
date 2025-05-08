<?php
// src/config.php

// 1) Démarrage de la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Détection Heroku dyno
$isHeroku = getenv('DYNO') !== false;

// 3) Récupération des credentials MySQL
$jawsdbUrl = getenv('JAWSDB_URL') ?: '';
if ($jawsdbUrl) {
    $dbparts = parse_url($jawsdbUrl);
    $dbHost = $dbparts['host']   ?? '';
    $dbPort = $dbparts['port']   ?? 3306;
    $dbName = ltrim($dbparts['path'] ?? '', '/');
    $dbUser = $dbparts['user']   ?? '';
    $dbPass = $dbparts['pass']   ?? '';
} else {
    // Si on est en local : valeurs par défaut
    $dbHost = '127.0.0.1';
    $dbPort = 3306;
    $dbName = 'ecoride';
    $dbUser = 'root';
    $dbPass = '';
}

// 4) Connexion MySQL via PDO (uniquement si on a un host et que, en prod Heroku, JAWSDB est configuré)
$pdo = null;
if ($dbHost && (!$isHeroku || $jawsdbUrl)) {
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
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (\PDOException $e) {
        // En debug on relance, sinon on ignore et $pdo reste null
        if (getenv('APP_DEBUG')) {
            throw $e;
        }
    }
}

// 5) Connexion MongoDB (uniquement si MONGODB_URI fourni)
$mongoDB = null;
$mongoUri    = getenv('MONGODB_URI')     ?: '';
$mongoDBName = getenv('MONGODB_DB_NAME') ?: '';
if ($mongoUri && class_exists(\MongoDB\Client::class)) {
    try {
        $mongoClient = new MongoDB\Client($mongoUri);
        $mongoDB     = $mongoClient->selectDatabase($mongoDBName);
    } catch (\Exception $e) {
        if (getenv('APP_DEBUG')) {
            throw $e;
        }
    }
}

// 6) On retourne un tableau contenant nos connexions
return [
    'pdo'     => $pdo,
    'mongoDB' => $mongoDB,
];
