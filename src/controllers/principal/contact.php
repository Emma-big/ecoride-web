<?php
namespace Adminlocal\EcoRide\Controllers;

// 1. Session + inactivité
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$inactive_duration = 600; // 10 min
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactive_duration) {
    session_unset();
    session_destroy();
    header("Location: /inactivite");
    exit;
}
$_SESSION['last_activity'] = time();

// 2. Variables pour le layout
$pageTitle   = "Contact";
$extraStyles = ["/assets/style/styleFormLogin.css"];
$hideTitle   = true;

// 3. Indiquer la vue relative à src/
$mainView = 'views/contact_form.php';

// 4. Charger le layout
require_once BASE_PATH . '/src/layout.php';
exit;
