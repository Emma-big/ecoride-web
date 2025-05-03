<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Session & auth
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

// 2) POST only + CSRF
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || empty($_POST['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(400);
    exit('Requête non autorisée.');
}

// 3) Charger PDO
require_once BASE_PATH . '/config/database.php';

// 4) Nettoyer et valider les IDs
$uid = (int) ($_SESSION['user']['utilisateur_id'] ?? 0);
$id  = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($uid <= 0 || $id <= 0) {
    $_SESSION['flash_error'] = 'Données invalides.';
    header('Location: /utilisateur');
    exit;
}

// 5) Récupérer le trajet
$stmt = $pdo->prepare(
    "SELECT utilisateur AS chauffeur_id, prix_personne
       FROM covoiturage
      WHERE covoiturage_id = ?"
);
$stmt->execute([$id]);
$ride = $stmt->fetch(\PDO::FETCH_ASSOC);
if (!$ride) {
    $_SESSION['flash_error'] = 'Covoiturage introuvable.';
    header('Location: /utilisateur');
    exit;
}
$isDriver = ((int)$ride['chauffeur_id'] === $uid);

if ($isDriver) {
    // a) Rembourser et notifier chaque passager
    $pStmt = $pdo->prepare(
        "SELECT r.utilisateur_id AS passager_id, r.prix AS prix_paye, u.email, u.pseudo
           FROM reservations r
           JOIN utilisateurs u ON u.utilisateur_id = r.utilisateur_id
          WHERE r.covoiturage_id = ?"
    );
    $pStmt->execute([$id]);
    $passagers = $pStmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($passagers as $p) {
        // remboursement
        $pdo->prepare("UPDATE utilisateurs SET credit = credit + ? WHERE utilisateur_id = ?")
            ->execute([$p['prix_paye'], $p['passager_id']]);
        // (envoi de mail éventuel)
    }
    // b) Supprimer les réservations
    $pdo->prepare("DELETE FROM reservations WHERE covoiturage_id = ?")
        ->execute([$id]);
    // c) Marquer comme annulé
    $pdo->prepare("UPDATE covoiturage SET statut_id = 9 WHERE covoiturage_id = ?")
        ->execute([$id]);
} else {
    // passager annule
    $res = $pdo->prepare(
        "SELECT prix FROM reservations WHERE covoiturage_id = ? AND utilisateur_id = ?"
    );
    $res->execute([$id, $uid]);
    $r = $res->fetch(\PDO::FETCH_ASSOC);
    if ($r) {
        // remboursement
        $pdo->prepare("UPDATE utilisateurs SET credit = credit + ? WHERE utilisateur_id = ?")
            ->execute([$r['prix'], $uid]);
        // supprimer la réservation
        $pdo->prepare(
            "DELETE FROM reservations WHERE covoiturage_id = ? AND utilisateur_id = ?"
        )->execute([$id, $uid]);
        // libérer une place
        $pdo->prepare("UPDATE covoiturage SET nb_place = nb_place + 1 WHERE covoiturage_id = ?")
            ->execute([$id]);
    }
}

// 6) Redirection finale
$_SESSION['flash'] = 'Action effectuée.';
header('Location: /utilisateur');
exit;