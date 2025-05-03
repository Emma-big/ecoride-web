<?php
// src/views/detail-covoiturage.php

 

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Variables pour le layout
$pageTitle   = "Détail du covoiturage";
$hideTitle   = true; // masque le big title
$extraStyles = ['/assets/style/styleCovoiturage.css'];

// 3) Charger la BDD + récupérer les préférences dynamiques
require_once BASE_PATH . '/config/database.php';
$stmtP = $pdo->prepare(
    "SELECT libelle 
       FROM covoiturage_preferences 
      WHERE covoiturage_id = :cid"
);
$stmtP->execute([':cid' => $covoiturage['covoiturage_id']]);
$dynamicPrefs = $stmtP->fetchAll(PDO::FETCH_COLUMN);

// 4) Calcul de la durée du trajet
$dtDepart  = new DateTime("{$covoiturage['date_depart']} {$covoiturage['heure_depart']}");
$dtArrivee = new DateTime("{$covoiturage['date_arrive']} {$covoiturage['heure_arrive']}");
$interval  = $dtDepart->diff($dtArrivee);
$heures    = $interval->h + ($interval->days * 24);
$minutes   = $interval->i;

// 5) Capture du contenu principal
ob_start();
?>
<section class="my-5">
  <h2 class="mb-4">Détail du covoiturage</h2>

  <div class="card mb-4">
    <div class="card-body d-flex gap-4">
      <img
        src="/assets/images/<?= htmlspecialchars($covoiturage['chauffeur_photo'] ?: 'default.png', ENT_QUOTES) ?>"
        alt="Photo de <?= htmlspecialchars($covoiturage['chauffeur_pseudo'], ENT_QUOTES) ?>"
        class="rounded-circle"
        width="100" height="100"
      >
      <div>
        <p><strong>Chauffeur :</strong> <?= htmlspecialchars($covoiturage['chauffeur_pseudo'], ENT_QUOTES) ?></p>
        <p>
          <strong>Trajet :</strong>
          <?= htmlspecialchars($covoiturage['lieu_depart'], ENT_QUOTES) ?> →
          <?= htmlspecialchars($covoiturage['lieu_arrive'], ENT_QUOTES) ?>
        </p>
        <p>
          <strong>Départ :</strong>
          <?= (new DateTime($covoiturage['date_depart']))->format('d/m/Y') ?>
          à <?= (new DateTime($covoiturage['heure_depart']))->format('H\hi') ?>
        </p>
        <p>
          <strong>Arrivée :</strong>
          <?= (new DateTime($covoiturage['date_arrive']))->format('d/m/Y') ?>
          à <?= (new DateTime($covoiturage['heure_arrive']))->format('H\hi') ?>
        </p>
        <p><strong>Durée du trajet :</strong> <?= $heures ?>h<?= str_pad($minutes, 2, '0', STR_PAD_LEFT) ?>min</p>
        <p><strong>Places restantes :</strong> <?= (int)$covoiturage['nb_place'] ?></p>
        <p><strong>Prix/personne :</strong> <?= htmlspecialchars($covoiturage['prix_personne'], ENT_QUOTES) ?> crédits</p>
        <p><strong>Voyage écologique :</strong> <?= $covoiturage['ecologique'] ? 'Oui' : 'Non' ?></p>
        <p>
          <strong>Préférences :</strong><br>
          Fumeurs : <?= $covoiturage['accepts_smoker'] ? 'Acceptés' : 'Non acceptés' ?><br>
          Animaux : <?= $covoiturage['accepts_animal'] ? 'Acceptés' : 'Non acceptés' ?>
        </p>
        <?php if (!empty($dynamicPrefs)): ?>
          <div class="mt-3">
            <h5>Autres préférences</h5>
            <ul class="list-group">
              <?php foreach ($dynamicPrefs as $pref): ?>
                <li class="list-group-item"><?= htmlspecialchars($pref, ENT_QUOTES) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <p>
          <strong>Voiture :</strong><br>
          Marque : <?= htmlspecialchars($covoiturage['marque'], ENT_QUOTES) ?><br>
          Modèle : <?= htmlspecialchars($covoiturage['modele'], ENT_QUOTES) ?><br>
          Énergie : <?= htmlspecialchars($covoiturage['energie_libelle'], ENT_QUOTES) ?>
        </p>
        <p><strong>Note moyenne :</strong> <?= number_format($covoiturage['note_moyenne'], 1) ?>/5</p>

      </div>
    </div>
  </div>

  <h3 class="mb-3">Avis des passagers</h3>
  <?php if (count($avis) > 0): ?>
    <ul class="list-group mb-5">
      <?php foreach ($avis as $a): ?>
        <li class="list-group-item">
          <strong><?= htmlspecialchars($a['passager'], ENT_QUOTES) ?></strong> —
          note : <?= htmlspecialchars($a['note'], ENT_QUOTES) ?>/5
          <p class="mb-0"><?= nl2br(htmlspecialchars($a['commentaire'], ENT_QUOTES)) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="fst-italic">Aucun avis disponible.</p>
  <?php endif; ?>

  <?php
    // Bouton « Participer »
    $isLogged    = !empty($_SESSION['user']['utilisateur_id']);
    $userCredit  = $isLogged ? (float)$_SESSION['user']['credit'] : 0;
    $price       = (float)$covoiturage['prix_personne'];
    $hasSeats    = ((int)$covoiturage['nb_place']) > 0;
    $isNotDriver = $isLogged && ($_SESSION['user']['utilisateur_id'] != $covoiturage['utilisateur']);
  ?>
  <div class="d-flex justify-content-between mt-4">
    <a
      href="/covoiturage?depart=<?= urlencode($covoiturage['lieu_depart']) ?>&amp;arrivee=<?= urlencode($covoiturage['lieu_arrive']) ?>&amp;date=<?= htmlspecialchars($covoiturage['date_depart'], ENT_QUOTES) ?>"
      class="btn btn-secondary"
    >
      ← Retour aux résultats
    </a>

    <?php if ($isLogged): ?>
      <?php if (!$isNotDriver): ?>
        <button class="btn btn-outline-secondary" disabled>Vous êtes le chauffeur</button>
      <?php elseif (!$hasSeats): ?>
        <button class="btn btn-outline-secondary" disabled>Complet</button>
      <?php elseif ($userCredit < $price): ?>
        <button class="btn btn-outline-warning" disabled>Crédits insuffisants</button>
      <?php else: ?>
        <form action="/participerCovoiturage" method="POST">
          <input type="hidden" name="id" value="<?= (int)$covoiturage['covoiturage_id'] ?>">
          <button class="btn btn-success">Participer (<?= $price ?> crédits)</button>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
        </form>
      <?php endif; ?>
    <?php else: ?>
      <a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-primary">
        Se connecter / créer un compte
      </a>
    <?php endif; ?>
  </div>
</section>
<?php
$mainContent = ob_get_clean();
require_once BASE_PATH . '/src/layout.php';
?>
