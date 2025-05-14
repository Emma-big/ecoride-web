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
    $secret = getenv('JWT_SECRET') ?: 'votre_clé_très_secrète';

    try {
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        $_SERVER['auth_user_id'] = $decoded->sub;
    } catch (\Exception $e) {
        setcookie('eco_jwt', '', time() - 3600, '/');
        header('Location: /login?expired=1');
        exit;
    }
}
