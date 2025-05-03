<?php
// src/views/noteForm.php

// 1) Session & CSRF
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['user'])) {
    header('Location: /accessDenied');
    exit;
}

// 2) Récupère l’ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: /utilisateur');
    exit;
}

// 3) Layout
$pageTitle   = 'Notez votre covoiturage - EcoRide';
$extraStyles = [
    '/assets/style/styleFormLogin.css',
    '/assets/style/styleBigTitle.css',
    '/assets/style/styleCovoiturage.css',
    '/assets/style/styleFooter.css'
];
$withTitle   = true;

// 4) Génère le formulaire
ob_start();
?>
<h2 class="text-center mb-4">
  Notez votre trajet #<?= htmlspecialchars($id, ENT_QUOTES) ?>
</h2>

<form action="/notePost" method="POST" class="mx-auto note-form" novalidate>
  <input type="hidden" name="covoiturage_id" value="<?= $id ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">

  <div class="mb-3">
    <label for="note" class="form-label">Votre note (1 à 5 étoiles) :</label>
    <select id="note" name="note" class="form-select" required>
      <option value="">--Choisissez--</option>
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <option value="<?= $i ?>"><?= $i ?> étoile<?= $i > 1 ? 's' : '' ?></option>
      <?php endfor; ?>
    </select>
    <div class="invalid-feedback">Veuillez attribuer une note.</div>
  </div>

  <div class="mb-3">
    <label for="commentaire" class="form-label">Votre commentaire (facultatif) :</label>
    <textarea id="commentaire" name="commentaire" class="form-control" rows="4" maxlength="500"
              placeholder="Partagez votre ressenti…"></textarea>
  </div>

  <div class="text-center">
    <button type="submit" class="btn btn-primary">Envoyer la note</button>
    <a href="/utilisateur" class="btn btn-secondary ms-2">Annuler</a>
  </div>
</form>

<script>
  // Validation Bootstrap
  (function () {
    'use strict';
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  })();
</script>
<?php
$mainContent = ob_get_clean();
require BASE_PATH . '/src/layout.php';
exit;
?>
