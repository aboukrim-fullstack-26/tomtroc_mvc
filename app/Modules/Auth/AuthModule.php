<?php
namespace App\Modules\Auth;

use App\Core\Router;

final class AuthModule
{
    public static function register(Router $router): void
    {
        $router->get('/connexion', [\App\Controllers\AuthController::class, 'login']);
        $router->post('/connexion', [\App\Controllers\AuthController::class, 'login']);

        $router->get('/inscription', [\App\Controllers\AuthController::class, 'register']);
        $router->post('/inscription', [\App\Controllers\AuthController::class, 'register']);

        $router->get('/deconnexion', [\App\Controllers\AuthController::class, 'logout']);
    }
}
