<?php
/**
 * app/Modules/Message/MessageModule.php
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

namespace App\Modules\Message;

use App\Core\Router;

/**
 * MessageModule (V4)
 * Déclare les routes du module Message.
 * Activer/désactiver via app/config/modules.php
 */
final class MessageModule
{
    public static function register(Router $router): void
    {
        $router->get('/messagerie', [\App\Controllers\MessageController::class, 'index']);
        $router->post('/message/nouveau', [\App\Controllers\MessageController::class, 'create']);
    }
}
