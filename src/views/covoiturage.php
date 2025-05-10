<?php
// src/views/covoiturage.php — avec pagination

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Variables pour le layout
$barreRecherche = 'views/barreRecherche.php';
$hideTitle   = true;
$extraStyles = ['/assets/style/styleIndex.css'];

// 3) Récupération et validation des paramètres GET
$departRaw  = trim($_GET['depart']   ?? '');
$arriveeRaw = trim($_GET['arrivee']  ?? '');
$dateRaw    = $_GET['date']         ?? '';

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

// Filtres optionnels
$eco       = isset($_GET['ecologique']);
$maxPrice  = is_numeric($_GET['max_price']   ?? null) ? floatval($_GET['max_price'])   : 0;
$maxDur    = is_numeric($_GET['max_duration']?? null) ? intval($_GET['max_duration'])   : 0;
$minRating = is_numeric($_GET['min_rating']  ?? null) ? floatval($_GET['min_rating'])   : 0;

$trajets   = [];
$next_date = null;

// Pagination
$page       = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int)$_GET['page'] : 1;
$limit      = 5;
$offset     = ($page - 1) * $limit;
$totalPages = 1;

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
        $where              .= " AND c.prix_personne <= :mp";
        $params[':mp']       = $maxPrice;
    }
    if ($maxDur > 0) {
        $where              .= " AND TIMESTAMPDIFF(
                                   MINUTE,
                                   CONCAT(c.date_depart,' ',c.heure_depart),
                                   CONCAT(c.date_arrive,' ',c.heure_arrive)
                                 ) <= :md";
        $params[':md']       = $maxDur;
    }

    // Comptage total
    $countSql  = "
        SELECT COUNT(DISTINCT c.covoiturage_id)
          FROM covoiturage c
          JOIN voitures v  ON v.voiture_id  = c.voiture_id
          JOIN energies en ON en.energie_id = v.energie
         WHERE $where
    ";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalItems = (int)$countStmt->fetchColumn();
    $totalPages = (int)ceil($totalItems / $limit);

    // Requête principale
    $sql  = "
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
            COALESCE(AVG(n.note),0) AS note_moyenne
          FROM covoiturage c
          JOIN voitures v  ON v.voiture_id  = c.voiture_id
          JOIN energies en ON en.energie_id = v.energie
          JOIN utilisateurs u ON u.utilisateur_id = c.utilisateur
     LEFT JOIN notes n  ON n.covoiturage_id = c.covoiturage_id
         WHERE $where
      GROUP BY c.covoiturage_id
    ";
    if ($minRating > 0) {
        $sql .= " HAVING note_moyenne >= :mr";
        $params[':mr'] = $minRating;
    }
    $sql .= " ORDER BY c.date_depart DESC, c.heure_depart DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($trajets)) {
        $dtStmt = $pdo->prepare(
            "SELECT MIN(date_depart) AS next_date
               FROM covoiturage
              WHERE lieu_depart LIKE :ld
                AND lieu_arrive LIKE :la
                AND date_depart > :dd"
        );
        $dtStmt->execute([':ld'=>"%{$departRaw}%",':la'=>"%{$arriveeRaw}%",':dd'=>$dateRaw]);
        $next_date = $dtStmt->fetchColumn();
    }
}

// 4) Capture du contenu principal
ob_start();
?>
<section class="my-5">
  <h2 class="text-center mb-4">Rechercher un covoiturage</h2>
  <?php require BASE_PATH . '/src/views/barreRecherche.php'; ?>

  <?php if ($departRaw && $arriveeRaw && $dateRaw): ?>
    <div class="mb-4">
      <h3>Affiner les résultats</h3>
      <form action="/covoiturage" method="get" class="row g-3 align-items-end" novalidate>
        <input type="hidden" name="depart" value="<?= htmlspecialchars($departRaw, ENT_QUOTES) ?>">
        <input type="hidden" name="arrivee" value="<?= htmlspecialchars($arriveeRaw, ENT_QUOTES) ?>">
        <input type="hidden" name="date" value="<?= htmlspecialchars($dateRaw, ENT_QUOTES) ?>">
        <!-- … reste du formulaire d’affinage … -->
      </form>
    </div>

    <!-- … affichage des trajets, pagination, etc. … -->

  <?php elseif ($next_date): ?>
    <div class="alert alert-info text-center">
      Aucun trajet le <strong><?= (new DateTime($dateRaw))->format('d/m/Y') ?></strong>.<br>
      Prochain : <strong><?= (new DateTime($next_date))->format('d/m/Y') ?></strong>.<br>
      <a href="/covoiturage?depart=<?= urlencode($departRaw) ?>&amp;arrivee=<?= urlencode($arriveeRaw) ?>&amp;date=<?= urlencode($next_date) ?>"
         class="btn btn-sm btn-primary mt-2">
        Voir
      </a>
    </div>
  <?php else: ?>
    <p class="text-center fst-italic">Aucun covoiturage ne correspond.</p>
  <?php endif; ?>
</section>
<?php
$mainContent = ob_get_clean();
