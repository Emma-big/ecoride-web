<?php
// public/testpdo.php

// Active le debug
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Charge la config PDO
try {
    $pdo = require __DIR__ . '/../src/config.php';
    echo "✅ Connecté à MySQL : " . $_ENV['DB_NAME'] . "<br>";
    echo "Hôte : " . $_ENV['DB_HOST'] . ":" . $_ENV['DB_PORT'];
} catch (\PDOException $e) {
    echo "❌ Échec de la connexion : " . $e->getMessage();
}
