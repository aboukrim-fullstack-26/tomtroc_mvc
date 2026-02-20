<?php
namespace App\Modules\Message;

use App\Core\Router;

final class MessageModule
{
    public static function register(Router $router): void
    {
        $router->get('/messagerie', [\App\Controllers\MessageController::class, 'index']);
        $router->get('/messages',   [\App\Controllers\MessageController::class, 'index']);
        $router->post('/message/nouveau', [\App\Controllers\MessageController::class, 'startOrSend']);
    }
}
