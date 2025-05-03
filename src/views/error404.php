<?php
// src/views/error404.php
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Erreur 404 – Page non trouvée</title>

  <!-- Viewport pour le mobile -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Overrides responsive pour mobile -->
  <link href="/assets/style/styleIndex.css" rel="stylesheet">
</head>
<body class="d-flex flex-column justify-content-center align-items-center vh-100 bg-light text-center">
  <h1 class="display-4 text-warning">Erreur 404</h1>
  <p class="lead">La page que vous recherchez est introuvable.</p>
  <a href="/" class="btn btn-primary mt-3">Retour à l'accueil</a>
</body>
</html>
