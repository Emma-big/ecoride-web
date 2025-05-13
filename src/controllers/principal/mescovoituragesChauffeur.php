<?php
// src/controllers/principal/mescovoituragesChauffeur.php

// --- Contexte : ---
// public/utilisateur.php a déjà démarré la session,
// chargé BASE_PATH, Dotenv, initialisé $pdo via src/config.php
// et défini $_SESSION['user']['utilisateur_id'] dans $uid.

// 1) Récupérer l’ID du chauffeur
$uid = (int)($_SESSION['user']['utilisateur_id'] ?? 0);

// 2) Pagination
$pageCh   = isset($_GET['pageCh']) && ctype_digit($_GET['pageCh']) ? (int)$_GET['pageCh'] : 1;
$limitCh  = 5;
$offsetCh = ($pageCh - 1) * $limitCh;

// 3) Nombre total de trajets
$stmtCnt = $pdo->prepare('SELECT COUNT(*) FROM covoiturage WHERE utilisateur = :uid');
$stmtCnt->execute([':uid' => $uid]);
$totalCh = (int)$stmtCnt->fetchColumn();
$pagesCh = (int)ceil($totalCh / $limitCh);

// 4) Requête paginée
$stmt = $pdo->prepare(
    'SELECT c.covoiturage_id,
            c.lieu_depart,
            c.lieu_arrive,
            c.date_depart,
            c.heure_depart,
            c.statut_id,
            s.libelle AS statut_libelle
       FROM covoiturage c
  LEFT JOIN statuts s ON s.statut = c.statut_id
      WHERE c.utilisateur = :uid
   ORDER BY c.date_depart DESC, c.heure_depart DESC
   LIMIT :lim OFFSET :off'
);
$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
$stmt->bindValue(':lim', $limitCh, PDO::PARAM_INT);
$stmt->bindValue(':off', $offsetCh, PDO::PARAM_INT);
$stmt->execute();
$rides = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5) Affichage
?>

<?php if (empty($rides)): ?>
    <p class="text-center text-muted">Vous n’avez pas encore créé de covoiturage.</p>
<?php else: ?>
    <div class="list-group mb-4">
        <?php foreach ($rides as $ride): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>#<?= (int)$ride['covoiturage_id'] ?></strong>
                    : <?= htmlspecialchars($ride['lieu_depart'], ENT_QUOTES) ?>
                    →
                    <?= htmlspecialchars($ride['lieu_arrive'], ENT_QUOTES) ?>
                    <br>
                    <small>
                        <?= (new DateTime($ride['date_depart']))->format('d/m/Y') ?>
                        à
                        <?= (new DateTime($ride['heure_depart']))->format('H\hi') ?>
                        <span class="badge bg-info ms-2">
                            <?= htmlspecialchars($ride['statut_libelle'], ENT_QUOTES) ?>
                        </span>
                    </small>
                </div>
                <div>
                    <?php if ($ride['statut_libelle'] === 'En attente'): ?>
                        <form action="/covoiturageDemarrer" method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= (int)$ride['covoiturage_id'] ?>">
                            <button class="btn btn-success btn-sm">Démarrer</button>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                        </form>
                    <?php elseif ($ride['statut_libelle'] === 'En cours'): ?>
                        <form action="/covoiturageTerminer" method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= (int)$ride['covoiturage_id'] ?>">
                            <button class="btn btn-warning btn-sm">Arrivée à destination</button>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                        </form>
                    <?php endif; ?>

                    <?php if (!in_array($ride['statut_id'], [2, 9], true)): ?>
                        <form action="/covoiturageAnnuler" method="POST" class="d-inline ms-1"
                              onsubmit="return confirm('Êtes-vous sûr d’annuler ce covoiturage ?');">
                            <input type="hidden" name="id" value="<?= (int)$ride['covoiturage_id'] ?>">
                            <button class="btn btn-danger btn-sm">Annuler</button>
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($pagesCh > 1): ?>
        <nav aria-label="Pagination chauffeur">
            <ul class="pagination justify-content-center">
                <?php for ($p = 1; $p <= $pagesCh; $p++): ?>
                    <li class="page-item<?= $p === $pageCh ? ' active' : '' ?>">
                        <a class="page-link" href="?pageCh=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
