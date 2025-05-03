<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'EcoRide'; ?></title>

  <!-- 1) Bootstrap & Icônes (jamais réordonné) -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css"
    rel="stylesheet" crossorigin="anonymous"
  >
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    rel="stylesheet"
  >

  <!-- 2) Vos CSS “fonctionnels” -->
  <link rel="stylesheet" href="/assets/style/styleFormLogin.css">
  <link rel="stylesheet" href="/assets/style/styleCovoiturage.css">
  <link rel="stylesheet" href="/assets/style/styleBarreRecherche.css">

  <!-- 3) VOTRE CSS PRINCIPAL (ici navbars, responsive…) -->
  <link rel="stylesheet" href="/assets/style/styleIndex.css">

  <!-- 4) Footer (en dernier, pour n’écraser que le footer) -->
  <link rel="stylesheet" href="/assets/style/styleFooter.css">

  <!-- puis vos éventuels extraStyles et scripts Maps -->
  <?php if (!empty($extraStyles)): foreach ($extraStyles as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
  <?php endforeach; endif; ?>
  <?php if (!empty($barreRecherche)): ?>
    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=TA_CLE_API&libraries=places&callback=initSearchAutocomplete">
    </script>
  <?php endif; ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="d-flex flex-column min-vh-100">

<?php
// === Header principal ===
$headerFile = __DIR__ . '/controllers/principal/scriptHeader.php';
if (file_exists($headerFile)) {
    require_once $headerFile;
} else {
    echo "<p class='text-danger'>Fichier introuvable : $headerFile</p>";
}

// === Big Title ===
if (empty($hideTitle)) {
    $titleFile = __DIR__ . '/views/bigTitle.php';
    if (file_exists($titleFile)) {
        require_once $titleFile;
    } else {
        echo "<p class='text-danger'>Fichier introuvable : $titleFile</p>";
    }
}

// === Flash messages ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_SESSION['flash'])) {
    echo '<div class="alert alert-success container mt-3">'
         . htmlspecialchars($_SESSION['flash'])
         . '</div>';
    unset($_SESSION['flash']);
}
if (!empty($_SESSION['flash_error'])) {
    echo '<div class="alert alert-danger container mt-3">'
         . htmlspecialchars($_SESSION['flash_error'])
         . '</div>';
    unset($_SESSION['flash_error']);
}

// === Form errors pour noteForm uniquement ===
if (strpos($_SERVER['REQUEST_URI'], '/noteForm') === 0
    && !empty($_SESSION['form_errors'])) {
    echo '<div class="alert alert-danger container mt-3"><ul>';
    foreach ($_SESSION['form_errors'] as $msg) {
        echo '<li>' . htmlspecialchars($msg) . '</li>';
    }
    echo '</ul></div>';
    unset($_SESSION['form_errors'], $_SESSION['old']);
}

// === Barre de recherche conditionnelle ===
if (!empty($barreRecherche)) {
    $barFile = __DIR__ . '/' . ltrim($barreRecherche, '/');
    if (file_exists($barFile)) {
        echo '<div class="container my-3">';
        require_once $barFile;
        echo '</div>';
    } else {
        echo "<p class='text-danger'>Fichier introuvable : $barFile</p>";
    }
}
?>

<main class="container my-4 flex-fill">
<?php
// === Contenu principal ===
if (!empty($mainContent)) {
    echo $mainContent;
} elseif (!empty($mainView)) {
    $viewFile = __DIR__ . '/' . ltrim($mainView, '/');
    if (file_exists($viewFile)) {
        require $viewFile;
        if (!empty($mainContent)) {
            echo $mainContent;
        }
    } else {
        echo "<p class='text-danger'>Vue introuvable : $viewFile</p>";
    }
} else {
    echo '<p class="text-muted text-center">Aucun contenu à afficher.</p>';
}
?>
</main>

<?php
// === Footer global ===
$footerFile = __DIR__ . '/views/footer.php';
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
