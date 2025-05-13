<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Session + vérification rôle admin
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 1) {
    header('Location: /accessDenied');
    exit;
}

// 2) POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin');
    exit;
}

// 3) Charger la BDD
$pdo = require BASE_PATH . '/src/config.php';

// 4) Récupérer & nettoyer
$input = [
    'pseudo'    => trim(filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''),
    'email'     => trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? ''),
    'password'  => $_POST['password'] ?? '',
    'name'      => trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''),
    'surname'   => trim(filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''),
    'naissance' => trim(filter_input(INPUT_POST, 'naissance', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''),
    'phone'     => trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''),
    'choix'     => trim(filter_input(INPUT_POST, 'choix', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''),
];

$errors = [];

// 5) Validation
if (!$input['pseudo'] || !preg_match('/^[A-Za-z0-9_-]{3,20}$/', $input['pseudo'])) {
    $errors['pseudo'] = 'Pseudo invalide (3–20 caractères alphanumériques, - ou _).';
}
if (!$input['email'] || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Adresse e-mail invalide.';
}
if (strlen($input['password']) < 8) {
    $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
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
if (!in_array($input['choix'], ['Homme','Femme'], true)) {
    $errors['choix'] = 'Choix de sexe invalide.';
}
if (!preg_match('/^\+?[0-9]{6,15}$/', $input['phone'])) {
    $errors['phone'] = 'Téléphone invalide (6–15 chiffres).';
}

// 6) Unicité email
$stmt = $pdo->prepare("SELECT 1 FROM utilisateurs WHERE email = :email");
$stmt->execute([':email' => $input['email']]);
if ($stmt->fetch()) {
    $errors['email'] = 'Cette adresse mail est déjà utilisée.';
}

// 7) Si erreurs : rediriger
if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old']         = $input;
    header('Location: /admin');
    exit;
}

// 8) Hash & defaults
$hashed  = password_hash($input['password'], PASSWORD_DEFAULT);
$photo   = $input['choix'] === 'Femme' ? 'employeF.png' : 'employe.png';
$role    = 2;   // employé
$credit  = 0;

// 9) Insertion
$insert = $pdo->prepare("
    INSERT INTO utilisateurs
      (pseudo,email,password,nom,prenom,date_naissance,telephone,role,photo,credit)
    VALUES
      (:pseudo,:email,:password,:name,:surname,:naissance,:phone,:role,:photo,:credit)
");
$insert->execute([
    ':pseudo'    => $input['pseudo'],
    ':email'     => $input['email'],
    ':password'  => $hashed,
    ':name'      => $input['name'],
    ':surname'   => $input['surname'],
    ':naissance' => $input['naissance'],
    ':phone'     => $input['phone'],
    ':role'      => $role,
    ':photo'     => $photo,
    ':credit'    => $credit,
]);

// 10) Confirmation et retour
$_SESSION['flash'] = 'Compte employé créé avec succès !';
header('Location: /admin');
exit;
