<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

// 1) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

// 2) Cas liste issue de la recherche
if (isset($_GET['depart'], $_GET['arrivee'], $_GET['date'])) {
    $depart  = $_GET['depart'];
    $arrivee = $_GET['arrivee'];
    $date    = $_GET['date'];

    $sql = <<<SQL
      SELECT
        c.covoiturage_id,
        c.lieu_depart,
        c.lieu_arrive,
        c.date_depart,
        c.heure_depart,
        c.date_arrive,
        c.heure_arrive,
        c.nb_place,
        c.prix_personne,
        CASE WHEN LOWER(en.libelle) = 'electrique' THEN 1 ELSE 0 END AS ecologique,
        u.pseudo            AS chauffeur_pseudo,
        u.photo             AS chauffeur_photo,
        COALESCE(AVG(n.note),0) AS note_moyenne
      FROM covoiturage c
      JOIN voitures      v ON v.voiture_id     = c.voiture_id
      JOIN energies      en ON en.energie_id    = v.energie
      JOIN utilisateurs  u ON u.utilisateur_id = c.utilisateur
      LEFT JOIN notes    n ON n.chauffeur_id    = c.utilisateur
      WHERE c.lieu_depart = :depart
        AND c.lieu_arrive  = :arrivee
        AND c.date_depart  = :date
        AND c.nb_place > 0
      GROUP BY c.covoiturage_id
      ORDER BY c.heure_depart ASC
    SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':depart'  => $depart,
        ':arrivee' => $arrivee,
        ':date'    => $date,
    ]);
    $rides = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $mainView    = 'views/list-covoiturage.php';
    $pageTitle   = "Propositions de covoiturage";
    $extraStyles = ['/assets/style/styleCovoiturage.css'];
    require_once BASE_PATH . '/src/layout.php';
    exit;
}

// 3) Cas détail unique
$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(404);
    exit('ID manquant');
}

$sqlDetail = <<<SQL
  SELECT
    c.covoiturage_id,
    c.utilisateur,
    c.lieu_depart,
    c.lieu_arrive,
    c.date_depart,
    c.heure_depart,
    c.date_arrive,
    c.heure_arrive,
    c.nb_place,
    c.prix_personne,
    CASE WHEN LOWER(en.libelle) = 'electrique' THEN 1 ELSE 0 END AS ecologique,
    v.modele,
    m.libelle    AS marque,
    en.libelle   AS energie_libelle,
    u.pseudo     AS chauffeur_pseudo,
    u.photo      AS chauffeur_photo,
    c.accepts_smoker,
    c.accepts_animal,
    COALESCE(AVG(n.note),0) AS note_moyenne
  FROM covoiturage   AS c
  JOIN voitures      AS v  ON v.voiture_id     = c.voiture_id
  JOIN marques       AS m  ON m.marque_id      = v.marque_id
  JOIN energies      AS en ON en.energie_id    = v.energie
  JOIN utilisateurs  AS u  ON u.utilisateur_id = c.utilisateur
  LEFT JOIN notes    AS n  ON n.chauffeur_id   = c.utilisateur
  WHERE c.covoiturage_id = ?
  GROUP BY c.covoiturage_id
SQL;

$stmt = $pdo->prepare($sqlDetail);
$stmt->execute([(int)$id]);
$covoiturage = $stmt->fetch(\PDO::FETCH_ASSOC);
if (!$covoiturage) {
    http_response_code(404);
    exit('Covoiturage introuvable');
}

// Durée du trajet
$dep      = new \DateTime("{$covoiturage['date_depart']} {$covoiturage['heure_depart']}");
$arr      = new \DateTime("{$covoiturage['date_arrive']} {$covoiturage['heure_arrive']}");
$interval = $dep->diff($arr);
$heures   = $interval->h + ($interval->days * 24);
$minutes  = $interval->i;

// 4) Avis des passagers
$driverId = (int)$covoiturage['utilisateur'];
$avisStmt = $pdo->prepare(<<<SQL
  SELECT n.note,
         n.commentaire,
         p.pseudo AS passager,
         n.covoiturage_id
    FROM notes        AS n
    JOIN utilisateurs AS p ON p.utilisateur_id = n.passager_id
   WHERE n.chauffeur_id = ?
   ORDER BY n.note DESC, n.covoiturage_id DESC
SQL
);
$avisStmt->execute([$driverId]);
$avis = $avisStmt->fetchAll(\PDO::FETCH_ASSOC);

// 5) Préférences dynamiques
$stmtP = $pdo->prepare(<<<SQL
  SELECT libelle
    FROM covoiturage_preferences
   WHERE covoiturage_id = :cid
SQL
);
$stmtP->execute([':cid' => $covoiturage['covoiturage_id']]);
$dynamicPrefs = $stmtP->fetchAll(\PDO::FETCH_COLUMN);

// 6) Passage à la vue
$mainView    = 'views/detail-covoiturage.php';
$pageTitle   = "Détail du covoiturage #{$id}";
$extraStyles = ['/assets/style/styleCovoiturage.css'];
