<?php
// src/views/besoinVehicule.php

// 2) Variables pour le layout
$pageTitle   = "Voiture requise - EcoRide";
$withTitle   = true;
$extraStyles = [
    "/assets/style/styleFormLogin.css",
    "/assets/style/styleBigTitle.css",
    "/assets/style/styleIndex.css"
];

// 3) Contenu principal
ob_start();
?>
<h2 class="text-center mt-4">Voiture manquante !</h2>
<div class="formLogin mx-auto text-center">
    <p>Vous devez enregistrer une voiture pour pouvoir créer un covoiturage en tant que chauffeur.</p>
    <div class="d-flex justify-content-center gap-3 mt-3">
        <a href="/vehiculeForm" class="btn btn-primary">Ajouter une voiture</a>
        <a href="/" class="btn btn-secondary">Retour</a>
    </div>
</div>
<?php
// 4) Appel du layout global
$mainContent = ob_get_clean();
require_once BASE_PATH . '/src/layout.php';
?>
