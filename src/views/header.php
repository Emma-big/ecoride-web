<?php
// src/views/header.php
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark site-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="/index">EcoRide</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarPublic" aria-controls="navbarPublic" aria-expanded="false"
                    aria-label="Basculer la navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarPublic">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/index">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/covoiturage">Covoiturages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/login">Connexion / Inscription</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
