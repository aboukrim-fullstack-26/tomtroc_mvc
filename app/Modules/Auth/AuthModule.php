<?php
/**
 * app/Modules/Auth/AuthModule.php
 *
 * Rôle :
 * - Point d’entrée / composant du MVC TomTroc.
 * - Commentaires ajoutés pour faciliter debug & évolutions (V4 stable).
 *
 * Ordre d’exécution (général) :
 * public/index.php → app/bootstrap.php → Router → Controller → Model(s) → View(s)
 *
 * @author aboukrim
 * @date 2026-02-10
 */

namespace App\Modules\Auth;

use App\Core\Router;

/**
 * AuthModule (V4)
 * Déclare les routes du module Auth.
 * Activer/désactiver via app/config/modules.php
 */
final class AuthModule
{
    public static function register(Router $router): void
    {
        $router->get('/connexion', [\App\Controllers\AuthController::class, 'loginForm']);
        $router->post('/connexion', [\App\Controllers\AuthController::class, 'login']);
        $router->get('/inscription', [\App\Controllers\AuthController::class, 'registerForm']);
        $router->post('/inscription', [\App\Controllers\AuthController::class, 'register']);
        $router->get('/deconnexion', [\App\Controllers\AuthController::class, 'logout']);
    }
}
