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

// 3) Récupérer d'anciennes valeurs et messages
$old = $_SESSION['old'] ?? [];
$flash = $_SESSION['flash'] ?? '';
$errors = $_SESSION['form_errors'] ?? [];
// Nettoyage
unset($_SESSION['old'], $_SESSION['flash'], $_SESSION['form_errors']);
?>

<div class="container my-5">
    <!-- Message de bienvenue -->
    <div class="card p-3 mb-4 bg-primary">
        <p class="mb-3 text-white">
            Connexion réussie ! Bienvenue <strong><?= htmlspecialchars($_SESSION['user']['prenom'], ENT_QUOTES) ?></strong>,<br>
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

    <!-- Affichage flash et erreurs de formulaire -->
    <?php if ($flash): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES) ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $msg): ?>
            <li><?= htmlspecialchars($msg, ENT_QUOTES) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Formulaires d'administration -->
    <div class="row">
        <!-- Création d'un compte employé -->
        <div class="col-12 col-md-6 mb-3">
            <div class="card p-3">
            <h2 class="h5">Créer un compte employé(e)</h2>
            <form method="POST"
                  action="/registerPostEmploye"
                  class="mt-3"
                  novalidate
                  autocomplete="off">
                <input type="hidden" name="photo" value="E">

                <div class="mb-3">
                    <label for="pseudo" class="form-label">Pseudo :</label>
                    <input type="text"
                           name="pseudo"
                           id="pseudo"
                           class="form-control"
                           required
                           value="<?= htmlspecialchars($old['pseudo'] ?? '', ENT_QUOTES) ?>"
                           autocomplete="off">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email :</label>
                    <input type="email"
                           name="email"
                           id="email"
                           class="form-control"
                           required
                           autocomplete="new-email"
                           value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES) ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe :</label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control"
                           required
                           autocomplete="new-password">
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Nom :</label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control"
                           required
                           value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES) ?>">
                </div>
                <div class="mb-3">
                    <label for="surname" class="form-label">Prénom :</label>
                    <input type="text"
                           name="surname"
                           id="surname"
                           class="form-control"
                           required
                           value="<?= htmlspecialchars($old['surname'] ?? '', ENT_QUOTES) ?>">
                </div>
                <div class="mb-3">
                    <label for="naissance" class="form-label">Date de naissance :</label>
                    <input type="date"
                           name="naissance"
                           id="naissance"
                           class="form-control"
                           required
                           value="<?= htmlspecialchars($old['naissance'] ?? '', ENT_QUOTES) ?>">
                </div>
                <div class="mb-3">
                    <label for="choix" class="form-label">Sexe :</label>
                    <select name="choix"
                            id="choix"
                            class="form-select"
                            required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="H" <?= isset($old['choix']) && $old['choix']==='Homme' ? 'selected' : '' ?>>Homme</option>
                        <option value="F" <?= isset($old['choix']) && $old['choix']==='Femme' ? 'selected' : '' ?>>Femme</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Téléphone :</label>
                    <input type="text"
                           name="phone"
                           id="phone"
                           class="form-control"
                           required
                           value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES) ?>">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES) ?>">
            </form>
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
