<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

use Adminlocal\EcoRide\Helpers\MongoHelper;

// 1) Vérification du rôle employé (role = 2)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 2) {
    http_response_code(403);
    exit('Accès interdit');
}

// 2) Charger PDO
$pdo = require BASE_PATH . '/src/config.php';

// 3) Pagination
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;

// 4) Récupérer UNIQUEMENT les statuts concernés (1,3,4,7,8),  
//    et cette fois **reclamation_id** (PK int) + **mongo_id**
$stmt = $pdo->prepare("
    SELECT reclamation_id,
           mongo_id,
           covoiturage_id,
           utilisateur_id,
           utilisateur_concerne,
           commentaire,
           statut_id,
           date_signal
      FROM reclamations
     WHERE statut_id IN (0,1,3,4,5,7,8)
  ORDER BY date_signal DESC
     LIMIT :lim OFFSET :off
");
$stmt->bindValue('lim', $perPage, \PDO::PARAM_INT);
$stmt->bindValue('off', $offset, \PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// 5) Comptage total
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM reclamations WHERE statut_id IN (0,1,3,4,5,7,8)");
$totalStmt->execute();
$total = (int)$totalStmt->fetchColumn();
$pages = (int)ceil($total / $perPage);

// 6) Enrichir chaque ligne
$reclamations = [];
foreach ($rows as $r) {
    // données covoiturage
    $stmt2 = $pdo->prepare("
        SELECT lieu_depart, date_depart,
               lieu_arrive, date_arrive,
               prix_personne
          FROM covoiturage
         WHERE covoiturage_id = ?
    ");
    $stmt2->execute([(int)$r['covoiturage_id']]);
    $covoit = $stmt2->fetch(\PDO::FETCH_ASSOC);

    // passager
    $stmt3 = $pdo->prepare("SELECT pseudo, email FROM utilisateurs WHERE utilisateur_id = ?");
    $stmt3->execute([(int)$r['utilisateur_id']]);
    $passager = $stmt3->fetch(\PDO::FETCH_ASSOC);

    // chauffeur
    $stmt4 = $pdo->prepare("SELECT pseudo, email FROM utilisateurs WHERE utilisateur_id = ?");
    $stmt4->execute([(int)$r['utilisateur_concerne']]);
    $chauffeur = $stmt4->fetch(\PDO::FETCH_ASSOC);

    // libellé statut
    $stmt5 = $pdo->prepare("SELECT libelle FROM statuts WHERE statut = ?");
    $stmt5->execute([(int)$r['statut_id']]);
    $statutLib = $stmt5->fetchColumn();

    $reclamations[] = [
        // **ici** on passe bien la PK SQL pour les forms
        'reclamation_id'   => (int)$r['reclamation_id'],
        'mongo_id'         => $r['mongo_id'],
        'covoiturage_id'   => $r['covoiturage_id'],
        'commentaire'      => $r['commentaire'],
        'date_signal'      => $r['date_signal'],
        'statut_id'        => (int)$r['statut_id'],
        'statut_libelle'   => $statutLib,
        'covoiturage'      => $covoit,
        'passager'         => $passager,
        'chauffeur'        => $chauffeur,
    ];
}

// 7) Envoi vers la vue
$pageTitle   = 'Trajets problématiques - EcoRide';
$hideTitle   = false;
$extraStyles = ['/assets/style/styleIndex.css'];
$mainView    = 'views/reclamations_problemes.php';

require_once BASE_PATH . '/src/layout.php';
exit;
