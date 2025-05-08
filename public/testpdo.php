<?php
// public/testpdo.php

// Force errors on
ini_set('display_errors','1');
error_reporting(E_ALL);

// Affiche la var d’environnement JAWSDB_URL
echo 'JAWSDB_URL = '.getenv('JAWSDB_URL')."\n";

// Charge la config PDO
try {
    $pdo = require __DIR__ . '/../src/config.php';
    echo "✅ Connecté à MySQL : ".getenv('DB_NAME')."\n";
    echo "Hôte : ".getenv('DB_HOST').":".getenv('DB_PORT')."\n";
    
} catch (\PDOException $e) {
    echo "❌ Échec de la connexion : " . $e->getMessage();
}
