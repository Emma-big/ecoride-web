<?php
namespace Adminlocal\EcoRide\Controllers\Post;

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

// Helpers de validation
function validateModel(string $m): bool {
    return (bool) preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ]{1,15}$/u", $m);
}
function validateColor(string $c): bool {
    return (bool) preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ]{1,15}$/u", $c);
}
function validateImmat(string $i): bool {
    return (bool) preg_match("/^[A-Za-z0-9-]{1,15}$/", $i);
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer
    $marqueForm        = trim($_POST['marque_id'] ?? '');
    $modeleForm        = trim($_POST['modele'] ?? '');
    $couleurForm       = trim($_POST['couleur'] ?? '');
    $immatForm         = strtoupper(trim($_POST['immatriculation'] ?? ''));
    $premiereImmatForm = $_POST['date_premiere_immat'] ?? '';
    $energieId         = trim($_POST['energie_id'] ?? '');
    $proprioId         = (int) ($_SESSION['user']['utilisateur_id'] ?? 0);

    // Validation serveur
    $errors = [];
    if (!validateModel($modeleForm)) {
        $errors[] = 'Modèle invalide (1–15 lettres).';
    }
    if (!validateColor($couleurForm)) {
        $errors[] = 'Couleur invalide (1–15 lettres).';
    }
    if (!validateImmat($immatForm)) {
        $errors[] = 'Immatriculation invalide (1–15 caractères alphanumériques ou tirets).';
    }
    if (empty($marqueForm) || empty($energieId  ) || empty($premiereImmatForm)) {
        $errors[] = 'Tous les champs sont requis.';
    }
    if ($errors) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old']         = $_POST;
        header('Location: /vehiculeForm');
        exit;
    }

    // Vérifier doublon immatriculation
    $stmt = $pdo->prepare(
        'SELECT voiture_id FROM voitures WHERE immatriculation = :immatriculation AND deleted_at IS NULL'
    );
    $stmt->execute([':immatriculation' => $immatForm]);
    if ($stmt->rowCount() > 0) {
        $message = 'Cette immatriculation est déjà enregistrée.';
    } else {
        // Insertion avec created_at et updated_at
        $stmt2 = $pdo->prepare(
            'INSERT INTO voitures (
                proprietaire_id, marque_id, modele, immatriculation,
                couleur, date_premiere_immat, energie, created_at, updated_at
            ) VALUES (
                :proprio_id, :marque_id, :modele, :immatriculation,
                :couleur, :date_premiere_immat, :energie, NOW(), NOW()
            )'
        );
        $stmt2->execute([
            ':proprio_id'           => $proprioId,
            ':marque_id'            => $marqueForm,
            ':modele'               => $modeleForm,
            ':immatriculation'      => $immatForm,
            ':couleur'              => $couleurForm,
            ':date_premiere_immat'  => $premiereImmatForm,
            ':energie'              => $energieId  ,
        ]);
        if ($stmt2->rowCount()) {
            $message = 'Voiture enregistré avec succès !';
            $success = true;
        } else {
            $message = 'Une erreur est survenue lors de l\'enregistrement.';
        }
    }
}

// Variables pour le layout
$pageTitle   = 'Résultat voiture';
$extraStyles = [
    '/assets/style/styleMessageLogin.css',
    '/assets/style/styleIndex.css'
];

// Affichage du message
ob_start();
?>
<div class="formLogin mx-auto text-center">
    <p class="<?= $success ? 'text-success' : 'text-danger' ?> fw-bold">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </p>
    <a href="/utilisateur" class="btn btn-primary mt-3">Retour à mon espace</a>
</div>
<?php
$mainContent = ob_get_clean();
require_once BASE_PATH . '/src/layout.php';
exit;
