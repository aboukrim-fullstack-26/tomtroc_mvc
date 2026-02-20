<?php
namespace App\Modules\Favorites;

use App\Core\Router;

/**
 * Module Favorites (Favoris)
 * - Active/désactive via app/config/modules.php
 * - Routes:
 *   GET  /favoris (alias /favori)
 *   POST /favori/ajouter
 *   POST /favori/supprimer
 */
final class FavoritesModule
{
    public static function register(Router $router): void
    {
        $router->get('/favoris', [\App\Modules\Favorites\Controllers\FavoriteController::class, 'index']);
        $router->get('/favori',  [\App\Modules\Favorites\Controllers\FavoriteController::class, 'index']); // alias

        $router->post('/favori/ajouter', [\App\Modules\Favorites\Controllers\FavoriteController::class, 'add']);
        $router->post('/favori/supprimer', [\App\Modules\Favorites\Controllers\FavoriteController::class, 'remove']);
    }
}
