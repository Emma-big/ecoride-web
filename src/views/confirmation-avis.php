<?php
// src/views/confirmation-avis.php

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Gérer l'inactivité (10 minutes)
$inactive_duration = 600;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactive_duration) {
    session_unset();
    session_destroy();
    header('Location: /inactivite');
    exit;
}
$_SESSION['last_activity'] = time();

// 3) Variables pour le layout
$pageTitle   = "Confirmation de l'avis - EcoRide";
$extraStyles = [
    "/assets/style/styleFormLogin.css",
    "/assets/style/styleBigTitle.css"
];
$withTitle = false;

// 4) Contenu principal
ob_start();
?>
<div class="formLogin container mt-5">
    <div class="text-center text-white">
        <h4 class="fw-bold">Merci pour votre signalement !</h4>
        <p class="fw-bold">Votre réclamation a bien été enregistrée et sera traitée par notre équipe dans les plus brefs délais.</p>
        <p class="fw-bold">Nous vous tiendrons informé par email dès que votre dossier sera étudié.</p>
        <hr>
        <a href="/utilisateur" class="btn btn-primary">Retour à mon espace</a>
    </div>
</div>
<?php
$mainContent = ob_get_clean();

// 5) Inclusion du layout global
require_once BASE_PATH . '/src/layout.php';
?>
