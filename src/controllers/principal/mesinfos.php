<?php
// src/controllers/principal/mesinfos.php
// Affichage des infos utilisateur avec avatar selon rôle et sexe

// 0) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 1) Charger la connexion PDO
$pdo = require_once __DIR__ . '/../../config.php';

// 2) Récupérer l’utilisateur à jour depuis la BDD
if (empty($_SESSION['user']['id'])) {
    header('Location: /login.php');
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = :id');
$stmt->execute(['id' => $_SESSION['user']['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// 3) Normaliser le sexe selon la colonne `choix` (valeurs “Homme”/“Femme”)
$sexeRaw  = trim($user['choix'] ?? 'Homme');
$sexeNorm = (mb_strtolower($sexeRaw, 'UTF-8') === 'femme') ? 'Femme' : 'Homme';
$isFemale = ($sexeNorm === 'Femme');

// 4) Sélection de l’avatar par rôle et genre
switch ((int)($user['role'] ?? 0)) {
    case 1:
        $defaultAvatar = 'admin.png';
        break;
    case 2:
        $defaultAvatar = $isFemale ? 'employeF.png' : 'employe.png';
        break;
    default:
        $defaultAvatar = $isFemale ? 'femme.png' : 'homme.png';
        break;
}

// 5) URL de l’image : si l’utilisateur a uploadé une photo, on l’utilise ; sinon l’avatar par défaut
$src = '/assets/images/' . (!empty($user['photo']) ? $user['photo'] : $defaultAvatar);
?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
      <div class="card mb-4 shadow-sm">
        <div class="card-header text-center bg-primary text-white">
          <h4 class="mb-0">Mes Informations</h4>
        </div>
        <div class="card-body text-center p-4">
          <img src="<?= htmlspecialchars($src, ENT_QUOTES) ?>"
               alt="Photo de <?= htmlspecialchars($user['pseudo'] ?? '', ENT_QUOTES) ?>"
               class="img-fluid rounded-circle mb-3 profile-img">

          <h5 class="mb-3"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''), ENT_QUOTES) ?></h5>
          <p class="mb-2"><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo'] ?? '', ENT_QUOTES) ?></p>
          <p class="mb-2"><strong>Email :</strong> <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?></p>
          <?php if (!empty($user['telephone'])): ?>
            <p class="mb-2"><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone'], ENT_QUOTES) ?></p>
          <?php endif; ?>
          <?php if (!empty($user['date_naissance'])): ?>
            <p class="mb-2">
              <strong>Date de naissance :</strong>
              <?= date('d-m-Y', strtotime($user['date_naissance'])) ?>
            </p>
          <?php endif; ?>
          <p class="mb-0"><strong>Crédits :</strong> <?= number_format($user['credit'] ?? 0, 2) ?> crédits</p>
          <p class="mt-3 text-muted small">
            * Pour toute modification de vos données personnelles, merci de contacter : 
            <a href="mailto:contact@ecoride.com" class="text-black">contact@ecoride.com</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
