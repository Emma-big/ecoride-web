<?php
// src/config.php

// 1) Récupération de l’URL JAWSDB depuis l’environnement Heroku
$jawsdbUrl = getenv('JAWSDB_URL')
         ?: ($_SERVER['JAWSDB_URL'] ?? null)
         ?: ($_ENV['JAWSDB_URL']   ?? null);

// Fallback si nécessaire
if (!$jawsdbUrl && isset($_SERVER['JAWSDB_URL'])) {
    $jawsdbUrl = $_SERVER['JAWSDB_URL'];
}
if (!$jawsdbUrl && isset($_ENV['JAWSDB_URL'])) {
    $jawsdbUrl = $_ENV['JAWSDB_URL'];
}

// **DEBUG** : afficher JAWSDB_URL brut
file_put_contents('php://stderr', "[DB DEBUG] JAWSDB_URL={$jawsdbUrl}\n");

// 2) Parsing de l’URL ou fallback local
if ($jawsdbUrl) {
    $parts  = parse_url($jawsdbUrl);
    $dbHost = $parts['host']   ?? '127.0.0.1';
    $dbPort = $parts['port']   ?? 3306;
    $dbName = ltrim($parts['path'] ?? '', '/');
    $dbUser = $parts['user']   ?? '';
    $dbPass = $parts['pass']   ?? '';
} else {
    // Base locale
    $dbHost = '127.0.0.1';
    $dbPort = 3306;
    $dbName = 'ecoride';
    $dbUser = 'root';
    $dbPass = '';
}

// **DEBUG** : afficher les paramètres extraits
file_put_contents('php://stderr', sprintf(
    "[DB DEBUG] host=%s port=%d db=%s user=%s\n",
    $dbHost, $dbPort, $dbName, $dbUser
));

// 3) Autoload Composer
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// 4) Connexion PDO (forcée en TCP)
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $dbHost,
    $dbPort,
    $dbName
);

// **DEBUG** : afficher le DSN construit (sans mot de passe)
file_put_contents('php://stderr', "[DB DEBUG] DSN={$dsn}\n");

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
    // Remonter l’erreur pour afficher la 500 et le message
    throw $e;
}

// 5) Connexion MongoDB
$mongoUri    = getenv('MONGODB_URI')     ?: 'mongodb://localhost:27017';
$mongoDBName = getenv('MONGODB_DB_NAME') ?: 'avisDB';
try {
    $mongoClient = new MongoDB\Client($mongoUri);
    $mongoDB     = $mongoClient->selectDatabase($mongoDBName);
} catch (\Exception $e) {
    throw $e;
}

return $pdo;
