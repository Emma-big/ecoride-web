<?php
// public/utilisateur.php — Mon espace utilisateur

// 1) Afficher les erreurs en debug
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

// 2) BASE_PATH
if (! defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// 3) Composer + Dotenv
require_once BASE_PATH . '/vendor/autoload.php';
Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();

// 4) Charger le PDO (via src/config.php)
try {
    /** @var \PDO $pdo */
    $pdo = require BASE_PATH . '/src/config.php';
} catch (\Throwable $e) {
    echo 'Erreur de connexion à la base de données : '
       . htmlspecialchars($e->getMessage());
    exit;
}

// 5) Gestion de l’inactivité
if (isset($_SESSION['last_activity'])
    && time() - $_SESSION['last_activity'] > 600
) {
    session_unset();
    session_destroy();
    header('Location: /inactivite');
    exit;
}
$_SESSION['last_activity'] = time();

// 6) Authentification
if (empty($_SESSION['user']['utilisateur_id'])) {
    header('Location: /accessDenied');
    exit;
}
$uid         = (int) $_SESSION['user']['utilisateur_id'];
$isChauffeur = ! empty($_SESSION['user']['is_chauffeur']);
$isPassager  = ! empty($_SESSION['user']['is_passager']);

// ──────────────────────────────────────────────────────────────────────────────
// 7) Si aucun rôle n’est coché, on affiche **seulement** le formulaire de choix:
// ──────────────────────────────────────────────────────────────────────────────
if (! $isChauffeur && ! $isPassager) {
    // Masquer le bigTitle global
    $hideTitle   = true;
    // Titre & styles
    $pageTitle   = 'Choisissez votre rôle – EcoRide';
    $extraStyles = ['/assets/style/styleIndex.css','/assets/style/styleAdmin.css'];

    ob_start();
    ?>
    <main class="container mt-5">
      <h2 class="text-center mb-4">Choisissez votre rôle</h2>
      <form action="/updateRolePost" method="POST" class="text-center">
        <label class="me-3">
          <input type="checkbox" name="role_chauffeur" value="1"> Chauffeur
        </label>
        <label class="me-3">
          <input type="checkbox" name="role_passager"  value="1"> Passager
        </label>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
      </form>
    </main>
    <?php
    $mainContent = ob_get_clean();
    require BASE_PATH . '/src/layout.php';
    exit;
}
// ──────────────────────────────────────────────────────────────────────────────

// 8) Charger les infos de l’utilisateur
try {
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE utilisateur_id = :id');
    $stmt->execute([':id' => $uid]);
    $user = $stmt->fetch();
    if (! $user) {
        throw new \Exception("Utilisateur introuvable.");
    }
} catch (\Throwable $e) {
    echo 'Erreur de connexion à la base de données : '
       . htmlspecialchars($e->getMessage());
    exit;
}

// 9) Config du layout
$pageTitle   = 'Mon espace utilisateur - EcoRide';
$extraStyles = ['/assets/style/styleIndex.css','/assets/style/styleAdmin.css'];
$hideTitle   = true; // on ne réaffiche pas le bigTitle global sur cette page

// 10) Contenu
ob_start();
require BASE_PATH . '/src/controllers/principal/mesinfos.php';

if ($isChauffeur) {
    require BASE_PATH . '/src/controllers/principal/mesvoitures.php';
    require BASE_PATH . '/src/controllers/principal/mescovoituragesChauffeur.php';
}
if ($isPassager) {
    require BASE_PATH . '/src/controllers/principal/mescovoituragesPassager.php';
    require BASE_PATH . '/src/controllers/principal/validezVosTrajets.php';
}

$mainContent = ob_get_clean();

// 11) Affichage via le layout
require BASE_PATH . '/src/layout.php';
exit;
