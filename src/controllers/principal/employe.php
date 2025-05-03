<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Vérifier que l'utilisateur est un employé (role = 2)
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 2) {
    http_response_code(403);
    exit('Accès interdit : espace employé uniquement.');
}

// 3) Variables pour le layout
$pageTitle   = 'Espace Employé - EcoRide';
$hideTitle   = true;
$extraStyles = [
    '/assets/style/styleIndex.css',
    '/assets/style/styleAdmin.css'
];

// 4) Choisir la vue principale
$mainView = 'views/employe.php';

// 5) Afficher via le layout global
require_once BASE_PATH . '/src/layout.php';
exit;
