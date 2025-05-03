<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Charger la config PDO
require_once BASE_PATH . '/config/database.php';

// 2) Vérifier la méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Requête non autorisée.');
}

// 3) Récupérer et valider l’ID du covoiturage
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    exit('ID de covoiturage invalide.');
}

// 4) Mettre à jour le statut_id à 1 (« En cours »)
try {
    $stmt = $pdo->prepare("
        UPDATE covoiturage
           SET statut_id = 1   -- 1 = “En cours”
         WHERE covoiturage_id = :id
    ");
    $stmt->execute([':id' => $id]);
} catch (\PDOException $e) {
    exit('Erreur lors du démarrage du covoiturage.');
}

// 5) Redirection vers l’espace utilisateur
header('Location: /utilisateur');
exit;
