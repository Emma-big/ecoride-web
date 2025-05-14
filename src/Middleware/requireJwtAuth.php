<?php
// src/Middleware/requireJwtAuth.php

namespace Adminlocal\EcoRide\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function requireJwtAuth(): void
{
    // 1. Si pas de cookie, on redirige vers le login
    if (empty($_COOKIE['eco_jwt'])) {
        header('Location: /login');
        exit;
    }

    $token  = $_COOKIE['eco_jwt'];
    $secret = getenv('JWT_SECRET') ?: 'fallback_secret';

    try {
        // 2. Décodage et vérification
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));

        // 3. Exposer les données de l’utilisateur pour le reste de l’app
        $_SERVER['auth_user_id'] = $decoded->sub;
        // Si vous avez inclus d’autres champs dans le payload, par ex. email :
        if (isset($decoded->email)) {
            $_SERVER['auth_email'] = $decoded->email;
        }

    } catch (\Exception $e) {
        // 4. En cas d’erreur (jeton expiré ou invalide), on efface le cookie et redirige
        setcookie('eco_jwt', '', time() - 3600, '/', '', true, true);
        header('Location: /login?expired=1');
        exit;
    }
}
