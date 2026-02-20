<?php
namespace App\Modules\Reports\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Helpers;
use App\Modules\Reports\Models\Report;

final class ReportController extends Controller
{
    private const REASONS = [
        'spam' => 'Spam',
        'fake' => 'Informations fausses',
        'offensive' => 'Contenu offensant',
        'copyright' => "Droits d'auteur",
        'other' => 'Autre',
    ];

    public function index(): void
    {
        Auth::requireLogin();

        $page = isset($_GET['page']) && ctype_digit((string)$_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;

        $items = Report::listForUser(Auth::id(), $page, $perPage);
        $total = Report::countForUser(Auth::id());
        $totalPages = (int)ceil($total / $perPage);

        $this->render('modules/reports/index', [
            'reports' => $items,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'csrf' => Csrf::token(),
            'reasons' => self::REASONS,
        ]);
    }

    public function reportBook(): void
    {
        Auth::requireLogin();
        $this->requirePostCsrf();

        $bookId = (int)($_POST['book_id'] ?? 0);
        $reason = trim((string)($_POST['reason'] ?? ''));
        $comment = trim((string)($_POST['comment'] ?? ''));
        $back = (string)($_POST['back'] ?? '');

        if ($bookId <= 0 || !array_key_exists($reason, self::REASONS)) {
            Helpers::redirect('/livres');
        }

        if (mb_strlen($comment) > 2000) {
            $comment = mb_substr($comment, 0, 2000);
        }

        Report::createForBook(Auth::id(), $bookId, $reason, $comment);

        Helpers::redirect($this->safeBackPath($back !== '' ? $back : ('/livre?id=' . $bookId)));
    }

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
