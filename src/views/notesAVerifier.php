<?php
// src/views/notesAVerifier.php

// Afficher le bigTitle via le layout
global $hideTitle;
$hideTitle = false;
?>
<main class="container my-4">
  <h2 class="text-center mb-4">Modération des avis</h2>

  <?php if (!empty($avisAVerifier)): ?>
    <div class="row">
      <?php foreach ($avisAVerifier as $avis): ?>
        <?php
        // Assurer la clé 'note' existe
        $score = isset($avis['note']) ? (float)$avis['note'] : 0.0;
        ?>
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h3 class="card-title">Trajet #<?= htmlspecialchars($avis['covoiturage_id'], ENT_QUOTES) ?></h3>
              <p><strong>Passager&nbsp;:</strong> <?= htmlspecialchars($avis['passager'], ENT_QUOTES) ?></p>
              <p><strong>Chauffeur&nbsp;:</strong> <?= htmlspecialchars($avis['chauffeur'], ENT_QUOTES) ?></p>
              <p><strong>Note&nbsp;:</strong> <?= number_format($score, 1) ?>/5</p>
              <p><strong>Commentaire&nbsp;:</strong><br>
                <?= nl2br(htmlspecialchars($avis['commentaire'], ENT_QUOTES)) ?>
              </p>
            </div>
            <div class="card-footer text-center">
              <form method="POST" action="/toggleAvisStatut" class="d-inline">
                <input type="hidden" name="note_id" value="<?= (int)$avis['note_id'] ?>">
                <button name="action" value="accept" class="btn btn-success">Valider</button>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
              </form>
              <form method="POST" action="/toggleAvisStatut" class="d-inline">
                <input type="hidden" name="note_id" value="<?= (int)$avis['note_id'] ?>">
                <button name="action" value="refuse" class="btn btn-danger">Refuser</button>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-center">Aucun avis à valider pour le moment.</p>
  <?php endif; ?>
</main>
