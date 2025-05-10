<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

 

// 1) Vérification du rôle employé (role = 2)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 2) {
    http_response_code(403);
    exit('Accès interdit');
}

// 2) Charger la BDD
$pdo = require BASE_PATH . '/src/config.php';

// 3) Récupérer les avis à valider (statut_id = 4)
$sql = "
SELECT
  n.note_id,
  n.note,
  n.commentaire,
  u1.pseudo       AS passager,
  u2.pseudo       AS chauffeur,
  c.covoiturage_id
FROM notes n
JOIN utilisateurs u1 ON u1.utilisateur_id = n.passager_id
JOIN utilisateurs u2 ON u2.utilisateur_id = n.chauffeur_id
JOIN covoiturage c  ON c.covoiturage_id   = n.covoiturage_id
WHERE n.statut_id = 4
ORDER BY n.note_id DESC
";
$stmt = $pdo->query($sql);
$avisAVerifier = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// 4) Afficher la vue via le layout
$pageTitle   = 'Modération des avis - EcoRide';
$hideTitle   = false;
$extraStyles = ['/assets/style/styleIndex.css'];
$mainView    = 'views/notesAVerifier.php';

require_once BASE_PATH . '/src/layout.php';
