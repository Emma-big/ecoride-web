<?php
// src/views/adminDashboard.php

// 1) Vérifier que l'utilisateur est bien admin
if (empty($_SESSION['user']) || (int)($_SESSION['user']['role'] ?? 0) !== 1) {
    header('Location: /login');
    exit;
}

// 2) Récupérer les données de l'utilisateur
$user = $_SESSION['user'];
?>

<div class="container my-5">
  <!-- Message de bienvenue -->
  <div class="card p-3 mb-4 bg-primary text-white">
    <p class="mb-3">
      Connexion réussie&nbsp;! Bienvenue <strong><?= htmlspecialchars($user['prenom'], ENT_QUOTES) ?></strong>,<br>
      Vous êtes connecté(e) en tant qu'Administrateur sous le pseudo&nbsp;:
      <strong><?= htmlspecialchars($user['pseudo'], ENT_QUOTES) ?></strong>
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

  <!-- Formulaire de création de compte employé -->
  <div class="row">
    <div class="col-12 col-md-6 mb-3">
      <div class="card p-3">
        <h2 class="h5">Créer un compte employé(e)</h2>
        <?php require_once BASE_PATH . '/src/views/registerEmploye.php'; ?>
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
    <p>Consultez les données détaillées sur les covoiturages et les crédits générés.</p>
    <a href="/stats" class="btn btn-success mt-2 mb-3">Voir les statistiques</a>
  </div>

  <!-- Compteur dynamique -->
  <div class="card p-3 my-4">
    <?php require_once BASE_PATH . '/src/controllers/principal/compteur.php'; ?>
  </div>
</div>
