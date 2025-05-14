<?php
declare(strict_types=1);

// 1) Mode TEST (forcé par bootstrap) → SQLite en mémoire
if (getenv('APP_ENV') === 'testing') {
    error_log('DEBUG: mode TEST, connexion SQLite en mémoire');
    return new PDO('sqlite::memory:', null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

// 2) Définir BASE_PATH
if (! defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// 3) Charger l’autoload Composer
require_once BASE_PATH . '/vendor/autoload.php';

// 4) Pour debug : on verra dans les logs quelle URL on utilise
$jawsUrl = getenv('JAWSDB_URL') ?: getenv('JAWSDB_MAUVE_URL');
error_log('ENV JAWSDB_URL_USED = ' . ($jawsUrl ?: 'none'));

// 5) Configuration MySQL (JAWSDB ou .env local)
if ($jawsUrl) {
    $url = parse_url($jawsUrl);
    $host   = $url['host']   ?? '127.0.0.1';
    $port   = $url['port']   ?? 3306;
    $dbname = isset($url['path']) ? ltrim($url['path'], '/') : 'ecoride';
    $user   = $url['user']   ?? '';
    $pass   = $url['pass']   ?? '';
} else {
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

// 6) Configuration MongoDB
$mongoUri    = getenv('MONGODB_URI')      ?: 'mongodb://localhost:27017';
$mongoDbName = getenv('MONGODB_DB_NAME') ?: 'avisDB';
error_log('DEBUG MongoDB URI utilisé : ' . $mongoUri);

try {
    $mongoClient = new MongoDB\Client($mongoUri);
    $mongoDB     = $mongoClient->selectDatabase($mongoDbName);
} catch (Exception $e) {
    error_log('Erreur MongoDB: ' . $e->getMessage());
    $mongoDB = null;
}

// 7) Clés reCAPTCHA uniquement si non définies
if (!defined('RECAPTCHA_SITE_KEY')) {
    define('RECAPTCHA_SITE_KEY',   getenv('RECAPTCHA_SITE_KEY')   ?: '');
}
if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', getenv('RECAPTCHA_SECRET_KEY') ?: '');
}

// 8) Retourne l’objet PDO MySQL (ou SQLite en test)
return $pdo;
