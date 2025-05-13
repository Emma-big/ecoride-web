<?php
namespace Adminlocal\EcoRide\Controllers\Post;

use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// 1) Dotenv
$dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// 2) Démarrer la session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 3) Méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /contact');
    exit;
}

// 4) Récupération + nettoyage
$input = [
    'email'       => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?: '',
    'titre'       => filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '',
    'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '',
];
$errors = [];

// 5) Validation des champs
if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Veuillez saisir une adresse e-mail valide.';
}
if (empty($input['titre'])) {
    $errors['titre'] = 'Le titre est requis.';
} elseif (mb_strlen($input['titre']) > 100) {
    $errors['titre'] = 'Le titre ne doit pas dépasser 100 caractères.';
}
if (empty($input['description'])) {
    $errors['description'] = 'La description est requise.';
}

// 6) En cas d'erreurs, rediriger vers le formulaire
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old']         = $input;
    header('Location: /contact');
    exit;
}

// 7) Inclure la config BDD
$pdo = require BASE_PATH . '/src/config.php';

// 8) Stockage en base
try {
    $stmt = $pdo->prepare(
        "INSERT INTO message (chat_id, utilisateur, role, content, created_at) VALUES (:chat_id, :utilisateur, :role, :content, NOW())"
    );
    $stmt->execute([
        ':chat_id'     => 0,
        ':utilisateur' => $input['email'],
        ':role'        => 'contact',
        ':content'     => "Titre : {$input['titre']}\n\n{$input['description']}",
    ]);
} catch (PDOException $e) {
    $_SESSION['flash_error'] = "Une erreur est survenue lors de l'enregistrement.";
    header('Location: /contact');
    exit;
}

// 9) Envoi du mail via PHPMailer
try {
    $mail = new PHPMailer(true);
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host       = getenv('MAIL_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('MAIL_USERNAME');
    $mail->Password   = getenv('MAIL_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = getenv('MAIL_PORT');

    // Expéditeur & destinataire
    $mail->setFrom(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'));
    $mail->addAddress(getenv('MAIL_ADMIN_ADDRESS'), 'Support EcoRide');
    $mail->addReplyTo($input['email']);

    // Contenu du message
    $mail->isHTML(true);
    $mail->Subject = 'Nouveau message de contact : ' . htmlspecialchars($input['titre'], ENT_QUOTES);
    $mail->Body    = '<p><strong>De :</strong> ' . htmlspecialchars($input['email'], ENT_QUOTES) . '</p>'
                   . '<p><strong>Sujet :</strong> ' . nl2br(htmlspecialchars($input['titre'], ENT_QUOTES)) . '</p>'
                   . '<p><strong>Message :</strong><br>' . nl2br(htmlspecialchars($input['description'], ENT_QUOTES)) . '</p>';

    // Debug SMTP
    $mail->SMTPDebug  = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = function($str, $level) {
        error_log("[SMTP DEBUG level {$level}] {$str}");
    };

    // Envoi
    $mail->send();
} catch (Exception $e) {
    error_log('PHPMailer Exception: ' . $e->getMessage());
}

// 10) Redirection de confirmation
header('Location: /confirmationContact?success=1');
exit;
