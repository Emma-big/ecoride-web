<?php
// src/views/employe.php

// 1) Lire l’utilisateur en session
$user = $_SESSION['user'] ?? [];

// Récupérer le libellé du rôle
$pdo     = require BASE_PATH . '/src/config.php';
$roleId  = (int) ($user['role'] ?? 0);
try {
    $stmt      = $pdo->prepare('SELECT libelle FROM roles WHERE role_id = ?');
    $stmt->execute([$roleId]);
    $roleLabel = $stmt->fetchColumn() ?: 'inconnu';
} catch (\Exception $e) {
    $roleLabel = 'inconnu';
}
?>
<div class="container my-4">
  <h1 class="text-center mb-4">Mon espace Employé</h1>
  <div class="text-center mb-5">
    <p>Connexion réussie ! Bienvenue <strong><?= htmlspecialchars($user['prenom'] ?? 'Utilisateur', ENT_QUOTES) ?></strong>,</p>
    <p class="mb-3">
      Vous êtes connecté(e) en tant qu'employé(e) sous le pseudo :
      <strong><?= htmlspecialchars($user['pseudo'] ?? 'inconnu', ENT_QUOTES) ?></strong>
    </p>
    <button class="btn btn-secondary" onclick="location.href='/deconnexion'">Déconnexion</button>
  </div>
  <div class="row justify-content-center mb-5">
    <div class="col-12 col-md-6 text-center">
      <a href="/notes-a-valider" class="btn btn-outline-primary w-100 mb-3">Avis à valider</a>
      <a href="/reclamations-problemes" class="btn btn-outline-danger w-100">Trajets problématiques</a>
    </div>
  </div>
</div>

