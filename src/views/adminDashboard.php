<?php
// src/views/adminDashboard.php

// 1) Démarrer la session et vérifier le rôle admin
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 1) {
    header('Location: /login');
    exit;
}

// 2) Inclusion du header et du bigTitle
require_once BASE_PATH . '/src/controllers/principal/scriptHeader.php';
require_once BASE_PATH . '/src/views/bigTitle.php';
?>

<div class="container my-5">
    <!-- Message de bienvenue -->
    <div class="card p-3 mb-4 bg-primary">
        <p class="mb-3 text-white">
            Connexion réussie ! Bienvenue
            <strong><?= htmlspecialchars($_SESSION['user']['prenom'], ENT_QUOTES) ?></strong>,<br>
            Vous êtes connecté(e) en tant qu'Administrateur sous le pseudo :
            <strong><?= htmlspecialchars($_SESSION['user']['pseudo'], ENT_QUOTES) ?></strong>
        </p>
        <div class="text-center">
            <button
              type="button"
              class="btn btn-danger btn-sm"
              onclick="location.href='/deconnexion'"
              aria-label="Se déconnecter"
            >
             Déconnexion
            </button>
        </div>
    </div>

    <!-- Formulaires d'administration -->
    <div class="row">
        <!-- Création d'un compte employé -->
        <div class="col-12 col-md-6 mb-3">
            <div class="card p-3">
                <h2 class="h5">Créer un compte employé(e)</h2>
                <?php
                  // On inclut directement le partial de formulaire,
                  // sans enveloppe <html>…</html> qui cassait la mise en page
                  require_once BASE_PATH . '/src/views/registerEmploye.php';
                ?>
            </div>
        </div>

        <!-- Gestion des rôles et suspensions -->
        <div class="col-12 col-md-6 mb-3">
            <div class="card p-3">
                <h2 class="h5">Suspendre ou modifier un compte</h2>
                <?php require_once BASE_PATH . '/src/views/selectCompteForm.php'; ?>
            </div>
        </div>
    </div>

    <!-- Lien vers les statistiques -->
    <div class="card text-center my-4 p-3">
        <h2 class="h5">Statistiques</h2>
        <p class="text-white">Consultez les données détaillées sur les covoiturages et les crédits générés.</p>
        <a href="/stats" class="btn btn-success mt-2 mb-3">Voir les statistiques</a>
    </div>

    <!-- Compteur dynamique -->
    <div class="card p-3 my-4">
        <?php require_once BASE_PATH . '/src/controllers/principal/compteur.php'; ?>
    </div>
</div>
