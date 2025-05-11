<?php
// src/views/covoiturage.php — avec pagination

// 1) Variables pour le layout
$barreRecherche = 'views/barreRecherche.php';
$hideTitle     = true;
$extraStyles   = ['/assets/style/styleIndex.css'];

// 2) Récupération et validation des paramètres GET
$departRaw   = trim($_GET['depart']   ?? '');
$arriveeRaw  = trim($_GET['arrivee']  ?? '');
$dateRaw     = $_GET['date']         ?? '';

// Validation adresse (5–150 caractères)
$validAddr = function(string $s): bool {
    return (bool) preg_match('/^.{5,150}$/', $s);
};
if (!$validAddr($departRaw))  $departRaw = '';
if (!$validAddr($arriveeRaw)) $arriveeRaw = '';

// Validation date YYYY-MM-DD
$dateObj = DateTime::createFromFormat('Y-m-d', $dateRaw);
if (!($dateObj && $dateObj->format('Y-m-d') === $dateRaw)) {
    $dateRaw = '';
}

// 3) Filtres optionnels
$eco        = isset($_GET['ecologique']);
$maxPrice   = is_numeric($_GET['max_price']   ?? null) ? floatval($_GET['max_price'])    : 0;
$maxDur     = is_numeric($_GET['max_duration']?? null) ? intval($_GET['max_duration'])    : 0;
$minRating  = is_numeric($_GET['min_rating']  ?? null) ? floatval($_GET['min_rating'])     : 0;

$trajets    = [];
$next_date  = null;

// 4) Pagination
$page        = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int)$_GET['page'] : 1;
$limit       = 5;
$offset      = ($page - 1) * $limit;
$totalPages  = 1;

if ($departRaw && $arriveeRaw && $dateRaw) {
    $pdo = require BASE_PATH . '/src/config.php';

    // Construction WHERE + params
    $where  = "c.lieu_depart LIKE :ld AND c.lieu_arrive LIKE :la AND c.date_depart = :dd";
    $params = [
        ':ld' => "%{$departRaw}%",
        ':la' => "%{$arriveeRaw}%",
        ':dd' => $dateRaw
    ];
    if ($eco) {
        $where .= " AND LOWER(en.libelle) = 'electrique'";
    }
    if ($maxPrice > 0) {
        $where           .= " AND c.prix_personne <= :mp";
        $params[':mp']   = $maxPrice;
    }
    if ($maxDur > 0) {
        $where           .= " AND TIMESTAMPDIFF(MINUTE,
                                    CONCAT(c.date_depart,' ',c.heure_depart),
                                    CONCAT(c.date_arrive,' ',c.heure_arrive)) <= :md";
        $params[':md']   = $maxDur;
    }

    // Comptage total
    $countSql  = "SELECT COUNT(DISTINCT c.covoiturage_id)
                   FROM covoiturage c
                   JOIN voitures v  ON v.voiture_id  = c.voiture_id
                   JOIN energies en ON en.energie_id = v.energie
                  WHERE $where";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalItems = (int) $countStmt->fetchColumn();
    $totalPages = (int) ceil($totalItems / $limit);

    // Requête principale
    $sql  = "SELECT c.covoiturage_id,
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
                     COALESCE(AVG(n.note),0) AS note_moyenne
              FROM covoiturage c
              JOIN voitures v  ON v.voiture_id  = c.voiture_id
              JOIN energies en ON en.energie_id = v.energie
              JOIN utilisateurs u ON u.utilisateur_id = c.utilisateur
         LEFT JOIN notes n  ON n.covoiturage_id = c.covoiturage_id
             WHERE $where
          GROUP BY c.covoiturage_id";
    if ($minRating > 0) {
        $sql .= " HAVING note_moyenne >= :mr";
        $params[':mr'] = $minRating;
    }
    $sql .= " ORDER BY c.date_depart DESC, c.heure_depart DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si aucun trajet, date prochaine
    if (empty($trajets)) {
        $dtStmt = $pdo->prepare("SELECT MIN(date_depart) AS next_date
                                  FROM covoiturage
                                 WHERE lieu_depart LIKE :ld
                                   AND lieu_arrive LIKE :la
                                   AND date_depart > :dd");
        $dtStmt->execute([
            ':ld' => "%{$departRaw}%",
            ':la' => "%{$arriveeRaw}%",
            ':dd' => $dateRaw
        ]);
        $next_date = $dtStmt->fetchColumn();
    }
}

// 5) Capture du contenu principal
ob_start();
?>
<section class="my-5">
  <h2 class="text-center mb-4">Rechercher un covoiturage</h2>
  <div class="container my-3">
    <?php require BASE_PATH . '/src/views/barreRecherche.php'; ?>
  </div>

  <?php if ($departRaw !== '' && $arriveeRaw !== '' && $dateRaw !== ''): ?>
    <div class="mb-4">
      <h3>Affiner les résultats</h3>
      <form action="/covoiturage" method="get" class="row g-3 align-items-end">
        <input type="hidden" name="depart"   value="<?= htmlspecialchars($departRaw) ?>">
        <input type="hidden" name="arrivee"  value="<?= htmlspecialchars($arriveeRaw) ?>">
        <input type="hidden" name="date"      value="<?= htmlspecialchars($dateRaw) ?>">

        <div class="col-md-3 form-check">
          <input class="form-check-input" type="checkbox" id="ecologique" name="ecologique" <?= $eco ? 'checked' : '' ?>>
          <label class="form-check-label" for="ecologique">Véhicule électrique uniquement</label>
        </div>

        <div class="col-md-3">
          <label for="max_price" class="form-label">Prix max (crédits)</label>
          <input type="number" step="0.01" id="max_price" name="max_price" class="form-control"
                 value="<?= htmlspecialchars($maxPrice) ?>">
        </div>

        <div class="col-md-3">
          <label for="max_duration" class="form-label">Durée max (min)</label>
          <input type="number" id="max_duration" name="max_duration" class="form-control"
                 value="<?= htmlspecialchars($maxDur) ?>">
        </div>

        <div class="col-md-3">
          <label for="min_rating" class="form-label">Note min. (0–5)</label>
          <input type="number" step="0.1" min="0" max="5" id="min_rating" name="min_rating"
                 class="form-control" value="<?= htmlspecialchars($minRating) ?>">
        </div>

        <div class="col-12 text-center mt-2">
          <button type="submit" class="btn btn-secondary">Appliquer les filtres</button>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <?php if ($departRaw !== '' && $arriveeRaw !== '' && $dateRaw !== ''): ?>
    <?php if (!empty($trajets)): ?>
      <h3 class="mb-4 text-center">Itinéraires disponibles</h3>
      <div class="list-group">
        <?php foreach ($trajets as $t): ?>
          <?php
            $d1        = new DateTime("{$t['date_depart']} {$t['heure_depart']}");
            $d2        = new DateTime("{$t['date_arrive']} {$t['heure_arrive']}");
            $diff      = $d1->diff($d2);
            $duree     = "{$diff->h}h" . str_pad($diff->i, 2, '0', STR_PAD_LEFT) . "min";
            $isLogged  = !empty($_SESSION['user']);
            $userCredit= (float)($_SESSION['user']['credit'] ?? 0);
            $price     = (float)$t['prix_personne'];
            $hasSeats  = ((int)$t['nb_place'] > 0);
          ?>
          <div class="list-group-item mb-3">
            <div class="d-flex align-items-center mb-2">
              <img src="/assets/images/<?= htmlspecialchars($t['photo'] ?: 'default.png', ENT_QUOTES) ?>" class="rounded-circle me-3" width="50" height="50" alt="Profil <?= htmlspecialchars($t['pseudo'], ENT_QUOTES) ?>">
              <div class="flex-grow-1">
                <h6 class="mb-0"><?= htmlspecialchars($t['pseudo'], ENT_QUOTES) ?></h6>
                <small class="text-muted">Note : <?= number_format($t['note_moyenne'], 1) ?>/5</small>
              </div>
              <span class="badge <?= $t['ecologique'] ? 'bg-success' : 'bg-secondary' ?> ms-3"><?= $t['ecologique'] ? 'Trajet écologique' : 'Standard' ?></span>
            </div>
            <p class="mb-1"><strong>Places :</strong> <?= (int)$t['nb_place'] ?></p>
            <p class="mb-1"><strong>Prix :</strong> <?= $price ?> crédits</p>
            <p class="mb-1"><strong>Départ :</strong> <?= (new DateTime($t['date_depart']))->format('d/m/Y') ?> à <?= (new DateTime($t['heure_depart']))->format('H\hi') ?></p>
            <p class="mb-1"><strong>Arrivée :</strong> <?= (new DateTime($t['date_arrive']))->format('d/m/Y') ?> à <?= (new DateTime($t['heure_arrive']))->format('H\hi') ?></p>
            <p class="mb-1"><strong>Durée :</strong> <?= $duree ?></p>
            <div class="text-end">   <div class="text-end">
              <a href="/detail-covoiturage?id=<?= (int)$t['covoiturage_id'] ?>"
                 class="btn btn-sm btn-primary">Détail</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php elseif ($next_date): ?>
      <div class="alert alert-info text-center">
        Aucun trajet le <strong><?= (new DateTime($date))->format('d/m/Y') ?></strong>.<br>
        Prochain covoiturage le <strong><?= (new DateTime($next_date))->format('d/m/Y') ?></strong>.<br>
        <a href="/covoiturage?depart=<?= urlencode($depart) ?>&arrivee=<?= urlencode($arrivee) ?>&date=<?= $next_date ?>"
           class="btn btn-sm btn-primary mt-2">Rechercher cette date</a>
      </div>
    <?php else: ?>
      <p class="text-center fst-italic">Aucun covoiturage ne correspond à vos critères.</p>
    <?php endif; ?>
  <?php endif; ?>
</section>