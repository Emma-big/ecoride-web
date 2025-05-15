<?php
namespace Adminlocal\EcoRide\Controllers\Post;
require_once BASE_PATH . '/src/Helpers/MongoHelper.php';
use Adminlocal\EcoRide\Helpers\MongoHelper;
use MongoDB\BSON\ObjectId;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

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
    $stmt = $pdo->prepare("
        SELECT prix_personne, utilisateur
          FROM covoiturage
         WHERE covoiturage_id = ?
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($row) {
        $prix        = (float) $row['prix_personne'];
        $chauffeurId = (int) $row['utilisateur'];
        $driverShare = max(0, $prix - 2); // Commission de 2 crédits

        if ($driverShare > 0) {
            $upd = $pdo->prepare("
                UPDATE utilisateurs
                   SET credit = credit + ?
                 WHERE utilisateur_id = ?
            ");
            $upd->execute([$driverShare, $chauffeurId]);
        }
    }

    // ➔ Après OK ➔ redirige vers formulaire de note
    header("Location: /noteForm?id={$id}");
    exit;
}

// 6) Cas "Signaler un problème"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 6.1) Validation
    $commentaire = trim($_POST['commentaire'] ?? '');
    if ($commentaire === '') {
        header("Location: /confirmerTrajet?id={$id}&ok=0&error=empty");
        exit;
    }

    // 6.2) Trouver le chauffeur concerné
    $stmt = $pdo->prepare("
        SELECT utilisateur
          FROM covoiturage
         WHERE covoiturage_id = ?
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$row) {
        header('Location: /utilisateur');
        exit;
    }
    $chauffeurId = (int) $row['utilisateur'];

    // 6.3) Insertion dans MongoDB
    $collection = MongoHelper::getCollection('reclamations');

    $mongoInsert = [
        'covoiturage_id'       => $id,
        'utilisateur_id'       => $uid,
        'utilisateur_concerne' => $chauffeurId,
        'date_signal'          => date('Y-m-d H:i:s'),
        'commentaire'          => $commentaire,
        'statut_id'            => 3
    ];

    $result   = $collection->insertOne($mongoInsert);
    $mongoId  = (string)$result->getInsertedId();

    // 6.4) Lier à la base relationnelle
    // Récupération du commentaire depuis le POST
$commentaire = trim($_POST['commentaire'] ?? '');

// Préparation de la requête avec les colonnes manquantes
$stmt = $pdo->prepare("
    INSERT INTO reclamations
        (mongo_id, covoiturage_id, utilisateur_id, utilisateur_concerne, commentaire, statut_id, date_signal)
    VALUES
        (:mongo_id, :covoiturage_id, :utilisateur_id, :utilisateur_concerne, :commentaire, :statut_id, NOW())
");

// Exécution avec tous les paramètres
$stmt->execute([
    ':mongo_id'            => $mongoId,
    ':covoiturage_id'      => $id,
    ':utilisateur_id'      => $uid,
    ':utilisateur_concerne'=> $chauffeurId,
    ':commentaire'         => $commentaire,
    ':statut_id'           => 1,         // ou tout autre statut par défaut voulu
]);

    // ➔ Après signalement ➔ rediriger vers confirmation
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
    <style>
        body { font-family: sans-serif; padding: 2rem; }
        textarea { width: 100%; height: 8em; }
        .error { color: red; margin-bottom: 1em; }
    </style>
</head>
<body class="report-problem">
    <div class="container py-4">
        <h1 class="mb-3">Signaler un problème pour le trajet #<?= htmlspecialchars($id) ?></h1>

        <?php if ($error === 'empty'): ?>
            <div class="error">Veuillez décrire le problème avant d'envoyer.</div>
        <?php endif; ?>

        <form method="post" action="?id=<?= urlencode($id) ?>&ok=0">
            <div class="mb-3">
                <label for="commentaire" class="form-label" rows="5">Votre commentaire :</label>
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
