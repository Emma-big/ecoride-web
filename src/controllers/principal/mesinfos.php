<?php
// src/controllers/principal/mesinfos.php
// simple affichage des infos déjà chargées dans $user

// Génération du chemin de la photo de profil
$src = '/uploads/avatars/' . ($user['avatar'] ?? 'default.png');
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
            <a href="mailto:contact@ecoride.com">contact@ecoride.com</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
