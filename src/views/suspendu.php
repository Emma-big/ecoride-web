<?php
// src/views/suspendu.php

// Variables pour le layout
$pageTitle   = "Compte suspendu - EcoRide";
$withTitle   = true;
$extraStyles = [
    "/assets/style/styleIndex.css",
    "/assets/style/styleFormLogin.css"
];

// Cacher le bigTitle si nécessaire
global $hideTitle;
$hideTitle = true;

// Contenu principal
ob_start();
?>
<div class="container text-center py-5">
  <h1>Compte suspendu</h1>
  <p>Votre compte a été suspendu. Pour toute question, contactez-nous à <a href="mailto:contact@ecoride.com">contact@ecoride.com</a>.</p>
</div>
<?php
$mainContent = ob_get_clean();

// Inclusion du layout global
require_once BASE_PATH . '/src/layout.php';
?>
