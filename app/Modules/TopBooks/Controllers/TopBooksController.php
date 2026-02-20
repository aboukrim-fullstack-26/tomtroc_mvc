<?php
/**
 * Controller TopBooks
 * Auteur : @aboukrim
 */

namespace App\Modules\TopBooks\Controllers;

use App\Core\Controller;
use App\Modules\TopBooks\Models\TopBooks;

final class TopBooksController extends Controller
{
    public function index(): void
    {
        $books = TopBooks::top5();
        $this->render('modules/topbooks/index', [
            'books' => $books,
        ]);
    }
}
