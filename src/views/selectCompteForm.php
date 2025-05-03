<?php
// src/views/selectCompteForm.php

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la config PDO
require_once BASE_PATH . '/config/database.php';

// 3) Récupérer tous les comptes utilisateurs
$queryCompte = "SELECT pseudo, email FROM utilisateurs ORDER BY pseudo ASC";
$stmt = $pdo->prepare($queryCompte);
$stmt->execute();
$comptes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <!-- Viewport pour activer le responsive sur mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sélection d'un compte</title>

    <!-- Styles personnalisés -->
    <link href="/assets/style/styleFormLogin.css" rel="stylesheet">
    <!-- Overrides responsive pour mobile -->
    <link href="/assets/style/styleIndex.css" rel="stylesheet">
</head>
<body>
<header>
    <?php require_once BASE_PATH . '/src/controllers/principal/scriptHeader.php'; ?>
</header>

<?php require_once BASE_PATH . '/src/views/bigTitle.php'; ?>

<div class="container my-4">
    
    <form class="formLogin2 mx-auto" action="/modifCompteForm" method="get" novalidate>
        <div class="mb-3">
            <label for="compte-select" class="form-label fw-bold text-dark">Compte :</label>
            <select id="compte-select" name="compte" class="form-select" required>
                <option value="">--Choisissez un compte--</option>
                <?php foreach ($comptes as $compte): ?>
                    <option value="<?= htmlspecialchars($compte['pseudo'], ENT_QUOTES) ?>">
                        <?= htmlspecialchars($compte['pseudo'], ENT_QUOTES) ?> (<?= htmlspecialchars($compte['email'], ENT_QUOTES) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Choisir</button>
        </div>
    </form>
</div>
</body>
</html>
