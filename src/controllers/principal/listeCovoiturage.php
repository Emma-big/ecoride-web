<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

 

// 1) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

// 2) Récupération des filtres
$villeDepart  = trim($_GET['depart']   ?? '');
$villeArrivee = trim($_GET['arrivee']  ?? '');
$date         = $_GET['date']         ?? '';

// 3) Construction de la requête
$sql = "
    SELECT
        c.covoiturage_id,
        c.lieu_depart,
        c.lieu_arrive,
        c.date_depart,
        c.heure_depart,
        c.date_arrive,
        c.heure_arrive,
        c.nb_place,
        CASE WHEN LOWER(en.libelle) = 'electrique' THEN 1 ELSE 0 END AS ecologique,
        c.prix_personne,
        u.pseudo,
        u.photo,
        COALESCE(AVG(n.note), 0) AS note_moyenne
      FROM covoiturage AS c
      JOIN voitures      AS v ON v.voiture_id     = c.voiture_id
      JOIN energies      AS en ON en.energie_id    = v.energie
      JOIN utilisateurs   AS u ON u.utilisateur_id = c.utilisateur
 LEFT JOIN notes          AS n ON n.covoiturage_id = c.covoiturage_id
     WHERE 1 = 1
";
$params = [];
if ($villeDepart !== '') {
    $sql .= " AND c.lieu_depart LIKE :depart";
    $params[':depart'] = "%{$villeDepart}%";
}
if ($villeArrivee !== '') {
    $sql .= " AND c.lieu_arrive LIKE :arrivee";
    $params[':arrivee'] = "%{$villeArrivee}%";
}
if ($date !== '') {
    $sql .= " AND c.date_depart = :date";
    $params[':date'] = $date;
}

$sql .= "
    GROUP BY c.covoiturage_id
    ORDER BY c.date_depart ASC, c.heure_depart ASC
";

// 4) Exécution
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$covoiturages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// 5) Affichage de la vue
require_once BASE_PATH . '/src/views/list-covoiturage.php';
