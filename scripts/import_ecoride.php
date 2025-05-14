<?php
// scripts/import_ecoride.php

// 1) Charger la config et obtenir le PDO
$pdo = require __DIR__ . '/../src/config.php';

// 2) Lire le dump depuis database/ecoride.sql
$sql = file_get_contents(__DIR__ . '/../database/ecoride.sql');

// 3) Découper sur les points-virgules
$commands = array_filter(
    array_map('trim', explode(";\n", $sql)),
    fn($c) => $c !== ''
);

try {
    foreach ($commands as $command) {
        $pdo->exec($command);
    }
    echo "Import de ecoride.sql terminé.\n";
} catch (PDOException $e) {
    echo "Erreur lors de l'import SQL : " . $e->getMessage() . "\n";
    exit(1);
}
