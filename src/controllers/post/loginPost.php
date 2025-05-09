<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la BDD via src/config.php (qui lit JAWSDB_URL ou local)
$pdo = require BASE_PATH . '/src/config.php';

// 3) Récupérer et nettoyer le formulaire
$input = [
    'email'    => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '',
    'password' => $_POST['password'] ?? '',
    'redirect' => filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL) ?: '',
];

$errors = [];

// 4) Validation
if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Veuillez saisir une adresse e-mail valide.';
}
if (empty($input['password'])) {
    $errors['password'] = 'Veuillez saisir votre mot de passe.';
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old'] = ['email' => $input['email']];
    $redir = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
    header('Location: ' . $redir);
    exit;
}

// 5) Recherche de l’utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute([':email' => $input['email']]);

if ($stmt->rowCount() === 1) {
    $user = $stmt->fetch();

    // 6) Vérif mot de passe
    if (password_verify($input['password'], $user['password'])) {
        $_SESSION['user'] = [
            'utilisateur_id' => (int) $user['utilisateur_id'],
            'pseudo'         => $user['pseudo'],
            'email'          => $user['email'],
            'nom'            => $user['nom'],
            'prenom'         => $user['prenom'],
            'role'           => (int) $user['role'],
            'credit'         => (float) $user['credit'],
        ];

        // redirection
        if ($input['redirect']) {
            header('Location: /' . ltrim($input['redirect'], '/'));
        } else {
            switch ($_SESSION['user']['role']) {
                case 1: header('Location: /admin');    break;
                case 2:
                case 3: header('Location: /index');    break;
                case 4: header('Location: /suspendu'); break;
                default: header('Location: /index');   break;
            }
        }
        exit;
    }
}

// 7) Échec d’authentification
$_SESSION['flash_error'] = 'Identifiants incorrects.';
$redir = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
header('Location: ' . $redir);
exit;
