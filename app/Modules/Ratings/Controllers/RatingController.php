<?php
namespace App\Modules\Ratings\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Helpers;
use App\Modules\Ratings\Models\Rating;

/**
 * RatingController
 * - Route: POST /note
 * - Sécurité: Auth obligatoire + CSRF obligatoire
 * - PRG: redirection après écriture DB
 */
final class RatingController extends Controller
{
    public function rate(): void
    {
        // POST only (évite un accès direct en GET)
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        Auth::requireLogin();
        $this->requirePostCsrf();

        $bookId = (int)($_POST['book_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $back   = (string)($_POST['back'] ?? '');

        if ($bookId <= 0 || $rating < 1 || $rating > 5) {
            Helpers::redirect('/livres');
        }

        Rating::rate(Auth::id(), $bookId, $rating);

        // Redirection sûre vers la page précédente (évite doublon /tomtroc_mvc/public/tomtroc_mvc/public/...)
        $path  = parse_url($back, PHP_URL_PATH) ?? '';
        $query = parse_url($back, PHP_URL_QUERY);
        $full  = ($path !== '' ? $path : '/livre') . ($query ? ('?' . $query) : '');

        $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
        $basePath = rtrim($basePath, '/');

        if ($basePath !== '' && str_starts_with($full, $basePath)) {
            $full = substr($full, strlen($basePath)) ?: '/';
        }

        if ($full === '' || $full[0] !== '/') {
            $full = '/' . ltrim($full, '/');
        }

        // fallback
        if ($full === '/livre' || $full === '/') {
            $full = '/livre?id=' . $bookId;
        }

        Helpers::redirect($full);
    }
}
