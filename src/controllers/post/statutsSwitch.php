<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Sécurité : uniquement admin (role = 1)
if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
    http_response_code(403);
    exit('Accès refusé.');
}

// 3) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

// 4) Récupérer l’ID et l’action
$id     = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

// 5) Validation des paramètres
if ($id <= 0 || !in_array($action, ['valider','rejeter'], true)) {
    http_response_code(400);
    exit('Requête invalide.');
}

$nouveauStatut = ($action === 'valider') ? 1 : 2;

// 6) Mise à jour du statut
try {
    $pdo->prepare("UPDATE Notes SET statut_id = ? WHERE note_id = ?")
        ->execute([$nouveauStatut, $id]);
    $_SESSION['flash'] = $action === 'valider' ? "Avis validé." : "Avis rejeté.";
} catch (\PDOException $e) {
    $_SESSION['flash_error'] = "Erreur lors de la mise à jour du statut.";
}

// 7) Redirection
header('Location: /notes-a-valider');
exit;
