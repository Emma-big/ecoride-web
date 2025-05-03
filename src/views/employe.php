<?php
// src/views/employe.php

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Cacher le bigTitle du layout
global $hideTitle;
$hideTitle = true;

// 3) Récupérer l'utilisateur en session
$user = $_SESSION['user'] ?? [];

// 4) Charger la config PDO
require_once BASE_PATH . '/config/database.php';

// 5) Récupérer le libellé du rôle
$roleId = isset($user['role']) ? (int)$user['role'] : 0;
try {
    $stmt = $pdo->prepare('SELECT libelle FROM roles WHERE role_id = ?');
    $stmt->execute([$roleId]);
    $roleLabel = $stmt->fetchColumn() ?: 'inconnu';
} catch (Exception $e) {
    $roleLabel = 'inconnu';
}
?>

<main class="container my-4">
  <h1 class="text-center mb-4">Mon espace Employé</h1>

  <div class="text-center mb-5">
    <p>Connexion réussie&nbsp;!</p>
    <p>Bienvenue&nbsp;<strong><?= htmlspecialchars($user['prenom'] ?? 'Utilisateur', ENT_QUOTES) ?></strong>,</p>
    <p>
      Vous êtes connecté(e) en tant que
      <strong><?= htmlspecialchars($roleLabel, ENT_QUOTES) ?></strong>
      sous le pseudo&nbsp;:
      <strong><?= htmlspecialchars($user['pseudo'] ?? '', ENT_QUOTES) ?></strong>
    </p>
    <button class="btn btn-secondary" onclick="location.href='/deconnexion'">Déconnexion</button>
  </div>

  <!-- Navigation Employé -->
  <div class="row justify-content-center mb-5">
    <div class="col-12 col-md-6 text-center">
      <a href="/notes-a-valider" class="btn btn-outline-primary w-100 mb-3">Avis à valider</a>
      <a href="/reclamations-problemes" class="btn btn-outline-danger w-100">Trajets problématiques</a>
    </div>
  </div>
</main>
