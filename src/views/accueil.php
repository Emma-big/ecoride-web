<?php
// src/views/accueil.php

// 1) Démarrer la session si nécessaire
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2) Inclusion du header et titre principal
require_once BASE_PATH . '/src/controllers/principal/scriptHeader.php';
require_once BASE_PATH . '/src/views/bigTitle.php';
?>

<div class="container mt-5">
    <!-- Première carte -->
    <div class="card mb-4">
        <div class="row g-0 align-items-center">
            <div class="col-md-6 text-center">
                <img src="/assets/images/carbonempreinte.png"
                     class="img-fluid rounded-start card-img-empreinte"
                     alt="Réduction de l'empreinte carbone">
            </div>
            <div class="col-md-6">
                <div class="card-body">
                    <h2 class="card-title">Réduisez votre empreinte carbone en partageant vos trajets !</h2>
                    <p class="card-text">
                        Découvrez notre plateforme innovante de covoiturage écologique, conçue pour connecter les voyageurs soucieux de l'environnement.
                        En partageant vos trajets, vous réduisez drastiquement votre empreinte carbone tout en rejoignant une communauté engagée pour un avenir plus vert.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième carte -->
    <div class="card mb-4">
        <div class="row g-0 align-items-center flex-row-reverse">
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <img src="/assets/images/accueil1.png"
                     class="img-fluid rounded-start card-img-impact"
                     alt="Impact positif">
            </div>
            <div class="col-md-6">
                <div class="card-body">
                    <h2 class="card-title">Une seule voiture, un impact positif pour tout le monde</h2>
                    <p class="card-text">
                        Et si une seule voiture suffisait à transporter plusieurs personnes ? Moins de pollution, moins d’embouteillages et plus d’économies.
                        Ensemble, faisons un pas vers un mode de transport plus pratique, économique et écologique.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
