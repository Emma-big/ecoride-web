<?php
namespace Adminlocal\EcoRide\Controllers;

// 1) Session & inactivité
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$inactive_duration = 600;
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $inactive_duration) {
    session_unset();
    session_destroy();
    header("Location: /inactivite");
    exit;
}
$_SESSION['last_activity'] = time();

// 2) Variables pour le layout
$pageTitle   = "Contact – EcoRide";
$hideTitle   = true;
$extraStyles = [
    "/assets/style/styleFormLogin.css",
    "/assets/style/styleIndex.css"
];

// 3) Récupération erreurs et anciennes valeurs
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['old']         ?? [];
unset($_SESSION['form_errors'], $_SESSION['old']);

// 4) Bufferisation de la vue (contact_form.php ne fait **que** le HTML du formulaire)
ob_start();
require BASE_PATH . '/src/views/contact_form.php';
$mainContent = ob_get_clean();

// 5) Affiche le layout qui va injecter $mainContent
require_once BASE_PATH . '/src/layout.php';
exit;