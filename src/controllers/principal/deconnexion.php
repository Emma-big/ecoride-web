<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session uniquement si elle n'est pas déjà active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Vider toutes les variables de session
$_SESSION = [];

// 3) Supprimer le cookie de session s'il existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4) Détruire la session
session_destroy();

// 5) Rediriger vers la page d'accueil
header("Location: /");
exit;
