<?php
// src/views/accessDenied.php

 

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Variables pour le layout
$pageTitle = "Accès refusé - EcoRide";
$withTitle = true;
$extraStyles = [
    "/assets/style/styleFormLogin.css",
    "/assets/style/styleBigTitle.css",
    "/assets/style/styleIndex.css"
];

// 3) Génération du contenu principal
ob_start();
?>
<h2 class="text-center mt-4">Accès refusé !</h2>
<div class="formLogin mx-auto">
    <p>Désolé, vous n'avez pas les permissions pour voir cette page.</p>
    <div class="d-flex justify-content-center gap-2">
        <a href="/login" class="btn btn-primary">Se connecter</a>
        <a href="/" class="btn btn-secondary">Retour</a>
    </div>
</div>
<?php
$content = ob_get_clean();

// 4) Appel du layout global
require_once BASE_PATH . '/src/layout.php';
?>
