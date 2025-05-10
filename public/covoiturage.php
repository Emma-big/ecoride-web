<?php
// public/covoiturage.php — redirige vers le layout global

// 1) Masquer le big-title du layout
global $hideTitle;
$hideTitle = true;

// 2) Afficher la barre de recherche via le layout
$barreRecherche = 'src/views/barreRecherche.php';

// 3) Vue principale et titre de la page
$pageTitle   = 'Rechercher un covoiturage';
$extraStyles = [
    '/assets/style/styleFormLogin.css',
    '/assets/style/styleCovoiturage.css'
];
$mainView    = 'src/views/covoiturage.php';

// 4) Inclusion du layout global
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/src/layout.php';
exit;