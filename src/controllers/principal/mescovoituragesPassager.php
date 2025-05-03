<?php
// src/controllers/principal/mescovoituragesPassager.php

use Adminlocal\EcoRide\Helpers\MongoHelper;

// Protection
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Charger la config PDO
require_once BASE_PATH . '/config/database.php';

$uid = (int) ($_SESSION['user']['utilisateur_id'] ?? 0);

// Pagination Passager
$pagePs   = isset($_GET['pagePs']) && ctype_digit($_GET['pagePs']) ? (int)$_GET['pagePs'] : 1;
$limitPs  = 5;
$offsetPs = ($pagePs - 1) * $limitPs;

// Nombre total réservations
$cntPs = $pdo->prepare(
    'SELECT COUNT(*)
       FROM reservations r
       JOIN covoiturage c ON c.covoiturage_id = r.covoiturage_id
      WHERE r.utilisateur_id = :uid'
);
$cntPs->execute(['uid' => $uid]);
$totalPs = (int)$cntPs->fetchColumn();
$pagesPs = (int)ceil($totalPs / $limitPs);

// Requête paginée
$stmt = $pdo->prepare(
    'SELECT c.covoiturage_id, c.lieu_depart, c.lieu_arrive,
            r.date_reservation, r.prix AS prix_paye,
            c.statut_id, s.libelle AS statut_libelle
       FROM reservations r
       JOIN covoiturage c ON c.covoiturage_id = r.covoiturage_id
  LEFT JOIN statuts s ON s.statut = c.statut_id
      WHERE r.utilisateur_id = :uid
   ORDER BY r.date_reservation DESC
   LIMIT :lim OFFSET :off'
);
$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
$stmt->bindValue(':lim', $limitPs, PDO::PARAM_INT);
$stmt->bindValue(':off', $offsetPs, PDO::PARAM_INT);
$stmt->execute();
$trajetsPassager = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Affichage
?>
<?php if (empty($trajetsPassager)): ?>
    <p class="text-center text-muted">Aucune réservation.</p>
<?php else: ?>
    <div class="list-group mb-4">
        <?php foreach ($trajetsPassager as $tp): ?>
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                    <strong>#<?= (int)$tp['covoiturage_id'] ?></strong> :
                    <?= htmlspecialchars($tp['lieu_depart'], ENT_QUOTES) ?> → <?= htmlspecialchars($tp['lieu_arrive'], ENT_QUOTES) ?>
                    <br>
                    <small class="text-muted">
                        <?= (new DateTime($tp['date_reservation']))->format('d/m/Y H\hi') ?>
                        — <?= htmlspecialchars($tp['statut_libelle'], ENT_QUOTES) ?>
                        — <?= number_format($tp['prix_paye'], 2) ?> crédits
                    </small>
                </div>
                <div>
                    <a href="/detail-covoiturage?id=<?= (int)$tp['covoiturage_id'] ?>" class="btn btn-sm btn-outline-primary">Détail</a>
                    <?php if (!in_array($tp['statut_id'], [2, 9], true)): ?>
                        <form action="/covoiturageAnnuler" method="POST" class="d-inline ms-1" onsubmit="return confirm('Annuler votre participation ?');">
                            <input type="hidden" name="id" value="<?= (int)$tp['covoiturage_id'] ?>">
                            <button class="btn btn-warning btn-sm">Annuler ma place</button>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($pagesPs > 1): ?>
        <nav aria-label="Pagination passager">
            <ul class="pagination justify-content-center">
                <?php for ($p = 1; $p <= $pagesPs; $p++): ?>
                    <li class="page-item<?= $p === $pagePs ? ' active' : '' ?>">
                        <a class="page-link" href="?pagePs=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
