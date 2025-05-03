<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /registerForm');
    exit;
}

// 3) Récupérer + nettoyer
$input = [
    'pseudo'    => trim($_POST['pseudo']    ?? ''),
    'email'     => filter_var(trim($_POST['email']   ?? ''), FILTER_SANITIZE_EMAIL),
    'password'  => $_POST['password'] ?? '',
    'name'      => trim($_POST['name']      ?? ''),
    'surname'   => trim($_POST['surname']   ?? ''),
    'naissance' => trim($_POST['naissance'] ?? ''),
    'choix'     => trim($_POST['choix']     ?? ''),
    'phone'     => trim($_POST['phone']     ?? ''),
];

$errors = [];

// 4) Validation
if (!$input['pseudo'] || !preg_match('/^[A-Za-z0-9_-]{3,20}$/', $input['pseudo'])) {
    $errors['pseudo'] = 'Pseudo invalide (3–20 caractères alphanumériques, - ou _).';
}
if (!$input['email'] || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Adresse e-mail invalide.';
}
if (strlen($input['password']) < 8) {
    $errors['password'] = 'Le mot de passe doit faire au moins 8 caractères.';
}
if (!$input['name'] || !preg_match('/^[\p{L}\' -]{1,50}$/u', $input['name'])) {
    $errors['name'] = 'Nom invalide.';
}
if (!$input['surname'] || !preg_match('/^[\p{L}\' -]{1,50}$/u', $input['surname'])) {
    $errors['surname'] = 'Prénom invalide.';
}
$dt = \DateTime::createFromFormat('Y-m-d', $input['naissance']);
if (!($dt && $dt->format('Y-m-d') === $input['naissance'])) {
    $errors['naissance'] = 'Date de naissance invalide.';
}
if (!in_array($input['choix'], ['H','F'], true)) {
    $errors['choix'] = 'Choix de sexe invalide.';
}
if (!preg_match('/^\+?[0-9]{6,15}$/', $input['phone'])) {
    $errors['phone'] = 'Téléphone invalide (6–15 chiffres).';
}

// 5) En cas d’erreurs
if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old']         = $input;
    header('Location: /registerForm');
    exit;
}

// 6) Charger la BDD
require_once BASE_PATH . '/src/config.php';
$pdo = $config['mysql'];

// 7) Vérifier unicité email
$stmt = $pdo->prepare("SELECT 1 FROM utilisateurs WHERE email = :email");
$stmt->execute([':email' => $input['email']]);
if ($stmt->fetch()) {
    $_SESSION['form_errors'] = ['email' => 'Cette adresse mail est déjà utilisée.'];
    $_SESSION['old']         = $input;
    header('Location: /registerForm');
    exit;
}

// 8) Hash & defaults
$hashed = password_hash($input['password'], PASSWORD_DEFAULT);
$photo  = $input['choix'] === 'F' ? 'femme.png' : 'homme.png';
$credit = 20;
$role   = 3;

// 9) Insertion
$insert = $pdo->prepare("
    INSERT INTO utilisateurs
      (pseudo,email,password,nom,prenom,date_naissance,telephone,credit,role,photo)
    VALUES
      (:pseudo,:email,:password,:name,:surname,:naissance,:phone,:credit,:role,:photo)
");
$insert->execute([
    ':pseudo'    => $input['pseudo'],
    ':email'     => $input['email'],
    ':password'  => $hashed,
    ':name'      => $input['name'],
    ':surname'   => $input['surname'],
    ':naissance' => $input['naissance'],
    ':phone'     => $input['phone'],
    ':credit'    => $credit,
    ':role'      => $role,
    ':photo'     => $photo,
]);

// 10) Success & redirection
$_SESSION['flash_success'] = 'Votre compte a bien été créé ! Vous pouvez maintenant vous connecter.';
header('Location: /login');
exit;
