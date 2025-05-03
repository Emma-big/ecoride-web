<?php
namespace Adminlocal\EcoRide\Controllers\Post;

use Adminlocal\EcoRide\Helpers\MongoHelper;
use MongoDB\BSON\ObjectId;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la config PDO
require_once BASE_PATH . '/config/database.php';

// 3) Vérifier les paramètres
$id  = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$ok  = isset($_GET['ok']) ? (int) $_GET['ok'] : 0;
$uid = (int) ($_SESSION['user']['utilisateur_id'] ?? 0);

// 4) Si paramètres invalides, rediriger
if ($id <= 0 || $uid <= 0) {
    header('Location: /utilisateur');
    exit;
}

// 5) Cas "Tout est OK" ➔ Créditer le chauffeur
if ($ok === 1) {
    // … (identique)
    header("Location: /noteForm?id={$id}");
    exit;
}

// 6) Cas "Signaler un problème"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // … (identique)
    header('Location: /confirmation-avis');
    exit;
}

// 7) Cas GET pour afficher formulaire signalement
$error = $_GET['error'] ?? '';

ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Signaler un problème</title>

    <!-- CSS Bootstrap si besoin -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Règles de responsive et styleIndex déjà chargés via le layout -->
</head>
<body class="report-problem">
    <div class="container py-4">
        <h1 class="mb-3">Signaler un problème pour le trajet #<?= htmlspecialchars($id) ?></h1>

        <?php if ($error === 'empty'): ?>
            <div class="error">Veuillez décrire le problème avant d'envoyer.</div>
        <?php endif; ?>

        <form method="post" action="?id=<?= urlencode($id) ?>&ok=0">
            <div class="mb-3">
                <label for="commentaire" class="form-label">Votre commentaire :</label>
                <textarea id="commentaire" name="commentaire" required
                          placeholder="Décrivez ici le problème rencontré…"></textarea>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Envoyer</button>
                <a href="/utilisateur" class="btn btn-link cancel-link">Annuler</a>
            </div>
            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
        </form>
    </div>
</body>
</html>
<?php
$mainContent = ob_get_clean();
require BASE_PATH . '/src/layout.php';
exit;
?>
