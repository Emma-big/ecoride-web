<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

// 3) POST uniquement
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /noteForm?id=' . urlencode($_POST['covoiturage_id'] ?? ''));
    exit;
}

// 4) Récupérer covoiturage_id, note, commentaire
$input = [
    'covoiturage_id' => (int)($_POST['covoiturage_id'] ?? 0),
    'note'           => trim($_POST['note'] ?? ''),
    'commentaire'    => trim($_POST['commentaire'] ?? ''),
];

// 5) Déterminer passager & chauffeur automatiquement
$input['passager_id'] = $_SESSION['user']['utilisateur_id'] ?? 0;

// On récupère l'ID du conducteur depuis la colonne `utilisateur` de la table `covoiturage`
$stmtChauffeur = $pdo->prepare(
    "SELECT utilisateur AS chauffeur_id
       FROM covoiturage
      WHERE covoiturage_id = :id"
);
$stmtChauffeur->execute(['id' => $input['covoiturage_id']]);
$covoit = $stmtChauffeur->fetch(\PDO::FETCH_ASSOC);

$input['chauffeur_id'] = $covoit['chauffeur_id'] ?? 0;

// 6) Validation basique
$errors = [];
if ($input['covoiturage_id'] <= 0) {
    $errors['covoiturage_id'] = 'ID du covoiturage invalide.';
}
if (!in_array($input['note'], ['1','2','3','4','5'], true)) {
    $errors['note'] = 'Veuillez choisir une note entre 1 et 5.';
}
if ($input['commentaire'] === '') {
    $errors['commentaire'] = 'Le commentaire ne peut pas être vide.';
} elseif (mb_strlen($input['commentaire']) > 500) {
    $errors['commentaire'] = 'Le commentaire ne doit pas dépasser 500 caractères.';
}

// 7) Vérifications supplémentaires

// a) Chauffeur existe ?
$stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE utilisateur_id = :id");
$stmt->execute(['id' => $input['chauffeur_id']]);
if ((int)$stmt->fetchColumn() === 0) {
    $errors['chauffeur_id'] = 'Le chauffeur sélectionné n\'existe pas.';
}

// b) Passager existe / non suspendu / actif
$stmt = $pdo->prepare(
    "SELECT role, is_passager
      FROM utilisateurs
     WHERE utilisateur_id = :id"
);
$stmt->execute(['id' => $input['passager_id']]);
$passager = $stmt->fetch(\PDO::FETCH_ASSOC);
if (!$passager) {
    $errors['passager_id'] = 'Le passager n\'existe pas.';
} else {
    if ((int)$passager['role'] === 4) {
        $errors['suspendu'] = 'Votre compte est suspendu. Vous ne pouvez pas noter.';
    }
    if ((int)$passager['is_passager'] === 0) {
        $errors['actif'] = 'Votre compte n\'est pas actif en tant que passager. Vous ne pouvez pas noter.';
    }
}

// c) Trajet existe ?
$stmt = $pdo->prepare("SELECT COUNT(*) FROM covoiturage WHERE covoiturage_id = :id");
$stmt->execute(['id' => $input['covoiturage_id']]);
if ((int)$stmt->fetchColumn() === 0) {
    $errors['covoiturage_id'] = 'Le trajet sélectionné n\'existe pas.';
}

// d) Pas déjà noté
$stmt = $pdo->prepare(
    "SELECT COUNT(*)
      FROM notes
     WHERE passager_id = :pid
       AND covoiturage_id = :cid"
);
$stmt->execute([
    'pid' => $input['passager_id'],
    'cid' => $input['covoiturage_id'],
]);
if ((int)$stmt->fetchColumn() > 0) {
    $errors['duplicate'] = 'Vous avez déjà noté ce trajet.';
}

// 8) En cas d’erreurs → retour au formulaire
if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old'] = [
        'note'        => $input['note'],
        'commentaire' => $input['commentaire'],
    ];
    header('Location: /noteForm?id=' . $input['covoiturage_id']);
    exit;
}

// 9) Insertion
try {
    $stmt = $pdo->prepare(
        "INSERT INTO notes
            (note, commentaire, chauffeur_id, covoiturage_id, passager_id, statut_id)
        VALUES
            (:note, :commentaire, :chauffeur, :covoiturage_id, :passager_id, 4)"
    );
    $stmt->execute([
        ':note'           => $input['note'],
        ':commentaire'    => $input['commentaire'],
        ':chauffeur'      => $input['chauffeur_id'],
        ':covoiturage_id' => $input['covoiturage_id'],
        ':passager_id'    => $input['passager_id'],
    ]);

    header('Location: /confirmation-reclamations');
    exit;
} catch (\PDOException $e) {
    http_response_code(500);
    exit('Erreur interne lors de l’enregistrement de l’avis.');
}
