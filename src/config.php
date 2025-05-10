<?php
// src/config.php — Initialise et retourne l’instance PDO

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Si Heroku fournit JAWSDB_URL…
if (getenv('JAWSDB_URL')) {
    $url = parse_url(getenv('JAWSDB_URL'));
    $host   = $url['host']   ?? 'localhost';
    $port   = $url['port']   ?? 3306;
    $dbname = ltrim($url['path'], '/') ?: 'ecoride';
    $user   = $url['user']   ?? '';
    $pass   = $url['pass']   ?? '';
} else {
    // Sinon on tombe sur le local / .env
    $host   = $_ENV['DB_HOST'] ?? 'localhost';
    $port   = $_ENV['DB_PORT'] ?? 3306;
    $dbname = $_ENV['DB_NAME'] ?? 'ecoride';
    $user   = $_ENV['DB_USER'] ?? 'root';
    $pass   = $_ENV['DB_PASS'] ?? '';
}

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    error_log("DEBUG PDO DSN utilisé : $dsn");
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log('❌ Erreur PDO: ' . $e->getMessage());
    throw $e;
}

return $pdo;
