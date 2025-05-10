<?php
namespace Adminlocal\EcoRide\Forms;

 

// 1) Démarrage de la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la configuration BDD
$pdo = require BASE_PATH . '/src/config.php';

// 3) Traitement du POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveauRoleId = filter_input(INPUT_POST, 'nouveau_role', FILTER_VALIDATE_INT);
    $pseudo        = trim(filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    if ($nouveauRoleId && $pseudo !== '') {
        // 4) Mise à jour en base
        $sql = "UPDATE utilisateurs SET role = :role WHERE pseudo = :pseudo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':role'   => $nouveauRoleId,
            ':pseudo' => $pseudo,
        ]);

        // 5) Feedback et redirection
        $_SESSION['flash'] = "Le rôle de “{$pseudo}” a bien été mis à jour.";
    } else {
        $_SESSION['flash_error'] = 'Tous les champs sont requis.';
    }

    header('Location: /admin');
    exit;
}

// 6) En cas de non-POST
$_SESSION['flash_error'] = 'Requête invalide.';
header('Location: /admin');
exit;
