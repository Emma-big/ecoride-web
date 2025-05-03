<?php
namespace Adminlocal\EcoRide\Controllers\Post;

use DateTime;

// 1) Charger le PDO
require_once BASE_PATH . '/config/database.php';

// 2) Session & auth
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

// 3) Récupérer et nettoyer les données
$vid = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$input = [
    'marque_id'           => trim($_POST['marque_id'] ?? ''),
    'modele'              => trim($_POST['modele'] ?? ''),
    'immatriculation'     => strtoupper(trim($_POST['immatriculation'] ?? '')),
    'couleur'             => trim($_POST['couleur'] ?? ''),
    'date_premiere_immat' => trim($_POST['date_premiere_immat'] ?? ''),
    'energie_id'          => trim($_POST['energie_id'] ?? ''),
];

$errors = [];

// 4) Validation des champs
if (empty($input['marque_id'])) {
    $errors['marque_id'] = 'Veuillez choisir une marque.';
}
if (!preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ]{1,15}$/u", $input['modele'])) {
    $errors['modele'] = 'Le modèle doit comporter 1 à 15 lettres.';
}
if (!preg_match("/^[A-Za-z0-9-]{1,15}$/", $input['immatriculation'])) {
    $errors['immatriculation'] = 'Immatriculation invalide (1-15 caractères alphanumériques ou tirets).';
}
if (!preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ]{1,15}$/u", $input['couleur'])) {
    $errors['couleur'] = 'La couleur doit comporter 1 à 15 lettres.';
}
if (empty($input['date_premiere_immat']) || !DateTime::createFromFormat('Y-m-d', $input['date_premiere_immat'])) {
    $errors['date_premiere_immat'] = 'Date de première immatriculation invalide.';
}
if (empty($input['energie_id'])) {
    $errors['energie_id'] = 'Veuillez choisir une énergie.';
}

// 5) En cas d'erreurs, stocker et rediriger
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old'] = $input;
    $redirect = '/vehiculeForm' . ($vid > 0 ? '?id=' . $vid : '');
    header('Location: ' . $redirect);
    exit;
}

// 6) Vérification de l'ID de la voiture
if ($vid <= 0) {
    $_SESSION['flash_error'] = 'ID de voiture invalide.';
    header('Location: /utilisateur');
    exit;
}

// 7) Mise à jour en base
$sql = "
    UPDATE voitures
       SET marque_id           = :marque_id,
           modele              = :modele,
           immatriculation     = :immatriculation,
           couleur             = :couleur,
           date_premiere_immat = :date_premiere_immat,
           energie             = :energie_id,
           updated_at          = NOW()
     WHERE voiture_id      = :vid
       AND proprietaire_id = :uid
       AND deleted_at IS NULL
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array_merge($input, [
    'vid' => $vid,
    'uid' => $_SESSION['user']['utilisateur_id'],
]));

// 8) Confirmation et redirection
$_SESSION['flash'] = 'Voiture mise à jour.';
header('Location: /utilisateur');
exit;
