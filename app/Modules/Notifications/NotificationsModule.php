<?php
namespace App\Modules\Notifications;

use App\Core\Router;

final class NotificationsModule
{
    public static function register(Router $router): void
    {
        $router->get('/notifications', [\App\Modules\Notifications\Controllers\NotificationController::class, 'index']);
        $router->post('/notification/lire-tout', [\App\Modules\Notifications\Controllers\NotificationController::class, 'markAllRead']);
    }
}
