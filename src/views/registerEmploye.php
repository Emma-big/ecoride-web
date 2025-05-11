<?php
// src/views/registerEmploye.php

// 2) Variables pour le layout
$pageTitle = "Inscription Employé - EcoRide";
$withTitle = true;
$extraStyles = [
    "/assets/style/styleFormLogin.css",
    "/assets/style/styleIndex.css"
];

// 3) Contenu principal
ob_start();
?>
<div class="container my-4">
    <h2 class="text-center mb-4">Création d'un compte employé</h2>

    <form class="formLogin2 mx-auto" action="/registerPostEmploye" method="POST" novalidate>
        <input type="hidden" name="photo" value="E">

        <div class="mb-3">
            <label for="pseudo" class="form-label">Pseudo :</label>
            <input type="text" name="pseudo" id="pseudo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Adresse email :</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe :</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Nom :</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="surname" class="form-label">Prénom :</label>
            <input type="text" name="surname" id="surname" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="naissance" class="form-label">Date de naissance :</label>
            <input type="date" name="naissance" id="naissance" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="choix" class="form-label">Sexe :</label>
            <select name="choix" id="choix" class="form-select" required>
                <option value="">-- Sélectionner --</option>
                <option value="H">Homme</option>
                <option value="F">Femme</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Téléphone :</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Créer</button>
        </div>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
    </form>
</div>
<?php
$mainContent = ob_get_clean();
require BASE_PATH . '/src/layout.php';
?>
