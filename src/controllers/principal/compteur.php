<div class="text-center my-4">
  <h4 class="compteur-title">
    Nombre de crédits gagnés par la plateforme :
  </h4>
  <div id="total-credits" class="compteur-value text-white" aria-live="polite">
    …
  </div>
</div>

<script>
  async function updateCredits() {
    try {
      const res  = await fetch('/compteur_api.php');
      const json = await res.json();
      document.getElementById('total-credits').textContent =
        new Intl.NumberFormat('fr-FR').format(json.total);
    } catch (err) {
      console.error('Erreur chargement crédit :', err);
    }
  }

  // Mise à jour immédiate et toutes les 30 s
  updateCredits();
  setInterval(updateCredits, 30000);
</script>