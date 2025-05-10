<?php
// public/utilisateur.php — Front Controller pour la page « Mon espace utilisateur »

// 1) Définir BASE_PATH
if (! defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// 2) Lancer la session (nécessaire avant tout appel à $_SESSION)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 3) Charger la config PDO
try {
    /** @var \PDO $pdo */
    $pdo = require BASE_PATH . '/src/config.php';
} catch (\Throwable $e) {
    echo '<h1>Erreur de connexion à la base de données</h1>';
    echo '<pre>'. htmlspecialchars($e->getMessage()) .'</pre>';
    exit;
}

// 4) Inactivité
$inactive_duration = 600;
if (
    isset($_SESSION['last_activity'])
    && (time() - $_SESSION['last_activity'] > $inactive_duration)
) {
    session_unset();
    session_destroy();
    header('Location: /inactivite');
    exit;
}
$_SESSION['last_activity'] = time();

// 5) Authentification
if (empty($_SESSION['user'])) {
    header('Location: /accessDenied');
    exit;
}

// 6) Rôles et ID
$isChauffeur = !empty($_SESSION['user']['is_chauffeur']);
$isPassager  = !empty($_SESSION['user']['is_passager']);
$uid         = (int) $_SESSION['user']['utilisateur_id'];

// 7) Variables pour le layout
$pageTitle   = 'Mon espace utilisateur - EcoRide';
$extraStyles = [
    '/assets/style/styleIndex.css',
    '/assets/style/styleAdmin.css'
];
// on masque le bigTitle global
$hideTitle   = true;

// 8) Contenu principal
ob_start(); 
?>
<main class="container mt-4">
    <?php require BASE_PATH . '/src/views/bigTitle.php'; ?>

    <?php require BASE_PATH . '/src/controllers/principal/mesinfos.php'; ?>

    <!-- Choix de rôle -->
    <form action="/updateRolePost" method="POST" class="mb-5">
        <label class="me-3">
            <input type="checkbox" name="role_chauffeur" value="1"
                <?= $isChauffeur ? 'checked' : '' ?>>
            Chauffeur
        </label>
        <label class="me-3">
            <input type="checkbox" name="role_passager"  value="1"
                <?= $isPassager  ? 'checked' : '' ?>>
            Passager
        </label>
        <button type="submit" class="btn btn-secondary btn-sm">Mettre à jour</button>
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES) ?>">
    </form>

    <h2>Mes voitures</h2>
    <?php if ($isChauffeur): ?>
        <?php require BASE_PATH . '/src/controllers/principal/mesvoitures.php'; ?>
    <?php else: ?>
        <p class="text-muted">Vous devez être chauffeur pour gérer vos voitures.</p>
    <?php endif; ?>

    <h2>Mes covoiturages (Chauffeur)</h2>
    <?php if ($isChauffeur): ?>
        <div class="text-center my-4">
            <a href="/covoiturageForm" class="btn btn-primary">Créer un covoiturage</a>
        </div>
        <?php require BASE_PATH . '/src/controllers/principal/mescovoituragesChauffeur.php'; ?>
    <?php else: ?>
        <p class="text-muted">Vous devez être chauffeur pour gérer vos trajets.</p>
    <?php endif; ?>

    <h2>Mes covoiturages (Passager)</h2>
    <?php if ($isPassager): ?>
        <?php require BASE_PATH . '/src/controllers/principal/mescovoituragesPassager.php'; ?>
        <?php require BASE_PATH . '/src/controllers/principal/validezVosTrajets.php'; ?>
    <?php else: ?>
        <p class="text-muted">Vous devez être passager pour voir vos réservations.</p>
    <?php endif; ?>
</main>
<?php
$mainContent = ob_get_clean();

// 9) Affichage via le layout
require BASE_PATH . '/src/layout.php';
exit;
