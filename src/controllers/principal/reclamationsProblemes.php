<?php
// src/views/reclamations_problemes.php

// Vue des réclamations problématiques avec responsive design
?>
<div class="container my-5">
  <h1 class="mb-4 text-center">Trajets problématiques</h1>

  <?php if (empty($reclamations)): ?>
    <p class="text-center">Aucune réclamation en cours.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Commentaire</th>
            <th>Passager</th>
            <th>Chauffeur</th>
            <th>Trajet</th>
            <th>Statut</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reclamations as $r): ?>
            <tr>
              <td><?= $r['reclamation_id'] ?></td>
              <td><?= date('d/m/Y H:i', strtotime($r['date_signal'])) ?></td>
              <td><?= htmlspecialchars($r['commentaire'], ENT_QUOTES) ?></td>
              <td>
                <strong><?= htmlspecialchars($r['passager']['pseudo'], ENT_QUOTES) ?></strong><br>
                <?= htmlspecialchars($r['passager']['email'], ENT_QUOTES) ?>
              </td>
              <td>
                <strong><?= htmlspecialchars($r['chauffeur']['pseudo'], ENT_QUOTES) ?></strong><br>
                <?= htmlspecialchars($r['chauffeur']['email'], ENT_QUOTES) ?>
              </td>
              <td>
                <?= htmlspecialchars($r['covoiturage']['lieu_depart'], ENT_QUOTES) ?> →
                <?= htmlspecialchars($r['covoiturage']['lieu_arrive'], ENT_QUOTES) ?><br>
                <?= date('d/m/Y', strtotime($r['covoiturage']['date_depart'])) ?>
              </td>
              <td><?= htmlspecialchars($r['statut_libelle'], ENT_QUOTES) ?></td>
              <td>
                <form method="POST" action="/reclamationTraitee" class="d-inline">
                  <input type="hidden" name="reclamation_id" value="<?= $r['reclamation_id'] ?>">
                  <button type="submit" class="btn btn-sm btn-success">Traité</button>
                </form>
                <form method="POST" action="/reclamationResolue" class="d-inline ms-1">
                  <input type="hidden" name="reclamation_id" value="<?= $r['reclamation_id'] ?>">
                  <button type="submit" class="btn btn-sm btn-warning">Résolu</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <nav aria-label="Pagination">
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>
