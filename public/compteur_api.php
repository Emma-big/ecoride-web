<?php
// public/compteur_api.php

// 1) Pas de sortie avant les headers
header('Content-Type: application/json');

// 2) Charger la configuration PDO (src/config.php) et démarrage session si besoin
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$pdo = require_once __DIR__ . '/../src/config.php';

try {
    $stmt = $pdo->prepare(
        "SELECT SUM(montant) AS total
           FROM transactions
          WHERE type_transaction = 'commission'"
    );
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = (int) ($row['total'] ?? 0);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur BDD']);
    exit;
}

// 3) Retour JSON
echo json_encode(['total' => $total]);
exit;
