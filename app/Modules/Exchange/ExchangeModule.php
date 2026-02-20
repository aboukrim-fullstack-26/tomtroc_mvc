<?php
namespace App\Modules\Exchange;

use App\Core\Router;

final class ExchangeModule
{
    public static function register(Router $router): void
    {
        $router->get('/demandes', [\App\Modules\Exchange\Controllers\ExchangeController::class, 'index']);
        $router->post('/demande/creer', [\App\Modules\Exchange\Controllers\ExchangeController::class, 'create']);
        $router->post('/demande/accepter', [\App\Modules\Exchange\Controllers\ExchangeController::class, 'accept']);
        $router->post('/demande/refuser', [\App\Modules\Exchange\Controllers\ExchangeController::class, 'reject']);
    }
}
