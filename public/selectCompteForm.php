<?php
// public/selectCompteForm.php

// Démarrage de session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusion du formulaire de sélection de compte (dans src/forms)
$path = __DIR__ . '/../src/forms/selectCompteForm.php';
if (!file_exists($path)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "ERREUR : formulaire introuvable ici :\n" . $path;
    exit;
}
require_once $path;
