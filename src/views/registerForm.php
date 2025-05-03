<?php
// src/views/registerForm.php

// 1) Session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Récupérer erreurs & anciennes valeurs
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EcoRide - Inscription</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS & jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Styles personnalisés -->
    <link href="/assets/style/styleFormLogin.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Crete+Round" rel="stylesheet">

    <!-- Overrides responsive pour mobile -->
    <link href="/assets/style/styleIndex.css" rel="stylesheet">
</head>
<body>
<header>
    <?php require_once BASE_PATH . '/src/controllers/principal/scriptHeader.php'; ?>
</header>

<?php require_once BASE_PATH . '/src/views/bigTitle.php'; ?>

<div class="container my-4">
    <h2 class="text-center mb-4">Formulaire d'inscription</h2>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $msg): ?>
            <li><?= htmlspecialchars($msg, ENT_QUOTES) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form class="formLogin mx-auto" action="/registerPost" method="POST" novalidate>
        <?php
          $pseudo    = htmlspecialchars($old['pseudo']    ?? '', ENT_QUOTES);
          $email     = htmlspecialchars($old['email']     ?? '', ENT_QUOTES);
          $name      = htmlspecialchars($old['name']      ?? '', ENT_QUOTES);
          $surname   = htmlspecialchars($old['surname']   ?? '', ENT_QUOTES);
          $naissance = htmlspecialchars($old['naissance'] ?? '', ENT_QUOTES);
          $choix     = $old['choix'] ?? '';
          $phone     = htmlspecialchars($old['phone']     ?? '', ENT_QUOTES);
        ?>

        <div class="mb-3">
            <label for="pseudo" class="form-label">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo"
                   class="form-control<?= isset($errors['pseudo']) ? ' is-invalid' : '' ?>"
                   value="<?= $pseudo ?>" required>
            <?php if(isset($errors['pseudo'])): ?>
              <div class="invalid-feedback"><?= htmlspecialchars($errors['pseudo'], ENT_QUOTES) ?></div>
            <?php endif; ?>
        </div>

        <!-- ... autres champs ... -->

        <div class="mb-3 text-center">
            <button type="submit" class="btn btn-primary">Créer un compte</button>
        </div>
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
    </form>
</div>

<footer>
    <?php require_once BASE_PATH . '/src/views/footer.php'; ?>
</footer>
</body>
</html>
