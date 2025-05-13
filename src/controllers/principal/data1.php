<?php
// src/controllers/principal/data1.php

// 1) header JSON
header('Content-Type: application/json; charset=UTF-8');

// 2) config PDO
$pdo = require BASE_PATH . '/src/config.php';

// 3) récupérer dates GET et valider
$rawStart = $_GET['start'] ?? '';
$rawEnd   = $_GET['end']   ?? '';
$start = preg_match('~^\d{4}-\d{2}-\d{2}$~', $rawStart) ? $rawStart : date('Y-m-d', strtotime('-30 days'));
$end   = preg_match('~^\d{4}-\d{2}-\d{2}$~', $rawEnd)   ? $rawEnd   : date('Y-m-d');

$start .= ' 00:00:00';
$end   .= ' 23:59:59';

// 4) requête
$sql = "
    SELECT DATE(date_depart) AS jour,
           COUNT(*)             AS nb
    FROM covoiturage
    WHERE date_depart BETWEEN :start AND :end
    GROUP BY jour
    ORDER BY jour
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':start' => $start, ':end' => $end]);
$data = $stmt->fetchAll();

// 5) construire le JSON
echo json_encode([
    'jours'  => array_column($data, 'jour'),
    'values' => array_map('intval', array_column($data, 'nb')),
]);
