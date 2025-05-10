<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la BDD via le même config.php que index.php
//    Il retourne l’objet PDO configuré avec JAWSDB_URL
$pdo = require BASE_PATH . '/src/config.php';

// 3) Récupérer et nettoyer le formulaire
$input = [
    'email'    => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '',
    'password' => $_POST['password'] ?? '',
    'redirect' => filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL) ?: '',
];

$errors = [];

// 4) Validation
if (empty($input['email']) || ! filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Veuillez saisir une adresse e-mail valide.';
}
if (empty($input['password'])) {
    $errors['password'] = 'Veuillez saisir votre mot de passe.';
}

if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old'] = ['email' => $input['email']];
    $url = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
    header('Location: ' . $url);
    exit;
}

// 5) Chercher l’utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute([':email' => $input['email']]);
$user = $stmt->fetch();

if ($user && password_verify($input['password'], $user['password'])) {
    // Stocke en session
    $_SESSION['user'] = [
        'utilisateur_id' => (int) $user['utilisateur_id'],
        'pseudo'         => $user['pseudo'],
        'email'          => $user['email'],
        'nom'            => $user['nom'],
        'prenom'         => $user['prenom'],
        'role'           => (int) $user['role'],
        'credit'         => (float) $user['credit'],
    ];

    // Redirection
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

// identifiants invalides
$_SESSION['flash_error'] = 'Identifiants incorrects.';
$errorUrl = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
header('Location: ' . $errorUrl);
exit;
