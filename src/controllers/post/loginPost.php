<?php
namespace Adminlocal\EcoRide\Controllers\Post;

use Exception;
use Firebase\JWT\JWT;

// 1) Démarrer la session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la BDD, les constantes reCAPTCHA et la clé JWT depuis l’environnement
$pdo     = require BASE_PATH . '/src/config.php';
$jwtKey  = getenv('JWT_SECRET');          // à mettre dans votre .env
// define('RECAPTCHA_SITE_KEY', '...');
// define('RECAPTCHA_SECRET_KEY', '...');

// 3) Protection brute-force
$ip            = $_SERVER['REMOTE_ADDR'];
$windowMinutes = 15;
$limitAttempts = 3;
$since = (new \DateTime())->modify("-{$windowMinutes} minutes")->format('Y-m-d H:i:s');

// 4) Vérifier la table utilisateurs
try {
    $res = $pdo->query("SHOW TABLES LIKE 'utilisateurs'")->fetchAll();
    if (count($res) === 0) {
        throw new Exception("Table 'utilisateurs' introuvable !");
    }
} catch (Exception $e) {
    die($e->getMessage());
}

// 5) Compter les tentatives récentes
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
      FROM login_attempts 
     WHERE ip_address = :ip 
       AND attempted_at >= :since
");
$stmt->execute([':ip' => $ip, ':since' => $since]);
$attempts = (int) $stmt->fetchColumn();

// 6) Exiger le captcha si trop d’échecs
$requireCaptcha = ($attempts >= $limitAttempts);
$_SESSION['requireCaptcha'] = $requireCaptcha;

// 7) Récupérer et nettoyer le formulaire
$input = [
    'email'    => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '',
    'password' => $_POST['password'] ?? '',
    'redirect' => filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL) ?: '',
    'captcha'  => $_POST['g-recaptcha-response'] ?? '',
];
$errors = [];

// 8) Validation des champs
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Veuillez saisir une adresse e-mail valide.';
}
if (empty($input['password'])) {
    $errors['password'] = 'Veuillez saisir votre mot de passe.';
}

// 9) Vérifier le reCAPTCHA si requis
if ($requireCaptcha) {
    if (empty($input['captcha'])) {
        $errors['captcha'] = 'Veuillez passer le captcha.';
    } else {
        $resp = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret='
            . urlencode(RECAPTCHA_SECRET_KEY)
            . '&response=' . urlencode($input['captcha'])
            . '&remoteip=' . urlencode($ip)
        );
        $json = json_decode($resp, true);
        if (empty($json['success'])) {
            $errors['captcha'] = 'Captcha invalide, veuillez réessayer.';
        }
    }
}

// 10) Si erreurs, on renvoie à la form
if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old']         = ['email' => $input['email']];
    $url = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
    header('Location: ' . $url);
    exit;
}

// 11) Authentification utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute([':email' => $input['email']]);
$user = $stmt->fetch();

if ($user && password_verify($input['password'], $user['password_hash'] ?? $user['password'])) {
    // a) Purge des tentatives
    $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = :ip")
        ->execute([':ip' => $ip]);
    unset($_SESSION['requireCaptcha']);

    // b) Génération du JWT (5 minutes)
    $now   = time();
    $token = [
        'iat' => $now,
        'exp' => $now + 300,                // expiration dans 5 minutes
        'sub' => (int)$user['utilisateur_id']
    ];
    $jwt = JWT::encode($token, $jwtKey, 'HS256');

    // c) Envoi dans un cookie sécurisé
    setcookie(
        'eco_jwt',
        $jwt,
        [
            'expires'  => $now + 300,
            'path'     => '/',
            'httponly' => true,
            'secure'   => true,
            'samesite' => 'Lax'
        ]
    );

    // d) Optionnel : Stocker l’utilisateur en session si vous avez encore besoin
    $_SESSION['user'] = [
        'utilisateur_id' => (int)$user['utilisateur_id'],
        'pseudo'         => $user['pseudo'],
        'email'          => $user['email'],
        'nom'            => $user['nom'],
        'prenom'         => $user['prenom'],
        'role'           => (int)$user['role'],
        'credit'         => (float)$user['credit'],
    ];

    // e) Redirection
    if ($input['redirect']) {
        header('Location: /' . ltrim($input['redirect'], '/'));
    } else {
        switch ($_SESSION['user']['role']) {
            case 1: $loc = '/admin';    break;
            case 2: $loc = '/employe';  break;
            case 3: $loc = '/index';    break;
            case 4: $loc = '/suspendu'; break;
            default: $loc = '/index';   break;
        }
        header('Location: ' . $loc);
    }
    exit;
}

// 12) Échec : enregistrer la tentative et message
$pdo->prepare("
    INSERT INTO login_attempts (ip_address, attempted_at)
    VALUES (:ip, NOW())
")->execute([':ip' => $ip]);

$_SESSION['flash_error'] = 'Identifiants incorrects.';
$errorUrl = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
header('Location: ' . $errorUrl);
exit;
