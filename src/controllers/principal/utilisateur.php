<?php
// src/controllers/principal/utilisateur.php

// 1) On assume que public/index.php a déjà fait session_start(), dotenv, etc.
//    et qu’il a défini BASE_PATH et inclus Composer.

// 2) On charge le PDO via src/config.php
try {
    /** @var \PDO $pdo */
    $pdo = require BASE_PATH . '/src/config.php';
} catch (\Throwable $e) {
    // Si ça plante ici, c’est que ta config PDO est mauvaise
    echo '<h1>Erreur de connexion à la base de données :</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

// 3) Vérifier que l’utilisateur est authentifié
if (empty($_SESSION['user']['utilisateur_id'])) {
    header('Location: /accessDenied');
    exit;
}
$uid         = (int) $_SESSION['user']['utilisateur_id'];
$isChauffeur = ! empty($_SESSION['user']['is_chauffeur']);
$isPassager  = ! empty($_SESSION['user']['is_passager']);

// 4) Récupérer les données de l’utilisateur
try {
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE utilisateur_id = :id');
    $stmt->execute([':id' => $uid]);
    $user = $stmt->fetch();
    if (! $user) {
        throw new \Exception("Utilisateur introuvable.");
    }
} catch (\Throwable $e) {
    echo '<h1>Erreur lors du chargement des informations :</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

// 5) Préparer les variables pour le layout
$pageTitle   = 'Mon espace utilisateur - EcoRide';
$extraStyles = [
    '/assets/style/styleIndex.css',
    '/assets/style/styleAdmin.css'
];
// On ne réaffiche pas le bigTitle ici car le layout l’inclut

// 6) Capturer le contenu principal
ob_start();
?>
<main class="container mt-4">
    <?php require BASE_PATH . '/src/views/bigTitle.php'; ?>
    <?php require BASE_PATH . '/src/controllers/principal/mesinfos.php'; ?>

    <form action="/updateRolePost" method="POST" class="mb-5">
        <label>
            <input type="checkbox" name="role_chauffeur" value="1"
                <?= $isChauffeur ? 'checked' : '' ?>> Chauffeur
        </label>
        <label class="ms-3">
            <input type="checkbox" name="role_passager" value="1"
                <?= $isPassager ? 'checked' : '' ?>> Passager
        </label>
        <button type="submit" class="btn btn-secondary btn-sm ms-3">
            Mettre à jour
        </button>
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
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
            <a href="/covoiturageForm" class="btn btn-primary">
                Créer un covoiturage
            </a>
        </div>
        <?php require BASE_PATH . '/src/controllers/principal/mescovoituragesChauffeur.php'; ?>
    <?php else: ?>
        <p class="text-muted">Vous devez être chauffeur pour gérer vos covoiturages.</p>
    <?php endif; ?>

    <h2>Mes covoiturages (Passager)</h2>
    <?php if ($isPassager): ?>
        <?php require BASE_PATH . '/src/controllers/principal/mescovoituragesPassager.php'; ?>
        <?php require BASE_PATH . '/src/controllers/principal/validezVosTrajets.php'; ?>
    <?php else: ?>
        <p class="text-muted">Vous devez être passager pour voir vos trajets.</p>
    <?php endif; ?>
</main>
<?php
$mainContent = ob_get_clean();

// 7) Affichage via le layout
require BASE_PATH . '/src/layout.php';
exit;
