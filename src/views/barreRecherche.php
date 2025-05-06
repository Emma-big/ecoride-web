<?php
// src/views/barreRecherche.php
// Fragment de la barre de recherche de trajets
?>
<form id="searchForm" action="/covoiturage" method="get"
      class="row g-3 justify-content-center mb-5" novalidate>
  <div class="col-md-4">
    <label for="depart" class="form-label">Adresse de départ</label>
    <input
      type="text"
      id="depart"
      name="depart"
      class="form-control"
      required
      maxlength="150"
      pattern=".{5,150}"
      value="<?= htmlspecialchars($_GET['depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >
    <div class="invalid-feedback">Adresse invalide (5–150 caractères).</div>
  </div>
  <div class="col-md-4">
    <label for="arrivee" class="form-label">Adresse d'arrivée</label>
    <input
      type="text"
      id="arrivee"
      name="arrivee"
      class="form-control"
      required
      maxlength="150"
      pattern=".{5,150}"
      value="<?= htmlspecialchars($_GET['arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >
    <div class="invalid-feedback">Adresse invalide (5–150 caractères).</div>
  </div>
  <div class="col-md-3">
    <label for="date" class="form-label">Date de départ</label>
    <input
      type="date"
      id="date"
      name="date"
      class="form-control"
      required
      value="<?= htmlspecialchars($_GET['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >
    <div class="invalid-feedback">Date requise.</div>
  </div>
  <div class="col-12 text-center">
    <button class="btn btn-primary">Rechercher</button>
  </div>
</form>

<!-- Charger Google Places API pour autocomplétion -->
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode($_ENV['GOOGLE_API_KEY'] ?? getenv('GOOGLE_API_KEY')) ?>&callback=initMap">
</script>

<script>
(function() {
  'use strict';

  // Validation front-end Bootstrap
  const form = document.getElementById('searchForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  }

  // Autocomplete Google Maps
  window.initSearchAutocomplete = function() {
    ['depart', 'arrivee'].forEach(id => {
      const el = document.getElementById(id);
      if (el) {
        new google.maps.places.Autocomplete(el, {
          types: ['address'],
          componentRestrictions: { country: 'fr' }
        });
      }
    });
  }
})();
</script>
