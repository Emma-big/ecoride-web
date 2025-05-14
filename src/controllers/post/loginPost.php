<?php
namespace Adminlocal\EcoRide\Controllers\Post;

use Exception; // Import de l'Exception globale

// 1) Démarrer la session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la BDD et les constantes reCAPTCHA
$pdo = require BASE_PATH . '/src/config.php';
// Assurez-vous dans config.php d'avoir :
// define('RECAPTCHA_SITE_KEY', 'votre_site_key');
// define('RECAPTCHA_SECRET_KEY', 'votre_secret_key');

// 3) Paramètres de la protection brute-force
$ip            = $_SERVER['REMOTE_ADDR'];
$windowMinutes = 15;
$limitAttempts = 3;
$since = (new \DateTime())->modify("-{$windowMinutes} minutes")->format('Y-m-d H:i:s');

// 4) Vérification de l'existence de la table utilisateurs
try {
    $res = $pdo->query("SHOW TABLES LIKE 'utilisateurs'")->fetchAll();
    if (count($res) === 0) {
        throw new Exception("La table 'utilisateurs' est toujours introuvable !");
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

// 6) Déterminer si on doit exiger le captcha
$requireCaptcha = ($attempts >= $limitAttempts);
// On stocke dans la session pour que la vue de login l'affiche
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
if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Veuillez saisir une adresse e-mail valide.';
}
if (empty($input['password'])) {
    $errors['password'] = 'Veuillez saisir votre mot de passe.';
}

// 9) Vérification du reCAPTCHA si requis
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

// 10) En cas d’erreurs, on redirige
if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old'] = ['email' => $input['email']];
    $url = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
    header('Location: ' . $url);
    exit;
}

// 11) Authentification
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute([':email' => $input['email']]);
$user = $stmt->fetch();

if ($user && password_verify($input['password'], $user['password_hash'] ?? $user['password'])) {
    // Succès : purge des tentatives
    $del = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = :ip");
    $del->execute([':ip' => $ip]);

    unset($_SESSION['requireCaptcha']);

    // Stocke l’utilisateur en session
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
            case 2: header('Location: /employe');  break;
            case 3: header('Location: /index');    break;
            case 4: header('Location: /suspendu'); break;
            default: header('Location: /index');   break;
        }
    }
    exit;
}

// 12) Échec : enregistrer la tentative
$ins = $pdo->prepare("
    INSERT INTO login_attempts (ip_address, attempted_at)
    VALUES (:ip, NOW())
");
$ins->execute([':ip' => $ip]);

// 13) Message d’erreur et redirection
$_SESSION['flash_error'] = 'Identifiants incorrects.';
$errorUrl = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
header('Location: ' . $errorUrl);
exit;
