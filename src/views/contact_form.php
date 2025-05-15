<?php
// src/views/contact_form.php
?>
<h2 class="text-center mb-4">Contact</h2>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $msg): ?>
        <li><?= htmlspecialchars($msg, ENT_QUOTES) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form class="formLogin mx-auto" action="/contactPost" method="POST" novalidate>
    <div class="mb-3">
        <label for="email" class="form-label">Votre mail :</label>
        <input type="email" name="email" id="email"
               class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
               value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES) ?>"
               required>
        <?php if (isset($errors['email'])): ?>
          <div class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES) ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="titre" class="form-label">Titre :</label>
        <input type="text" name="titre" id="titre"
               class="form-control<?= isset($errors['titre']) ? ' is-invalid' : '' ?>"
               value="<?= htmlspecialchars($old['titre'] ?? '', ENT_QUOTES) ?>"
               required>
        <?php if (isset($errors['titre'])): ?>
          <div class="invalid-feedback"><?= htmlspecialchars($errors['titre'], ENT_QUOTES) ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description :</label>
        <textarea name="description" id="description"
                  class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"
                  rows="10" required><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES) ?></textarea>
        <?php if (isset($errors['description'])): ?>
          <div class="invalid-feedback"><?= htmlspecialchars($errors['description'], ENT_QUOTES) ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-3 text-center">
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </div>

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
</form>
