<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Sécurité : uniquement employé (role = 2)
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 2) {
    http_response_code(403);
    exit('Accès interdit');
}

// 3) Charger la config PDO
require_once BASE_PATH . '/config/database.php';

// 4) Récupérer et valider l’ID de la note et l’action
$noteId = isset($_POST['note_id']) ? (int) $_POST['note_id'] : 0;
$action = $_POST['action'] ?? '';

if ($noteId <= 0 || !in_array($action, ['accept', 'refuse'], true)) {
    http_response_code(400);
    exit('Requête invalide');
}

// 5) Déterminer le nouveau statut_id (5 = validé, 6 = refusé)
$newStatut = ($action === 'accept') ? 5 : 6;

// 6) Mise à jour dans la table notes
$stmt = $pdo->prepare("
    UPDATE notes
       SET statut_id = :statut_id
     WHERE note_id   = :id
");
$stmt->execute([
    ':statut_id' => $newStatut,
    ':id'        => $noteId,
]);

// 7) Redirection de retour
header('Location: /notes-a-valider');
exit;
