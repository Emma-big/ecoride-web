<?php
// Pour qu’on voie clairement le texte brut
header('Content-Type: text/plain; charset=utf-8');

// Renseigner ici le mot de passe admin exact
$motDePasse = '12345!Ma';

// Génération du hash bcrypt
$hash = password_hash($motDePasse, PASSWORD_BCRYPT);

echo "Voici votre hash bcrypt :\n\n" . $hash . "\n";