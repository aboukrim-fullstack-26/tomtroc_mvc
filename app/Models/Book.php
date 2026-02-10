<?php
/**
 * app/Models/Book.php
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

/*
 * TomTroc — Model
 * - Encapsule l'accès à la base (PDO)
 * - Porte la logique métier (CRUD + requêtes)
 * - Utilisé par les contrôleurs
 */


namespace App\Models;

use App\Core\Database;

final class Book
{
    /**
     * Méthode : find()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::pdo();

        $sql = "
            SELECT b.*, u.pseudo AS owner_pseudo
            FROM books b
            JOIN users u ON u.id = b.user_id
            WHERE b.id = ?
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $book = $stmt->fetch();

        return $book ?: null;
    }

    /**
     * Livres récents DISPONIBLES (page accueil)
     */
    public static function latest(int $limit): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT b.*, u.pseudo AS owner_pseudo
             FROM books b
             JOIN users u ON u.id = b.user_id
             WHERE b.status = 'available'
             ORDER BY b.created_at DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Recherche (Nos livres à l'échange)
     */
    /*public static function search(string $q, ?string $author = null, string $status = 'available', int $page = 1, int $perPage = 12): array
    {
        $pdo = Database::pdo();
        $q = trim($q);

        $where = [];
        $params = [];

        // Filtre disponibilité (par défaut: available)
        // Pour rester robuste, on accepte plusieurs valeurs possibles selon le seed/ancienne version.
        if ($status !== 'all' && $status !== '') {
            if ($status === 'available') {
                $where[] = "b.status IN ('available','disponible')";
            } elseif ($status === 'unavailable') {
                $where[] = "b.status IN ('unavailable','indisponible','non_disponible','not_available')";
            } else {
                // valeur libre : on filtre tel quel
                $where[] = "b.status = :status";
                $params['status'] = $status;
            }
        }

        // Recherche titre/auteur (2 placeholders distincts pour MySQL)
        if ($q !== '') {
            $where[] = "(b.title LIKE :q1 OR b.author LIKE :q2)";
            $params['q1'] = '%' . $q . '%';
            $params['q2'] = '%' . $q . '%';
        }

        // Filtre auteur (optionnel) — on utilise LIKE pour tolérer les espaces/casse
        if ($author !== null && trim($author) !== '') {
            $where[] = "b.author LIKE :author";
            $params['author'] = $author;
        }$sql = "SELECT b.*, u.pseudo AS owner_pseudo
                FROM books b
                JOIN users u ON u.id = b.user_id";

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY b.created_at DESC";

        $offset = max(0, ($page - 1) * $perPage);
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        // Normalisation des paramètres (tolérance)
        if (isset($params['author'])) {
            $params['author'] = '%' . trim((string)$params['author']) . '%';
        }

        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->bindValue(':limit', (int)$perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }



    

    /**
     * Compte le nombre de résultats pour la page "Nos livres à l'échange"
     * (utile pour la pagination)
     */
    public static function countSearchResults(string $q, ?string $author = null, string $status = 'available'): int
    {
        $pdo = Database::pdo();
        $q = trim($q);

        $where = [];
        $params = [];

        if ($status !== 'all' && $status !== '') {
            $where[] = "b.status = :status";
            $params['status'] = $status;
        }

        if ($q !== '') {
            $where[] = "(b.title LIKE :q1 OR b.author LIKE :q2)";
            $params['q1'] = '%' . $q . '%';
            $params['q2'] = '%' . $q . '%';
        }

        if ($author !== null && trim($author) !== '') {
            $where[] = "b.author = :author";
            $params['author'] = $author;
        }

        $sql = "SELECT COUNT(*) AS cnt FROM books b";
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->execute();
        $row = $stmt->fetch();

        return (int)($row['cnt'] ?? 0);
    }

    /**
     * Liste d'auteurs (pour alimenter un filtre simple)
     */
    public static function distinctAuthors(): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query("SELECT DISTINCT TRIM(author) AS author FROM books WHERE author IS NOT NULL AND TRIM(author) <> '' ORDER BY TRIM(author) ASC");
        $rows = $stmt->fetchAll();

        return array_values(array_filter(array_map(fn($r) => $r['author'] ?? null, $rows)));
    }

    /**
     * Livres d’un utilisateur avec pagination + filtres (Mon compte)
     */
    public static function byUserPaged(int $userId, string $q = '', string $status = 'all', int $page = 1, int $perPage = 10, string $sort = 'created_desc'): array
    {
        $pdo = Database::pdo();
        $q = trim($q);

        $where = ["user_id = :uid"];
        $params = ['uid' => $userId];

        if ($status !== 'all' && $status !== '') {
            $where[] = "status = :status";
            $params['status'] = $status;
        }

        if ($q !== '') {
            $where[] = "(title LIKE :q1 OR author LIKE :q2)";
            $params['q1'] = '%' . $q . '%';
            $params['q2'] = '%' . $q . '%';
        }

        $orderBy = "created_at DESC";
        if ($sort === 'title_asc') $orderBy = "title ASC";
        if ($sort === 'title_desc') $orderBy = "title DESC";
        if ($sort === 'created_asc') $orderBy = "created_at ASC";

        $sql = "SELECT * FROM books WHERE " . implode(" AND ", $where) . " ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";
        $offset = max(0, ($page - 1) * $perPage);

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->bindValue(':limit', (int)$perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Méthode : countByUser()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public static function countByUser(int $userId, string $q = '', string $status = 'all'): int
    {
        $pdo = Database::pdo();
        $q = trim($q);

        $where = ["user_id = :uid"];
        $params = ['uid' => $userId];

        if ($status !== 'all' && $status !== '') {
            $where[] = "status = :status";
            $params['status'] = $status;
        }

        if ($q !== '') {
            $where[] = "(title LIKE :q1 OR author LIKE :q2)";
            $params['q1'] = '%' . $q . '%';
            $params['q2'] = '%' . $q . '%';
        }

        $sql = "SELECT COUNT(*) AS cnt FROM books WHERE " . implode(" AND ", $where);
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->execute();
        $row = $stmt->fetch();

        return (int)($row['cnt'] ?? 0);
    }
/**
     * Livres d’un utilisateur (Mon compte)
     */
    public static function byUser(int $userId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM books WHERE user_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    /**
     * Création d’un livre (image facultative)
     */
    public static function create(
        ?int $userId,
        array $data,
        ?array $photoFile,
        ?string $photoKeep
    ): int {
        $photoPath = $photoKeep;

        $stored = Uploads::storeImage($photoFile);
        if ($stored !== null) {
            $photoPath = $stored;
        }

        $stmt = Database::pdo()->prepare(
            'INSERT INTO books (user_id, title, author, description, status, photo_path)
             VALUES (?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $userId,
            $data['title'],
            $data['author'],
            $data['description'],
            $data['status'],
            $photoPath
        ]);

        return (int) Database::pdo()->lastInsertId();
    }

    /**
     * Mise à jour d’un livre
     */
    public static function update(
        int $id,
        int $userId,
        array $data,
        ?array $photoFile,
        ?string $photoKeep
    ): void {
        $book = self::find($id);

        if (!$book || (int) $book['user_id'] !== $userId) {
            throw new \RuntimeException('Livre introuvable.');
        }

        $photoPath = $photoKeep ?? $book['photo_path'];

        $stored = Uploads::storeImage($photoFile);
        if ($stored !== null) {
            $photoPath = $stored;
        }

        $stmt = Database::pdo()->prepare(
            'UPDATE books
             SET title = ?, author = ?, description = ?, status = ?, photo_path = ?
             WHERE id = ? AND user_id = ?'
        );

        $stmt->execute([
            $data['title'],
            $data['author'],
            $data['description'],
            $data['status'],
            $photoPath,
            $id,
            $userId
        ]);
    }

    /**
     * Suppression d’un livre
     */
    public static function delete(int $id, int $userId): void
    {
        $stmt = Database::pdo()->prepare(
            'DELETE FROM books WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$id, $userId]);
    }
}
