<?php
/**
 * app/Modules/TopBooks/TopBooksModule.php
 *
 * Module : TopBooks (Top 5)
 * Auteur : @aboukrim
 */

namespace App\Modules\TopBooks;

use App\Core\Router;

final class TopBooksModule
{
    public static function register(Router $router): void
    {
        $router->get('/top-livres', [\App\Modules\TopBooks\Controllers\TopBooksController::class, 'index']);
    }
}
