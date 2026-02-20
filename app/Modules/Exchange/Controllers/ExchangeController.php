<?php
namespace App\Modules\Exchange\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Helpers;
use App\Modules\Exchange\Models\ExchangeRequest;

final class ExchangeController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        $page = isset($_GET['page']) && ctype_digit((string)$_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;

        $filters = [
            'status' => isset($_GET['status']) ? trim((string)$_GET['status']) : '',
            'q'      => isset($_GET['q']) ? trim((string)$_GET['q']) : '',
            'sort'   => isset($_GET['sort']) ? trim((string)$_GET['sort']) : 'date',
            'dir'    => (isset($_GET['dir']) && strtolower((string)$_GET['dir']) === 'asc') ? 'asc' : 'desc',
            'box'    => isset($_GET['box']) ? trim((string)$_GET['box']) : 'all',
        ];

        $items = ExchangeRequest::paginateAllForUser(Auth::id(), $page, $perPage, $filters);
        $total = ExchangeRequest::countAllForUser(Auth::id(), $filters);
        $totalPages = (int)ceil($total / $perPage);

        $this->render('modules/exchange/index', [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters,
            'csrf' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        $this->requirePostCsrf();

        $bookId = (int)($_POST['book_id'] ?? 0);
        $message = trim((string)($_POST['message'] ?? ''));
        $back = (string)($_POST['back'] ?? '/livres');

        if ($bookId > 0) {
            ExchangeRequest::createFromBook(Auth::id(), $bookId, $message);
        }

        Helpers::redirect($this->safeBackPath($back));
    }

    public function accept(): void
    {
        Auth::requireLogin();
        $this->requirePostCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) ExchangeRequest::updateStatus($id, Auth::id(), 'accepted');

        Helpers::redirect('/demandes');
    }

    public function reject(): void
    {
        Auth::requireLogin();
        $this->requirePostCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) ExchangeRequest::updateStatus($id, Auth::id(), 'rejected');

        Helpers::redirect('/demandes');
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
