<?php
namespace Adminlocal\EcoRide\Controllers\Principal;

// 1) Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Rediriger si l'utilisateur n'est pas connecté ou n'est pas suspendu (rôle = 4)
if (!isset($_SESSION["user"]["role"]) || $_SESSION["user"]["role"] != 4) {
    header("Location: " . BASE_PATH . "/public/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accès refusé</title>
    <!-- Viewport pour mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Liens CSS (relatifs à la racine exposée, c'est-à-dire public/) -->
    <link href="/assets/style/styleFormLogin.css" rel="stylesheet">
    <link href="/assets/style/styleBigTitle.css" rel="stylesheet">
    <link href="/assets/style/styleIndex.css" rel="stylesheet">
    
</head>
<body>
    <?php 
        require_once BASE_PATH . '/src/controllers/principal/scriptHeader.php'; 
        require_once BASE_PATH . '/src/views/bigTitle.php'; 
    ?>

    <div class="container my-4">
        <h2 class="text-center mb-4">Accès refusé</h2>
        <div class="formLogin mx-auto">
            <p>Désolé, votre compte a été suspendu par un administrateur. Veuillez nous contacter pour plus d'informations.</p>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <a href="/contact" class="btn btn-primary">Nous contacter</a>
                <a href="/deconnexion" class="btn btn-secondary">Se déconnecter</a>
            </div>
        </div>
    </div>
</body>
</html>
