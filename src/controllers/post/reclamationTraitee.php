<?php
namespace Adminlocal\EcoRide\Controllers\Post;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger .env (pour les identifiants SMTP)
$dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// 3) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

// 4) Sécurité : seul un employé (role = 2) peut accéder
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 2) {
    http_response_code(403);
    exit('Accès interdit');
}

// 5) Récupérer l’ID de la réclamation depuis le POST
$rid = (int) ($_POST['reclamation_id'] ?? 0);
if ($rid <= 0) {
    header('Location: /reclamations-problemes');
    exit;
}

// 6) Mettre à jour le statut en base MySQL (7 = « Prise en charge en cours »)
$stmt = $pdo->prepare("UPDATE reclamations SET statut_id = 7 WHERE reclamation_id = :rid");
$stmt->execute([':rid' => $rid]);

// 7) Récupérer l’email du chauffeur et l’ID du covoiturage
$stmtMail = $pdo->prepare("
    SELECT u.email, r.covoiturage_id
      FROM reclamations r
      JOIN utilisateurs u
        ON u.utilisateur_id = r.utilisateur_concerne
     WHERE r.reclamation_id = :rid
");
$stmtMail->execute([':rid' => $rid]);

// 8) Envoyer l’e-mail si on a bien trouvé l’adresse
if ($row = $stmtMail->fetch(\PDO::FETCH_ASSOC)) {
    $to       = $row['email'];
    $covoitId = (int) $row['covoiturage_id'];

    $smtpHost    = $_ENV['MAIL_HOST']            ?? '';
    $smtpPort    = $_ENV['MAIL_PORT']            ?? '';
    $smtpUser    = $_ENV['MAIL_USERNAME']        ?? '';
    $smtpPass    = $_ENV['MAIL_PASSWORD']        ?? '';
    $fromAddress = $_ENV['MAIL_FROM_ADDRESS']    ?? 'no-reply@ecoride.local';
    $fromName    = $_ENV['MAIL_FROM_NAME']       ?? 'EcoRide';

    $mail = new PHPMailer(true);
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';
    try {
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $smtpPort;

        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = "Réclamation prise en charge – trajet #{$covoitId}";
        $mail->Body    = "
            <!doctype html>
            <html lang='fr'>
            <head><meta charset='UTF-8'></head>
            <body>
              <p>Bonjour,</p>
              <p>Un passager a émis une réclamation pour le trajet <strong>#{$covoitId}</strong>. Cette réclamation a été prise en charge par un employé.</p>
              <p>Merci de nous contacter au plus vite à contact@ecoride.com afin de régulariser cette situation et permettre la mise à jour de votre crédit.</p>
              <p>Cordialement,<br>L’équipe EcoRide</p>
            </body>
            </html>
        ";

        $mail->send();
    } catch (Exception $e) {
        // Si l'envoi échoue, on ignore l'erreur pour ne pas bloquer le flow
    }
}

// 9) Redirection vers la liste pour voir le nouveau bouton « Problème résolu »
header('Location: /reclamations-problemes');
exit;
