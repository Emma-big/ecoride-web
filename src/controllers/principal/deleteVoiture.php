<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

// 1) Session & auth
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

// 2) POST only + CSRF
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || empty($_POST['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(400);
    exit('Requête non autorisée.');
}

// 3) Charger PDO
$pdo = require BASE_PATH . '/src/config.php';

// 4) Nettoyer et valider l’ID de la voiture
$voitureId      = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$proprietaireId = $_SESSION['user']['utilisateur_id'];
if ($voitureId <= 0) {
    $_SESSION['flash_error'] = 'Identifiant de voiture invalide.';
    header('Location: /utilisateur');
    exit;
}

// 5) Soft-delete
$stmt = $pdo->prepare(
    'UPDATE voitures
        SET deleted_at = NOW()
      WHERE voiture_id      = :vid
        AND proprietaire_id = :uid'
);
$stmt->execute([
    ':vid' => $voitureId,
    ':uid' => $proprietaireId,
]);

// 6) Retour utilisateur
if ($stmt->rowCount()) {
    $_SESSION['flash'] = 'Voiture supprimée.';
} else {
    $_SESSION['flash_error'] = 'Impossible de supprimer cette voiture.';
}

header('Location: /utilisateur');
exit;