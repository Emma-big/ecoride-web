<?php
// src/views/headerAdmin.php
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="/index.php">EcoRide</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarAdmin" aria-controls="navbarAdmin" aria-expanded="false"
                    aria-label="Basculer la navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarAdmin">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/index">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/covoiturage">Covoiturages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/admin">Administration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/deconnexion">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
