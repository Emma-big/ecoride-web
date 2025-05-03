<?php
// src/controllers/principal/data2.php

header('Content-Type: application/json; charset=UTF-8');
require_once BASE_PATH . '/config/database.php';

$rawStart = $_GET['start'] ?? '';
$rawEnd   = $_GET['end']   ?? '';
$start = preg_match('~^\d{4}-\d{2}-\d{2}$~', $rawStart) ? $rawStart : date('Y-m-d', strtotime('-30 days'));
$end   = preg_match('~^\d{4}-\d{2}-\d{2}$~', $rawEnd)   ? $rawEnd   : date('Y-m-d');
$start .= ' 00:00:00';
$end   .= ' 23:59:59';

$sql = "
    SELECT DATE(date_depart) AS jour,
           SUM(prix_personne) AS credits
    FROM covoiturage
    WHERE date_depart BETWEEN :start AND :end
    GROUP BY jour
    ORDER BY jour
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':start' => $start, ':end' => $end]);
$data = $stmt->fetchAll();

echo json_encode([
    'jours'  => array_column($data, 'jour'),
    'values' => array_map('floatval', array_column($data, 'credits')),
]);
