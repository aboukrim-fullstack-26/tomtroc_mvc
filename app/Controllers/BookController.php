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

final class BookController extends Controller
{
    
    /**
     * Page "Nos livres à l'échange"
     * - Recherche par titre/auteur (q)
     * - Filtres: author + status
     * - Pagination
     *
     * NOTE: On garde la requête SQL ici (au lieu d'appeler Book::search())
     * pour être 100% robuste même si Book.php varie selon les versions.
     */
    public function index(): void
    {
        $q = trim((string)($_GET['q'] ?? ''));
        $author = trim((string)($_GET['author'] ?? ''));
        $status = (string)($_GET['status'] ?? 'available');

        $page = (int)($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;

        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $pdo = \App\Core\Database::pdo();

        // WHERE dynamique
        $where = [];
        $params = [];

        // Filtre disponibilité (par défaut: available)
        if ($status !== 'all' && $status !== '') {
            if ($status === 'available') {
                $where[] = "b.status IN ('available','disponible')";
            } elseif ($status === 'unavailable') {
                $where[] = "b.status IN ('unavailable','indisponible','non_disponible','not_available')";
            } else {
                $where[] = "b.status = :status";
                $params['status'] = $status;
            }
        }

        // Recherche par texte
        if ($q !== '') {
            $where[] = "(b.title LIKE :q1 OR b.author LIKE :q2)";
            $params['q1'] = '%' . $q . '%';
            $params['q2'] = '%' . $q . '%';
        }

        // Filtre auteur (exact via select => on tolère avec LIKE)
        if ($author !== '') {
            $where[] = "b.author LIKE :author";
            $params['author'] = '%' . $author . '%';
        }

        $sqlBase = " FROM books b
                     JOIN users u ON u.id = b.user_id";

        $sqlWhere = $where ? (" WHERE " . implode(" AND ", $where)) : "";

        // Total pour pagination
        $stmtCount = $pdo->prepare("SELECT COUNT(*)" . $sqlBase . $sqlWhere);
        foreach ($params as $k => $v) {
            $stmtCount->bindValue(':' . $k, $v);
        }
        $stmtCount->execute();
        $total = (int)$stmtCount->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));

        // Résultats paginés
        $stmt = $pdo->prepare(
            "SELECT b.*, u.pseudo AS owner_pseudo" . $sqlBase . $sqlWhere . "
             ORDER BY b.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $books = $stmt->fetchAll();

        // Liste auteurs (pour select)
        $authors = \App\Models\Book::distinctAuthors();

        $hasResult = count($books) > 0;

        $this->render('books/index', [
            'books' => $books,
            'q' => $q,
            'author' => $author,
            'status' => $status,
            'authors' => $authors,
            'page' => $page,
            'totalPages' => $totalPages,
            'hasResult' => $hasResult,
        ]);
    }


    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $book = Book::find($id);
        if (!$book) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }
        $owner = User::findById((int)$book['user_id']);

        $this->render('books/show', [
            'book' => $book,
            'owner' => $owner,
            'csrf' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requirePostCsrf();

            $data = [
                'title' => trim((string)($_POST['title'] ?? '')),
                'author' => trim((string)($_POST['author'] ?? '')),
                'description' => trim((string)($_POST['description'] ?? '')),
                'status' => trim((string)($_POST['status'] ?? 'available')),
            ];
            if ($data['title'] === '') $errors[] = "Titre requis.";
            if ($data['author'] === '') $errors[] = "Auteur requis.";
            if (!in_array($data['status'], ['available','unavailable'], true)) $errors[] = "Statut invalide.";

// --- Photo: upload OU URL (champ libre avec validation) ---
$photoSource = (string)($_POST['photo_source'] ?? 'upload');
$photoUrl = trim((string)($_POST['photo_url'] ?? ''));
$photoKeep = $_POST['photo_keep'] ?? null;

if ($photoSource === 'url') {
    if ($photoUrl === '') {
        $errors[] = "Veuillez saisir une URL d'image.";
    } elseif (!filter_var($photoUrl, FILTER_VALIDATE_URL)) {
        $errors[] = "URL invalide.";
    } else {
        $u = parse_url($photoUrl);
        $scheme = strtolower($u['scheme'] ?? '');
        if (!in_array($scheme, ['http','https'], true)) {
            $errors[] = "L'URL doit commencer par http ou https.";
        } else {
            $path = strtolower($u['path'] ?? '');
            if (!preg_match('/\.(jpe?g|png|webp)$/', $path)) {
                $errors[] = "L'URL doit pointer vers une image (.jpg, .png, .webp).";
            } else {
                $photoKeep = $photoUrl; // on stocke l'URL directement dans photo_path
            }
        }
    }
}


            if (!$errors) {
                $bookId = $photoFile = ($photoSource === 'upload') ? ($_FILES['photo'] ?? null) : null;
                $bookId = Book::create(Auth::id(), $data, $photoFile, $photoKeep);
                Helpers::redirect('/livre?id=' . $bookId);
            }
        }

        $this->render('books/edit', [
            'mode' => 'create',
            'book' => null,
            'errors' => $errors,
            'csrf' => Csrf::token(),
        ]);
    }

    public function edit(): void
    {
        Auth::requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $book = Book::find($id);
        if (!$book || (int)$book['user_id'] !== Auth::id()) {
            http_response_code(403);
            exit('Accès interdit.');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requirePostCsrf();

            $data = [
                'title' => trim((string)($_POST['title'] ?? '')),
                'author' => trim((string)($_POST['author'] ?? '')),
                'description' => trim((string)($_POST['description'] ?? '')),
                'status' => trim((string)($_POST['status'] ?? 'available')),
            ];
            if ($data['title'] === '') $errors[] = "Titre requis.";
            if ($data['author'] === '') $errors[] = "Auteur requis.";
            if (!in_array($data['status'], ['available','unavailable'], true)) $errors[] = "Statut invalide.";

// --- Photo: upload OU URL (champ libre avec validation) ---
$photoSource = (string)($_POST['photo_source'] ?? 'upload');
$photoUrl = trim((string)($_POST['photo_url'] ?? ''));
$photoKeep = $_POST['photo_keep'] ?? null;

if ($photoSource === 'url') {
    if ($photoUrl === '') {
        $errors[] = "Veuillez saisir une URL d'image.";
    } elseif (!filter_var($photoUrl, FILTER_VALIDATE_URL)) {
        $errors[] = "URL invalide.";
    } else {
        $u = parse_url($photoUrl);
        $scheme = strtolower($u['scheme'] ?? '');
        if (!in_array($scheme, ['http','https'], true)) {
            $errors[] = "L'URL doit commencer par http ou https.";
        } else {
            $path = strtolower($u['path'] ?? '');
            if (!preg_match('/\.(jpe?g|png|webp)$/', $path)) {
                $errors[] = "L'URL doit pointer vers une image (.jpg, .png, .webp).";
            } else {
                $photoKeep = $photoUrl; // on stocke l'URL directement dans photo_path
            }
        }
    }
}


            if (!$errors) {
                $photoFile = ($photoSource === 'upload') ? ($_FILES['photo'] ?? null) : null;
                Book::update($id, Auth::id(), $data, $photoFile, $photoKeep);
                Helpers::redirect('/mon-compte');
            }
        }

        $this->render('books/edit', [
            'mode' => 'edit',
            'book' => $book,
            'errors' => $errors,
            'csrf' => Csrf::token(),
        ]);
    }

    public function delete(): void
    {
        Auth::requireLogin();
        $this->requirePostCsrf();
        $id = (int)($_POST['id'] ?? 0);
        Book::delete($id, Auth::id());
        Helpers::redirect('/mon-compte');
    }
}
