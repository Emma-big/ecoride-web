<?php
// public/compteur_api.php

// 1) Pas de sortie avant les headers
header('Content-Type: application/json');

// 2) Session si besoin (ce endpoint ne nécessite pas d'authentification)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 3) Charger la connexion PDO (database.php est à la racine config)
require_once __DIR__ . '/../config/database.php';

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

// 4) Retour JSON
echo json_encode(['total' => $total]);
exit;
