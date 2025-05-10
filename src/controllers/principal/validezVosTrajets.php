<?php
// src/controllers/principal/validezVosTrajets.php

use Adminlocal\EcoRide\Helpers\MongoHelper;

// Protection
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Chargement PDO
require_once __DIR__ . '/../../../config/database.php';

$uid = (int) ($_SESSION['user']['utilisateur_id'] ?? 0);

$stmt = $pdo->prepare(
    'SELECT c.covoiturage_id, c.lieu_depart, c.lieu_arrive, 
            c.date_depart, c.heure_depart, c.prix_personne
       FROM covoiturage c
       JOIN reservations r ON r.covoiturage_id = c.covoiturage_id
      WHERE r.utilisateur_id = :uid
        AND c.statut_id = 2
        AND NOT EXISTS (
              SELECT 1 FROM notes n
               WHERE n.covoiturage_id = c.covoiturage_id
                 AND n.passager_id = :uid2
          )
        AND NOT EXISTS (
              SELECT 1 FROM reclamations rc
               WHERE rc.covoiturage_id = c.covoiturage_id
                 AND rc.utilisateur_id = :uid3
          )'
);
$stmt->execute(['uid' => $uid, 'uid2' => $uid, 'uid3' => $uid]);
$toConfirm = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Affichage
?>

<?php if (!empty($toConfirm)): ?>
    <h2 class="mt-4">Validez vos trajets</h2>
    <div class="list-group mb-4">
        <?php foreach ($toConfirm as $trajet): ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Trajet #<?= (int)$trajet['covoiturage_id'] ?></strong> :
                        <?= htmlspecialchars($trajet['lieu_depart'], ENT_QUOTES) ?> → <?= htmlspecialchars($trajet['lieu_arrive'], ENT_QUOTES) ?><br>
                        <small>
                            Départ : <?= (new DateTime($trajet['date_depart'] . ' ' . $trajet['heure_depart']))->format('d/m/Y H\hi') ?> –
                            <?= number_format($trajet['prix_personne'], 2) ?> crédits
                        </small>
                    </div>
                    <div>
                        <a href="/confirmerTrajet?id=<?= (int)$trajet['covoiturage_id'] ?>&ok=1" class="btn btn-success btn-sm me-1">Tout OK</a>
                        <a href="/confirmerTrajet?id=<?= (int)$trajet['covoiturage_id'] ?>&ok=0" class="btn btn-danger btn-sm">Signaler un problème</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
