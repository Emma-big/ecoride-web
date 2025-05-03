<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

// 1) Démarrage de la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Chargement de la connexion PDO
require_once BASE_PATH . '/config/database.php';

// 3) Récupération de l'ID utilisateur en session
$utilisateur = $_SESSION['user']['utilisateur_id'] ?? null;
if (!$utilisateur) {
    echo '<p>Utilisateur non connecté.</p>';
    return;
}

// 4) Chargement des infos
$sql = "
    SELECT nom, prenom, email, telephone,
           date_naissance, photo, pseudo, credit
      FROM utilisateurs
     WHERE utilisateur_id = :id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $utilisateur]);
$user = $stmt->fetch(\PDO::FETCH_ASSOC);

if (!$user) {
    echo '<p>Aucune information disponible.</p>';
    return;
}

// 5) Préparation de la photo de profil
$filename = $user['photo'] ?: 'default.png';
$src      = '/assets/images/' . htmlspecialchars($filename, ENT_QUOTES);
?>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
      <div class="card mb-4 shadow-sm">
        <div class="card-header text-center bg-primary text-white">
          <h4 class="mb-0">Mes Informations</h4>
        </div>
        <div class="card-body text-center p-4">
          <img src="<?= $src ?>"
               alt="Photo de <?= htmlspecialchars($user['pseudo'], ENT_QUOTES) ?>"
               class="img-fluid rounded-circle mb-3 profile-img">

          <h5 class="mb-3"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom'], ENT_QUOTES) ?></h5>
          <p class="mb-2 text-white"><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo'], ENT_QUOTES) ?></p>
          <p class="mb-2"><strong>Email :</strong> <?= htmlspecialchars($user['email'], ENT_QUOTES) ?></p>
          <p class="mb-2"><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone'], ENT_QUOTES) ?></p>
          <p class="mb-2">
            <strong>Date de naissance :</strong>
            <?= htmlspecialchars(date('d-m-Y', strtotime($user['date_naissance'])), ENT_QUOTES) ?>
          </p>
          <p class="mb-0"><strong>Crédits :</strong> <?= number_format($user['credit'], 2) ?> crédits</p>
          <p>*Pour toute demande de modification de vos informations personnelles, merci de joindre contact@ecoride.com.
          </p> 
        </div>
      </div>
    </div>
  </div>
</div>
