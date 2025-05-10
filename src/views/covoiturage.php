<?php
// src/views/covoiturage.php — avec pagination

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Variables pour le layout
$hideTitle      = true;
$extraStyles    = ['/assets/style/styleIndex.css'];

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

$trajets    = [];
$next_date  = null;

// Pagination
$page   = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = 5;
$offset = ($page - 1) * $limit;
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
<div class="container my-3">  
<section class="my-5">
   <h2 class="text-center mb-4">Rechercher un covoiturage</h2>
   <?php require BASE_PATH . '/src/views/barreRecherche.php'; ?>
    
    <?php if ($departRaw && $arriveeRaw && $dateRaw): ?>
        <!-- Affinage des résultats -->
        <div class="mb-4">
            <h3>Affiner les résultats</h3>
            <form action="/covoiturage" method="get" class="row g-3 align-items-end" novalidate>
                <input type="hidden" name="depart" value="<?= htmlspecialchars($departRaw, ENT_QUOTES) ?>">
                <input type="hidden" name="arrivee" value="<?= htmlspecialchars($arriveeRaw, ENT_QUOTES) ?>">
                <input type="hidden" name="date" value="<?= htmlspecialchars($dateRaw, ENT_QUOTES) ?>">
                <div class="col-md-3 form-check">
                    <input class="form-check-input" type="checkbox" id="ecologique" name="ecologique" <?= $eco ? 'checked' : '' ?>>
                    <label class="form-check-label" for="ecologique">Voyage écologique (voiture électrique)</label>
                </div>
                <div class="col-md-3">
                    <label for="max_price" class="form-label">Prix max (crédits)</label>
                    <input type="number" step="0.01" id="max_price" name="max_price" class="form-control" value="<?= htmlspecialchars($maxPrice, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-3">
                    <label for="max_duration" class="form-label">Durée max (min)</label>
                    <input type="number" id="max_duration" name="max_duration" class="form-control" value="<?= htmlspecialchars($maxDur, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-3">
                    <label for="min_rating" class="form-label">Note min (/5)</label>
                    <input type="number" step="0.1" min="0" max="5" id="min_rating" name="min_rating" class="form-control" value="<?= htmlspecialchars($minRating, ENT_QUOTES) ?>">
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-secondary">Appliquer</button>
                </div>
            </form>
        </div>

        <!-- Affichage des trajets -->
        <?php if ($trajets): ?>
            <h3 class="mb-4 text-center">Itinéraires disponibles</h3>
            <div class="list-group">
                <?php foreach ($trajets as $t): ?>
                    <?php
                        $d1    = new DateTime("{$t['date_depart']} {$t['heure_depart']}");
                        $d2    = new DateTime("{$t['date_arrive']} {$t['heure_arrive']}");
                        $diff  = $d1->diff($d2);
                        $duree = "{$diff->h}h" . str_pad($diff->i,2,'0',STR_PAD_LEFT) . "min";

                        $isLogged   = !empty($_SESSION['user']);
                        $userCredit = (float)($_SESSION['user']['credit'] ?? 0);
                        $price      = (float)$t['prix_personne'];
                        $hasSeats   = ((int)$t['nb_place'] > 0);
                    ?>
                    <div class="list-group-item mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <img src="/assets/images/<?= htmlspecialchars($t['photo'] ?: 'default.png', ENT_QUOTES) ?>" class="rounded-circle me-3" width="50" height="50" alt="Profil <?= htmlspecialchars($t['pseudo'], ENT_QUOTES) ?>">
                            <div class="flex-grow-1">
                <h6 class="mb-0"><?= htmlspecialchars($t['pseudo'], ENT_QUOTES) ?></h6>
                <small class="text-muted">Note : <?= number_format($t['note_moyenne'],1) ?>/5</small>
              </div>
              <span class="badge <?= $t['ecologique'] ? 'bg-success' : 'bg-secondary' ?> ms-3"><?= $t['ecologique'] ? 'Écologique' : 'Standard' ?></span>
            </div>
            <p class="mb-1"><strong>Places :</strong> <?= (int)$t['nb_place'] ?></p>
            <p class="mb-1"><strong>Prix :</strong> <?= $price ?> crédits</p>
            <p class="mb-1"><strong>Départ :</strong> <?= (new DateTime($t['date_depart']))->format('d/m/Y') ?> à <?= (new DateTime($t['heure_depart']))->format('H\hi') ?></p>
            <p class="mb-1"><strong>Arrivée :</strong> <?= (new DateTime($t['date_arrive']))->format('d/m/Y') ?> à <?= (new DateTime($t['heure_arrive']))->format('H\hi') ?></p>
            <p class="mb-1"><strong>Durée :</strong> <?= $duree ?></p>
            <div class="text-end">
              <a href="/detail-covoiturage?id=<?= (int)$t['covoiturage_id'] ?>" class="btn btn-outline-primary btn-sm me-2">Détail</a>
              <?php if (!$isLogged): ?>
                <a href="/login" class="btn btn-outline-primary btn-sm">Se connecter</a>
              <?php elseif (!$hasSeats): ?>
                <button class="btn btn-outline-secondary btn-sm" disabled>Complet</button>
              <?php elseif ($isLogged && $hasSeats && $userCredit >= $price): ?>
                <form action="/participerCovoiturage" method="POST" class="d-inline" onsubmit="return confirm('Confirmez l’utilisation de <?= $price ?> crédits ?');">
                  <input type="hidden" name="id" value="<?= (int)$t['covoiturage_id'] ?>">
                  <button class="btn btn-success btn-sm">Participer</button>
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                </form>
              <?php elseif ($isLogged && $userCredit < $price): ?>
                <button class="btn btn-outline-warning btn-sm" disabled>Crédits insuffisants : <?= $userCredit ?> / <?= $price ?></button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
        <nav aria-label="Pagination covoiturages" class="mt-4">
          <ul class="pagination justify-content-center">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                <a class="page-link" href="?depart=<?= urlencode($departRaw) ?>&arrivee=<?= urlencode($arriveeRaw) ?>&date=<?= urlencode($dateRaw) ?>&page=<?= $p ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>

    <?php elseif ($next_date): ?>
      <div class="alert alert-info text-center">
        Aucun trajet le <strong><?= (new DateTime($dateRaw))->format('d/m/Y') ?></strong>.<br>
        Prochain : <strong><?= (new DateTime($next_date))->format('d/m/Y') ?></strong>.<br>
        <a href="/covoiturage?depart=<?= urlencode($departRaw) ?>&amp;arrivee=<?= urlencode($arriveeRaw) ?>&amp;date=<?= urlencode($next_date) ?>" class="btn btn-sm btn-primary mt-2">Voir</a>
      </div>
    <?php else: ?>
      <p class="text-center fst-italic">Aucun covoiturage ne correspond.</p>
    <?php endif; ?>

  <?php endif; ?>
</section>
<?php
$mainContent = ob_get_clean();
require_once BASE_PATH . '/src/layout.php';
?>
