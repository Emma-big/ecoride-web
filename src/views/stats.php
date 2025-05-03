<?php
// src/views/stats.php

// 1) Calcul des dates par défaut (30 derniers jours)
$defaultEnd   = date('Y-m-d');
$defaultStart = date('Y-m-d', strtotime('-30 days'));

// 2) Récupération des paramètres GET (ou valeurs par défaut)
$start = $_GET['start'] ?? $defaultStart;
$end   = $_GET['end']   ?? $defaultEnd;
?>

<div class="stats-page container my-5">
  <form method="get" action="/stats" class="row g-3 align-items-end mb-4">
    <div class="col-auto">
      <label for="start-date" class="form-label">Du&nbsp;:</label>
      <input type="date"
             id="start-date"
             name="start"
             class="form-control"
             value="<?= htmlspecialchars($start, ENT_QUOTES) ?>">
    </div>
    <div class="col-auto">
      <label for="end-date" class="form-label">Au&nbsp;:</label>
      <input type="date"
             id="end-date"
             name="end"
             class="form-control"
             value="<?= htmlspecialchars($end, ENT_QUOTES) ?>">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary mb-3">Afficher</button>
    </div>
  </form>

  <div class="stat-container">
    <h2 class="h4 mb-3">Nombre de covoiturages par jour</h2>
    <canvas id="chartCovoiturages"
            class="mb-5"
            style="max-width:100%; height:300px;"></canvas>

    <h2 class="h4 mb-3">Gains (en crédits) générés par jour</h2>
    <canvas id="chartCredits"
            style="max-width:100%; height:300px;"></canvas>
  </div>
</div>

<!-- Chart.js depuis CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
  async function fetchData(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error(res.statusText);
    return res.json();
  }

  // 1) Cov oiturages
  fetchData(`/stats/data1?start=<?= urlencode($start) ?>&end=<?= urlencode($end) ?>`)
    .then(json => {
      new Chart(document.getElementById('chartCovoiturages'), {
        type: 'bar',
        data: {
          labels: json.jours,
          datasets: [{
            label: 'Covoiturages',
            data: json.values,
            backgroundColor: 'rgba(66,122,161,0.6)',
            borderColor: 'rgba(66,122,161,1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    })
    .catch(console.error);

  // 2) Crédits
  fetchData(`/stats/data2?start=<?= urlencode($start) ?>&end=<?= urlencode($end) ?>`)
    .then(json => {
      new Chart(document.getElementById('chartCredits'), {
        type: 'bar',
        data: {
          labels: json.jours,
          datasets: [{
            label: 'Crédits',
            data: json.values,
            backgroundColor: 'rgba(103,148,54,0.6)',
            borderColor: 'rgba(103,148,54,1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    })
    .catch(console.error);
</script>
