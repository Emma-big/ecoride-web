<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 1.1) CSRF (déjà géré globalement dans index.php)

// 2) Charger la BDD
require_once BASE_PATH . '/config/database.php';

// 3) Récupérer et nettoyer le formulaire
$input = [
    'email'    => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '',
    'password' => $_POST['password'] ?? '',
    'redirect' => filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL) ?: '',
];

$errors = [];

// 4) Validation côté serveur
if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Veuillez saisir une adresse e-mail valide.';
}
if (empty($input['password'])) {
    $errors['password'] = 'Veuillez saisir votre mot de passe.';
}

// 5) En cas d'erreurs, stocker et rediriger
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    // Pour la sécurité, on ne renvoie pas l'ancien mot de passe
    $_SESSION['old'] = ['email' => $input['email']];
    $redirectUrl = '/login';
    if ($input['redirect']) {
        $redirectUrl .= '?redirect=' . urlencode($input['redirect']);
    }
    header('Location: ' . $redirectUrl);
    exit;
}

// 6) Chercher l’utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute([':email' => $input['email']]);
if ($stmt->rowCount() === 1) {
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    // 7) Vérifier le mot de passe
    if (password_verify($input['password'], $user['password'])) {
        // 8) Stocker en session, y compris le crédit
        $_SESSION['user'] = [
            'utilisateur_id' => (int) $user['utilisateur_id'],
            'pseudo'         => $user['pseudo'],
            'email'          => $user['email'],
            'nom'            => $user['nom'],
            'prenom'         => $user['prenom'],
            'role'           => (int) $user['role'],
            'is_chauffeur'   => false,
            'is_passager'    => false,
            'credit'         => (float) $user['credit'],
        ];

        // 9) Redirection si besoin
        if ($input['redirect']) {
            header('Location: /' . ltrim($input['redirect'], '/'));
            exit;
        }

        // 10) Redirection par défaut selon le rôle
        switch ($_SESSION['user']['role']) {
            case 1: header('Location: /admin');    break;
            case 2:
            case 3: header('Location: /index');    break;
            case 4: header('Location: /suspendu'); break;
            default: header('Location: /index');   break;
        }
        exit;
    }
}

// 11) Identifiants incorrects
$_SESSION['flash_error'] = 'Identifiants incorrects.';

// 12) Redirection en cas d’erreur d’auth
$errorUrl = '/login';
if ($input['redirect']) {
    $errorUrl .= '?redirect=' . urlencode($input['redirect']);
}
header('Location: ' . $errorUrl);
exit;
