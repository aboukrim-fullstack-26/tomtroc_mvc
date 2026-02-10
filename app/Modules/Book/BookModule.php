<?php
/**
 * app/Modules/Book/BookModule.php
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

namespace App\Modules\Book;

use App\Core\Router;

/**
 * BookModule (V4)
 * Déclare les routes du module Book.
 * Activer/désactiver via app/config/modules.php
 */
final class BookModule
{
    public static function register(Router $router): void
    {
        $router->get('/livres', [\App\Controllers\BookController::class, 'index']);
        $router->get('/livre', [\App\Controllers\BookController::class, 'show']);
        $router->get('/livre/creer', [\App\Controllers\BookController::class, 'create']);
        $router->post('/livre/creer', [\App\Controllers\BookController::class, 'create']);
        $router->get('/livre/editer', [\App\Controllers\BookController::class, 'edit']);
        $router->post('/livre/editer', [\App\Controllers\BookController::class, 'edit']);
        $router->post('/livre/supprimer', [\App\Controllers\BookController::class, 'delete']);
    }
}
