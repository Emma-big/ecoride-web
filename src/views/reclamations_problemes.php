<?php
// src/views/reclamations_problemes.php
?>
<main class="container my-4">
  <h2 class="text-center mb-4">Trajets signalés</h2>

  <?php if (!empty($reclamations)): ?>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID trajet</th>
          <th>Passager</th>
          <th>Email passager</th>
          <th>Chauffeur</th>
          <th>Email chauffeur</th>
          <th>Date départ</th>
          <th>Lieu départ</th>
          <th>Date arrivée</th>
          <th>Lieu arrivée</th>
          <th>Commentaire</th>
          <th>Signalé le</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reclamations as $r): ?>
          <?php $sid = (int) $r['statut_id']; ?>
          <tr>
            <td><?= htmlspecialchars($r['covoiturage_id'],   ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($r['passager']['pseudo'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($r['passager']['email'],  ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($r['chauffeur']['pseudo'],ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($r['chauffeur']['email'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($r['covoiturage']['date_depart'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($r['covoiturage']['lieu_depart'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($r['covoiturage']['date_arrive'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($r['covoiturage']['lieu_arrive'], ENT_QUOTES) ?></td>
            <td><?= nl2br(htmlspecialchars($r['commentaire'], ENT_QUOTES)) ?></td>
            <td><?= htmlspecialchars($r['date_signal'], ENT_QUOTES) ?></td>
            <td>
              <?php
                // 7 = Prise en charge en cours
                // 8 = Résolu
                // Tout autre -> nouveau signalement
                if ($sid === 7): ?>
                  <form method="POST" action="/reclamationResolue" class="d-inline">
                    <input type="hidden" name="reclamation_id" value="<?= htmlspecialchars($r['reclamation_id'], ENT_QUOTES) ?>">
                    <input type="hidden" name="csrf_token"      value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                    <button class="btn btn-sm btn-primary">Problème résolu</button>
                  </form>
              <?php
                elseif ($sid === 8): ?>
                  <span class="badge bg-secondary">Résolu</span>
              <?php
                else: // ni 7 ni 8 -> on considère que c'est à prendre en charge ?>
                  <form method="POST" action="/reclamationTraitee" class="d-inline">
                    <input type="hidden" name="reclamation_id" value="<?= htmlspecialchars($r['reclamation_id'], ENT_QUOTES) ?>">
                    <input type="hidden" name="csrf_token"      value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                    <button class="btn btn-sm btn-warning">Prendre en charge</button>
                  </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Pagination">
      <ul class="pagination justify-content-center">
        <?php for ($p = 1; $p <= $pages; $p++): ?>
          <li class="page-item <?= $p === $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>

  <?php else: ?>
    <p class="text-center">Aucune réclamation à traiter.</p>
  <?php endif; ?>
</main>
