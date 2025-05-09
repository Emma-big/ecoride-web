<?php
// Affichage des erreurs si APP_DEBUG=true
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1) Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Définir BASE_PATH comme la racine du projet
if (! defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/..'));
}

// 3) Charger le helper d’erreur
require_once BASE_PATH . '/src/Helpers/ErrorHelper.php';
use function Helpers\renderError;

// Gestionnaire des exceptions non capturées → page 500
//set_exception_handler(function(\Throwable $e) {
 //   renderError(500);
//});

// Gestionnaire des erreurs PHP → transforme en Exception
//set_error_handler(function($severity, $message, $file, $line) {
//    throw new \ErrorException($message, 0, $severity, $file, $line);
//});

// 3.1) Protection CSRF pour les POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || ! hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        renderError(403);
    }
}
// 3.2) Générer un token si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3.3) Autoload Composer + Dotenv
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
    Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();
}

// 4) Connexions PDO & MongoDB
try {
    $pdo = require BASE_PATH . '/src/config.php';
    $mongoClient = new MongoDB\Client(getenv('MONGODB_URI') ?: 'mongodb://localhost:27017');
    $mongoDB     = $mongoClient->selectDatabase(getenv('MONGODB_DB_NAME') ?: 'avisDB');
} catch (\Throwable $e) {
    echo '<h1>Erreur de connexion à la BDD</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    exit;
}


// 5) Gestion de l'inactivité
$inactive_duration = 600;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive_duration)) {
    session_unset();
    session_destroy();
    header('Location: /inactivite');
    exit;
}
$_SESSION['last_activity'] = time();

// === ROUTEUR SIMPLIFIÉ ===
$uri = strtok($_SERVER['REQUEST_URI'], '?');

// Log de route (optionnel)
file_put_contents(__DIR__.'/../logs/route.log', date('c').' → '.$_SERVER['HTTP_HOST'].' '.$_SERVER['REQUEST_URI'].PHP_EOL, FILE_APPEND);

switch ($uri) {
    // ... (tout ton switch reste inchangé)
}

// 6) Affichage du layout global
require_once BASE_PATH . '/src/layout.php';
exit;
