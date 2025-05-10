<?php
// src/views/vehiculeForm.php

// 1) Session + auth
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

// Récupérer les erreurs et anciennes valeurs si présentes
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old']);

// 2) Charger le PDO
$pdo = require BASE_PATH . '/src/config.php';

// 3) Charger listes pour les <select>
$marques  = $pdo->query("SELECT marque_id, libelle FROM marques ORDER BY libelle")
                ->fetchAll(PDO::FETCH_ASSOC);
$energies = $pdo->query("SELECT energie_id, libelle FROM energies ORDER BY libelle")
                ->fetchAll(PDO::FETCH_ASSOC);

// 4) Détecter l’édition
$isEdit = false;
$formData = [
    'voiture_id'          => '',
    'marque_id'           => '',
    'modele'              => '',
    'immatriculation'     => '',
    'couleur'             => '',
    'date_premiere_immat' => '',
    'energie_id'          => '',
];
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $isEdit = true;
    $vid = (int) $_GET['id'];
    $stmt = $pdo->prepare(
      "SELECT * FROM voitures
       WHERE voiture_id = :vid
         AND proprietaire_id = :uid
         AND deleted_at IS NULL"
    );
    $stmt->execute([
      ':vid' => $vid,
      ':uid' => $_SESSION['user']['utilisateur_id'],
    ]);
    if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $formData = $data;
    } else {
        $_SESSION['flash_error'] = 'Voiture introuvable ou non autorisée.';
        header('Location: /utilisateur');
        exit;
    }
}

// 5) Variables pour le layout
$pageTitle   = $isEdit ? 'Modifier la voiture' : 'Ajouter une voiture';
$hideTitle   = true;
$extraStyles = ['/assets/style/styleFormLogin.css','/assets/style/styleIndex.css'];

// 6) Générer le formulaire
ob_start();
?>
<h2 class="text-center mb-4"><?= $isEdit ? 'Modifier la voiture' : 'Ajouter une voiture' ?></h2>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $msg): ?>
        <li><?= htmlspecialchars($msg, ENT_QUOTES) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form class="formLogin mx-auto"
      action="<?= $isEdit 
                  ? '/updateVehiculePost?id=' . $formData['voiture_id'] 
                  : '/registerVehiculePost' ?>"
      method="POST" novalidate>

  <!-- Marque -->
  <?php $val = $old['marque_id'] ?? $formData['marque_id']; ?>
  <div class="mb-3">
    <label for="marque_id" class="form-label">Marque :</label>
    <select id="marque_id" name="marque_id" 
            class="form-select<?= isset($errors['marque_id']) ? ' is-invalid' : '' ?>" required>
      <option value="">-- Choisissez une marque --</option>
      <?php foreach ($marques as $m): ?>
        <option value="<?= $m['marque_id'] ?>"
          <?= ($m['marque_id'] == $val) ? 'selected' : '' ?>>
          <?= htmlspecialchars($m['libelle'],ENT_QUOTES) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php if(isset($errors['marque_id'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['marque_id'],ENT_QUOTES) ?></div>
    <?php endif; ?>
  </div>

  <!-- Modèle -->
  <?php $val = $old['modele'] ?? $formData['modele']; ?>
  <div class="mb-3">
    <label for="modele" class="form-label">Modèle :</label>
    <input type="text" id="modele" name="modele"
           class="form-control<?= isset($errors['modele']) ? ' is-invalid' : '' ?>"
           value="<?= htmlspecialchars($val,ENT_QUOTES) ?>"
           required>
    <?php if(isset($errors['modele'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['modele'],ENT_QUOTES) ?></div>
    <?php endif; ?>
  </div>

  <!-- Immatriculation -->
  <?php $val = $old['immatriculation'] ?? $formData['immatriculation']; ?>
  <div class="mb-3">
    <label for="immatriculation" class="form-label">Immatriculation :</label>
    <input type="text" id="immatriculation" name="immatriculation"
           class="form-control<?= isset($errors['immatriculation']) ? ' is-invalid' : '' ?>"
           value="<?= htmlspecialchars($val,ENT_QUOTES) ?>"
           required>
    <?php if(isset($errors['immatriculation'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['immatriculation'],ENT_QUOTES) ?></div>
    <?php endif; ?>
  </div>

  <!-- Couleur -->
  <?php $val = $old['couleur'] ?? $formData['couleur']; ?>
  <div class="mb-3">
    <label for="couleur" class="form-label">Couleur :</label>
    <input type="text" id="couleur" name="couleur"
           class="form-control<?= isset($errors['couleur']) ? ' is-invalid' : '' ?>"
           value="<?= htmlspecialchars($val,ENT_QUOTES) ?>"
           required>
    <?php if(isset($errors['couleur'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['couleur'],ENT_QUOTES) ?></div>
    <?php endif; ?>
  </div>

  <!-- Date première immat -->
  <?php $val = $old['date_premiere_immat'] ?? $formData['date_premiere_immat']; ?>
  <div class="mb-3">
    <label for="date_premiere_immat" class="form-label">Date de première immatriculation :</label>
    <input type="date" id="date_premiere_immat" name="date_premiere_immat"
           class="form-control<?= isset($errors['date_premiere_immat']) ? ' is-invalid' : '' ?>"
           value="<?= htmlspecialchars($val,ENT_QUOTES) ?>"
           required>
    <?php if(isset($errors['date_premiere_immat'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['date_premiere_immat'],ENT_QUOTES) ?></div>
    <?php endif; ?>
  </div>

  <!-- Énergie -->
  <?php $val = $old['energie_id'] ?? $formData['energie_id']; ?>
  <div class="mb-3">
    <label for="energie_id" class="form-label">Énergie :</label>
    <select id="energie_id" name="energie_id" 
            class="form-select<?= isset($errors['energie_id']) ? ' is-invalid' : '' ?>" required>
      <option value="">-- Choisissez une énergie --</option>
      <?php foreach ($energies as $e): ?>
        <option value="<?= $e['energie_id'] ?>"
          <?= ($e['energie_id'] == $val) ? 'selected' : '' ?>>
          <?= htmlspecialchars($e['libelle'],ENT_QUOTES) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php if(isset($errors['energie_id'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['energie_id'],ENT_QUOTES) ?></div>
    <?php endif; ?>
  </div>

  <div class="text-center">
    <button type="submit" class="btn btn-primary">
      <?= $isEdit ? 'Modifier la voiture' : 'Ajouter la voiture' ?>
    </button>
  </div>

  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
</form>
<?php
$mainContent = ob_get_clean();

// 7) Appeler le layout central (qui inclura $mainContent dans le <main>)
require_once BASE_PATH . '/src/layout.php';
