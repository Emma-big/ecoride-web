<?php
// src/views/inactivite.php

// 1) Variables pour le layout
$pageTitle   = "Inactivité - EcoRide";
$withTitle   = true;
$extraStyles = [
    "/assets/style/styleFormLogin.css",
    "/assets/style/styleBigTitle.css",
    "/assets/style/styleIndex.css",
];

// 2) Contenu principal à injecter
ob_start();
?>
<div class="formLogin mx-auto text-center mt-5">
    <h2 class="mb-4">Inactivité</h2>
    <p class="mb-4 text-white fw-bold">
        Vous avez été déconnecté pour cause d'inactivité. Veuillez vous reconnecter.
    </p>
    <div class="d-flex justify-content-center gap-3">
        <a href="/login" class="btn btn-primary">Se reconnecter</a>
        <a href="/index" class="btn btn-outline-dark">Retour à l'accueil</a>
    </div>
</div>
<?php
$mainContent = ob_get_clean();
// 3) Inclusion du layout global
require_once BASE_PATH . '/src/layout.php';
?>
