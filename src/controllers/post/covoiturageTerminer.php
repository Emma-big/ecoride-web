<?php
namespace Adminlocal\EcoRide\Controllers\Post;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1) Charger .env
$dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// 2) Récupérer la config SMTP depuis .env
$smtpHost    = $_ENV['MAIL_HOST']     ?? '';
$smtpPort    = $_ENV['MAIL_PORT']     ?? '';
$smtpUser    = $_ENV['MAIL_USERNAME'] ?? '';
$smtpPass    = $_ENV['MAIL_PASSWORD'] ?? '';
$fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@ecoride.local';
$fromName    = $_ENV['MAIL_FROM_NAME']    ?? 'EcoRide';

// 3) Charger la BDD
require_once BASE_PATH . '/config/database.php';

// 4) Vérifier la méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Requête non autorisée.');
}

// 5) Récupérer et valider l’ID du covoiturage
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    exit('ID de covoiturage invalide.');
}

// 6) Mettre à jour le statut à “Terminer” (2)
$pdo->prepare("
    UPDATE covoiturage
       SET statut_id = 2
     WHERE covoiturage_id = :id
")->execute([':id' => $id]);

// 7) Récupérer les passagers et leurs mails
$passagersStmt = $pdo->prepare("
    SELECT DISTINCT u.email, u.pseudo
      FROM reservations r
      JOIN utilisateurs u ON u.utilisateur_id = r.utilisateur_id
     WHERE r.covoiturage_id = ?
");
$passagersStmt->execute([$id]);
$passagers = $passagersStmt->fetchAll(\PDO::FETCH_ASSOC);

// 8) Envoyer un mail à chaque passager
foreach ($passagers as $p) {
    $mail = new PHPMailer(true);
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';
    try {
        // SMTP
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $smtpPort;

        // Expéditeur & destinataire
        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($p['email'], $p['pseudo']);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = "Votre trajet #{$id} est terminé – merci de valider !";
        $mail->Body    = sprintf(
            '<!doctype html>
        <html lang="fr">
            <head><meta charset="UTF-8"></head>
            <body>
                <p>Bonjour %s,</p>
                <p>Le covoiturage <strong>#%d</strong> est désormais terminé.</p>
                <p>Pour confirmer que tout s’est bien passé et déclencher le versement des crédits au chauffeur, 
                <a href="http://ecoride.local/login">connectez-vous à votre espace</a> et validez votre participation.</p>
                <p>Merci !<br>L’équipe EcoRide</p>
            </body>
        </html>',
            htmlspecialchars($p['pseudo'], ENT_QUOTES),
            $id
        );

        $mail->send();
    } catch (Exception $e) {
        // on ignore l'erreur pour ne pas casser l'UX
    }
}

// 9) Redirection
header('Location: /utilisateur');
exit;
