<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

 

// 1) Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Déterminer le rôle de l'utilisateur (0 par défaut)
$role = isset($_SESSION["user"]["role"]) ? (int)$_SESSION["user"]["role"] : 0;

// 3) Sélection du fichier d'en-tête selon le rôle
switch ($role) {
    case 1:
        $headerFile = BASE_PATH . "/src/views/headerAdmin.php";
        break;
    case 2:
        $headerFile = BASE_PATH . "/src/views/headerEmploye.php";
        break;
    case 3:
        $headerFile = BASE_PATH . "/src/views/headerUtilisateur.php";
        break;
    case 4:
        $headerFile = BASE_PATH . "/src/views/headerSuspendu.php";
        break;
    default:
        $headerFile = BASE_PATH . "/src/views/header.php";
        break;
}

// 4) Inclusion ou fallback
if (file_exists($headerFile)) {
    require_once $headerFile;
} else {
    // Fallback : header simple
    ?>
    <header id="site-header">
        <nav class="navbar">
            <a class="navbar-brand" href="/">EcoRide</a>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="/index">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="/covoiturage">Covoiturages</a></li>
                <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="/login">Connexion / Inscription</a></li>
            </ul>
        </nav>
    </header>
    <?php
}
