<?php
namespace Adminlocal\EcoRide\Controllers\Post;

use PDOException;

// 1) Démarrer la session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Méthode POST uniquement
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /covoiturageForm');
    exit;
}

// 2.5) Vérification CSRF
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('Requête non autorisée (CSRF).');
}

// 3) Charger la BDD
$pdo = require BASE_PATH . '/src/config.php';

// 4) Nettoyer les données
$input = [
    'createur'       => trim($_POST['createur'] ?? ''),
    'ville_depart'   => trim($_POST['ville_depart'] ?? ''),
    'ville_arrivee'  => trim($_POST['ville_arrivee'] ?? ''),
    'depart_lat'     => trim($_POST['depart_lat'] ?? ''),
    'depart_lng'     => trim($_POST['depart_lng'] ?? ''),
    'arrivee_lat'    => trim($_POST['arrivee_lat'] ?? ''),
    'arrivee_lng'    => trim($_POST['arrivee_lng'] ?? ''),
    'date_depart'    => trim($_POST['date_depart'] ?? ''),
    'heure_depart'   => trim($_POST['heure_depart'] ?? ''),
    'date_arrivee'   => trim($_POST['date_arrivee'] ?? ''),
    'heure_arrivee'  => trim($_POST['heure_arrivee'] ?? ''),
    'accepts_smoker' => isset($_POST['accepts_smoker']) ? 1 : 0,
    'accepts_animals'=> isset($_POST['accepts_animals']) ? 1 : 0,
    'prefs_libres'   => trim($_POST['prefs_libres'] ?? ''),
    'prix'           => trim($_POST['prix'] ?? ''),
    'nombre_place'   => trim($_POST['nombre_place'] ?? ''),
    'voiture_id'     => trim($_POST['voiture_id'] ?? ''),
];

$errors = [];

// 5) Helpers de validation
function validateAddress(string $addr): bool {
    return (bool) preg_match("/^.{5,150}$/u", $addr);
}
function validateDate(string $d): bool {
    $dt = \DateTime::createFromFormat('Y-m-d', $d);
    return $dt && $dt->format('Y-m-d') === $d;
}
function validateTime(string $t): bool {
    return (bool) preg_match('/^[0-2]\d:[0-5]\d$/', $t);
}

// 6) Validation des champs
if (empty($input['ville_depart']) || !validateAddress($input['ville_depart'])) {
    $errors['ville_depart'] = 'Adresse de départ invalide (5–150 caractères).';
}
if (empty($input['ville_arrivee']) || !validateAddress($input['ville_arrivee'])) {
    $errors['ville_arrivee'] = 'Adresse d’arrivée invalide (5–150 caractères).';
}
if (!validateDate($input['date_depart'])) {
    $errors['date_depart'] = 'Date de départ invalide.';
}
if (!validateTime($input['heure_depart'])) {
    $errors['heure_depart'] = 'Heure de départ invalide.';
}
if (!validateDate($input['date_arrivee'])) {
    $errors['date_arrivee'] = 'Date d’arrivée invalide.';
}
if (!validateTime($input['heure_arrivee'])) {
    $errors['heure_arrivee'] = 'Heure d’arrivée invalide.';
}

// **6bis) Interdire un départ dans le passé**
if (empty($errors['date_depart']) && empty($errors['heure_depart'])) {
    $dtDepart = \DateTime::createFromFormat(
        'Y-m-d H:i',
        "{$input['date_depart']} {$input['heure_depart']}"
    );
    if (! $dtDepart) {
        $errors['date_depart'] = 'Format date/heure de départ invalide.';
    } elseif ($dtDepart <= new \DateTime()) {
        $errors['date_depart'] = 'La date et l’heure de départ doivent être dans le futur.';
    }
}

// Chronologie arrivée > départ
if (
    empty($errors['date_depart'])
    && empty($errors['heure_depart'])
    && empty($errors['date_arrivee'])
    && empty($errors['heure_arrivee'])
) {
    $d1 = new \DateTime("{$input['date_depart']} {$input['heure_depart']}");
    $d2 = new \DateTime("{$input['date_arrivee']} {$input['heure_arrivee']}");
    if ($d2 <= $d1) {
        $errors['chrono'] = 'La date/heure d’arrivée doit être après le départ.';
    }
}

if (!preg_match('/^\d+(\.\d{1,2})?$/', $input['prix']) || (float)$input['prix'] < 2) {
    $errors['prix'] = 'Le prix doit être ≥ 2 et au format décimal.';
}
if (!filter_var($input['nombre_place'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>6]])) {
    $errors['nombre_place'] = 'Nombre de places invalide (1–6).';
}
if (!filter_var($input['voiture_id'], FILTER_VALIDATE_INT)) {
    $errors['voiture_id'] = 'Voiture invalide.';
}

// Préférences libres
$prefs = array_filter(array_map('trim', explode("\n", $input['prefs_libres'])));
foreach ($prefs as $i => $line) {
    if (strlen($line) > 100) {
        $errors["prefs_{$i}"] = 'Préférence ligne ' . ($i+1) . ' trop longue (max 100).';
    }
}

// 7) En cas d'erreurs, redirection
if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old']         = $input;
    header('Location: /covoiturageForm');
    exit;
}

// 9) Vérifier utilisateur
$userId = $_SESSION['user']['utilisateur_id'] ?? 0;
if ((int)$input['createur'] !== $userId) {
    exit('Erreur de session, utilisateur invalide.');
}

// 10) Vérifier que la voiture appartient à l’utilisateur
$stmt = $pdo->prepare("SELECT COUNT(*) FROM voitures WHERE voiture_id=:vid AND proprietaire_id=:uid");
$stmt->execute([':vid'=>$input['voiture_id'], 'uid'=>$userId]);
if (!$stmt->fetchColumn()) {
    exit('Voiture non autorisé.');
}

// 11) Calcul écologie
$stmtE = $pdo->prepare("SELECT en.libelle FROM voitures v JOIN energies en ON en.energie_id=v.energie WHERE v.voiture_id=:vid");
$stmtE->execute([':vid'=>$input['voiture_id']]);
$norm = iconv('UTF-8','ASCII//TRANSLIT',$stmtE->fetchColumn());
$input['ecologique'] = stripos($norm, 'electri') !== false ? 1 : 0;

// 12) Insertion covoiturage
$ins = $pdo->prepare(
    "INSERT INTO covoiturage (
        date_depart, heure_depart, lieu_depart,
        depart_lat, depart_lng,
        date_arrive, heure_arrive, lieu_arrive,
        arrivee_lat, arrivee_lng,
        nb_place, prix_personne, statut_id,
        voiture_id, utilisateur, ecologique,
        accepts_smoker, accepts_animal
    ) VALUES (
        :dd, :hd, :ld,
        :lat_d, :lng_d,
        :da, :ha, :la,
        :lat_a, :lng_a,
        :nb, :prix, 3,
        :vid, :uid, :eco,
        :smoke, :animal
    )"
);
$ins->execute([
    ':dd'     => $input['date_depart'],
    ':hd'     => $input['heure_depart'],
    ':ld'     => $input['ville_depart'],
    ':lat_d'  => $input['depart_lat'],
    ':lng_d'  => $input['depart_lng'],
    ':da'     => $input['date_arrivee'],
    ':ha'     => $input['heure_arrivee'],
    ':la'     => $input['ville_arrivee'],
    ':lat_a'  => $input['arrivee_lat'],
    ':lng_a'  => $input['arrivee_lng'],
    ':nb'     => $input['nombre_place'],
    ':prix'   => $input['prix'],
    ':vid'    => $input['voiture_id'],
    ':uid'    => $userId,
    ':eco'    => $input['ecologique'],
    ':smoke'  => $input['accepts_smoker'],
    ':animal' => $input['accepts_animals'],
]);

// 13) Insertion prefs libres
if (!empty($prefs)) {
    $ins2 = $pdo->prepare("INSERT INTO covoiturage_preferences (covoiturage_id, libelle) VALUES (:cid, :libelle)");
    $cid  = $pdo->lastInsertId();
    foreach ($prefs as $lib) {
        $ins2->execute([':cid' => $cid, ':libelle' => $lib]);
    }
}

// 14) Régénérer un nouveau CSRF token pour la prochaine action
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// 15) Redirection finale
header('Location: /utilisateur');
exit;
