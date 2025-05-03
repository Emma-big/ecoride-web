<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

// 1) Démarrer la session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Vérification de la connexion
if (empty($_SESSION['user']['utilisateur_id'])) {
    header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// 3) Récupérer erreurs et anciennes valeurs
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old']);

// 4) Récupération de l’ID du covoiturage
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) 
?>
<div class="container py-5">
  <h3>Signaler un problème pour le trajet #<?= htmlspecialchars($id, ENT_QUOTES) ?></h3>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $msg): ?>
          <li><?= htmlspecialchars($msg, ENT_QUOTES) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="/reclamationPost" novalidate>
    <input type="hidden" name="covoiturage_id" value="<?= htmlspecialchars($_GET['id'] ?? '', ENT_QUOTES) ?>">
    <div class="mb-3">
      <label for="commentaire" class="form-label">Votre message</label>
      <textarea id="commentaire"
                name="commentaire"
                class="form-control<?= isset($errors['commentaire']) ? ' is-invalid' : '' ?>"
                rows="4"
                required><?= htmlspecialchars($old['commentaire'] ?? '', ENT_QUOTES) ?></textarea>
      <?php if (isset($errors['commentaire'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['commentaire'], ENT_QUOTES) ?></div>
      <?php endif; ?>
    </div>
    <div class="text-center">
      <button type="submit" class="btn btn-danger">Envoyer la réclamation</button>
      <a href="/utilisateur" class="btn btn-secondary">Annuler</a>
    </div>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
  </form>
</div>
