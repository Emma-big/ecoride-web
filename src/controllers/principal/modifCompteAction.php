<?php
// public/modifCompteAction.php

// 1) Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Déléguer à src/forms/modifCompteAction.php
$path = __DIR__ . '/../src/forms/modifCompteAction.php';
if (! file_exists($path)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "ERREUR : action introuvable ici :\n" . $path;
    exit;
}
require_once $path;
