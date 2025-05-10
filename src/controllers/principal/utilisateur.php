<?php
// src/controllers/principal/utilisateur.php

// 1) La session est déjà démarrée dans public/index.php

// 2) Vérifier l’authentification
if (empty($_SESSION['user']['utilisateur_id'])) {
    header('Location: /accessDenied');
    exit;
}

// 3) On récupère le PDO créé par public/index.php
/** @var \PDO $pdo */
global $pdo;

$uid         = (int) $_SESSION['user']['utilisateur_id'];
$isChauffeur = ! empty($_SESSION['user']['is_chauffeur']);
$isPassager  = ! empty($_SESSION['user']['is_passager']);

try {
    // 4) Charger les infos de l’utilisateur via $pdo
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE utilisateur_id = :id');
    $stmt->execute([':id' => $uid]);
    $user = $stmt->fetch();
    if (! $user) {
        throw new \Exception("Utilisateur introuvable.");
    }
} catch (\Throwable $e) {
    echo '<p>Erreur de connexion à la base de données : '
       . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}

// 5) Config du layout
$pageTitle   = 'Mon espace utilisateur - EcoRide';
$extraStyles = ['/assets/style/styleIndex.css', '/assets/style/styleAdmin.css'];
$withTitle   = false;

// 6) Contenu
ob_start();
?>
<main class="container mt-4">
    <?php require BASE_PATH . '/src/views/bigTitle.php'; ?>
    <?php require BASE_PATH . '/src/controllers/principal/mesinfos.php'; ?>

    <!-- Choix de rôle -->
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

    <!-- Mes voitures -->
    <h2>Mes voitures</h2>
    <?php if ($isChauffeur): ?>
        <?php require BASE_PATH . '/src/controllers/principal/mesvoitures.php'; ?>
    <?php else: ?>
        <p class="text-muted">
            Vous devez être chauffeur pour gérer vos voitures.
        </p>
    <?php endif; ?>

    <!-- Mes covoiturages (Chauffeur) -->
    <h2>Mes covoiturages (Chauffeur)</h2>
    <?php if ($isChauffeur): ?>
        <div class="text-center my-4">
            <a href="/covoiturageForm" class="btn btn-primary">
                Créer un covoiturage
            </a>
        </div>
        <?php require BASE_PATH . '/src/controllers/principal/mescovoituragesChauffeur.php'; ?>
    <?php else: ?>
        <p class="text-muted">
            Vous devez être chauffeur pour gérer vos covoiturages.
        </p>
    <?php endif; ?>

    <!-- Mes covoiturages (Passager) -->
    <h2>Mes covoiturages (Passager)</h2>
    <?php if ($isPassager): ?>
        <?php require BASE_PATH . '/src/controllers/principal/mescovoituragesPassager.php'; ?>
        <?php require BASE_PATH . '/src/controllers/principal/validezVosTrajets.php'; ?>
    <?php else: ?>
        <p class="text-muted">
            Vous devez être passager pour voir vos trajets.
        </p>
    <?php endif; ?>
</main>
<?php
$mainContent = ob_get_clean();

// 7) On affiche via le layout
require BASE_PATH . '/src/layout.php';
exit;
