<?php
namespace App\Modules\Favorites\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Helpers;
use App\Core\Database;
use App\Modules\Favorites\Models\Favorite;

/**
 * FavoriteController
 * - Sécurité: Auth obligatoire + CSRF obligatoire pour les POST
 * - PRG: après add/remove -> redirect (retour à la page précédente)
 */
final class FavoriteController extends Controller
{
    /** GET /favoris */
    public function index(): void
    {
        Auth::requireLogin();

        $favorites = Favorite::listForUser(Auth::id());

        $this->render('modules/favorites/index', [
            'favorites' => $favorites,
            'csrf' => Csrf::token(),
        ]);
    }

    /** POST /favori/ajouter */
    public function add(): void
    {
        Auth::requireLogin();
        $this->requirePostCsrf();

        $bookId = (int)($_POST['book_id'] ?? 0);
        if ($bookId > 0) {
            Favorite::add(Auth::id(), $bookId);			
        }

        Helpers::redirect($this->safeBackPath((string)($_POST['back'] ?? '/favoris')));
    }

    /** POST /favori/supprimer */
    public function remove(): void
    {
        Auth::requireLogin();
        $this->requirePostCsrf();

        $bookId = (int)($_POST['book_id'] ?? 0);
        if ($bookId > 0) {
            Favorite::remove(Auth::id(), $bookId);
        }

        Helpers::redirect($this->safeBackPath((string)($_POST['back'] ?? '/favoris')));
    }

    /**
     * Empêche le doublon BASE_URL + PATH (ex: /tomtroc_mvc/public/tomtroc_mvc/public/...)
     * On renvoie un chemin relatif compatible Helpers::redirect()
     */
    private function safeBackPath(string $back): string
    {
        $path  = parse_url($back, PHP_URL_PATH) ?? '/';
        $query = parse_url($back, PHP_URL_QUERY);
        $full  = $path . ($query ? ('?' . $query) : '');

        $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
        $basePath = rtrim($basePath, '/');

        if ($basePath !== '' && str_starts_with($full, $basePath)) {
            $full = substr($full, strlen($basePath)) ?: '/';
        }

        if ($full === '' || $full[0] !== '/') $full = '/' . ltrim($full, '/');
        return $full;
    }
}
