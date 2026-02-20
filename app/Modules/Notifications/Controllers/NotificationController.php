<?php
namespace App\Modules\Notifications\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Helpers;
use App\Modules\Notifications\Models\Notification;

final class NotificationController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        $page = isset($_GET['page']) && ctype_digit((string)$_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;

        $filters = [
            'read' => isset($_GET['read']) ? trim((string)$_GET['read']) : '',
            'q'    => isset($_GET['q']) ? trim((string)$_GET['q']) : '',
            'sort' => isset($_GET['sort']) ? trim((string)$_GET['sort']) : 'date',
            'dir'  => (isset($_GET['dir']) && strtolower((string)$_GET['dir']) === 'asc') ? 'asc' : 'desc',
        ];

        $items = Notification::paginateForUser(Auth::id(), $page, $perPage, $filters);
        $total = Notification::countForUser(Auth::id(), $filters);
        $totalPages = (int)ceil($total / $perPage);

        $this->render('modules/notifications/index', [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters,
            'csrf' => Csrf::token(),
        ]);
    }

    public function markAllRead(): void
    {
        Auth::requireLogin();
        $this->requirePostCsrf();

        Notification::markAllRead(Auth::id());
        Helpers::redirect('/notifications');
    }
}
