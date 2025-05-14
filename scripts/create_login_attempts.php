<?php
// scripts/create_login_attempts.php

// 1) Démarrage de la session et chargement de votre bootstrap  
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Récupération de la connexion PDO depuis votre config  
$pdo = require __DIR__ . '/../src/config.php';

// 3) Exécution du CREATE TABLE  
$sql = <<<SQL
CREATE TABLE IF NOT EXISTS login_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ip_address VARCHAR(45) NOT NULL,
  attempted_at DATETIME NOT NULL,
  KEY idx_ip_time (ip_address, attempted_at)
);
SQL;

try {
    $pdo->exec($sql);
    echo "Table login_attempts créée ou déjà existante.\n";
} catch (PDOException $e) {
    echo "Erreur SQL : " . $e->getMessage() . "\n";
    exit(1);
}
