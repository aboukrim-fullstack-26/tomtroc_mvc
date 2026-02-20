<?php
/**
 * Module Ratings (Évaluation)
 * Auteur : @aboukrim
 */

namespace App\Modules\Ratings;

use App\Core\Router;

final class RatingsModule
{
    public static function register(Router $router): void
    {
        $router->post('/note', [\App\Modules\Ratings\Controllers\RatingController::class, 'rate']);
    }
}
