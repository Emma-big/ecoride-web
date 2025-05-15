<?php
namespace Adminlocal\EcoRide\forms;

// 1) Démarrage de la session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Récupérer erreurs & anciennes valeurs
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['old']         ?? [];
unset($_SESSION['form_errors'], $_SESSION['old']);

// 3) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

// 4) Récupérer le pseudo passé en GET
$pseudo = trim(filter_input(INPUT_GET, 'compte', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
if ($pseudo === '') {
    echo '<p class="text-center">Aucun compte sélectionné.</p>';
    return;
}

// 5) Charger les infos du compte
$stmt = $pdo->prepare("SELECT pseudo, role FROM utilisateurs WHERE pseudo = :pseudo");
$stmt->execute([':pseudo' => $pseudo]);
$compte = $stmt->fetch(\PDO::FETCH_ASSOC);
if (!$compte) {
    echo '<p class="text-center">Le compte « ' . htmlspecialchars($pseudo, ENT_QUOTES) . ' » n’existe pas.</p>';
    return;
}

// 6) Charger la liste des rôles
$roles = $pdo->query("SELECT role_id, libelle FROM roles ORDER BY role_id")->fetchAll(\PDO::FETCH_ASSOC);

// 7) Variables pour le layout
$pageTitle   = 'Modifier le rôle du compte';
$hideTitle   = true;
$extraStyles = ['/assets/style/styleFormLogin.css'];
  
// 8) Générer le contenu principal
?>
<h2 class="text-center mb-4">Modifier le rôle du compte</h2>
<h3 class="text-center">Compte : <strong><?= htmlspecialchars($compte['pseudo'], ENT_QUOTES) ?></strong></h3>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $msg): ?>
        <li><?= htmlspecialchars($msg, ENT_QUOTES) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form class="formLogin mx-auto" action="/modifCompteAction" method="POST" novalidate>
  <input type="hidden" name="pseudo" value="<?= htmlspecialchars($compte['pseudo'], ENT_QUOTES) ?>">

  <div class="mb-3">
    <label for="nouveau_role" class="form-label">Nouveau rôle :</label>
    <select id="nouveau_role" name="nouveau_role"
            class="form-select<?= isset($errors['nouveau_role']) ? ' is-invalid' : '' ?>"
            required>
      <option value="">-- Sélectionnez un rôle --</option>
      <?php foreach ($roles as $role): ?>
        <option value="<?= $role['role_id'] ?>"
          <?= ((int)($old['nouveau_role'] ?? $compte['role'])) === (int)$role['role_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($role['libelle'], ENT_QUOTES) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php if (isset($errors['nouveau_role'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['nouveau_role'], ENT_QUOTES) ?></div>
    <?php endif; ?>
  </div>

  <div class="text-center">
    <button type="submit" class="btn btn-outline-light">Mettre à jour</button>
  </div>

  <input type="hidden" name="csrf_token"
         value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
</form>
