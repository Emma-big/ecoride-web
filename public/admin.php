<?php
// public/admin.php

// 1) Définir BASE_PATH sur la racine du projet
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// 2) Charger Composer + Dotenv
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
    \Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();
}

// 3) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 4) Gestion de l'inactivité (5 minutes)
$inactive_duration = 300;
if (isset($_SESSION['last_activity'])
    && (time() - $_SESSION['last_activity']) > $inactive_duration
) {
    session_unset();
    session_destroy();
    header('Location: /inactivite');
    exit;
}
$_SESSION['last_activity'] = time();

// 5) Vérification du rôle admin (role = 1)
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 1) {
    header('Location: /accessDenied');
    exit;
}

// 6) Variables pour le layout global
$pageTitle   = 'EcoRide - Espace Admin';
$extraStyles = [
    '/assets/style/styleIndex.css',
    '/assets/style/styleAdmin.css',
];
$mainView    = 'views/adminDashboard.php';

// 7) Inclusion du layout
require_once BASE_PATH . '/src/layout.php';
