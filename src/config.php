<?php
// src/config.php

// 1) Démarrage de la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Configuration DB locale ou via JAWSDB_URL (Heroku)
$jawsdbUrl = getenv('JAWSDB_URL');
if ($jawsdbUrl) {
    $dbparts = parse_url($jawsdbUrl);
    $_ENV['DB_HOST'] = $dbparts['host']   ?? '';
    $_ENV['DB_NAME'] = ltrim($dbparts['path'] ?? '', '/');
    $_ENV['DB_USER'] = $dbparts['user']   ?? '';
    $_ENV['DB_PASS'] = $dbparts['pass']   ?? '';
    $_ENV['DB_PORT'] = $dbparts['port']   ?? 3306;
} else {
    $_ENV['DB_HOST'] = 'localhost';
    $_ENV['DB_NAME'] = 'ecoride';
    $_ENV['DB_USER'] = 'root';
    $_ENV['DB_PASS'] = '';
    $_ENV['DB_PORT'] = 3306;
}

// 3) Charger l’autoloader Composer (PDO, MongoDB, etc.)
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// 4) Connexion MySQL via PDO
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $_ENV['DB_HOST'],
    $_ENV['DB_PORT'],
    $_ENV['DB_NAME']
);
try {
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    // Votre handler global attrapera l’exception et renverra la 500
    throw $e;
}

// 5) Connexion MongoDB
$mongoUri    = getenv('MONGODB_URI')       ?: 'mongodb://localhost:27017';
$mongoDBName = getenv('MONGODB_DB_NAME')   ?: 'avisDB';
try {
    $mongoClient = new MongoDB\Client($mongoUri);
    $mongoDB     = $mongoClient->selectDatabase($mongoDBName);
} catch (\Exception $e) {
    // Transformer ça en exception non cachée pour renvoyer la 500
    throw $e;
}

// 6) Retourner l’objet PDO
return $pdo;
