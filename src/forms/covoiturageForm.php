<?php
namespace Adminlocal\EcoRide\Forms;

// Charger l'autoloader Composer
require_once BASE_PATH . '/vendor/autoload.php';

// En local, charger .env s'il existe
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
} else {
    error_log('No .env file found, skipping Dotenv load');
}

// 1) Démarrage de la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Charger la config PDO
$pdo = require BASE_PATH . '/src/config.php';

// 2.5) Générer un nouveau CSRF token si besoin
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3) Vérifier le rôle "chauffeur"
if (empty($_SESSION["user"]["is_chauffeur"])) {
    header("Location: /accessDenied");
    exit;
}

// 4) Afficher les erreurs de validation (puis les vider)
if (!empty($_SESSION['form_errors'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($_SESSION['form_errors'] as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php
    unset($_SESSION['form_errors']);
endif;

// 5) Récupérer les anciennes valeurs en cas d'erreur
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

// 6) Récupération de l'ID utilisateur
$stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateurs WHERE pseudo = :pseudo");
$stmt->execute([':pseudo' => $_SESSION["user"]["pseudo"]]);
$createurIdHidden = (int) $stmt->fetchColumn();

// 7) Vérifier que l’utilisateur a au moins une voiture non supprimée
$stmt = $pdo->prepare(
    "SELECT v.voiture_id, CONCAT(m.libelle,' ',v.modele) AS display
      FROM voitures v
      JOIN marques m ON v.marque_id = m.marque_id
     WHERE v.proprietaire_id = :uid
       AND v.deleted_at IS NULL"
);
$stmt->execute([':uid' => $createurIdHidden]);
$voitures = $stmt->fetchAll(\PDO::FETCH_ASSOC);
if (empty($voitures)) {
    require_once BASE_PATH . '/src/forms/besoinVehicule.php';
    exit;
}
?>

<main class="container my-4">
    <h2 class="text-center mt-4">Création d'un covoiturage</h2>
    <form id="trajetForm" class="formLogin mx-auto" action="/registerCovoituragePost" method="POST" novalidate>

        <input type="hidden" name="createur" value="<?= htmlspecialchars($createurIdHidden, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

        <!-- Adresse de départ -->
        <div class="mb-3">
            <label for="ville_depart" class="form-label">Adresse de départ</label>
            <input type="text" id="ville_depart" name="ville_depart" class="form-control" required maxlength="150" pattern=".{5,150}" value="<?= htmlspecialchars($old['ville_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div class="invalid-feedback">Adresse invalide (5–150 caractères).</div>
        </div>

        <!-- Adresse d'arrivée -->
        <div class="mb-3">
            <label for="ville_arrivee" class="form-label">Adresse d'arrivée</label>
            <input type="text" id="ville_arrivee" name="ville_arrivee" class="form-control" required maxlength="150" pattern=".{5,150}" value="<?= htmlspecialchars($old['ville_arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div class="invalid-feedback">Adresse invalide (5–150 caractères).</div>
        </div>

        <!-- Champs cachés lat/lng -->
        <input type="hidden" id="depart_lat"  name="depart_lat">
        <input type="hidden" id="depart_lng"  name="depart_lng">
        <input type="hidden" id="arrivee_lat" name="arrivee_lat">
        <input type="hidden" id="arrivee_lng" name="arrivee_lng">

        <!-- Date & heure de départ -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="date_depart" class="form-label">Date de départ</label>
                <input type="date" id="date_depart" name="date_depart" class="form-control" required value="<?= htmlspecialchars($old['date_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="heure_depart" class="form-label">Heure de départ</label>
                <input type="time" id="heure_depart" name="heure_depart" class="form-control" required value="<?= htmlspecialchars($old['heure_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <!-- Date & heure d'arrivée -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="date_arrivee" class="form-label">Date d'arrivée</label>
                <input type="date" id="date_arrivee" name="date_arrivee" class="form-control" required value="<?= htmlspecialchars($old['date_arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="heure_arrivee" class="form-label">Heure d'arrivée</label>
                <input type="time" id="heure_arrivee" name="heure_arrivee" class="form-control" required value="<?= htmlspecialchars($old['heure_arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <!-- Options fumeurs/animaux -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="accepts_smoker" name="accepts_smoker" value="1" <?= !empty($old['accepts_smoker']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="accepts_smoker">Fumeurs acceptés</label>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="accepts_animals" name="accepts_animals" value="1" <?= !empty($old['accepts_animals']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="accepts_animals">Animaux acceptés</label>
        </div>

        <!-- Préférences libres -->
        <div class="mb-3">
            <label for="prefs_libres" class="form-label">Autres préférences (une par ligne)</label>
            <textarea class="form-control" id="prefs_libres" name="prefs_libres" rows="4" maxlength="100" placeholder="Ex : Pas de musique forte"><?= htmlspecialchars($old['prefs_libres'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- Prix par personne -->
        <div class="mb-3">
            <label for="prix" class="form-label">Prix par personne (crédits)</label>
            <input type="number" id="prix" name="prix" class="form-control" min="2" step="0.01" required value="<?= htmlspecialchars($old['prix'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <small class="text-white">La plateforme perçoit 2 crédits par passager.</small>
        </div>

        <!-- Nombre de places -->
        <div class="mb-3">
            <label for="nombre_place" class="form-label">Nombre de places</label>
            <select id="nombre_place" name="nombre_place" class="form-select" required>
                <option value="">--Choisissez--</option>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <option value="<?= $i ?>" <?= (isset($old['nombre_place']) && $old['nombre_place'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Voitures -->
        <div class="mb-3">
            <label for="voiture_id" class="form-label">Voiture</label>
            <select id="voiture_id" name="voiture_id" class="form-select" required>
                <option value="">--Choisissez--</option>
                <?php foreach ($voitures as $v): ?>
                    <option value="<?= htmlspecialchars($v['voiture_id'], ENT_QUOTES, 'UTF-8') ?>" <?= (isset($old['voiture_id']) && $old['voiture_id'] == $v['voiture_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($v['display'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Créer</button>
        </div>

    </form>
</main>
<!-- Réintégration de l’API Google Maps pour initAutocomplete -->
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode($_ENV['GOOGLE_API_KEY'] ?? '') ?>&libraries=places&callback=initAutocomplete">
</script>
<script>
  (function(){
    'use strict';
    const form = document.getElementById('trajetForm');
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) {
        e.preventDefault(); e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  })();

  function initAutocomplete() {
    ['ville_depart','ville_arrivee'].forEach(id => {
      const inp = document.getElementById(id);
      const auto = new google.maps.places.Autocomplete(inp, { types: ['address'], componentRestrictions: { country: 'fr' } });
      auto.addListener('place_changed', () => {
        const p = auto.getPlace();
        if (!p.geometry) return;
        document.getElementById(id==='ville_depart'?'depart_lat':'arrivee_lat').value = p.geometry.location.lat().toFixed(7);
        document.getElementById(id==='ville_depart'?'depart_lng':'arrivee_lng').value = p.geometry.location.lng().toFixed(7);
      });
    });
  }
</script>
