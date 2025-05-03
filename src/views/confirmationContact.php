<?php
// src/views/confirmationContact.php

 

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Gérer l'inactivité (10 minutes)
$inactive_duration = 600;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactive_duration) {
    session_unset(); session_destroy();
    header('Location: /inactivite'); exit;
}
$_SESSION['last_activity'] = time();

// 3) Variables pour le layout
$pageTitle   = "Confirmation Contact";
$hideTitle   = false;
$extraStyles = [
    "/assets/style/styleFormLogin.css",
    "/assets/style/styleBigTitle.css"
];

// 4) Contenu principal
ob_start();
?>
<div class="container mt-5">
  <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
    <div class="alert alert-success text-center" role="alert">
      <h4 class="fw-bold">Message envoyé !</h4>
      <p class="mb-0">Merci de nous avoir contactés, nous reviendrons vers vous rapidement.</p>
    </div>
  <?php else: ?>
    <div class="alert alert-danger text-center" role="alert">
      <h4 class="fw-bold">Erreur d'envoi</h4>
      <p class="mb-0">Une erreur est survenue lors de l'envoi de votre message. Veuillez réessayer.</p>
    </div>
  <?php endif; ?>
  <div class="text-center mt-4">
    <a href="/" class="btn btn-secondary me-2">Accueil</a>
    <a href="/contact" class="btn btn-primary">Retour au contact</a>
  </div>
</div>
<?php
$mainContent = ob_get_clean();

// 5) Appel du layout global
require_once BASE_PATH . '/src/layout.php';
?>
