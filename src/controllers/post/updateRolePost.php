<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la configuration de la base de données
require_once BASE_PATH . '/config/database.php';

// 3) Sécurité : s’assurer que l’utilisateur est bien connecté
if (empty($_SESSION['user']['pseudo'])) {
    header('Location: /accessDenied');
    exit;
}

// 4) Lire les checkbox
$isChauffeur = isset($_POST['role_chauffeur']) ? 1 : 0;
$isPassager  = isset($_POST['role_passager'])  ? 1 : 0;

// 5) Mettre à jour en base
$sql = "
  UPDATE utilisateurs
  SET is_chauffeur = :chauffeur,
      is_passager  = :passager
  WHERE pseudo = :pseudo
";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':chauffeur' => $isChauffeur,
    ':passager'  => $isPassager,
    ':pseudo'    => $_SESSION['user']['pseudo'],
]);

// 6) Mettre à jour la session pour prise en compte immédiate
$_SESSION['user']['is_chauffeur'] = $isChauffeur;
$_SESSION['user']['is_passager']  = $isPassager;

// 7) Redirection vers l’espace utilisateur
header('Location: /utilisateur');
exit;
