<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

// 1) Charger la config PDO
require_once BASE_PATH . '/config/database.php';

// 2) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 3) Récupérer l'ID de l'utilisateur via son pseudo en session
$stmtId = $pdo->prepare("SELECT utilisateur_id FROM utilisateurs WHERE pseudo = :pseudo");
$stmtId->execute([':pseudo' => $_SESSION['user']['pseudo']]);
$proprio = $stmtId->fetch(\PDO::FETCH_ASSOC);
$proprietaireId = $proprio['utilisateur_id'] ?? null;

if (!$proprietaireId) {
    echo "<p>Aucune voiture trouvée.</p>";
    return;
}

// 4) Récupérer les voitures de cet utilisateur (soft-delete)
$stmt = $pdo->prepare("SELECT v.voiture_id, v.modele, v.immatriculation,
         v.couleur, v.date_premiere_immat,
         m.libelle AS marque, e.libelle AS energie
    FROM voitures v
    JOIN marques m   ON v.marque_id     = m.marque_id
    JOIN energies e  ON v.energie       = e.energie_id
   WHERE v.proprietaire_id = :uid
     AND v.deleted_at IS NULL
   ORDER BY v.modele");
$stmt->execute([':uid' => $proprietaireId]);
$mesVoitures = $stmt->fetchAll(\PDO::FETCH_ASSOC);
?>
<?php if (!empty($mesVoitures)): ?>
  <div class="row">
    <?php foreach ($mesVoitures as $vehicule): ?>
      <div class="col-md-6 col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-success text-white text-center">
            <h5 class="mb-0">
              <?= htmlspecialchars($vehicule['marque'], ENT_QUOTES) ?>
              <?= htmlspecialchars($vehicule['modele'], ENT_QUOTES) ?>
            </h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <strong>Immatriculation :</strong>
                <?= htmlspecialchars($vehicule['immatriculation'], ENT_QUOTES) ?>
              </li>
              <li class="list-group-item">
                <strong>Couleur :</strong>
                <?= htmlspecialchars($vehicule['couleur'], ENT_QUOTES) ?>
              </li>
              <li class="list-group-item">
                <strong>1ère immatriculation :</strong>
                <?= htmlspecialchars($vehicule['date_premiere_immat'], ENT_QUOTES) ?>
              </li>
              <li class="list-group-item">
                <strong>Énergie :</strong>
                <?= htmlspecialchars($vehicule['energie'], ENT_QUOTES) ?>
              </li>
            </ul>
          </div>
          <div class="card-footer text-center">
            <!-- Bouton Modifier -->
            <a href="/vehiculeForm?id=<?= (int)$vehicule['voiture_id'] ?>" class="btn btn-secondary btn-sm me-2">Modifier</a>
            <!-- Bouton Supprimer (soft-delete) -->
            <form action="/deleteVoiture" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette voiture ?');">
                <input type="hidden" name="id" value="<?= (int)$vehicule['voiture_id'] ?>">
                <button class="btn btn-danger btn-sm">Supprimer</button>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p class="text-center text-muted">Aucune voiture trouvée.</p>
<?php endif; ?>

<div class="text-center mt-3">
  <a class="btn btn-primary" href="/vehiculeForm">Ajouter une voiture</a>
</div>
