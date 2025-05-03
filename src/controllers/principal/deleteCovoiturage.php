<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la configuration de la base de données
require_once BASE_PATH . '/config/database.php';

// 3) Vérifier l’authentification
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(403);
    exit('Accès refusé.');
}

// 4) Vérifier la requête POST et l’ID
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    http_response_code(400);
    exit('Requête invalide.');
}
$id = (int) $_POST['id'];

// 5) Suppression conditionnelle
$stmt = $pdo->prepare(
    "DELETE FROM covoiturage
     WHERE covoiturage_id = :id
       AND utilisateur     = :userId"
);
$stmt->execute([
    ':id'     => $id,
    ':userId' => $userId,
]);

// 6) Redirection vers l’espace utilisateur
header('Location: /utilisateur');
exit;
