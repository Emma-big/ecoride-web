<?php
namespace Adminlocal\EcoRide\Forms;

// 1) Démarrage de la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Récupérer erreurs et anciennes valeurs
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['old']         ?? [];
unset($_SESSION['form_errors'], $_SESSION['old']);

// 3) Messages flash
if (!empty($_SESSION['flash_success'])): ?>
  <div class="alert alert-success text-center">
    <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES) ?>
  </div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="alert alert-danger text-center">
    <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES) ?>
  </div>
<?php unset($_SESSION['flash_error']); endif; ?>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $msg): ?>
        <li><?= htmlspecialchars($msg, ENT_QUOTES) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php
// 4) Variables pour le layout
$pageTitle   = 'Connexion - EcoRide';
$hideTitle   = false;  // on affiche bigTitle
$extraStyles = ['/assets/style/styleFormLogin.css'];

// 5) Générer le contenu principal
ob_start();
?>
<h2 class="text-center mb-4">Connexion</h2>

<form class="formLogin mx-auto" action="/loginPost" method="POST">
  <?php if (!empty($_GET['redirect'])): ?>
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'], ENT_QUOTES) ?>">
  <?php endif; ?>

  <div class="mb-3">
    <label for="email" class="form-label">Adresse e-mail :</label>
    <input type="email" id="email" name="email"
           class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
           value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES) ?>"
           required>
    <?php if (isset($errors['email'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES) ?></div>
    <?php endif; ?>
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Mot de passe :</label>
    <div class="input-group">
      <input type="password" id="password" name="password"
             class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
             required aria-describedby="togglePassword">
      <span class="input-group-text" id="togglePassword" role="button" aria-label="Afficher le mot de passe">
        <i class="bi bi-eye-slash"></i>
      </span>
      <?php if (isset($errors['password'])): ?>
        <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['password'], ENT_QUOTES) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <div class="mb-3 text-center">
    <button type="submit" class="btn btn-primary">Se connecter</button>
  </div>

  <div class="text-center">
    <a href="/registerForm">Créer un compte</a>
  </div>

  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
</form>
<?php
$mainContent = ob_get_clean();

// 6) Appel du layout global
require_once BASE_PATH . '/src/layout.php';
