<?php
// src/config.php

if (! defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Pour debug : on verra dans les logs quelle URL on utilise
$jawsUrl = getenv('JAWSDB_URL') ?: getenv('JAWSDB_MAUVE_URL');
error_log('ENV JAWSDB_URL_USED = ' . ($jawsUrl ?: 'none'));

// CONFIGURATION MySQL
if ($jawsUrl) {
    $url = parse_url($jawsUrl);
    $host   = $url['host']   ?? '127.0.0.1';
    $port   = $url['port']   ?? 3306;
    $dbname = isset($url['path']) ? ltrim($url['path'], '/') : 'ecoride';
    $user   = $url['user']   ?? '';
    $pass   = $url['pass']   ?? '';
} else {
    // Fallback local / dev
    $host   = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port   = $_ENV['DB_PORT'] ?? 3306;
    $dbname = $_ENV['DB_NAME'] ?? 'ecoride';
    $user   = $_ENV['DB_USER'] ?? 'root';
    $pass   = $_ENV['DB_PASS'] ?? '';
}

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
error_log("DEBUG PDO DSN utilisé : $dsn");

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log('Erreur PDO: ' . $e->getMessage());
    throw $e;
}

// CONFIGURATION MongoDB
// Lire l'URI et le nom de base depuis les vars d'env
$mongoUri = getenv('MONGODB_URI') ?: 'mongodb://localhost:27017';
$mongoDbName = getenv('MONGODB_DB_NAME') ?: 'avisDB';
error_log('DEBUG MongoDB URI utilisé : ' . $mongoUri);

try {
    // Chargement de l'autoloader Composer pour MongoDB\Client
    require_once BASE_PATH . '/vendor/autoload.php';
    $mongoClient = new MongoDB\Client($mongoUri);
    $mongoDB     = $mongoClient->selectDatabase($mongoDbName);
} catch (Exception $e) {
    error_log('Erreur MongoDB: ' . $e->getMessage());
    // On ne lève pas d'exception pour ne pas bloquer MySQL si Mongo est indisponible
    $mongoDB = null;
}

// Retourne l'objet PDO pour MySQL. MongoDB est disponible via \$mongoDB global.
return $pdo;
