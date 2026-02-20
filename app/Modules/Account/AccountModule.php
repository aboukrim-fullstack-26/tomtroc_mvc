<?php
namespace App\Modules\Account;

use App\Core\Router;

final class AccountModule
{
    public static function register(Router $router): void
    {
        $router->get('/mon-compte', [\App\Controllers\AccountController::class, 'index']);
        $router->post('/mon-compte', [\App\Controllers\AccountController::class, 'index']);
        $router->get('/profil', [\App\Controllers\AccountController::class, 'publicProfile']);
    }
}
