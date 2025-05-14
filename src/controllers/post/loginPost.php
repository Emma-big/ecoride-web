<?php
namespace Adminlocal\EcoRide\Controllers\Post;

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

// 4) Compter les tentatives récentes
$stmt = $pdo->prepare("
  SELECT COUNT(*) 
  FROM login_attempts 
  WHERE ip_address = :ip 
    AND attempted_at >= :since
");
$stmt->execute([':ip' => $ip, ':since' => $since]);
$attempts = (int) $stmt->fetchColumn();

// 5) Déterminer si on doit exiger le captcha
$requireCaptcha = ($attempts >= $limitAttempts);
// On stocke dans la session pour que la vue de login l'affiche
$_SESSION['requireCaptcha'] = $requireCaptcha;

// 6) Récupérer et nettoyer le formulaire
$input = [
    'email'    => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '',
    'password' => $_POST['password'] ?? '',
    'redirect' => filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL) ?: '',
    'captcha'  => $_POST['g-recaptcha-response'] ?? '',
];

$errors = [];

// 7) Validation simple des champs
if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Veuillez saisir une adresse e-mail valide.';
}
if (empty($input['password'])) {
    $errors['password'] = 'Veuillez saisir votre mot de passe.';
}

// 8) Si captcha requis, vérifier la réponse Google
if ($requireCaptcha) {
    if (empty($input['captcha'])) {
        $errors['captcha'] = 'Veuillez passer le captcha.';
    } else {
        // Appel API Google reCAPTCHA
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

// 9) En cas d’erreurs de validation (email/password/captcha), on redirige
if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old'] = ['email' => $input['email']];
    $url = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
    header('Location: ' . $url);
    exit;
}

// 10) Authentification
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute([':email' => $input['email']]);
$user = $stmt->fetch();

if ($user && password_verify($input['password'], $user['password_hash'] ?? $user['password'])) {
    // Succès : on purge les tentatives de cette IP
    $del = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = :ip");
    $del->execute([':ip' => $ip]);

    // On peut aussi nettoyer le flag captcha
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

    // Redirection vers la page ciblée
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

// 11) Échec d’authentification : enregistrer la tentative
$ins = $pdo->prepare("
  INSERT INTO login_attempts (ip_address, attempted_at)
  VALUES (:ip, NOW())
");
$ins->execute([':ip' => $ip]);

// 12) Préparer le message d’erreur et rediriger
$_SESSION['flash_error'] = 'Identifiants incorrects.';
$errorUrl = '/login' . ($input['redirect'] ? '?redirect=' . urlencode($input['redirect']) : '');
header('Location: ' . $errorUrl);
exit;
