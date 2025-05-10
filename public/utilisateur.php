<?php
// public/utilisateur.php — Front Controller pour la page « Mon espace utilisateur »

// 1) Définir la racine du projet et démarrer la session + inactivité
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (! defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
$inactive_duration = 600;
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $inactive_duration) {
    session_unset();
    session_destroy();
    header('Location: /inactivite');
    exit;
}
$_SESSION['last_activity'] = time();

// 2) Authentification
if (empty($_SESSION['user'])) {
    header('Location: /accessDenied');
    exit;
}

// 3) Masquer le bigTitle global
$hideTitle = true;

// 4) Rôles
$isChauffeur = !empty($_SESSION['user']['is_chauffeur']);
$isPassager  = !empty($_SESSION['user']['is_passager']);
$uid         = (int) $_SESSION['user']['utilisateur_id'];

// 5) Variables layout
$pageTitle   = 'Mon espace utilisateur - EcoRide';
$extraStyles = [
    '/assets/style/styleIndex.css',
    '/assets/style/styleAdmin.css',
];

// === Contenu principal ===
ob_start();
?>
<main class="container mt-4">
  <?php require BASE_PATH . '/src/views/bigTitle.php'; ?>

  <!-- Mes informations -->
  <?php require BASE_PATH . '/src/controllers/principal/mesinfos.php'; ?>

  <!-- Choix de rôle -->
  <form action="/updateRolePost" method="POST" class="mb-5">
    <label class="me-3">
      <input type="checkbox" name="role_chauffeur" value="1" <?= $isChauffeur ? 'checked' : '' ?>> Chauffeur
    </label>
    <label class="me-3">
      <input type="checkbox" name="role_passager" value="1" <?= $isPassager ? 'checked' : '' ?>> Passager
    </label>
    <button type="submit" class="btn btn-secondary btn-sm">Mettre à jour</button>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
  </form>

  <!-- Mes voitures -->
  <h2>Mes voitures</h2>
  <?php if ($isChauffeur): ?>
    <?php require BASE_PATH . '/src/controllers/principal/mesvoitures.php'; ?>
  <?php else: ?>
    <p class="text-muted">Vous devez être chauffeur pour gérer vos voitures.</p>
  <?php endif; ?>

  <!-- Mes covoiturages (Chauffeur) -->
  <h2 class="mt-5">Mes covoiturages (Chauffeur)</h2>
  <?php if ($isChauffeur): ?>
    <div class="text-center mb-4">
      <a href="/covoiturageForm" class="btn btn-primary">Créer un covoiturage</a>
    </div>
    <?php require BASE_PATH . '/src/controllers/principal/mescovoituragesChauffeur.php'; ?>
  <?php else: ?>
    <p class="text-muted">Vous devez être chauffeur pour gérer vos covoiturages.</p>
  <?php endif; ?>

  <!-- Mes covoiturages (Passager) -->
  <h2 class="mt-5">Mes covoiturages (Passager)</h2>
  <?php if ($isPassager): ?>
    <?php require BASE_PATH . '/src/controllers/principal/mescovoituragesPassager.php'; ?>
    <?php require BASE_PATH . '/src/controllers/principal/validezVosTrajets.php'; ?>
  <?php else: ?>
    <p class="text-muted">Vous devez être passager pour voir vos trajets réservés.</p>
  <?php endif; ?>
</main>
<?php
$mainContent = ob_get_clean();

// 6) Appel du layout
require BASE_PATH . '/src/layout.php';
exit;
