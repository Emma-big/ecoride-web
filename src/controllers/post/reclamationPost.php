<?php
namespace Adminlocal\EcoRide\Controllers\Post;

use Adminlocal\EcoRide\Helpers\MongoHelper;

// 1) Démarrer la session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la config PDO
require_once BASE_PATH . '/config/database.php';

// 3) Méthode POST uniquement
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /reclamationForm?id=' . urlencode($_POST['covoiturage_id'] ?? ''));
    exit;
}

// 4) Récupérer et nettoyer
$input = [
    'covoiturage_id' => (int)($_POST['covoiturage_id'] ?? 0),
    'commentaire'    => trim($_POST['commentaire'] ?? ''),
    'passager'       => (int)($_SESSION['user']['utilisateur_id'] ?? 0),
];

$errors = [];

// 5) Validation
if ($input['covoiturage_id'] <= 0) {
    $errors[] = 'ID de covoiturage invalide.';
}
if ($input['commentaire'] === '') {
    $errors['commentaire'] = 'Le message est requis.';
} elseif (mb_strlen($input['commentaire']) > 500) {
    $errors['commentaire'] = 'Le message ne doit pas dépasser 500 caractères.';
}

// 6) En cas d’erreurs, rediriger
if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old']         = ['commentaire' => $input['commentaire']];
    header('Location: /reclamationForm?id=' . $input['covoiturage_id']);
    exit;
}

// 7) Récupérer l’ID du chauffeur lié au trajet
$stmt = $pdo->prepare("SELECT utilisateur FROM covoiturage WHERE covoiturage_id = :id");
$stmt->execute([':id' => $input['covoiturage_id']]);
$chauffeurId = (int) $stmt->fetchColumn();

// 8) Insertion dans MongoDB
$collection = MongoHelper::getCollection('reclamations');
$mongoResult = $collection->insertOne([
    'covoiturage_id'      => $input['covoiturage_id'],
    'utilisateur_id'      => $input['passager'],
    'utilisateur_concerne'=> $chauffeurId,
    'commentaire'         => $input['commentaire'],
    'statut_id'           => 3,
    'date_signal'         => new \DateTimeImmutable(),
]);
$mongoId = (string) $mongoResult->getInsertedId();

// 9) Insertion dans la table Réclamations (statut_id = 3 pour "En attente")
$ins = $pdo->prepare(
    "INSERT INTO reclamations
        (mongo_id, covoiturage_id, utilisateur_id, utilisateur_concerne, commentaire, statut_id, date_signal)
     VALUES
        (:mongo_id, :covoiturage_id, :passager, :chauffeur, :comment, 3, NOW())"
);
$ins->execute([
    ':mongo_id'       => $mongoId,
    ':covoiturage_id' => $input['covoiturage_id'],
    ':passager'       => $input['passager'],
    ':chauffeur'      => $chauffeurId,
    ':comment'        => $input['commentaire'],
]);

// 10) Confirmation et redirection
$_SESSION['flash'] = 'Réclamation enregistrée.';
header('Location: /utilisateur');
exit;
