<?php
// src/views/confirm-participation.php

 

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Variables pour le layout
$pageTitle   = "Confirmation de participation - EcoRide";
$withTitle   = false;  // pas de bigTitle ici
$extraStyles = [
    "/assets/style/styleFormLogin.css",
    "/assets/style/styleIndex.css"
];

// 3) Contenu principal
ob_start();
?>
<div class="container py-5">
  <h3>Vous allez dépenser <?= htmlspecialchars($price, ENT_QUOTES) ?> crédits.</h3>
  <form method="POST" action="/participerCovoiturage">
    <input type="hidden" name="id"      value="<?= htmlspecialchars($covoitId, ENT_QUOTES) ?>">
    <input type="hidden" name="confirm" value="1">
    <button type="submit" class="btn btn-danger">Confirmer</button>
    <a href="/detail-covoiturage?id=<?= htmlspecialchars($covoitId, ENT_QUOTES) ?>"
       class="btn btn-secondary">Annuler</a>
       <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
  </form>
</div>
<?php
// 4) Appel du layout global
$mainContent = ob_get_clean();
require_once BASE_PATH . '/src/layout.php';
?>
