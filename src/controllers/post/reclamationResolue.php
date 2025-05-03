<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger PDO
require_once BASE_PATH . '/config/database.php';

// 3) Sécurité : seul un employé (role = 2) peut accéder
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 2) {
    http_response_code(403);
    exit('Accès interdit');
}

// 4) Récupérer l’ID de la réclamation depuis le POST
$rid = (int)($_POST['reclamation_id'] ?? 0);
if ($rid <= 0) {
    header('Location: /reclamations-problemes');
    exit;
}

// 5) Mettre à jour le statut en base MySQL (8 = « Résolu »)
$stmt = $pdo->prepare("UPDATE reclamations SET statut_id = 8 WHERE reclamation_id = :rid");
$stmt->execute([':rid' => $rid]);

// 6) Redirection pour recharger la liste
header('Location: /reclamations-problemes');
exit;
