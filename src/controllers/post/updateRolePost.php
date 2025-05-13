<?php
// src/controllers/post/updateRolePost.php

// 1) Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Charger Composer + Dotenv pour lire JAWSDB_URL
require_once BASE_PATH . '/vendor/autoload.php';
Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();

// 3) Charger la config et récupérer le PDO
try {
    /** @var \PDO $pdo */
    $pdo = require BASE_PATH . '/src/config.php';
} catch (\Throwable $e) {
    echo 'Erreur de connexion à la base de données : '
       . htmlspecialchars($e->getMessage());
    exit;
}

// 4) Sécurité : vérifier que l’utilisateur est connecté
if (empty($_SESSION['user']['utilisateur_id'])) {
    header('Location: /accessDenied');
    exit;
}
$uid = (int) $_SESSION['user']['utilisateur_id'];

// 5) Lire les cases cochées
$isChauffeur = ! empty($_POST['role_chauffeur']);
$isPassager  = ! empty($_POST['role_passager']);

// 6) Mettre à jour en base
try {
    $stmt = $pdo->prepare("
        UPDATE utilisateurs
        SET is_chauffeur = :chauffeur,
            is_passager  = :passager
        WHERE utilisateur_id = :id
    ");
    $stmt->execute([
        ':chauffeur' => $isChauffeur ? 1 : 0,
        ':passager'  => $isPassager  ? 1 : 0,
        ':id'        => $uid,
    ]);
} catch (\Throwable $e) {
    echo 'Erreur lors de la mise à jour du rôle : '
       . htmlspecialchars($e->getMessage());
    exit;
}

// 7) Mettre à jour la session
$_SESSION['user']['is_chauffeur'] = $isChauffeur;
$_SESSION['user']['is_passager']  = $isPassager;

// 8) Redirection
header('Location: /utilisateur');
exit;
