<?php
// src/Middleware/AuthMiddleware.php
namespace Adminlocal\EcoRide\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function requireJwtAuth(): void
{
    if (empty($_COOKIE['eco_jwt'])) {
        header('Location: /login');
        exit;
    }

    $token  = $_COOKIE['eco_jwt'];
    $secret = getenv('JWT_SECRET') ?: ''; // nulle fallback, on doit toujours le définir en .env

    try {
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        $_SERVER['auth_user_id'] = $decoded->sub;
        if (isset($decoded->email)) {
            $_SERVER['auth_email'] = $decoded->email;
        }
    } catch (\Exception $e) {
        // suppression du cookie de façon sécurisée
        setcookie('eco_jwt', '', time() - 3600, '/', '', true, true);
        header('Location: /login?expired=1');
        exit;
    }
}
