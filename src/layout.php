<?php
// src/layout.php

// 1) Démarrage de la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Debug (optionnel)
error_log('DEBUG SESSION: ' . print_r($_SESSION, true));

// 3) En-tête HTML
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'EcoRide', ENT_QUOTES) ?></title>

  <!-- Vos CSS & libs -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css"
    rel="stylesheet" crossorigin="anonymous"
  >
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="/assets/style/styleFormLogin.css">
  <link rel="stylesheet" href="/assets/style/styleCovoiturage.css">
  <link rel="stylesheet" href="/assets/style/styleBarreRecherche.css">
  <link rel="stylesheet" href="/assets/style/styleIndex.css">
  <link rel="stylesheet" href="/assets/style/styleFooter.css">
  <?php if (!empty($extraStyles)): foreach ($extraStyles as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES) ?>">
  <?php endforeach; endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">

<?php
// 4) Header
$headerFile = BASE_PATH . '/src/controllers/principal/scriptHeader.php';
if (file_exists($headerFile)) {
    require_once $headerFile;
} else {
    echo "<p class='text-danger'>Fichier introuvable : $headerFile</p>";
}

// 5) Big Title
if (empty($hideTitle)) {
    $titleFile = BASE_PATH . '/src/views/bigTitle.php';
    if (file_exists($titleFile)) {
        require_once $titleFile;
    } else {
        echo "<p class='text-danger'>Fichier introuvable : $titleFile</p>";
    }
}

// 6) Flash messages
if (!empty($_SESSION['flash_success'])) {
    echo '<div class="alert alert-success container mt-3">'
         . htmlspecialchars($_SESSION['flash_success'])
         . '</div>';
    unset($_SESSION['flash_success']);
}
if (!empty($_SESSION['flash_error'])) {
    echo '<div class="alert alert-danger container mt-3">'
         . htmlspecialchars($_SESSION['flash_error'])
         . '</div>';
    unset($_SESSION['flash_error']);
}

// 7) Contenu principal
echo '<main class="flex-fill container mt-4">';

if (!empty($mainContent)) {
    echo $mainContent;

} elseif (!empty($mainView)) {
    // on ne tente plus dans controllers/, juste forms/ et views/
    $path = BASE_PATH . '/src/' . ltrim($mainView, '/');
    if (file_exists($path)) {
        require $path;
    } else {
        echo "<p class='text-danger'>Vue introuvable : $path</p>";
    }

} else {
    echo '<p class="text-muted text-center">Aucun contenu à afficher.</p>';
}

echo '</main>';
?>

// 8) Footer
$footerFile = BASE_PATH . '/src/views/footer.php';
if (file_exists($footerFile)) {
    require_once $footerFile;
} else {
    echo "<p class='text-danger'>Fichier introuvable : $footerFile</p>";
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js">
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('togglePassword');
    const pwd    = document.getElementById('password');
    if (!toggle || !pwd) return;
    toggle.addEventListener('click', () => {
      pwd.type = pwd.type === 'password' ? 'text' : 'password';
      toggle.querySelector('i')
            .classList.toggle('bi-eye');
      toggle.querySelector('i')
            .classList.toggle('bi-eye-slash');
    });
  });
</script>
</body>
</html>
