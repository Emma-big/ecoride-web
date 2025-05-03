<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

 

// 1) On suppose $a_valider et $termine fournis par le contrôleur parent
if (!isset($a_valider) || !is_array($a_valider)) {
    $a_valider = [];
}
if (!isset($termine) || !is_array($termine)) {
    $termine = [];
}
?>
<h2 class="text-center mb-4">Mes covoiturages (Chauffeur)</h2>

<section class="my-5">
  <h2 class="h2statut text-center mb-4">À Valider</h2>
  <div class="container"><div class="row">
    <?php if ($a_valider): ?>
      <?php foreach ($a_valider as $c): ?>
        <div class="col-md-4 mb-4">
          <div class="card shadow h-100">
            <div class="card-body">
              <!-- détails du covoiturage <?= htmlspecialchars($c['covoiturage_id'], ENT_QUOTES) ?> -->
            </div>
            <?php if (($c['total_votes'] ?? 0) === ($c['total_passagers'] ?? 0)): ?>
            <div class="card-footer text-center">
              <form method="post" action="/covoiturageStatutsSwitch">
                <input type="hidden" name="id" value="<?= htmlspecialchars($c['covoiturage_id'], ENT_QUOTES) ?>">
                <button class="btn btn-success">Valider</button>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
              </form>
            </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">Aucun covoiturage à valider pour le moment.</p>
    <?php endif; ?>
  </div></div>
</section>

<section class="my-5">
  <h2 class="h2statut text-center mb-4">Terminé</h2>
  <div class="container"><div class="row">
    <?php if ($termine): ?>
      <?php foreach ($termine as $c): ?>
        <div class="col-md-4 mb-4">
          <div class="card shadow h-100">
            <div class="card-body">
              <!-- détails du covoiturage terminé <?= htmlspecialchars($c['covoiturage_id'], ENT_QUOTES) ?> -->
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">Aucun covoiturage terminé pour le moment.</p>
    <?php endif; ?>
  </div></div>
</section>
