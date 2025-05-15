<?php
// src/views/confirm-participation.php

// (Ne définissez ici QUE le HTML à injecter dans <main>)

// Variables passées par le controller : $price et $covoitId
?>
<div class="container py-5">
  <h3>Vous allez dépenser <?= htmlspecialchars($price, ENT_QUOTES) ?> crédits.</h3>
  <form id="confirmForm" method="POST" action="/participerCovoiturage">
    <input type="hidden" name="id"      value="<?= htmlspecialchars($covoitId, ENT_QUOTES) ?>">
    <input type="hidden" name="confirm" value="1">
    <button type="submit" class="btn btn-danger">Confirmer</button>
    <a href="/detail-covoiturage?id=<?= htmlspecialchars($covoitId, ENT_QUOTES) ?>"
       class="btn btn-secondary">Annuler</a>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
  </form>
</div>

<script>
document.getElementById('confirmForm').addEventListener('submit', function(e) {
  if (! window.confirm('Êtes-vous sûr de vouloir valider votre participation ?')) {
    e.preventDefault();
  }
});
</script>
