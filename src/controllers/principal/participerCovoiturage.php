<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

use Adminlocal\EcoRide\Services\PaymentService; // <- import du service
use \DateTime;

// 1) Session + gestion de l'inactivité
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

// 3) Récupérer l'ID du covoiturage
$covoitId = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
if ($covoitId <= 0) {
    http_response_code(400);
    exit('ID invalide');
}

// 4) Charger le covoiturage (avec date et heure pour la vérification)
$stmt = $pdo->prepare("
    SELECT nb_place, prix_personne, utilisateur AS conducteur_id,
           date_depart, heure_depart
      FROM covoiturage
     WHERE covoiturage_id = ?
");
$stmt->execute([$covoitId]);
$covoit = $stmt->fetch(\PDO::FETCH_ASSOC);
if (!$covoit) {
    http_response_code(404);
    exit('Trajet introuvable.');
}

// 4.1) Empêcher la participation si la date du trajet est dépassée
$dtDepart = new DateTime("{$covoit['date_depart']} {$covoit['heure_depart']}");
if ($dtDepart < new DateTime()) {
    $_SESSION['flash_error'] = "Vous ne pouvez pas participer à un trajet déjà passé.";
    header("Location: /detail-covoiturage?id={$covoitId}");
    exit;
}

// 5) Vérifier la connexion
if (empty($_SESSION['user']['utilisateur_id'])) {
    $_SESSION['flash_error'] = "Vous devez être connecté pour participer.";
    header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
$userId = (int) $_SESSION['user']['utilisateur_id'];

// 6) Empêcher le chauffeur de réserver son propre trajet
if ($covoit['conducteur_id'] === $userId) {
    $_SESSION['flash_error'] = "Vous ne pouvez pas participer à votre propre trajet.";
    header("Location: /detail-covoiturage?id={$covoitId}");
    exit;
}

// 7) Vérifier disponibilité des places
if ((int)$covoit['nb_place'] < 1) {
    $_SESSION['flash_error'] = "Plus de places disponibles.";
    header("Location: /detail-covoiturage?id={$covoitId}");
    exit;
}

// 8) Vérifier le crédit de l'utilisateur
$userCredit = (float)($_SESSION['user']['credit'] ?? 0);
$price      = (float)$covoit['prix_personne'];
if ($userCredit < $price) {
    $_SESSION['flash_error'] = "Crédit insuffisant.";
    header("Location: /detail-covoiturage?id={$covoitId}");
    exit;
}

// 9) Afficher la confirmation si nécessaire
if (empty($_POST['confirm'])) {
    // 9.1) Variables pour le layout
    $pageTitle   = 'Confirmation de participation - EcoRide';
    $hideTitle   = true;
    $extraStyles = [
      '/assets/style/styleFormLogin.css',
      '/assets/style/styleIndex.css',
    ];

    $GLOBALS['price']    = $price;
    $GLOBALS['covoitId'] = $covoitId;

    ob_start();
    require BASE_PATH . '/src/views/confirm-participation.php';
    $mainContent = ob_get_clean();
    require_once BASE_PATH . '/src/layout.php';
    exit;
}

// 10) Traitement après confirmation
$paymentService = new PaymentService($pdo);

try {
    $pdo->beginTransaction();

    // 10.1 Insérer la réservation
    $pdo->prepare("
      INSERT INTO reservations (covoiturage_id, utilisateur_id, prix, date_reservation)
      VALUES (?, ?, ?, NOW())
    ")->execute([$covoitId, $userId, $price]);

    // 10.2 Décrémenter le nombre de places
    $pdo->prepare("
      UPDATE covoiturage
         SET nb_place = nb_place - 1
       WHERE covoiturage_id = ?
    ")->execute([$covoitId]);

    // 10.3 Traiter le paiement et historiser
    $paymentService->processRidePayment(
        $covoitId,
        $userId,
        $covoit['conducteur_id'],
        $price
    );

    // 10.4 Mettre à jour le crédit en session
    $_SESSION['user']['credit'] = $userCredit - $price;

    $pdo->commit();

    $_SESSION['flash'] = "Participation confirmée !";
    header("Location: /detail-covoiturage?id={$covoitId}");
    exit;

} catch (\Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_error'] = "Erreur lors de la participation : " . $e->getMessage();
    header("Location: /detail-covoiturage?id={$covoitId}");
    exit;
}
