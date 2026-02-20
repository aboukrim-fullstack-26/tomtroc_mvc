<?php

/*
 * TomTroc — Controller
 * - Reçoit la requête (GET/POST)
 * - Valide/autorise (Auth, CSRF)
 * - Appelle les modèles (DB + logique métier)
 * - Rend une vue (View::render) ou redirige après POST (PRG)
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Helpers;
use App\Models\Book;
use App\Models\User;

final class AccountController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $errors = [];
        $user = User::findById(Auth::id());

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requirePostCsrf();

            $email = trim((string)($_POST['email'] ?? ''));
            $pseudo = trim((string)($_POST['pseudo'] ?? ''));
            $pass = (string)($_POST['password'] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Adresse email invalide.";
            if ($pseudo === '' || strlen($pseudo) < 3) $errors[] = "Pseudo invalide (min 3).";

            $otherEmail = User::findByEmail($email);
            if ($otherEmail && (int)$otherEmail['id'] !== Auth::id()) $errors[] = "Email déjà utilisé.";
            $otherPseudo = User::findByPseudo($pseudo);
            if ($otherPseudo && (int)$otherPseudo['id'] !== Auth::id()) $errors[] = "Pseudo déjà utilisé.";

            if (!$errors) {
                User::update(Auth::id(), $pseudo, $email, $pass ?: null, $_FILES['avatar'] ?? null, $_POST['avatar_keep'] ?? null);
                Helpers::redirect('/mon-compte');
            }
        }

        // --- Bibliothèque (tableau) : pagination + filtre (GET) ---
		
        $bq = trim((string)($_GET['bq'] ?? ''));
        $bstatus = isset($_GET['bstatus']) && (string)$_GET['bstatus'] !== '' ? trim((string)$_GET['bstatus']) : 'all';
        $bsort = isset($_GET['bsort']) && (string)$_GET['bsort'] !== '' ? trim((string)$_GET['bsort']) : 'created_desc';
        $bpage = isset($_GET['bpage']) && ctype_digit((string)$_GET['bpage']) ? max(1, (int)$_GET['bpage']) : 1;

        $bPerPage = 5;
        $books = Book::byUserPaged(Auth::id(), $bq, $bstatus, $bpage, $bPerPage, $bsort);
        $bTotal = Book::countByUser(Auth::id(), $bq, $bstatus);
        $bTotalPages = max(1, (int)ceil($bTotal / $bPerPage));

        $this->render('account/index', [
            'user' => $user,
            'books' => $books,
            'errors' => $errors,
            'csrf' => Csrf::token(),
            'bq' => $bq,
            'bstatus' => $bstatus,
            'bsort' => $bsort,
            'bpage' => $bpage,
            'bTotalPages' => $bTotalPages,
        ]);
    }

    public function publicProfile(): void
    {
        // Profil public accessible via /profil?id=xx
        $id = (int)($_GET['id'] ?? 0);
        $user = User::findById($id);

        if (!$user) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        // --- Pagination + filtres sur la bibliothèque du profil ---
        $bq = trim((string)($_GET['bq'] ?? ''));
        $bstatus = isset($_GET['bstatus']) && (string)$_GET['bstatus'] !== '' ? trim((string)$_GET['bstatus']) : 'all';
        $bsort = isset($_GET['bsort']) && (string)$_GET['bsort'] !== '' ? trim((string)$_GET['bsort']) : 'created_desc';
        $bpage = isset($_GET['bpage']) && ctype_digit((string)$_GET['bpage']) ? max(1, (int)$_GET['bpage']) : 1;

        // ✅ demandé: 5 enregistrements par page sur le profil
        $bPerPage = 5;

        $books = Book::byUserPaged((int)$user['id'], $bq, $bstatus, $bpage, $bPerPage, $bsort);
        $bTotal = Book::countByUser((int)$user['id'], $bq, $bstatus);
        $bTotalPages = max(1, (int)ceil($bTotal / $bPerPage));

        $this->render('account/public', [
            'user' => $user,
            'books' => $books,
            'csrf' => Csrf::token(),
            'bq' => $bq,
            'bstatus' => $bstatus,
            'bsort' => $bsort,
            'bpage' => $bpage,
            'bTotal' => $bTotal,
            'bTotalPages' => $bTotalPages,
        ]);
    }
}
