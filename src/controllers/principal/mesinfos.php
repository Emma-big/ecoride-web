<?php
// src/controllers/principal/mesinfos.php
// Affichage des infos utilisateur avec avatar selon rôle et sexe

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Récupérer l'utilisateur
$user = $_SESSION['user'] ?? [];

// 3) Normalisation du genre en majuscule ('F' ou 'M')
$rawSexe = strtolower(trim($user['sexe'] ?? 'M'));
if (strpos($rawSexe, 'Femme') === 0) {
    $gender = 'F';
} else {
    $gender = 'M';
}

// 4) Choix de l'avatar par défaut selon rôle et genre
switch ((int)($user['role'] ?? 0)) {
    case 1: // Administrateur
        $defaultAvatar = 'admin.png';
        break;
    case 2: // Employé
        $defaultAvatar = ($gender === 'F') ? 'employeF.png' : 'employe.png';
        break;
    default: // Passager ou autre
        $defaultAvatar = ($gender === 'F') ? 'femme.png' : 'homme.png';
        break;
}

// 5) Utiliser la colonne photo si renseignée, sinon l'avatar par défaut
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
               alt="Photo de <?= htmlspecialchars($user['pseudo'], ENT_QUOTES) ?>"
               class="img-fluid rounded-circle mb-3 profile-img">

          <h5 class="mb-3"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom'], ENT_QUOTES) ?></h5>
          <p class="mb-2"><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo'], ENT_QUOTES) ?></p>
          <p class="mb-2"><strong>Email :</strong> <?= htmlspecialchars($user['email'], ENT_QUOTES) ?></p>
          <?php if (!empty($user['telephone'])): ?>
            <p class="mb-2"><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone'], ENT_QUOTES) ?></p>
          <?php endif; ?>
          <?php if (!empty($user['date_naissance'])): ?>
            <p class="mb-2">
              <strong>Date de naissance :</strong>
              <?= date('d-m-Y', strtotime($user['date_naissance'])) ?>
            </p>
          <?php endif; ?>
          <p class="mb-0"><strong>Crédits :</strong> <?= number_format($user['credit'], 2) ?> crédits</p>
          <p class="mt-3 text-muted small">
            * Pour toute modification de vos données personnelles, merci de contacter : 
            <a href="mailto:contact@ecoride.com" class="text-black">contact@ecoride.com</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
